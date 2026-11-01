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

/** this class loads events from the database, and returns arrays with url and other fields suitable for frontend display **/
final class cgc_event_loader
{
    private $_idlist;
    private $_eventtemplate;
    private $_events = array();
    private $_fields = array();
    private $_categories = array();
    private $_destpage;
    private $_url_prefix = 'calendar';
    private $_reverse = FALSE;
    private $_actionid;

    public function __construct($actionid,$idlist,$destpage,$reverse)
    {
        if( is_array($idlist) ) {
            $this->_idlist = array();
            foreach( $idlist as $one ) {
                $one = (int)$one;
                if( $one < 1 ) continue;
                $this->_idlist[] = $one;
            }
            $this->_idlist = array_unique($this->_idlist);
        }
        else if( (int)$idlist > 0 ) {
            $this->_idlist = array((int)$idlist);
        }
        $this->_actionid = $actionid;
        $this->_reverse = (bool)$reverse;
        $this->_destpage = $destpage;
        $tmp = cms_utils::get_module(MOD_CGCALENDAR)->GetPreference('url_prefix');
        if( $tmp ) $this->_url_prefix = $tmp;
    }

    public function set_event_template($eventtemplate)
    {
        $this->_eventtemplate = $eventtemplate;
    }

    private function reorder_customfields($customfields)
    {
        $fields = cms_utils::get_module('CGCalendar')->GetFields();
        $fields = cge_array::to_hash($fields,'field_name');

        $func = function($a,$b) use ($fields) {
            if( $a['event_id'] < $b['event_id'] ) return -1;
            if( $a['event_id'] > $b['event_id'] ) return 1;
            $aa = $fields[$a['field_name']];
            $aa = $fields[$b['field_name']];
            if( $aa['field_order'] < $bb['field_order'] ) return -1;
            if( $aa['field_order'] > $bb['field_order'] ) return 1;
            // now compare by field value.
            if( $a['field_value'] < $b['field_value'] ) return -1;
            if( $a['field_value'] > $b['field_value'] ) return -1;
            return 0;
        };

        usort($customfields,$func);
    }

    public function get_events()
    {
        $gCms = cmsms();
        $sorting = ($this->_reverse) ? 'DESC' : 'ASC';
        $query = 'SELECT * FROM '.cms_db_prefix().'module_cgcalendar_events WHERE event_id IN (';
        $query .= implode(',',$this->_idlist).')';
        $query .= " ORDER BY event_date_start $sorting";
        $db = cmsms()->GetDb();

        // get the parent ids and events.
        $events = $db->GetArray($query);
        $parent_ids = $this->_idlist;
        if( !is_array($events) || count($events) == 0 ) return;

        foreach( $events as $row ) {
            if( $row['event_parent_id'] > 0 ) $parent_ids[] = $row['event_parent_id'];
        }
        $parent_ids = array_unique($parent_ids);

        $customfields = array();
        {
            $query = 'SELECT * FROM '.cms_db_prefix().'module_cgcalendar_event_field_values
                WHERE event_id IN ('.implode(',',$parent_ids).') ORDER BY event_id';
            $frs = $db->GetArray($query);
            foreach( $frs as $row ) {
                $customfields[$row['event_id']][] = $row;
            }
        }

        $allcategories = array();
        {
            $query = 'SELECT * FROM '.cms_db_prefix().'module_cgcalendar_events_to_categories
                       WHERE event_id IN ('.implode(',',$this->_idlist).') ORDER BY event_id';
            $crs = $db->Execute($query);
            while( $crs && !$crs->EOF() ) {
                $row = $crs->fields;
                $allcategories[$row['event_id']][] = $row;
                $crs->MoveNext();
            }
            if( count($allcategories) ) \CGCalendar\category::get_categories(); // forces preload.
        }

        $users = array();
        {
            $userops = $gCms->GetUserOperations();
            $userlist = $userops->LoadUsers();
            foreach ($userlist as $oneuser) {
                $users[$oneuser->id] = $oneuser;
            }
        }

        $module = cms_utils::get_module(MOD_CGCALENDAR);
        $allfields = \cge_array::to_hash($module->GetFields(),'field_name');
        foreach( $events as &$row ) {
            $titleSEO = munge_string_to_url($row['event_title']);
            $prettyurl = sprintf($this->_url_prefix.'/%d/%d-%s', $this->_destpage, $row['event_id'], $titleSEO);
            $parms = array();
            $parms['event_id'] = $row['event_id'];
            $parms['display'] = 'event';
            if( $this->_eventtemplate ) $parms['eventtemplate'] = $this->_eventtemplate;
            $row['url'] = $module->create_url($this->_actionid,'default',$this->_destpage,$parms,false,false,$prettyurl);

            $ptr = null;
            if( isset($allcategories[$row['event_id']]) ) {
                $ptr = $allcategories[$row['event_id']];
            }
            else if( $row['event_parent_id'] > 0 && isset($allcategories[$row['event_parent_id']]) ) {
                $ptr = $allcategories[$row['event_parent_id']];
            }

            $row['bgcolor'] = $row['fgcolor'] = null;
            if( $ptr ) {
                $categories = array();
                foreach( $ptr as $cat ) {
                    $categories[] = $cat['category_id'];
                    if( !$row['bgcolor'] ) {
                        // get colors for this event...
                        $cat_obj = \CGCalendar\category::load($cat['category_id']);
                        $row['fgcolor'] = $cat_obj['fgcolor'];
                        $row['bgcolor'] = $cat_obj['bgcolor'];
                    }
                }
                $row['categories'] = $categories;
                $row['category_names'] = $module->GetCategoryNames($categories);
            }

            $ptr = null;
            if( isset($customfields[$row['event_id']]) ) {
                $ptr = $customfields[$row['event_id']];
            }
            else if( $row['event_parent_id'] > 0 && isset($customfields[$row['event_parent_id']]) ) {
                $ptr = $customfields[$row['event_parent_id']];
            }
            if( $ptr ) {
                $fields = array();
                foreach( $ptr as $key => $rec ) {
                    $field_name = $rec['field_name'];
                    $rec = array_merge($allfields[$field_name],$rec);
                    $rec['raw_value'] = $rec['field_value'];
                    if( $rec['field_type'] == 6 ) {
                        if( !empty($rec['field_extra']['ddopts']) ) {
                            $rec['options'] = \cge_array::explode_with_key($rec['field_extra']['ddopts'],'=',"\n");
                            $rec['field_value'] = $rec['options'][$rec['raw_value']];
                        }
                    }
                    $fields[$field_name] = $rec;
                }
                $row['fields'] = $fields;
            }

            if( $row['event_created_by'] < 0 ) {
                // negative values (smaller than -100) indicate an admin user account
                $uid = $row['event_created_by'] + 100 * -1;
                $row['admin_uid'] = $uid;
                if( isset($users[$uid]) ) $row['author'] = $users[$uid]->username;
            }

            $row['is_parent'] = ($row['event_recur_interval'] != 'none' && $row['event_recur_interval'] != '');
        }
        return $events;
    }
} // end of class

?>
