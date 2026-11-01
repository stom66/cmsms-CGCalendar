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
$db = $this->GetDb();				/* @var $db ADOConnection */
$dict = NewDataDictionary($db); 	/* @var $dict ADODB_DataDict */

if( version_compare(phpversion(),'5.4.3') < 0 ) {
    return "Minimum PHP version of 5.4.3 required";
}

define('CGCALENDAR_PREF_NEWCALENDAR_TEMPLATE','cgcalendar_pref_newcalendar_template');
define('CGCALENDAR_PREF_DFLTCALENDAR_TEMPLATE','cgcalendar_pref_dfltcalendar_template');
define('CGCALENDAR_PREF_NEWLIST_TEMPLATE','cgcalendar_pref_newlist_template');
define('CGCALENDAR_PREF_DFLTLIST_TEMPLATE','cgcalendar_pref_dfltlist_template');
define('CGCALENDAR_PREF_NEWUPCOMINGLIST_TEMPLATE','cgcalendar_pref_newupcominglist_template');
define('CGCALENDAR_PREF_DFLTUPCOMINGLIST_TEMPLATE','cgcalendar_pref_dfltupcominglist_template');
define('CGCALENDAR_PREF_NEWEVENT_TEMPLATE','cgcalendar_pref_newevent_template');
define('CGCALENDAR_PREF_DFLTEVENT_TEMPLATE','cgcalendar_pref_dfltevent_template');
define('CGCALENDAR_PREF_NEWSEARCH_TEMPLATE','cgcalendar_pref_newsearch_template');
define('CGCALENDAR_PREF_DFLTSEARCH_TEMPLATE','cgcalendar_pref_dfltsearch_template');
define('CGCALENDAR_PREF_NEWSEARCHRESULT_TEMPLATE','cgcalendar_pref_newsearchresult_template');
define('CGCALENDAR_PREF_DFLTSEARCHRESULT_TEMPLATE','cgcalendar_pref_dfltsearchresult_template');
define('CGCALENDAR_PREF_NEWMYEVENTS_TEMPLATE','cgcalendar_pref_newmyevents_template');
define('CGCALENDAR_PREF_DFLTMYEVENTS_TEMPLATE','cgcalendar_pref_dfltmyevents_template');
define('CGCALENDAR_PREF_NEWEDITEVENT_TEMPLATE','cgcalendar_pref_neweditevent_template');
define('CGCALENDAR_PREF_DFLTEDITEVENT_TEMPLATE','cgcalendar_pref_dflteditevent_template');
define('CGCALENDAR_PREF_NEWDELETEEVENT_TEMPLATE','cgcalendar_pref_newdeleteevent_template');
define('CGCALENDAR_PREF_DFLTDELETEEVENT_TEMPLATE','cgcalendar_pref_dfltdeleteevent_template');
define('CGCALENDAR_PREF_NEWFULLCALENDAR_TEMPLATE','cgcalendar_pref_newfullcalendar_template');
define('CGCALENDAR_PREF_DFLTFULLCALENDAR_TEMPLATE','cgcalendar_pref_dfltfullcalendar_template');

