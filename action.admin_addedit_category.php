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
if( !$this->CheckPermission('Manage Calendar Attributes') ) return;
$this->SetCurrentAction('admin_settings');
$this->SetCurrentTab('admin_manage_categories');

// handle cancel
if( isset($params['cancel']) ) $this->RedirectToTab($id);

// setup
$catid = (int)cge_utils::get_param($params,'catid');
$category = null;
try {
    // create/get initial empty category (for adding)
    $category = new \CGCalendar\category;

    // if we have a category load it.
    if( $catid > 0 ) $category = \CGCalendar\category::load($catid);

    // handle submit
    if( isset($params['submit']) ) {
        // merge the form data into the category
        $category['name'] = $params['name'];
        $category['bgcolor'] = $params['bgcolor'];
        $category['fgcolor'] = $params['fgcolor'];

        // save
        $category->save();

        // redirect outa here.
        $this->SetMessage($this->Lang('categoryupdated'));
        $this->RedirectToTab();
    }

}
catch( Exception $e ) {
    echo $this->ShowErrors($e->GetMessage());
}

// give the category to smarty
$parms = array();
if( $catid > 0 ) $parms['catid'] = $catid;
$smarty->assign('formstart',$this->CGCreateFormStart($id,'admin_addedit_category',$returnid,$parms));
$smarty->assign('formend',$this->CreateFormEnd());
$smarty->assign('category',$category);

// display the template
echo $this->ProcessTemplate('admin_addedit_category.tpl');
?>