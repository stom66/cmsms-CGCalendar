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
if( !$this->CheckPermission('Modify Site Preferences') && !$this->CheckPermission('Manage Calendar Attributes') ) exit;


echo $this->StartTabHeaders();
if( $this->CheckPermission('Manage Calendar Attributes') ) {
    echo $this->SetTabHeader('admin_manage_categories', $this->Lang('cal_categories'));
    echo $this->SetTabHeader('fields',$this->Lang('fields'));
}
if( $this->CheckPermission('Modify Site Preferences') ) echo $this->SetTabHeader('admin_manage_settings',$this->Lang('cal_settings'));
echo $this->EndTabHeaders();

echo $this->StartTabContent();
if( $this->CheckPermission('Manage Calendar Attributes') ) {
    echo $this->StartTab("admin_manage_categories",$params);
    include(__DIR__.'/function.admin_categories_tab.php');
    echo $this->EndTab();

    echo $this->StartTab("fields",$params);
    include(__DIR__.'/function.admin_fieldstab.php');
    echo $this->EndTab();
}

if( $this->CheckPermission('Modify Site Preferences') ) {
    echo $this->StartTab("admin_manage_settings");
    include(__DIR__.'/function.admin_settingstab.php');
    echo $this->EndTab();
}
echo $this->EndTabContent();
