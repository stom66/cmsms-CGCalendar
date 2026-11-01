<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: CGCalendar (c) 2008
#      by Robert Allen (akrabat) to 2008 and
#         Robert Campbell (calguy1000@cmsmadesimple.org) (2008 and beyond)
#  An addon module for CMS Made Simple to allow displaying calendars,
#  and management and display of time based events.
#
#  This module was originally forked in 2009 from the Calendar module by Robert Allen,
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This projects homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE
if( version_compare(phpversion(),'5.4.3') < 0 ) {
    return "Minimum PHP version of 5.4.3 required";
}

$uid = get_userid();
$db = $this->GetDb();
$dict = NewDataDictionary($db);
$table_options = array('mysql' => 'ENGINE=InnoDB');

// create categories table
$fields = "
			category_id I KEY NOT NULL,
			category_name C(255) NOT NULL,
            category_bgcolor C(10),
            category_fgcolor C(10),
			category_order I
		";
$sql_array = $dict->CreateTableSQL($this->categories_table_name, $fields, $table_options);
$dict->ExecuteSQLArray($sql_array);

$db->CreateSequence($this->categories_table_name . '_seq');

$sqlarray = $dict->CreateIndexSQL('index_category_name',$this->categories_table_name,'category_name');
$dict->ExecuteSQLArray($sqlarray);

// create fields table
$fields = "field_name C(255) KEY,
           field_type I,
           field_searchable I1,
           field_extra X,
           field_order I
	  ";
$sqlarray = $dict->CreateTableSQL($this->fields_table_name, $fields, $table_options);
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->CreateIndexSQL('index_field_name',$this->fields_table_name,'field_name');
$dict->ExecuteSQLArray($sqlarray);


// create events table
$fields = "
			event_id I KEY,
			event_title C(255),
			event_summary X,
			event_details X,
			event_date_start ".CMS_ADODB_DT.",
			event_date_end ".CMS_ADODB_DT.",
			event_parent_id I,
			event_recur_period C(10),
			event_date_recur_end ".CMS_ADODB_DT.",
			event_created_by I,
			event_create_date ".CMS_ADODB_DT.",
			event_modified_date ".CMS_ADODB_DT.",
            event_recur_nevents I,
            event_recur_interval I,
            event_recur_weekdays C(255),
            event_recur_monthdays C(255),
            event_allows_overlap I1,
            event_all_day I1,
            event_status C(1)";
$sqlarray = $dict->CreateTableSQL($this->events_table_name, $fields, $table_options);
$dict->ExecuteSQLArray($sqlarray);

$db->Execute('ALTER TABLE '.$this->events_table_name.' ADD FULLTEXT(event_title,event_summary,event_details)');
$sqlarray = $dict->CreateIndexSQL('cgcalendar_events_date',$this->events_table_name,'event_date_start,event_date_end');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL('cgcalendar_events_date2',$this->events_table_name,'event_date_start,event_date_end,event_status');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL('cgcalendar_events_created',$this->events_table_name,'event_create_date');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL('cgcalendar_events_modified',$this->events_table_name,'event_modified_date');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->CreateIndexSQL('cgcalendar_events_parent',$this->events_table_name,'event_parent_id');
$dict->ExecuteSQLArray($sqlarray);

$db->CreateSequence($this->events_table_name.'_seq');

// create event_field_values table
$fields = "
			field_name C(255) KEY,
			event_id I KEY,
			field_value X
		";
$sqlarray = $dict->CreateTableSQL($this->event_field_values_table_name, $fields, $table_options );
$dict->ExecuteSQLArray( $sqlarray );

// create events_to_categories table
$fields = "
			category_id I KEY,
			event_id I KEY
		";
$sqlarray = $dict->CreateTableSQL($this->events_to_categories_table_name, $fields, $table_options );
$dict->ExecuteSQLArray( $sqlarray );

// set up a General category
$new_id = $db->GenID($this->categories_table_name.'_seq');

$sql = 'INSERT INTO ' . $this->categories_table_name . " (category_id, category_name, category_order)
						VALUES ($new_id, 'General', 50)";
$db->Execute($sql);



//
// templates
//

// todo: get the id of the default design.