switch( $oldversion )
  {
  case '1.0':
  case '1.0.1':
    $db->Execute('ALTER TABLE '.$this->events_table_name.' ADD FULLTEXT(event_title,event_summary,event_details)');
    $sqlarray = $dict->AddColumnSQL($this->events_table_name, "event_recur_monthdays C(255)");
    $dict->ExecuteSQLArray($sqlarray);

    // Search form template(s)
    $fn = cms_join_path(dirname(__FILE__),'templates','orig_search_template.tpl');
    if( file_exists($fn) )
      {
	$template = file_get_contents($fn);
	$this->SetPreference(CGCALENDAR_PREF_NEWSEARCH_TEMPLATE,$template);
	$this->SetTemplate('search_Sample',$template);
	$this->SetPreference(CGCALENDAR_PREF_DFLTSEARCH_TEMPLATE,'Sample');
      }

    // Search result template(s)
    $fn = cms_join_path(dirname(__FILE__),'templates','orig_searchresult_template.tpl');
    if( file_exists($fn) )
      {
	$template = file_get_contents($fn);
	$this->SetPreference(CGCALENDAR_PREF_NEWSEARCHRESULT_TEMPLATE,$template);
	$this->SetTemplate('searchresult_Sample',$template);
	$this->SetPreference(CGCALENDAR_PREF_DFLTSEARCHRESULT_TEMPLATE,'Sample');
      }

  case '1.2':
  case '1.2.1':
  case '1.2.2':
  case '1.2.3':
  case '1.2.4':
  case '1.2.5':
    $sqlarray = $dict->AddColumnSQL($this->events_table_name,
				    "event_allows_overlap I1");
    $dict->ExecuteSQLArray($sqlarray);

  case '1.3':
  case '1.3.1':
    // My Events template(s)
    $fn = cms_join_path(dirname(__FILE__),'templates','orig_myevents_template.tpl');
    if( file_exists($fn) )
      {
	$template = file_get_contents($fn);
	$this->SetPreference(CGCALENDAR_PREF_NEWMYEVENTS_TEMPLATE,$template);
	$this->SetTemplate('myevents_Sample',$template);
	$this->SetPreference(CGCALENDAR_PREF_DFLTMYEVENTS_TEMPLATE,'Sample');
      }

    // Edit Event form template(s)
    $fn = cms_join_path(dirname(__FILE__),'templates','orig_editevent_template.tpl');
    if( file_exists($fn) )
      {
	$template = file_get_contents($fn);
	$this->SetPreference(CGCALENDAR_PREF_NEWEDITEVENT_TEMPLATE,$template);
	$this->SetTemplate('editevent_Sample',$template);
	$this->SetPreference(CGCALENDAR_PREF_DFLTEDITEVENT_TEMPLATE,'Sample');
      }

    $sqlarray = $dict->CreateIndexSQL('event_period',$this->events_table_name,
				      'event_date_start,event_date_end');
    $dict->ExecuteSQLArray( $sqlarray );

    $query = 'UPDATE '.$this->events_table_name.'
                 SET event_created_by = event_created_by * -1 - 100';
    $db->Execute($query);

  case '1.4':
    $sqlarray = $dict->AddColumnSQL($this->fields_table_name,'field_searchable I1');
    $dict->ExecuteSQLArray($sqlarray);

    $query = 'UPDATE '.$this->fields_table_name.' SET field_searchable = 1
               WHERE field_type != 1';
    $db->Execute($query);

  case '1.4.1': // just for safety
  case '1.4.2': // just for safety
  case '1.5':
  case '1.5.1':
  case '1.5.2':
  case '1.5.3':
  case '1.5.4':
  case '1.5.5':
  case '1.5.6':
  case '1.5.7':
    $sqlarray = $dict->AddColumnSQL($this->fields_table_name,'field_extra X');
    $dict->ExecuteSQLArray($sqlarray);

  }

if( version_compare($oldversion,'1.11.1') < 0 ) {
  $cats = $this->GetCategories();
  if( is_array($cats) && count($cats) ) {
    // change the order stuff to be automatically ascending
    $query = 'UPDATE '.$this->categories_table_name.' SET category_order = ? WHERE category_id = ?';
    for( $i = 0; $i < count($cats); $i++ ) {
      $db->Execute($query,array($i,$cats[$i]['category_id']));
    }
  }

  // update fields.
  $fields = $this->GetFields();
  $sqlarray = $dict->AddColumnSQL($this->fields_table_name,'field_order I');
  $dict->ExecuteSQLArray($sqlarray);
  if( is_array($fields) && count($fields) ) {
    // add the order stuff.
    $query = 'UPDATE '.$this->fields_table_name.' SET field_extra = ?, field_order = ? WHERE field_name = ?';
    for( $i = 0; $i < count($fields); $i++ ) {
      $row = $fields[$i];
      if( $row['field_type'] != 0 && $row['field_type'] != 2 ) $row['field_searchable'] = 0;
      $field_extra = serialize(array('searchable',$row['field_searchable'],'wysiwyg'=>0));
      $db->Execute($query,array($field_extra,$i,$row['field_name']));
    }
  }
  $sqlarray = $dict->DropColumnSQL($this->fields_table_name,'field_searchable');
  $dict->ExecuteSQLArray($sqlarray);
}

if( version_compare($oldversion,'1.13') < 0 ) {
  $this->CreatePermission('Add Calendar Events', 'Add Calendar Events');
  $this->CreatePermission('Edit My Calendar Events', 'Edit My Calendar Events');
}

