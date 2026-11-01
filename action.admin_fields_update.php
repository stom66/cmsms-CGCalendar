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
if( !$this->CheckPermission("Manage Calendar Attributes") ) return;
$this->SetCurrentAction('admin_settings');
$this->SetCurrentTab('admin_manage_categories');

function _cgcalDeleteField($field_oldname)
{
    $db = cmsms()->GetDb();
    $mod = cms_utils::get_module('CGCalendar');
    $fields_table_name = $mod->fields_table_name;
    $event_field_values_table_name = $mod->event_field_values_table_name;

    $sql = "DELETE FROM $$event_field_values_table_name where field_name='$field_oldname'";
    $db->Execute($sql);

    $sql = "DELETE FROM $fields_table_name where field_name='$field_oldname'";
    $db->Execute($sql);
}


$error="";
$message="";
$searchable = null;
$wysiwyg = null;
$ddopts = null;
$this->SetCurrentTab('fields');
$nupdates = 0;
$nadds = 0;

$ids = $params['field_ids']; // (old names... empty value indicates an add)
$new_names = $params['field_names'];
$types = $params['field_types'];
if( isset($params['field_searchable']) ) $searchable = $params['field_searchable'];
if( isset($params['field_wysiwyg']) ) $wysiwyg = $params['field_wysiwyg'];
if( isset($params['field_ddopts']) ) $ddopts = $params['field_ddopts'];

// find deletions.
$fields = $this->GetFields();
$field_names = cge_array::extract_field($fields,'field_name');
$deleted = array();

foreach( $field_names as $name ) {
    if( $name == '' ) continue;
    if( !in_array($name,$ids) ) _cgcalDeleteField($name);
}

// now process new records and updates
$num_records = count($ids);
$field_order = -1;
for($i = 0; $i < $num_records; $i++) {
    $field_oldname = $ids[$i];
    $field_newname = trim($new_names[$i]);
    $field_type = (int)$types[$i];
    $field_searchable = (isset($searchable[$i]))?$searchable[$i]:0;
    $field_extra = array('searchable'=>0,'wysiwyg'=>0);
    $field_extra['searchable'] = (isset($searchable[$i]))?$searchable[$i]:0;
    $field_extra['wysiwyg'] = (isset($wysiwyg[$i]))?$wysiwyg[$i]:0;
    $field_extra['ddopts'] = (isset($ddopts[$i]))?$ddopts[$i]:null;

    $field_order++;

    if( $field_oldname == '' && $field_newname == '' ) continue; // empty... nothing to do.

    if( $field_oldname == '' && $field_newname != '' ) {
        // an add..
        $sql = 'INSERT INTO ' . $this->fields_table_name . ' (field_name,field_type,field_searchable,field_extra,field_order) VALUES (?,?,?,?,?)';
        if( $field_extra ) $field_extra = serialize($field_extra);
        $db->Execute($sql,array($field_newname,$field_type,$field_searchable,$field_extra,$field_order));
        $nadds++;
    }
    else {
        // an update
        $sql = 'UPDATE ' . $this->fields_table_name . ' SET field_name = ?, field_type = ?, field_searchable = ?,
               field_extra = ?, field_order = ? WHERE field_name = ?';
        if( $field_extra ) $field_extra = serialize($field_extra);
        $db->Execute($sql,array($field_newname, $field_type, $field_searchable, $field_extra, $field_order, $field_oldname));
        if( $field_newname != $field_oldname ) {
            $sql = 'UPDATE ' . $this->event_field_values_table_name . ' SET field_name = ? WHERE field_name = ?';
            $db->Execute($sql,array($field_newname, $field_oldname));
        }
        $nupdates++;
    }
}

$this->SetMessage($this->Lang('msg_fieldsupdated'));
$this->RedirectToTab($id);
?>
