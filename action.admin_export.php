<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: CGCalendar (c) 2017
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
if( !$this->CheckPermission('Modify Calendar') ) return;

$all_categories = \CGCalendar\category::get_categories();
$categories = null;
if( count($all_categories) ) {
    foreach( $all_categories as $one ) {
        $categories[$one['category_id']] = $one['category_name'];
    }
}
$settings = new \CGCalendar\ExportSettings;
if( ($tmp = $this->GetPreference('exportsettings')) ) $settings = unserialize($tmp);

if( \cge_param::exists($params,'cancel') ) {
    $this->RedirectToTab();
}
else if( \cge_param::exists($params,'submit') ) {
    $settings->datesel = \cge_param::get_string($params,'datesel');
    $tmp = \cge_param::get_string($params,'fromdate');
    if( $tmp ) $tmp = strtotime($tmp);
    $settings->fromdate = $tmp;
    $settings->category = \cge_param::get_int($params,'category');
    $settings->delimiter = \cge_param::get_string($params,'delimiter');
    $this->SetPreference('exportsettings',serialize($settings));

    $exporter = new \CGCalendar\Exporter( $this, $settings );
    $exporter->generate();

    // if we even get here, nothing was exported
    echo $this->ShowErrors($this->Lang('err_exported_nomatches'));
}
$tpl = $this->CreateSmartyTemplate('admin_export.tpl');
$tpl->assign('categories',$categories);
$tpl->assign('settings',$settings);
$tpl->display();
