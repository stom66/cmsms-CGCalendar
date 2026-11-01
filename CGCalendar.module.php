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

final class CGCalendar extends CGExtensions
{
  var $categories_table_name;
  var $events_to_categories_table_name;
  var $events_table_name;
  var $fields_table_name;
  var $event_field_values_table_name;
  var $admin_tools_loaded;
  static $_cached_fields;

  function __construct()
  {
	parent::__construct();
	$this->AddImageDir('images');

	$this->categories_table_name = cms_db_prefix() . 'module_cgcalendar_categories';
	$this->events_to_categories_table_name = cms_db_prefix().'module_cgcalendar_events_to_categories';
	$this->events_table_name = cms_db_prefix().'module_cgcalendar_events';
	$this->event_field_values_table_name = cms_db_prefix() . 'module_cgcalendar_event_field_values';
	$this->fields_table_name = cms_db_prefix() . 'module_cgcalendar_fields';
	$this->admin_tools_loaded = false;
  }

  function InitializeFrontend()
  {
	$this->RegisterModulePlugin();
	$this->RestrictUnknownParams();

	$this->SetParameterType('day',CLEAN_INT);

	$this->SetParameterType('display',CLEAN_STRING);
	$this->SetParameterType('category',CLEAN_STRING);
    $this->SetParameterType('listtype',CLEAN_STRING);
	$this->SetParameterType('week',CLEAN_INT);
	$this->SetParameterType('month',CLEAN_INT);
	$this->SetParameterType('year',CLEAN_INT);
	$this->SetParameterType('use_session',CLEAN_STRING);
	$this->SetParameterType('inline',CLEAN_INT);
	$this->SetParameterType('reverse',CLEAN_STRING);
	$this->SetParameterType('detailpage',CLEAN_STRING);
	$this->SetParameterType('eventtemplate',CLEAN_STRING);

	$this->SetParameterType('event_id',CLEAN_INT);
	$this->SetParameterType('limit',CLEAN_INT);
	$this->SetParameterType('fullcalendartemplate',CLEAN_STRING);
	$this->SetParameterType('eventtemplate',CLEAN_STRING);
	$this->SetParameterType('listtemplate',CLEAN_STRING);
	$this->SetParameterType('searchtemplate',CLEAN_STRING); // todo, doc this.
	$this->SetParameterType('searchresulttemplate',CLEAN_STRING); // todo, doc this.
	$this->SetParameterType('searchresultpage',CLEAN_STRING); // todo, doc this

	$this->SetParameterType('myeventstemplate',CLEAN_STRING);
	$this->SetParameterType('editeventtemplate',CLEAN_STRING);
	$this->SetParameterType('deleteeventtemplate',CLEAN_STRING);
	$this->SetParameterType('editpage',CLEAN_INT);

	$this->SetParameterType('unique_only',CLEAN_INT);
	$this->SetParameterType('return_id',CLEAN_INT);
	$this->SetParameterType('show_ical',CLEAN_STRING);
	$this->SetParameterType('showchildren',CLEAN_INT);

	$this->SetParameterType(CLEAN_REGEXP.'/cal_.*/',CLEAN_STRING);

	$prefix = $this->GetPreference('url_prefix');
	if( !$prefix ) $prefix = 'calendar';
	$this->RegisterRoute('/'.$prefix.'\/(?P<returnid>[0-9]+)\/(?P<event_id>[0-9]+)-.*$/',array('action'=>'default','display'=>'event'));
  }

  function InitializeAdmin()
  {
	$this->CreateParameter('myeventstemplate','',$this->Lang('help_param_myeventstemplate'));
	$this->CreateParameter('editeventtemplate','',$this->Lang('help_param_editeventtemplate'));
	$this->CreateParameter('deleteeventtemplate','',$this->Lang('help_param_deleteeventtemplate'));
	$this->CreateParameter('editpage','',$this->Lang('help_param_editpage'));
  }

