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

$detailpage = $this->GetPreference('defaultcalendarpage',$returnid);
$detailpage=($detailpage>0)?$detailpage:$returnid;
if (isset($params['detailpage'])) $detailpage = $this->resolve_alias_or_id($params['detailpage'],$detailpage);
$thetemplate = \cgcalendar_utils::find_layout_template($params,'listtemplate','CGCalendar::eventlist');

$year = $month = $day = $week = $orig_year = $orig_week = $orig_month = $orig_day = null;
$listtype = \cge_param::get_string($params,'listtype','month');
$reverse = \cge_param::get_bool($params,'reverse');
$limit = min(5000,\cge_param::get_int($params,'limit',1000));
$category = html_entity_decode(html_entity_decode(\cge_param::get_string($params, 'category', null)));

// build the initial list of paraeters that will be used on all navigation links
$navparms = array();
if( isset($params['listtemplate']) ) $navparms['listtemplate'] = $params['listtemplate'];
if( isset($params['detailpage']) ) $navparms['detailpage'] = $params['detailpage'];

if( isset($params['cal_origparams']) ) {
    // prolly the result of a pagination link or something
    // set some defaults, but allow params to override the date stuff, but not the template stuff.
    list($listtype,$year,$month,$day,$week,$reverse,$limit,$category) = unserialize(base64_decode(\cge_param::get_string($params,'cal_origparams')));
    $navparms['cal_origparams'] = $params['cal_origparams'];
    $orig_month = $month;
    $orig_week = $week;
    $orig_day = $day;
    $orig_year = $year;
}

// we do the max 0 stuff... to handle -1 in month, week and day params.
$year = \cge_param::get_int($params,'year',date('Y'));
$month = max(0,\cge_param::get_int($params,'month',date('m')));
$week = max(0,\cge_param::get_int($params,'week',date('W')));
$day = max(0,\cge_param::get_int($params,'day',date('d')));

if( !isset($params['cal_origparams']) ) {
    // raw execution of the view (not via a pagination link or something) we store the origparams
    $navparms['cal_origparams'] = base64_encode(serialize(array($listtype,$year,$month,$day,$week,$reverse,$limit,$category)));
    $orig_month = $month;
    $orig_week = $week;
    $orig_day = $day;
    $orig_year = $year;
}

// get the events based on the listtype and the year/month/day parameters.
$has_nav = TRUE;
$start_ts = $end_ts = null;
switch( $listtype ) {
case 'day':
    // display a day view specified by month, day, and year
    // start and end are for today
    $start_ts = mktime(00,00,01,$month,$day,$year);
    $end_ts = mktime(23,59,59,$month,$day,$year);
    // correct $month, $day, $year incase of negative or invalid values (i.e:  32 days in January)
    $day = date('d',$start_ts);
    $month = date('m',$start_ts);
    $year = date('Y',$start_ts);
    break;

case 'week':
    // displa a week view specified by year, and week
    // fix up week and year incase of negative or invalid values (i.e: week 53 or week 0)
    if( $week < 1 ) {
        $week = 53;
        $year--;
    } else if( $week > 53 ) {
        $week = 1;
        $year++;
    }
    // adjust for first day of week
    $first_day_of_week = (int) $this->GetPreference('firstdayofweek',1);
    if( $first_day_of_week == 1 ) {
        $sday = 1;
        $eday = 7;
    }
    else {
        $sday = 0;
        $eday = 6;
    }
    // calculate start and end ts
    $fmt = '%04d-W%02d-%d';
    $start_ts = strtotime(sprintf($fmt,$year,$week,$sday));
    $end_ts = strtotime(sprintf($fmt,$year,$week,$eday).' 23:59:59');
    break;

case 'month':
default:
    // default, display a month view
    $listtype = 'month';
    $start_ts = mktime(0,0,0,$month,1,$year);
    $last_day_of_month = date('t',$start_ts);
    $end_ts = mktime(23,59,59,$month,$last_day_of_month,$year);
    // fix up month and year in case of invalid or negative values (i.e: month -1 or month 13)
    $month = date('m',$start_ts);
    $year = date('Y',$start_ts);
    break;

case 'year':
    $start_ts = mktime(0,0,0,1,1,$year);
    $end_ts = mktime(23,59,59,12,31,$year);
    break;

case 'future':
case 'upcoming':
case 'upcominglist':
    // display future events
    $listtype = 'upcominglist';
    // no navigation, and no use of month/day/year params
    $start_ts = mktime(0,0,1);  // everything after the start of the day
    $has_nav = false;
    break;

case 'past':
case 'pastlist':
    // display past events
    $listtype = 'pastlist';
    // no navigation, and no use of month/day/year params
    $end_ts = mktime(0,0,1);  // everything before the start of the day.
    $has_nav = false;
    $reverse = !$reverse;
    break;
}

