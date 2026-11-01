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

$detailpage = $this->GetPreference('defaultcalendarpage',$returnid);
if( $detailpage < 1 ) $detailpage = $returnid;
if (isset($params['detailpage'])) {
  $tmp = $this->resolve_alias_or_id($params['detailpage']);
  if( $tmp > 0 ) $detailpage = $tmp;
}

$sessionkey  = null;
$category = null;
$category = html_entity_decode(html_entity_decode(get_parameter_value($params, 'category', $category)));
$limit = get_parameter_value($params, 'limit', 1000);
$limit = min($limit,1000);
$inline = get_parameter_value($params,'inline',-1);
$reverse = get_parameter_value($params, 'reverse', false);

// defaults for y/md/d (default to current month view).
$year = date('Y');
$month = date('m');
$day = -1;

// get y/m/d from session
$use_session = get_parameter_value($params,'use_session');
if( $use_session ) {
  $sessionkey = $use_session.cgcalendar_utils::get_sessionkey($params);
  list($year,$month,$day) = cgcalendar_utils::to_ymd($this->session_get($sessionkey.'date',$thedate));
}

// get y/m/d from params
if( isset($params['date']) ) {
    list($year,$month,$day) = cgcalendar_utils::to_ymd($params['date']);
}
else {
    $year = (int) cge_utils::get_param($params,'year',$year);
    $month = (int) cge_utils::get_param($params,'month',$month);
    $day = (int) cge_utils::get_param($params,'day',$day);
}

$thedate = sprintf("%d-%d",(int)$year,(int)$month);
if( $day > 0 ) $thedate .= sprintf('-%d',(int)$day);
if( $day > 0 ) $thedate .= sprintf('-%d',(int)$day);
if( !empty($sessionkey) ) {
    $this->session_put($sessionkey.'date',$thedate);
    $inline = 1;
}
$thedate_ut = strtotime($thedate);
$last_day_of_month = date('t',$thedate_ut);
$next = cgcalendar_utils::next_ymd($thedate);
$prev = cgcalendar_utils::prev_ymd($thedate);

if($day > 0) {
  $start = mktime(0,0,0,$month,$day,$year);
  $end = mktime(23,59,59,$month,$day,$year);
}
else {
   $start = mktime(0,0,0,$month,1,$year);
   $end = mktime(23,59,59,$month,$last_day_of_month,$year);
}

$db = cmsms()->GetDb();
$events = null;
$event_ids = $db->GetCol(cgcalendar_utils::get_query($start,$end,$category,$reverse));
if( is_array($event_ids) && count($event_ids) ) {
  $loader = new cgc_event_loader($id,$event_ids,$detailpage,FALSE);
  if( isset($params['eventtemplate']) ) $loader->set_event_template($params['eventtemplate']);
  $events = $loader->get_events();
}

$parms = $params;
unset($parms['month'],$parms['day'],$parms['year'],$parms['date'],$parms['returnid'],$parms['action']);
$parms['display'] = 'list';
$parms['date'] = $next;
$navigation['next'] = $this->CreateURL($id, 'default', $returnid, $parms, $inline ); // deprecated
$navigation['ni_next'] = $this->CreateURL($id, 'default', $returnid, $parms, false );
$navigation['in_next'] = $this->CreateURL($id, 'default', $returnid, $parms, true );
$parms['date'] = $prev;
$navigation['prev'] = $this->CreateURL($id, 'default', $returnid, $parms, $inline ); // deprecated
$navigation['ni_prev'] = $this->CreateURL($id, 'default', $returnid, $parms, false );
$navigation['in_prev'] = $this->CreateURL($id, 'default', $returnid, $parms, true );

$day_names = $this->GetDayNames();
$day_short_names = $this->GetDayShortNames();

// other language fields
$lang = $this->GetLabels();

// assign to Smarty
$thetemplate = $this->GetPreference(CGCALENDAR_PREF_DFLTLIST_TEMPLATE);
$thetemplate = \cge_param::get_string($params,'listtemplate',$thetemplate);
$tpl = $this->CreateSmartyTemplate($thetemplate,'list_');
$tpl->assign('day_names', $day_names);
$tpl->assign('day_short_names', $day_short_names);
$tpl->assign('events', $events);
$tpl->assign('day', $day);
$tpl->assign('month', $month);
$tpl->assign('year', $year);
$tpl->assign('lang', $lang);
$tpl->assign('navigation', $navigation);
$tpl->assign('thedate',strtotime($thedate));
$tpl->assign('file_location',cgcalendar_utils::get_upload_location());
$tpl->display();

?>