  function GetName() { return 'CGCalendar'; }
  function GetAuthor() { return 'Robert Campbell'; }
  function GetAuthorEmail() { return 'calguy1000@gmail.com'; }
  function IsPluginModule() { return true; }
  function HasAdmin() { return true; }
  function GetEventDescription( $eventname ) { return $this->lang('eventdesc-' . $eventname); }
  function GetEventHelp( $eventname ) {	return $this->lang('eventhelp-' . $eventname); }
  function GetVersion() { return '2.6.2'; }
  function GetDependencies() { return [ 'CGExtensions'=>'1.61','CGSimpleSmarty'=>'2.1.6' ]; }
  function GetDescription($lang = 'en_US') { return $this->Lang("description"); }
  function GetAdminDescription() { return $this->Lang('cal_description'); }
  function GetAdminSection() { return 'content'; }
  function MinimumCMSVersion() { return '2.2.8'; }
  function GetChangeLog() { return file_get_contents(__DIR__.'/changelog.inc'); }
  function InstallPostMessage() { return $this->Lang('install_postmessage'); }
  function UninstallPreMessage() { return $this->Lang('areyousure_uninstall'); }
  function AllowAutoInstall() { return FALSE; }
  function AllowAutoUpgrade() { return FALSE; }
  function UninstallPostMessage() { return $this->Lang('post_uninstall'); }
  public function LazyLoadFrontend() { return FALSE; }
  public function LazyLoadAdmin() { return TRUE; }

  function GetFriendlyName()
  {
	$tmp = $this->GetPreference('friendlyname');
	if( !$tmp ) $tmp = $this->Lang('cal_calendar');
	return $tmp;
  }

  function VisibleToAdminUser()
  {
      return $this->CheckPermission('Modify Calendar') ||
          $this->CheckPermission('Add Calendar Events') ||
          $this->CheckPermission('Edit My Calendar Events') ||
          $this->CheckPermission('Manage Calendar Attributes') ||
          $this->CheckPermission('Modify Site Preferences');
  }

  protected function HaveManageRight()
  {
      if( CmsApp::get_instance()->is_frontend_request() ) {
          $feu = \cms_utils::get_module('FrontEndUsers');
          if( !$feu ) return FALSE; // no feu
          $feu_uid = $feu->LoggedInId();
          if( $feu_uid < 1 ) return FALSE; // not logged in
          $master_group = (int) $this->GetPreference('frontend_mastergroup');
          if( $master_group > 0 ) {
              if( $feu->MemberOfGroup($feu_uid,$master_group) ) return TRUE; // can manage any group
          }
          return FALSE;
      }
      if( $this->CheckPermission('Modify Calendar') ) return TRUE; // can manage any event
      return FALSE;
  }

  protected function CanManageEvent($event_id)
  {
      $event_id = (int) $event_id;
      if( $this->HaveManageRight() ) return TRUE;
      if( CmsApp::get_instance()->is_frontend_request() ) {
          // have to check the database for ownership.
          $db = \cge_utils::get_db();
          $sql = 'SELECT event_created_by FROM '.$this->events_table_name.' WHERE event_id = ?';
          $res = (int) $db->GetOne($sql,array($event_id));
          if( $res == $feu_uid ) return TRUE;
          return FALSE;
      }

      // admin requests
      if( $this->CheckPermission('Edit My CalendarEvents') ) {
          // have to check the database
          $user_id = get_userid(FALSE) * -1 - 100;
          $db = \cge_utils::get_db();
          $sql = 'SELECT event_created_by FROM '.$this->events_table_name.' WHERE event_id = ?';
          $res = (int) $db->GetOne($sql,array($event_id));
          if( $res == $user_id ) return TRUE;
      }
      return FALSE;
  }

