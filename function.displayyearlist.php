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
if( !isset($gCms) ) exit;

$sessionkey = null;
$detailpage = $this->GetPreference('defaultcalendarpage',$returnid);
if( $detailpage < 1 ) $detailpage = $returnid;
if (isset($params['detailpage'])) {
  $tmp = $this->resolve_alias_or_id($params['detailpage']);
  if( $tmp ) $detailpage = (int)$tmp;
}
$inline = get_parameter_value($params,'inline',0);
$category = html_entity_decode(html_entity_decode(get_parameter_value($params, 'category')));
$limit = get_parameter_value($params, 'limit', 1000);
$limit = min($limit,1000);
$reverse = get_parameter_value($params, 'reverse', false);

$use_session = get_parameter_value($params, 'use_session', true);
if($use_session) {
  $sessionkey = $use_session.cgcalendar_utils::get_sessionkey($params);
  $year = $this->session_get($sessionkey.'year');
}
$year = get_parameter_value($params, 'year', date('Y'));

if( $sessionkey ) {
  $this->session_put($sessionkey.'year',$year);
  $inline = 1;
}

// basic information about dates
$prev_year['timestamp'] = strtotime("-1 year", mktime(0,0,0,1, 1, $year));
$prev_year['year'] = date('Y', $prev_year['timestamp']);
$next_year['timestamp'] = strtotime("+1 year", mktime(0,0,0,1, 1, $year));
$next_year['year'] = date('Y', $next_year['timestamp']);
$last_day_of_year = mktime(0, 0, 0, 1, 0, $next_year['year']);

$start = mktime(0,0,0,1,1,$year);
$end = mktime(23,59,59,12,31,$year);
$idlist = $db->GetCol(cgcalendar_utils::get_query($start,$end,$category,$reverse));
$idlist = array_slice($idlist,0,$limit);
$loader = new cgc_event_loader($id,$idlist,$detailpage,$reverse);
if( isset($params['eventtemplate']) ) $loader->set_event_template($params['eventtemplate']);
$events = $loader->get_events();

$parms = $params;
unset($parms['action'],$parms['returnid'],$parms['page']);
$parms['display'] = 'yearlist';
$parms['year'] = ($reverse == false)?$next_year['year']:$prev_year['year'];
$navigation['next'] = $this->CreateURL($id, 'default', $returnid, $parms, $inline ); // deprecated
$navigation['ni_next'] = $this->CreateURL($id, 'default', $returnid, $parms, false );
$navigation['in_next'] = $this->CreateURL($id, 'default', $returnid, $parms, true );
$parms['year'] = ($reverse == false)?$prev_year['year']:$next_year['year'];
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
$tpl->assign('thedate',strtotime(sprintf('15 January %d',$year)));
$tpl->assign('day_names', $day_names);
$tpl->assign('day_short_names', $day_short_names);
$tpl->assign('events', $events);
$tpl->assign('year', $year);
$tpl->assign('lang', $lang);
$tpl->assign('navigation', $navigation);
$tpl->assign('file_location',cgcalendar_utils::get_upload_location());
$tpl->display();

?>