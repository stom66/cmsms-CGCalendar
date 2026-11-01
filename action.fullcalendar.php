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

$thetemplate = \cgcalendar_utils::find_layout_template($params,'fullcalendartemplate','CGCalendar::fullcalendar');
$tpl = $smarty->CreateTemplate($this->GetTemplateResource($thetemplate),null,null,$smarty);
$orig_locale_info = $locale_info = cgcalendar_utils::get_locale_dates();
foreach( $locale_info as $key => &$value ) {
    $value = json_encode($value);
}

$category = \cge_param::get_string($params,'category');
$tpl->assign('all_categories',\CGCalendar\category::get_categories(TRUE));
$tpl->assign('firstdayofweek',(int) $this->GetPreference('firstdayofweek',1));
$tpl->assign('datestrings',$locale_info);
$tpl->assign('datestringsjson',json_encode($orig_locale_info));
$tpl->assign('text_week',$this->Lang('week'));
$tpl->assign('text_month',$this->Lang('month'));
$tpl->assign('text_day',$this->Lang('day'));
$tpl->assign('text_today',$this->Lang('today'));
$tpl->assign('dflt_fgcolor',$this->GetPreference('dflt_fgcolor','#ffffff'));
$tpl->assign('dflt_bgcolor',$this->GetPreference('dflt_bgcolor','#009900'));

$detailpage = $returnid;
$detailpage_str = \cge_param::get_string($params,'detailpage');
if( $detailpage_str ) $detailpage = $this->resolve_alias_or_id($detailpage_str,$detailpage);

$parms = array();
$parms['detailpage'] = $detailpage;
if( $category ) $parms['category'] = $category;
$fetch_url = $this->create_url('cntnt01','ajax_fetchevents',$returnid,$parms);
$fetch_url = str_replace('amp;','',$fetch_url);

$tpl->assign('fetch_url',$fetch_url);
$tpl->assign('dflt_fgcolor',$this->GetPreference('dflt_fgcolor','#ffffff'));
$tpl->assign('dflt_bgcolor',$this->GetPreference('dflt_bgcolor','#009900'));
$tpl->assign('holiday_fgcolor',$this->GetPreference('holidayfgcolor','#000000'));
$tpl->assign('holiday_bgcolor',$this->GetPreference('holidaybgcolor','#ccffff'));
$tpl->assign('detailpage',$detailpage);
$tpl->display();

#
# EOF
#
?>