  protected function GetEvent($event_id,$uid = -1)
  {
      $explode2 = function($delim,$in_str) {
          // explode a string by every second ,
          $tmp = explode($delim,$in_str);
          $out = [];
          while( $tmp ) {
              $out[] = implode($delim,array_splice($tmp,0,2));
          }
          return $out;
      };

      $rs = null;
      $event_id = (int) $event_id;
      $uid = (int) $uid;

      if( $event_id > 0 ) {
          $db = $this->GetDb(); /* @var $db ADOConnection */
          $sql = 'SELECT * FROM ' . $this->events_table_name .' WHERE event_id = ?';
          $parms = array($event_id);
          if( $uid > 0 && !$this->HaveManageRight() ) {
              $sql .= ' AND event_created_by = ?';
              $parms[] = $uid;
          }
          $rs = $db->Execute($sql,$parms);
      }

      if( $rs && $rs->RecordCount() > 0) {
          $result = $rs->FetchRow();
          $result['event_date_start_ut'] = $db->UnixTimeStamp($result['event_date_start']);
          if( is_null($result['event_date_end']) || $result['event_date_end'] == '0000-00-00 00:00:00') $result['event_date_end'] = NULL;
          $result['event_date_end_ut'] = $db->UnixTimeStamp($result['event_date_end']);
          if( is_null($result['event_date_recur_end']) || $result['event_date_recur_end'] == '0000-00-00 00:00:00' ) {
              $result['event_date_recur_end'] = NULL;
          }
          $result['event_date_recur_end_ut'] = $db->UnixTimeStamp($result['event_date_recur_end']);
          $result['event_recur_count'] = $result['event_recur_nevents'];
          $result['event_recur_weekdays'] = $explode2(',',$result['event_recur_weekdays']);
          $result['event_recur_monthdays'] = $explode2(',',$result['event_recur_monthdays']);
          $result['categories'] = array();

          // now pick up categories
          $sql = 'SELECT category_id FROM ' . $this->events_to_categories_table_name . ' WHERE event_id = ?';
          $rs = $db->Execute($sql,array($event_id));
          if($rs) {
              while($row = $rs->FetchRow()) {
                  $result['categories'][] = $row['category_id'];
              }
          }

          // now pick up the custom fields
          $result['fields'] = array();
          $sql = 'SELECT field_name, field_value FROM ' . $this->event_field_values_table_name . ' WHERE event_id = ?';
          $rs = $db->GetArray($sql,array($event_id));
          if( empty($rs) && $result['event_parent_id'] > 0 ) {
              $sql = 'SELECT field_name, field_value FROM ' . $this->event_field_values_table_name . ' WHERE event_id = ?';
              $rs = $db->GetArray($sql,array($result['event_parent_id']));
          }
          if( is_array($rs) && count($rs) ) {
              foreach( $rs as $row ) {
                  $result['fields'][$row['field_name']] = $row['field_value'];
              }
          }

          // test if this is an all day event.
          // cuz we don't store that info in the database.
          $sd = date('z',$result['event_date_start_ut']);
          $sh = date('G',$result['event_date_start_ut']);
          $sm = date('i',$result['event_date_start_ut']);
          $ed = date('z',$result['event_date_end_ut']);
          $eh = date('G',$result['event_date_end_ut']);
          $em = date('i',$result['event_date_end_ut']);
          $result['all_day_event'] = $result['event_all_day'];
          $result['alt_end_date'] = 0;
          if( $result['event_date_start_ut'] < $result['event_date_end_ut'] ) $result['alt_end_date'] = 1;
          //if( $sd !=$ed ) $result['alt_end_date'] = 1;
      }
      else {
          // create an empty record
          $result = array();
          $result['event_id'] = -1;
          $result['event_title'] = '';
          $result['event_summary'] = '';
          $result['event_details'] = '';
          $result['event_date_start'] = NULL;
          $result['event_date_end'] = NULL;
          $result['event_date_start_ut'] = strtotime($this->GetPreference('dflt_starttime'));
          $result['event_date_end_ut'] = NULL;
          $result['event_created_by'] = -1;
          $result['event_create_date'] = NULL;
          $result['event_modified_date'] = NULL;
          $result['event_recur_period'] = 'none';
          $result['event_date_recur_end_ut'] = NULL;
          $result['event_date_recur_end'] = NULL;
          $result['event_recur_interval'] = -1;
          $result['event_parent_id'] = -1;
          $result['event_recur_count'] = -1;
          $result['event_recur_weekdays'] = array();
          $result['event_recur_monthdays'] = array();
          $result['event_allows_overlap'] = 1; // default to yes.
          $result['categories'] = array();
          $result['fields'] = array();
          $result['all_day_event'] = $this->GetPreference('dflt_alldayevent');
          $result['alt_end_date'] = 0;
          $result['event_status'] = $this->GetPreference('dflt_status','D');
      }

      return $result;
  }

  // deprecated
  function GetCategories()
  {
      return \CGCalendar\category::get_categories();
  }

  private function _LoadAdmin()
  {
      if( $this->admin_tools_loaded === false) require_once('functions.admin_tools.php');
  }

  function GetFields()
  {
      if( !is_array(self::$_cached_fields) ) {
          self::$_cached_fields = array();

          $db = $this->GetDb(); /* @var $db ADOConnection */
          $fields_table_name = $this->fields_table_name;
          $sql = "SELECT * FROM $fields_table_name ORDER BY field_order,field_name";

          $result = array();
          $rs = $db->Execute($sql);
          if($rs && $rs->RecordCount() > 0) {
              $result = $rs->GetArray();
              if( is_array($result) && count($result) ) {
                  foreach( $result as &$one ) {
                      if( $one['field_extra'] != '' ) $one['field_extra'] = unserialize($one['field_extra']);
                  }
              }
              self::$_cached_fields = $result;
          }
      }
      return self::$_cached_fields;
  }

