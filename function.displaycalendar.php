<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: Skeleton (c) 2008
#      by Robert Allen (akrabat) and
#         Robert Campbell (calguy1000@cmsmadesimple.org)
#  An addon module for CMS Made Simple to allow displaying calendars,
#  and management and display of time based events.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
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

$use_session = isset($params['use_session']) ? trim($params['use_session']) : '';
$month = -1;
$year = -1;
$p_detailpage = '';
$detailpage = $this->GetPreference('defaultcalendarpage',$returnid);
if( $detailpage < 1 ) $detailpage = $returnid;
$category = '';
$inline = 0;
$sessionkey = null;
$thetemplate = $this->GetPreference(CGCALENDAR_PREF_DFLTCALENDAR_TEMPLATE);
$thetemplate = get_parameter_value($params,'calendartemplate',$thetemplate);
if( $use_session ) {
  // we use a unique key, based on most parameters (except the ones that can change)
  // and only extract items from the session that can change (like dates, or page numbers).
  $sessionkey = $use_session.cgcalendar_utils::get_sessionkey($params);
  $month = $this->session_get($sessionkey.'cur_month',$month);
  $year = $this->session_get($sessionkey.'cur_year',$year);
}

// get selected data from parameters
$year_list = array();
for($i = date('Y')-5; $i <= date('Y')+2; $i++ ) {
  $year_list[$i] = $i;
}
$smarty->assign('year_list',$year_list);
$month = get_parameter_value($params, 'month', $month);
$year = get_parameter_value($params, 'year', $year);
$category = html_entity_decode(html_entity_decode(get_parameter_value($params, 'category', $category)));
$inline = get_parameter_value($params,'inline',$inline);
$p_detailpage = get_parameter_value($params,'detailpage',$p_detailpage);

if ($p_detailpage) {
  $tmp = $this->resolve_alias_or_id($p_detailpage);
  if( $tmp > 0 ) $detailpage = $tmp;
}

if( $month == -1 ) {
  // fallback to current month and year
  $month = date('n');
  $year  = date('Y');
}

if( !empty($sessionkey) ) {
  // store them back in the session.
  $this->session_put($sessionkey.'cur_month',$month);
  $this->session_put($sessionkey.'cur_year',$year);
  $inline = 1;
}

// basic information about dates
$prev_month['timestamp'] = strtotime("-1 month", mktime(0,0,0,$month, 1, $year));
$prev_month['year'] = date('Y', $prev_month['timestamp']);
$prev_month['month'] = date('n', $prev_month['timestamp']);
$next_month['timestamp'] = strtotime("+1 month", mktime(0,0,0,$month, 1, $year));
$next_month['year'] = date('Y', $next_month['timestamp']);
$next_month['month'] = date('n', $next_month['timestamp']);

$last_day_of_month = date('t',strtotime("$year-$month-02"));

$db = $this->GetDb();
$start = mktime(0,0,0,$month,1,$year);
$end = mktime(23,59,59,$month,$last_day_of_month,$year);
$event_ids = $db->GetCol(cgcalendar_utils::get_query($start,$end,$category,FALSE));

$days = array();
$parms = $params;
unset($parms['returnid'],$parms['action'],$parms['page']);
$parms['display'] = 'list';
for($i = 1; $i <= $last_day_of_month; $i++) {
  $parms['date'] = "$year-$month-$i";
  $days[$i]['date'] = mktime(0,0,0,$month,$i,$year);
  $days[$i]['week'] = date('W',$days[$i]['date']);
  $days[$i]['url'] = $this->create_url($id, 'default', $detailpage, $parms, $inline); // deprecated.
  $days[$i]['ni_url'] = $this->create_url($id, 'default', $detailpage, $parms, FALSE );
  $days[$i]['in_url'] = $this->create_url($id, 'default', $detailpage, $parms, TRUE );
  $days[$i]['events'] = array();
  $days[$i]['class'] = '';
}
if($year == date('Y') && $month == date('m')) {
  // month being displayed is this month. Therefore today exists
  $today = date('j');
  $days[$today]['class'] .= 'calendar-today';
}

if( is_array($event_ids) && count($event_ids) ) {
  $loader = new cgc_event_loader($id,$event_ids,$detailpage,FALSE);
  if( isset($params['eventtemplate']) ) $loader->set_event_template($params['eventtemplate']);
  $events = $loader->get_events();

  foreach( $events as &$row ) {
    $start_date = strtotime($row['event_date_start']);
    $end_date = $start_date;
    if( $row['event_date_end'] != '' ) $end_date = strtotime($row['event_date_end']);

    $start_month = date('n', $start_date);
    $end_month = date('n', $end_date);
    $start_year = date('Y', $start_date);
    $end_year = date('Y', $end_date);

    // find out where the event starts within this month
    $first_day_of_event_in_this_month = date('j', $start_date);
    if($start_month < $month || $start_year < $year) $first_day_of_event_in_this_month = 1;

    // find out where the event ends within in this month
    $last_day_of_event_in_this_month = date('j', $end_date);
    if($end_month > $month || $end_year > $year) $last_day_of_event_in_this_month = $last_day_of_month;

    for( $i = $first_day_of_event_in_this_month; $i <= $last_day_of_event_in_this_month; $i++ ) $days[$i]['events'][] = $row;
  }
}

$parms = $params;
unset($parms['returnid'],$parms['action'],$parms['page']);
//if( $use_session ) $parms['use_session'] = $use_session;
$parms['year'] = $next_month['year'];
$parms['month'] = $next_month['month'];
$navigation['next'] = $this->CreateURL($id,'default',$returnid,$parms,$inline); // deprecated
$navigation['ni_next'] = $this->CreateURL($id,'default',$returnid,$parms,false);
$navigation['in_next'] = $this->CreateURL($id,'default',$returnid,$parms,true);

$parms['year'] = $prev_month['year'];
$parms['month'] = $prev_month['month'];
$navigation['prev'] = $this->CreateURL($id,'default',$returnid,$parms,$inline); // deprecated
$navigation['ni_prev'] = $this->CreateURL($id,'default',$returnid,$parms,false);
$navigation['in_prev'] = $this->CreateURL($id,'default',$returnid,$parms,true);

$day_names = $this->GetDayNames();
$day_short_names = $this->GetDayShortNames();

// calendar stuff
$first_of_month = gmmktime(0,0,0,$month,1,$year);
$first_of_month_weekday_number = gmstrftime('%w',$first_of_month);
$first_of_month_weekday_number = ($first_of_month_weekday_number + 7 - $this->GetPreference('firstdayofweek',1)) % 7;

// assign to Smarty
$tpl = $this->CreateSmartyTemplate($thetemplate,'calendar_');
$tpl->assign('date',mktime(1,0,0,$month,1,$year));
$tpl->assign('day_names', $day_names);
$tpl->assign('day_short_names', $day_short_names);
$tpl->assign('first_of_month_weekday_number', $first_of_month_weekday_number);
$tpl->assign('navigation', $navigation);
$tpl->assign('days', $days);
$tpl->assign('month', $month);
$tpl->assign('year', $year);
$tpl->assign('file_location',cgcalendar_utils::get_upload_location());
$tpl->display();

?>