$events = null;
$sql = cgcalendar_utils::get_query($start_ts,$end_ts,$category,$reverse,$limit);
$event_ids = $db->GetCol(cgcalendar_utils::get_query($start_ts,$end_ts,$category,$reverse,$limit));
if( is_array($event_ids) && count($event_ids) ) {
    $loader = new cgc_event_loader($id,$event_ids,$detailpage,$reverse);
    if( isset($params['eventtemplate']) ) $loader->set_event_template($params['eventtemplate']);
    $events = $loader->get_events();
}

$tpl = $smarty->CreateTemplate($this->GetTemplateResource($thetemplate),null,null,$smarty);
$nav = null;
if( $has_nav ) {
    $nav = array();
    $oparms = $navparms;
    switch( $listtype ) {
    case 'day':
        $oparms['month'] = $month;
        $oparms['year'] = $year;
        if( $day == 1 ) {
            $oparms['day'] = -1;
        }
        else {
            $oparms['day'] = $day - 1;
        }
        $nav['prev_url'] = $this->create_url($id,'list',$returnid,$oparms);
        $oparms['day'] = $day + 1;
        $nav['next_url'] = $this->create_url($id,'list',$returnid,$oparms);
        $oparms['month'] = $orig_month;
        $oparms['day'] = $orig_day;
        $oparms['year'] = $orig_year;
        $nav['home_url'] = $this->create_url($id,'list',$returnid,$oparms);
        break;

    case 'week':
        $oparms['year'] = $year;
        if( $week == 1 ) {
            $oparms['week'] = -1;
        }
        else {
            $oparms['week'] = $week - 1;
        }
        $nav['prev_url'] = $this->create_url($id,'list',$returnid,$oparms);
        $oparms['week'] = $week + 1;
        $nav['next_url'] = $this->create_url($id,'list',$returnid,$oparms);
        $oparms['week'] = $orig_week;
        $oparms['year'] = $orig_year;
        $nav['home_url'] = $this->create_url($id,'list',$returnid,$oparms);
        break;

    case 'year':
        $oparms['year'] = $year - 1;
        $nav['prev_url'] = $this->create_url($id,'list',$returnid,$oparms);
        $oparms['year'] = $year + 1;
        $nav['next_url'] = $this->create_url($id,'list',$returnid,$oparms);
        $oparms['year'] = $orig_year;
        $nav['home_url'] = $this->create_url($id,'list',$returnid,$oparms);
        break;

    case 'month':
        $oparms['year'] = $year;
        if( $month == 1 ) {
            $oparms['month'] = -1;
        } else {
            $oparms['month'] = $month - 1;
        }
        $nav['prev_url'] = $this->create_url($id,'list',$returnid,$oparms);
        $oparms['month'] = $month + 1;
        $nav['next_url'] = $this->create_url($id,'list',$returnid,$oparms);
        $oparms['month'] = $orig_month;
        $oparms['year'] = $orig_year;
        $nav['home_url'] = $this->create_url($id,'list',$returnid,$oparms);
        break;
    }
}


// other language fields
$day_names = $this->GetDayNames();
$day_short_names = $this->GetDayShortNames();
$lang = $this->GetLabels();

// assign to Smarty
$tpl->assign('all_categories',\CGCalendar\category::get_categories(TRUE));
$tpl->assign('events', $events);
$tpl->assign('start_ts',$start_ts);
$tpl->assign('end_ts',$end_ts);
$tpl->assign('nav',$nav);
$tpl->assign('day_names', $day_names);
$tpl->assign('day_short_names', $day_short_names);
$tpl->assign('reverse',$reverse);
$tpl->assign('listtype',$listtype);
$tpl->assign('day', $day);
$tpl->assign('month', $month);
$tpl->assign('week',$week);
$tpl->assign('year', $year);
$tpl->assign('lang', $lang);
$tpl->assign('file_location',cgcalendar_utils::get_upload_location());
$tpl->display();