  function GetDayNames()
  {
      $tmp = cgcalendar_utils::get_locale_dates();
      $day_names = $tmp['dayNames'];

      if($this->GetPreference('firstdayofweek',1)) {
          $first = array_shift($day_names);
          $day_names[] = $first;
      }
      return $day_names;
  }

  function GetDayShortNames()
  {
      $tmp = cgcalendar_utils::get_locale_dates();
      $day_short_names = $tmp['dayNamesShort'];

      if($this->GetPreference('firstdayofweek',1)) {
          $first = array_shift($day_short_names);
          $day_short_names[] = $first;
      }
      return $day_short_names;
  }

  function GetMonthNames()
  {
      $tmp = cgcalendar_utils::get_locale_dates();
      $month_names = $tmp['monthNames'];
      return $month_names;
  }

  function GetLabels()
  {
      $lang['date'] = $this->Lang('cal_date');
      $lang['summary'] = $this->Lang('cal_summary');

      $lang['details'] = $this->Lang('cal_details');
      $lang['return'] = $this->Lang('cal_return');
	  $lang['current'] = $this->Lang('cal_current');
      $lang['today'] = $this->Lang('cal_today');
	  $lang['from'] = $this->Lang('cal_from');
      $lang['to'] = $this->Lang('cal_to');
      $lang['next'] = $this->Lang('cal_next');
      $lang['prev'] = $this->Lang('cal_prev');
      $lang['past_events'] = $this->Lang('cal_past_events');
      $lang['upcoming_events'] = $this->Lang('cal_upcoming_events');
      $lang['recur_period'] = array('daily'=>$this->Lang('daily'),'weekly'=>$this->Lang('weekly'),'monthly'=>$this->Lang('monthly'),'yearly'=>$this->Lang('yearly'));
      return $lang;
  }


  function isValidFilename($filename)
  {
      $this->_LoadAdmin();
      return calendar_isValidFilename($this,$filename);
  }


  function HandleUpload($fldname,&$error)
  {
      $this->_LoadAdmin();
      return calendar_HandleUpload($this,$fldname,$error);
  }

  function GetDefaultTemplate($template)
  {
      $fn = sprintf('orig_%s_template.tpl',$template);
      $fn = cms_join_path(__DIR__,'templates',$fn);
      $data = @file_get_contents($fn);
      return $data;
  }

  function NoBreak($string) {
    return str_replace(" ","&nbsp;",$string);
  }

  function SearchResult($returnid, $event_id, $attr = '')
  {
      $result = array();
      if( $attr != 'event') return $result;

      $db = $this->GetDb();
      $query = 'SELECT event_title FROM '.$this->events_table_name.' WHERE event_id = ? AND event_status = ?';
      $title = $db->GetOne( $query, [ $event_id, 'P' ] );
      if( ! $title ) return $result;

      $result[0] = $this->GetFriendlyName();
      $result[1] = $title;

      $titleSEO = munge_string_to_url($title);
      $destpage = (int) $this->GetPreference('defaultcalendarpage');
      if( $destpage < 1 ) $destpage = $returnid;

      $prefix = $this->GetPreference('url_prefix');
      if( !$prefix ) $prefix = 'calendar';
      $prettyurl = sprintf($prefix."/%d/%d-%s", $destpage,$event_id,$titleSEO);
      $result[2] = $this->CreateLink('cntnt01','default',$destpage,'',
					 array('event_id'=>$event_id,'display'=>'event','detailpage'=>$destpage,'return_id'=>$returnid),
					 '', true, '', '', $prettyurl);
      return $result;
  }

  function SearchReindex(&$module)
  {
      $db = $this->GetDb();

      $query = 'SELECT * FROM '.$this->events_table_name.' WHERE event_status = ? ORDER BY event_date_start';
      $dbr = $db->Execute($query, [ 'P' ] );

	  $fquery = 'SELECT fv.field_value FROM '.$this->fields_table_name.' fd
                   LEFT JOIN '.$this->event_field_values_table_name.' fv
                     ON fd.field_name = fv.field_name AND fd.field_searchable = 1
                  WHERE fv.event_id = ?';

      while ($dbr && !$dbr->EOF) {
          $text = $dbr->fields['event_title'].' '.$dbr->fields['event_summary'].' '.$dbr->fields['event_details'];

          $morewords = $db->GetCol($fquery,array($dbr->fields['event_id']));
          if( $morewords ) $text .= ' '.implode(' ',$morewords);
          $module->AddWords($this->GetName(), $dbr->fields['event_id'], 'event', $text, NULL);
          $dbr->MoveNext();
	  }
  }