try {
    // search form template type
    $search_template_type = new \CmsLayoutTemplateType();
    $search_template_type->set_originator($this->GetName());
    $search_template_type->set_name('searchform');
    $search_template_type->set_dflt_flag(TRUE);
    $search_template_type->set_lang_callback('CGCalendar::page_type_lang_callback');
    $search_template_type->set_content_callback('CGCalendar::reset_page_type_defaults');
    $search_template_type->reset_content_to_factory();
    $search_template_type->save();

    // create a sample template of this type
    $fn = __DIR__.'/templates/orig_search_template.tpl';
    if( is_file( $fn ) ) {
        $contents = @file_get_contents($fn);
        $tpl = new CmsLayoutTemplate();
        $tpl->set_name('CGCalendar Search Form Sample');
        $tpl->set_owner($uid);
        $tpl->set_content($contents);
        $tpl->set_type($search_template_type);
        $tpl->set_type_dflt(TRUE);
        $tpl->save();
    }
}
catch( \Exception $e ) {
    // log it
    debug_to_log('ERROR: '.$e->GetMessage());
    debug_to_log($e->GetTraceAsString());
    audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
    $searchresult_template_type = new \CmsLayoutTemplateType();
    $searchresult_template_type->set_originator($this->GetName());
    $searchresult_template_type->set_name('searchresult');
    $searchresult_template_type->set_dflt_flag(TRUE);
    $searchresult_template_type->set_lang_callback('CGCalendar::page_type_lang_callback');
    $searchresult_template_type->set_content_callback('CGCalendar::reset_page_type_defaults');
    $searchresult_template_type->reset_content_to_factory();
    $searchresult_template_type->save();

    // create a sample template of this type
    $fn = __DIR__.'/templates/orig_searchresult_template.tpl';
    if( is_file( $fn ) ) {
        $contents = @file_get_contents($fn);
        $tpl = new CmsLayoutTemplate();
        $tpl->set_name('CGCalendar Search Results Sample');
        $tpl->set_owner($uid);
        $tpl->set_content($contents);
        $tpl->set_type($searchresult_template_type);
        $tpl->set_type_dflt(TRUE);
        $tpl->save();
    }
}
catch( \Exception $e ) {
    // log it
    debug_to_log('ERROR: '.$e->GetMessage());
    debug_to_log($e->GetTraceAsString());
    audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
    $event_template_type = new \CmsLayoutTemplateType();
    $event_template_type->set_originator($this->GetName());
    $event_template_type->set_name('event');
    $event_template_type->set_dflt_flag(TRUE);
    $event_template_type->set_lang_callback('CGCalendar::page_type_lang_callback');
    $event_template_type->set_content_callback('CGCalendar::reset_page_type_defaults');
    $event_template_type->reset_content_to_factory();
    $event_template_type->save();

    // create a sample template of this type
    $fn = __DIR__.'/templates/orig_event_template.tpl';
    if( is_file( $fn ) ) {
        $contents = @file_get_contents($fn);
        $tpl = new CmsLayoutTemplate();
        $tpl->set_name('CGCalendar Event View Sample');
        $tpl->set_owner($uid);
        $tpl->set_content($contents);
        $tpl->set_type($event_template_type);
        $tpl->set_type_dflt(TRUE);
        $tpl->save();
    }
}
catch( \Exception $e ) {
    // log it
    debug_to_log('ERROR: '.$e->GetMessage());
    debug_to_log($e->GetTraceAsString());
    audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
    $eventlist_template_type = new \CmsLayoutTemplateType();
    $eventlist_template_type->set_originator($this->GetName());
    $eventlist_template_type->set_name('eventlist');
    $eventlist_template_type->set_dflt_flag(TRUE);
    $eventlist_template_type->set_lang_callback('CGCalendar::page_type_lang_callback');
    $eventlist_template_type->set_content_callback('CGCalendar::reset_page_type_defaults');
    $eventlist_template_type->reset_content_to_factory();
    $eventlist_template_type->save();

    // create a sample template of this type
    $fn = __DIR__.'/templates/orig_list_template.tpl';
    if( is_file( $fn ) ) {
        $contents = @file_get_contents($fn);
        $tpl = new CmsLayoutTemplate();
        $tpl->set_name('CGCalendar List View Sample');
        $tpl->set_owner($uid);
        $tpl->set_content($contents);
        $tpl->set_type($eventlist_template_type);
        $tpl->set_type_dflt(TRUE);
        $tpl->save();
    }
}
catch( \Exception $e ) {
    // log it
    debug_to_log('ERROR: '.$e->GetMessage());
    debug_to_log($e->GetTraceAsString());
    audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
    $myevents_template_type = new \CmsLayoutTemplateType();
    $myevents_template_type->set_originator($this->GetName());
    $myevents_template_type->set_name('myevents');
    $myevents_template_type->set_dflt_flag(TRUE);
    $myevents_template_type->set_lang_callback('CGCalendar::page_type_lang_callback');
    $myevents_template_type->set_content_callback('CGCalendar::reset_page_type_defaults');
    $myevents_template_type->reset_content_to_factory();
    $myevents_template_type->save();

    // create a sample template of this type
    $fn = __DIR__.'/templates/orig_myevents_template.tpl';
    if( is_file( $fn ) ) {
        $contents = @file_get_contents($fn);
        $tpl = new CmsLayoutTemplate();
        $tpl->set_name('CGCalendar MyEvents View Sample');
        $tpl->set_owner($uid);
        $tpl->set_content($contents);
        $tpl->set_type($myevents_template_type);
        $tpl->set_type_dflt(TRUE);
        $tpl->save();
    }
}
catch( \Exception $e ) {
    // log it
    debug_to_log('ERROR: '.$e->GetMessage());
    debug_to_log($e->GetTraceAsString());
    audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
    $editevent_template_type = new \CmsLayoutTemplateType();
    $editevent_template_type->set_originator($this->GetName());
    $editevent_template_type->set_name('editevent');
    $editevent_template_type->set_dflt_flag(TRUE);
    $editevent_template_type->set_lang_callback('CGCalendar::page_type_lang_callback');
    $editevent_template_type->set_content_callback('CGCalendar::reset_page_type_defaults');
    $editevent_template_type->reset_content_to_factory();
    $editevent_template_type->save();

    // create a sample template of this type
    $fn = __DIR__.'/templates/orig_editevent_template.tpl';
    if( is_file( $fn ) ) {
        $contents = @file_get_contents($fn);
        $tpl = new CmsLayoutTemplate();
        $tpl->set_name('CGCalendar Edit Event Form Sample');
        $tpl->set_owner($uid);
        $tpl->set_content($contents);
        $tpl->set_type($editevent_template_type);
        $tpl->set_type_dflt(TRUE);
        $tpl->save();
    }
}
catch( \Exception $e ) {
    // log it
    debug_to_log('ERROR: '.$e->GetMessage());
    debug_to_log($e->GetTraceAsString());
    audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
    $deleteevent_template_type = new \CmsLayoutTemplateType();
    $deleteevent_template_type->set_originator($this->GetName());
    $deleteevent_template_type->set_name('deleteevent');
    $deleteevent_template_type->set_dflt_flag(TRUE);
    $deleteevent_template_type->set_lang_callback('CGCalendar::page_type_lang_callback');
    $deleteevent_template_type->set_content_callback('CGCalendar::reset_page_type_defaults');
    $deleteevent_template_type->reset_content_to_factory();
    $deleteevent_template_type->save();

    // create a sample template of this type
    $fn = __DIR__.'/templates/orig_deleteevent_template.tpl';
    if( is_file( $fn ) ) {
        $contents = @file_get_contents($fn);
        $tpl = new CmsLayoutTemplate();
        $tpl->set_name('CGCalendar Delete Event Form Sample');
        $tpl->set_owner($uid);
        $tpl->set_content($contents);
        $tpl->set_type($deleteevent_template_type);
        $tpl->set_type_dflt(TRUE);
        $tpl->save();
    }
}
catch( \Exception $e ) {
    // log it
    debug_to_log('ERROR: '.$e->GetMessage());
    debug_to_log($e->GetTraceAsString());
    audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

try {
    $fullcalendar_template_type = new \CmsLayoutTemplateType();
    $fullcalendar_template_type->set_originator($this->GetName());
    $fullcalendar_template_type->set_name('fullcalendar');
    $fullcalendar_template_type->set_dflt_flag(TRUE);
    $fullcalendar_template_type->set_lang_callback('CGCalendar::page_type_lang_callback');
    $fullcalendar_template_type->set_content_callback('CGCalendar::reset_page_type_defaults');
    $fullcalendar_template_type->reset_content_to_factory();
    $fullcalendar_template_type->save();

    // create a sample template of this type
    $fn = __DIR__.'/templates/orig_fullcalendar_template.tpl';
    if( is_file( $fn ) ) {
        $contents = @file_get_contents($fn);
        $tpl = new CmsLayoutTemplate();
        $tpl->set_name('CGCalendar FullCalendar View Sample');
        $tpl->set_owner($uid);
        $tpl->set_content($contents);
        $tpl->set_type($fullcalendar_template_type);
        $tpl->set_type_dflt(TRUE);
        $tpl->save();
    }
}
catch( \Exception $e ) {
    // log it
    debug_to_log('ERROR: '.$e->GetMessage());
    debug_to_log($e->GetTraceAsString());
    audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
}

// Preferences
$this->SetPreference('uploaddirectory','');

// Events
$this->CreateEvent('EventAdded');
$this->CreateEvent('EventEditedPre');
$this->CreateEvent('EventEdited');
$this->CreateEvent('EventDeleted');
$this->CreateEvent('CategoryAdded');
$this->CreateEvent('CategoryEdited');
$this->CreateEvent('CategoryDeleted');

// Permissions
$this->CreatePermission('Modify Calendar', 'Modify Calendar');
$this->CreatePermission('Add Calendar Events', 'Add Calendar Events');
$this->CreatePermission('Edit My Calendar Events', 'Edit My Calendar Events');
$this->CreatePermission('Manage Calendar Attributes','Manage Calendar Attributes');
