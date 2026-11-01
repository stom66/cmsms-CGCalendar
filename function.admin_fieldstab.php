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

$smarty->assign('formstart', $this->CreateFormStart($id, 'admin_fields_update', $returnid, 'post', ''));
$smarty->assign('formend',$this->CreateFormEnd());
$fields = $this->GetFields();
if( is_array($fields) && count($fields) ) $smarty->assign('fields',$fields);

$types = array();
$types[$this->Lang('textfield')] = '0';
$types[$this->Lang('uploadfield')] = '1';
$types[$this->Lang('textarea')] = '2';
$cdmod = cms_utils::get_module('CompanyDirectory');
if( is_object($cdmod) ) $types[$this->Lang('cd_company')] = '3';
$types[$this->Lang('fileattachment')] = 4;
$types[$this->Lang('imageattachment')] = 7;
$types[$this->Lang('checkbox')] = 5;
$types[$this->Lang('dropdown')] = 6;
$types[$this->Lang('dropdownfromudt')] = 8;
$smarty->assign('types',array_flip($types));

if( version_compare(CMS_VERSION,'2.2.99') < 0 ) {
    $smarty->assign('usertags',$this->cms->GetUserTagOperations()->ListUserTags());
}
else {
    $smarty->assign('usertags',$this->cms->GetSimplePluginOperations()->get_list());
}
$smarty->assign('typetext',$this->Lang('type'));
$smarty->assign('nametext',$this->Lang('name'));
echo $this->ProcessTemplate('fieldstab.tpl');

// EOF
