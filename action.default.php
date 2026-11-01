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

$display = strtolower(\cge_param::get_string($params,'display','fullcalendar'));
switch($display) {
case 'event':
    require(__DIR__.'/function.displayevent.php');
    break;

case 'month':
case 'monthlist':
case 'list':
    $params['listtype'] = 'month';
    require(__DIR__.'/action.list.php');
    break;

case 'daylist':
    $params['listtype'] = 'day';
    require(__DIR__.'/action.list.php');
    break;

case 'weeklist':
    $params['listtype'] = 'week';
    require(__DIR__.'/action.list.php');
    break;

case 'yearlist':
    $params['listtype'] = 'year';
    require(__DIR__.'/action.list.php');
    break;

case 'pastlist':
    $params['listtype'] = 'pastlist';
    require(__DIR__.'/action.list.php');
    break;

case 'upcominglist':
    $params['listtype'] = 'upcominglist';
    require(__DIR__.'/action.list.php');
    break;

case 'calendar':
case 'fullcalendar':
   require(__DIR__.'/action.fullcalendar.php');
   break;

default:
   echo "Calendar: unknown display attribute '$display'!";
   break;
}
