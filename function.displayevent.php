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
if( !isset($gCms) ) exit;

$events_table_name = $this->events_table_name;
$event_id = \cge_param::get_int($params,'event_id');
if( $event_id < 1 ) $event_id = 'next';

$thetemplate = \cge_param::get_string($params,'eventtemplate');
if( !$thetemplate ) {
    $tpl = CmsLayoutTemplate::load_dflt_by_type('CGCalendar::event');
    if( !is_object($tpl) ) {
        audit('',$this->GetName(),'No default event template found');
        return;
    }
    $thetemplate = $tpl->get_name();
    unset($tpl);
}

if( $event_id == 'next' ) {
    $start = date('Y-m-d H:i:s'); // start now !
    $sql = "SELECT DISTINCT event_id FROM $events_table_name ";
    $sql .= "WHERE event_status = 'P' AND (event_date_start > '$start' OR event_date_end > '$start')";
    $sql .= ' LIMIT 1';
    $event_id = $db->GetOne($sql);
}
else if( (int)$event_id > 0 ) {
    $sql = "SELECT event_status FROM $events_table_name WHERE event_id = ?";
    $tmp = $db->GetOne( $sql,  [ (int) $event_id ]);
    if( $tmp != 'P' ) {
        echo '<div class="calendar-error">'.$this->Lang('error_event_not_found',$event_id).'</div>';
        return;
    }
}
else {
    // no event
    echo '<div class="calendar-error">'.$this->Lang('error_event_not_found',$event_id).'</div>';
    return;
}

$loader = new cgc_event_loader($id,array($event_id),$returnid,FALSE);
$events = $loader->get_events();
if( !is_array($events) || count($events) != 1 ) {
    echo '<div class="calendar-error">'.$this->Lang('error_event_not_found',$event_id).'</div>';
    return;
}
$event = $events[0];

// other language fields
$lang = $this->GetLabels();

if (isset($params['show_ical'])) {
    $domain = $gCms->config['root_url'];
    $time_zone = 0;
    $outstr  = "BEGIN:VCALENDAR\n";
    $outstr .= "PRODID:-//".$domain."//CGCalendar ".$this->GetVersion()."//EN\n";
    $outstr .= "VERSION:2.0\n";
    $outstr .= "CALSCALE:GREGORIAN\n";
    $outstr .= "METHOD:PUBLISH\n";
    $outstr .= "X-WR-CALNAME:".strtoupper($this->ConvertCategoriesToString($categories))."\n";
    if (date('e', strtotime($event['event_date_start'])) != 'e')
        $outstr .= "X-WR-TIMEZONE:".date('e', strtotime($event['event_date_start']))."\n";
    $outstr .= "X-WR-CALDESC:Schedule from ".get_site_preference('sitename', 'CMSMS Site')."\n";

    $outstr .= "BEGIN:VEVENT\n";
    $outstr .= "DTSTART:";
    $outstr .= $this->UnixTimestampToiCal(strtotime($event['event_date_start']), $time_zone)."\n";
    $outstr .= "DTEND:";
    $end_date = strtotime($event['event_date_start']) + 3600;
    if(!(empty($event['event_date_end']) || $event['event_date_end'] == null || $event['event_date_end'] == 0)) {
        $end_date = strtotime($event['event_date_end']);
    }
    $outstr .= $this->UnixTimestampToiCal($end_date, $time_zone)."\n";
    $outstr .= 'SUMMARY:'.str_replace("\n", "\\n", $event['event_title'])."\n";
    $outstr .= 'DESCRIPTION:'.str_replace("\n", "\\n", $event['event_details']);
    $extrafields = "SELECT field_name,field_value FROM ".$this->event_field_values_table_name." WHERE event_id = ?";
    $frs = $db->Execute($extrafields,array($event_id));
    while ($frs && $row=$frs->FetchRow()) {
        $outstr .= "\\n" . $row['field_name'].': '.str_replace("\n", "\\n", $row['field_value']);
    }
    $outstr .= "\n";
    $outstr .= 'UID:'.$event_id.'@'.$domain."\n";
    $outstr .= 'DTSTAMP:'.$this->unixTimestampToiCal(time(), $time_zone)."\n";
    $outstr .= "CLASS:PUBLIC\n";
    $outstr .= "END:VEVENT\n";
    $outstr .= "END:VCALENDAR\n";

    while(@ob_end_clean());
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private',false);
    header('Content-Description: File Transfer');
    header('Content-Type: text/calendar');
    header('Content-Length: ' . strlen($outstr));
    header('Content-Disposition: attachment; filename=calendar.ics');
    echo $outstr;
    exit;
}
else {
    // assign to Smarty
    //$tpl = $smarty->CreateTemplate($this->GetTemplateResource('orig_event_template.tpl'),null,null,$smarty);
    $tpl = $smarty->CreateTemplate($this->GetTemplateResource($thetemplate),null,null,$smarty);
    $tpl->assign('event', $event);
    $tpl->assign('lang', $lang);
    $tpl->assign('ical_url', $this->CreateLink('cntnt01', 'default', $returnid, '', array('show_ical' => 'true', 'display' => 'event', 'event_id' => $event_id), '', true));
    $tpl->assign('file_location',cgcalendar_utils::get_upload_location());
    $tpl->assign('all_categories',\CGCalendar\category::get_categories(TRUE));

    $feu = \cms_utils::get_module('FrontEndUsers');
    if( $feu ) {
        $feu_uid = $feu->LoggedInId();
        $master_group = $this->GetPrefernce('frontend_mastergroup');
        if( $this->HaveManageRight() || ($feu_uid > 0 && $event['event_created_by'] == $feu_uid) ) {
            $editpage = $returnid;
            {
                $tmp = $this->GetPreference('frontend_redirectpage','');
                if( $tmp ) {
                    $tmp = $this->ProcessTemplateFromData($tmp);
                    $editpage = $this->resolve_alias_or_id($tmp,$editpage);
                }
                $tmp = \cge_param::get_string($params,'editpage');
                if( $tmp ) {
                    $editpage = $this->resolve_alias_or_id($tmp,$editpage);
                }
            }
            $parms = array('event_id'=>$event_id);
            $parms['return_id'] = $returnid;
            if( isset($params['editeventtemplate']) ) $parms['editeventtemplate'] = $params['editeventtemplate'];
            $edit_url = $this->create_url('cntnt01','addedit_event', $editpage, $parms);
            $tpl->assign('edit_url',$edit_url);
        }
    }
    // Assign title to class var so event title can be added to page title in  ContentPostRender()
    $this->current_event_title = $event['event_title'];

    // Display template
    $tpl->display();
}

?>