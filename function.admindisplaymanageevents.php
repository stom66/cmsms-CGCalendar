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
if( !$gCms ) exit;
if( !$this->CheckPermission('Modify Calendar') &&
    !$this->CheckPermission('Add Calendar Events') &&
    !$this->CheckPermission('Edit My Calendar Events') ) return;


// get the locale specified dates
$locale_info = cgcalendar_utils::get_locale_dates();
/*
foreach( $locale_info as $key => &$value ) {
    $value = json_encode($value);
}
*/
$dflt_starttime = $this->GetPreference('dflt_starttime','07:00');
$parts = explode(':',trim($dflt_starttime),2);
$smarty->assign('timezone',$config['timezone']);
$smarty->assign('dflt_starttime',json_encode($parts));
$smarty->assign('can_manage',$this->CheckPermission('Modify Calendar'));
$smarty->assign('firstdayofweek',(int) $this->GetPreference('firstdayofweek',1));
$smarty->assign('datestrings',json_encode($locale_info));
$smarty->assign('text_week',$this->Lang('week'));
$smarty->assign('text_month',$this->Lang('month'));
$smarty->assign('text_day',$this->Lang('day'));
$smarty->assign('text_today',$this->Lang('today'));
$smarty->assign('dflt_fgcolor',$this->GetPreference('dflt_fgcolor','black'));
$smarty->assign('dflt_bgcolor',$this->GetPreference('dflt_bgcolor','lightcyan'));
$smarty->assign('holiday_fgcolor',$this->GetPreference('holidayfgcolor','#000000'));
$smarty->assign('holiday_bgcolor',$this->GetPreference('holidaybgcolor','#ccffff'));
$categories = $this->GetCategories();
if( is_array($categories) && count($categories) ) {
    $out = array();
    $out[-1] = $this->Lang('any');
    foreach( $categories as $row ) {
        $out[$row['category_id']] = $row['category_name'];
    }
    $smarty->assign('categories',$out);
}

$opts = [];
$opts['all'] = $this->Lang('filter_all');
$opts['published'] = $this->Lang('filter_published');
$opts['draft'] = $this->Lang('filter_draft');
$smarty->assign('filter_opts',$opts);
echo $this->ProcessTemplate('admin_events_tab.tpl');