  function ToAbbreviatedWeekdays($data)
  {
	if( !is_array($data) ) $data = explode(',',$data);

	$weekdays = array(0=>'sunday',
					  1=>'monday',
					  2=>'tuesday',
					  3=>'wednesday',
					  4=>'thursday',
					  5=>'friday',
					  6=>'saturday');
	$result = array();
	foreach( $data as $one ) {
	  $result[] = $this->Lang('abbr_'.$weekdays[$one]);
	}
    if( !count($result) ) return '';
	$tmp = implode(',',$result);
	return $tmp;
  }

  // timestamp is a Unix timestamp (seconds since epoch)
  // tzone is an time-zone offset from GMT
  function UnixTimestampToiCal($timestamp, $tzone = 0)
  {
	$zulu = $timestamp + ($tzone * 3600);
	$iCal  = date("Ymd\THis\Z", $zulu);
	return $iCal;
  }

  function GetCategoryNames($category_ids)
  {
	if( !is_array($category_ids) && (int)$category_ids > 0 ) {
	  $category_ids = array($catetegory_ids);
	}

	$allcats = $this->GetCategories();
	if( !is_array($allcats) || count($allcats) == 0 ) return;

	$tmp = array();
	foreach( $category_ids as $one ) {
	  for( $i = 0; $i < count($allcats); $i++ ) {
		if( $one == $allcats[$i]['category_id'] ) {
		  $tmp[] = $allcats[$i]['category_name'];
		  break;
		}
	  }
	}
	if( count($tmp) > 0 ) return implode(', ',$tmp);
  }

  function ConvertCategoriesToString($categories)
  {
	$ary = array();
	foreach ($categories as $one) {
	  $ary[] = $one['category_name'];
	}
	return implode(',', $ary);
  }

  static public function page_type_lang_callback($str)
  {
      $mod = cms_utils::get_module('CGCalendar');
      if( is_object($mod) ) return $mod->Lang('tpltype_'.$str);
  }

  static public function reset_page_type_defaults(CmsLayoutTemplateType $type)
  {
      if( $type->get_originator() != 'CGCalendar' ) throw new CmsLogicException('Cannot reset contents for this template type');

      $fn = null;
      switch( $type->get_name() ) {
      case 'searchform':
          $fn = 'orig_search_template.tpl';
          break;
      case 'searchresult':
          $fn = 'orig_searchresult_template.tpl';
          break;
      case 'event':
          $fn = 'orig_event_template.tpl';
          break;
      case 'eventlist':
          $fn = 'orig_list_template.tpl';
          break;
      case 'myevents':
          $fn = 'orig_myevents_template.tpl';
          break;
      case 'editevent':
          $fn = 'orig_editevent_template.tpl';
          break;
      case 'deleteevent':
          $fn = 'orig_deleteevent_template.tpl';
          break;
      case 'fullcalendar':
          $fn = 'orig_fullcalendar_template.tpl';
          break;
      default:
          throw new \LogicException($type->get_name().' is not a known type for the '.$type->get_originator().' originator');
      }

      $fn = cms_join_path(__DIR__,'templates',$fn);
      if( file_exists($fn) ) return @file_get_contents($fn);
  }

  public function GetAdminMenuItems()
  {
      $out = array();
      if( $this->CheckPermission('Modify Calendar') ||
          $this->CheckPermission('Add Calendar Events') ||
          $this->CheckPermission('Edit My Calendar Events') ) $out[] = CmsAdminMenuItem::from_module($this);

      if( $this->CheckPermission('Modify Site Preferences') ||
          $this->CheckPermission('Manage Calendar Attributes')) {
          $obj = new CmsAdminMenuItem();
          $obj->module = $this->GetName();
          $obj->section = 'siteadmin';
          $obj->title = $this->Lang('title_adminnav_settings');
          $obj->description = $this->Lang('desc_adminnav_settings');
          $obj->action = 'admin_settings';
          $obj->url = $this->create_url('m1_',$obj->action);
          $out[] = $obj;
      }
      return $out;
  }

}; // class