if( version_compare($oldversion,'1.14') < 0 ) {
    try {
        $sqlarray = $dict->AddColumnSQL($this->events_table_name, "event_all_day C(255)");
        $dict->ExecuteSQLArray($sqlarray);
    }
    catch( \Exception $e ) {
        // nothing here.
    }

    // convert events that start at 00:00, and end at 23:59 to all day events (set their start and end times to 13:00 and 14:00
    $query = 'SELECT event_id FROM '.$this->events_table_name.' WHERE HOUR(event_date_start) = 0 AND MINUTE(event_date_start) = 0 AND HOUR(event_date_end) = 23 AND MINUTE(event_date_end) = 59';
    $list = $db->GetCol($query);
    if( is_array($list) && count($list) ) {
        $query = 'UPDATE '.$this->events_table_name.' SET event_date_start = CONCAT_WS(\' \', DATE(event_date_start), \'13:00:00\'),
                     event_date_end = CONCAT_WS(\' \', DATE(event_date_end), \'14:00:00\'), event_all_day = 1
                  WHERE event_id IN ('.implode(',',$list).')';
        $db->Execute($query);
    }

    // add some columns to the categories table, and set some initial values.
    $sqlarray = $dict->AddColumnSQL($this->categories_table_name,'category_bgcolor C(10),category_fgcolor C(10)');
    $dict->ExecuteSQLArray($sqlarray);
    $query = 'UPDATE '.$this->categories_table_name.' SET category_bgcolor = ?, category_fgcolor = ?';
    $db->Execute($query,array('#3366ff','#ffffff'));

    // fix category orders
    $query = 'SELECT * FROM '.$this->categories_table_name.' ORDER BY category_order';
    $cats = $db->GetArray($query);
    if( count($cats) ) {
        $query = 'UPDATE '.$this->categories_table_name.' SET category_order = ? WHERE category_id = ?';
        for( $i = 0; $i < count($cats); $i++ ) {
            $row = $cats[$i];
            $db->Execute($query,array($i+1,$row['category_id']));
        }
    }
}

if( version_compare($oldversion,'1.15.3') < 0 ) {
    $tmp1 = $this->GetPreference(CGCALENDAR_PREF_NEWFULLCALENDAR_TEMPlATE);
    $tmp2 = $this->GetPreference(CGCALENDAR_PREF_DFLTFULLCALENDAR_TEMPLATE);
    if( !$tmp1 || !$tmp2 ) {
        $fn = cms_join_path(dirname(__FILE__),'templates','orig_fullcalendar_template.tpl');
        $template = file_get_contents($fn);
        $this->SetPreference(CGCALENDAR_PREF_NEWFULLCALENDAR_TEMPLATE,$template);
        $this->SetTemplate('fullcalendar_Sample',$template);
        $this->SetPreference(CGCALENDAR_PREF_DFLTFULLCALENDAR_TEMPLATE,'Sample');
    }
}

// Delete Event form template(s)
if( version_compare($oldversion,'1.16') < 0 ) {
    $fn = cms_join_path(dirname(__FILE__),'templates','orig_deleteevent_template.tpl');
    if( file_exists($fn) ) {
        $template = file_get_contents($fn);
        $this->SetPreference(CGCALENDAR_PREF_NEWDELETEEVENT_TEMPLATE,$template);
        $this->SetTemplate('deleteevent_Sample',$template);
        $this->SetPreference(CGCALENDAR_PREF_DFLTDELETEEVENT_TEMPLATE,'Sample');
    }
}

if( version_compare($oldversion,'2.0') < 0 ) {
    $uid = get_userid();
    $search_template_type = null;
    $searchresult_template_type = null;
    $event_template_type = null;
    $eventlist_template_type = null;
    $myevents_template_type = null;
    $editevent_template_type = null;
    $deleteevent_template_type = null;
    $fullcalendar_template_type = null;

    // create template types
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

    // a map of the old prefixes, to the new types.
    $map = array('search' => array('type_name'=>'CGCalendar::searchform',
                                   'type'=>$search_template_type,
                                   'out_prefix' => 'CGCalendar-Search-Form-'),
                 'searchresult' => array('type_name'=>'CGCalendar::searchresult',
                                         'type'=>$searchresult_template_type,
                                         'out_prefix' => 'CGCalendar-Searchresult-View-'),
                 'event' => array('type_name' => 'CGCalendar::event',
                                  'type'=>$event_template_type,
                                  'out_prefix' => 'CGCalendar-Event-View-'),
                 'list' => array('type_name'=>'CGCalendar::eventlist',
                                 'type'=>$eventlist_template_type,
                                 'out_prefix'=>'CGCalendar-List-View-'),
                 'upcominglist' => array('type_name'=>'CGCalendar::eventlist',
                                         'type'=>$eventlist_template_type,
                                         'out_prefix'=>'CGCalendar-List-View-'),
                 'myevents' => array('type_name'=>'CGCalendar::myevents',
                                     'type'=>$myevents_template_type,
                                     'out_prefix' => 'CGCalendar-Myevents-View-'),
                 'editevent' => array('type_name'=>'CGCalendar::editevent',
                                      'type'=>$editevent_template_type,
                                      'out_prefix'=>'CGCalendar-Editevent-Form-'),
                 'deleteevent' => array('type_name'=>'CGCalendar::deleteevent',
                                        'type'=>$deleteevent_template_type,
                                        'out_prefix'=>'CGCalendar-Deleteevent-Form-'),
                 'fullcalendar' => array('type_name'=>'CGCalendar::fullcalendar',
                                         'type'=>$fullcalendar_template_type,
                                         'out_prefix'=>'CGCalendar-fullCalendar-View-')
        );

    // now, just in case... we're gonna reload the types.
    foreach( $map as $key => $rec ) {
        if( !$rec['type']->get_id() ) {
            $rec['type'] = \CmsLayoutTemplateType::load('CGCalendar::'.$rec['type']->get_name());
        }
    }

    $_fix_name = function($str){
        if( \CmsAdminUtils::is_valid_itemname($str) ) return $str;
        $orig = $str;
        $str = trim($str);
        if( !CmsAdminUtils::is_valid_itemname($str[0]) ) $str[0] = '_';
        for( $i = 1; $i < strlen($str); $i++ ) {
            if( !CmsAdminUtils::is_valid_itemname($str[$i]) ) $str[$i] = '_';
        }
        for( $i = 0; $i < 5; $i++ ) {
            $in = $str;
            $str = str_replace('__','_',$str);
            if( $in == $str ) break;
        }
        if( $str == '_' ) throw new \Exception('Invalid name '.$orig.' and cannot be corrected');
        return $str;
    };

    $uid = get_userid(false);
    $mod = $this;
    $_upgrade_template = function(CmsLayoutTemplateType $type,$type_prefix,$tpl_name,$is_dflt,$nameprefix) use (&$mod,&$_fix_name,$uid) {
        $contents = $mod->GetTemplate($type_prefix.'_'.$tpl_name);
        if( !$contents ) return;

        //$prototype_name = substr($tpl_name,strlen($type_prefix));
        $prototype = $nameprefix.$_fix_name($tpl_name);

        try {
            $tpl = new \CmsLayoutTemplate();
            $tpl->set_owner($uid);
            $tpl->set_type($type);
            $tpl->set_name($tpl::generate_unique_name($prototype,$nameprefix));
            $tpl->set_content($contents);
            $tpl->set_type_dflt($is_dflt);
            $tpl->save();

            $mod->DeleteTemplate($tpl_name);
        }
        catch( \CmsInvalidDataException $e ) {
            // log it.
            debug_to_log('ERROR: '.$e->GetMessage());
            debug_to_log($e->GetTraceAsString());
            audit('',$this->GetName(),'Installation Error: '.$e->GetMessage());
        }
    };

    // note, old 'calendar' templates are not converted, but still in the database.
    // get all of the module database templates
    $all_module_db_templates = $this->ListTemplates();
    // for each template
    foreach( $all_module_db_templates as $tpl_name ) {
        //    get its prefix
        list($prefix,$namepart) = explode('_',$tpl_name);
        if( !isset($map[$prefix]) ) continue;

        $rec =& $map[$prefix];
        $type = $rec['type'];

        $have_dflt = (isset($rec['have_dflt']) && $rec['have_dflt']) ? TRUE : FALSE;
        if( !$have_dflt ) $rec['have_dflt'] = TRUE;
        $is_dflt = !$have_dflt;
        $_upgrade_template($rec['type'],$prefix,$namepart,$is_dflt,$rec['out_prefix']);
    }

    // remove some preferences
    $prefs = array('cgcalendar_pref_newcalendar_template','cgcalendar_pref_dfltcalendar_template','cgcalendar_pref_newlist_template',
                   'cgcalendar_pref_dfltlist_template','cgcalendar_pref_newupcominglist_template','cgcalendar_pref_dfltupcominglist_template',
                   'cgcalendar_pref_newevent_template','cgcalendar_pref_dfltevent_template','cgcalendar_pref_newsearch_template',
                   'cgcalendar_pref_dfltsearch_template','cgcalendar_pref_newsearchresult_template','cgcalendar_pref_dfltsearchresult_template',
                   'cgcalendar_pref_newmyevents_template','cgcalendar_pref_dfltmyevents_template',
                   'cgcalendar_pref_neweditevent_template','cgcalendar_pref_dflteditevent_template','cgcalendar_pref_newdeleteevent_template',
                   'cgcalendar_pref_dfltdeleteevent_template','cgcalendar_pref_newfullcalendar_template','cgcalendar_pref_dfltfullcalendar_template');
    foreach( $prefs as $pref_name ) {
        $this->RemovePreference($pref_name);
    }

    // should be all done now.
}

if( version_compare($oldversion,'2.3') < 0 ) {
    $sqlarray = $dict->AddColumnSQL($this->events_table_name, "event_status C(1)");
    $dict->ExecuteSQLArray($sqlarray);
    $sqlarray = $dict->CreateIndexSQL('cgcalendar_events_date2',$this->events_table_name,'event_date_start,event_date_end,event_status');
    $dict->ExecuteSQLArray($sqlarray);
    $sql = 'UPDATE '.$this->events_table_name.' SET event_status = ?';
    $db->Execute( $sql, [ 'P' ] );
}
if( version_compare($oldversion,'2.4.1') < 0 ) {
    $this->CreateEvent('EventEditedPre');
}
