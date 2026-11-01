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

namespace CGCalendar;

final class category implements \ArrayAccess
{
    private static $_cached_categories;
    private $_data = array();
    private $_dirty;

    public function __construct()
    {
        $mod = \cms_utils::get_module(MOD_CGCALENDAR);
        $this->_data['fgcolor'] = $mod->GetPreference('dflt_fgcolor','000000');
        $this->_data['bgcolor'] = $mod->GetPreference('dflt_bgcolor','ffffff');
    }

    public function OffsetGet($key)
    {
        switch( $key ) {
        case 'id':
        case 'order':
            if( isset($this->_data[$key]) ) return (int)$this->_data[$key];
            break;

        case 'name':
        case 'bgcolor':
        case 'fgcolor':
            if( isset($this->_data[$key]) ) return trim($this->_data[$key]);
            break;

        default:
            throw new \Exception($key.' is not a valid member of a '.__CLASS__.' object');
        }
    }

    public function OffsetSet($key,$value)
    {
        switch( $key ) {
        case 'id':
        case 'order':
            // thse properties cannot be set this way
            throw new \Exception("The $key member cannot be set this way in a ".__CLASS__);

        case 'name':
            $this->_data[$key] = trim($value);
            $this->_dirty = true;
            break;

        case 'bgcolor':
        case 'fgcolor':
            $this->_data[$key] = trim($value);
            $this->_dirty = true;
            break;

        default:
            throw new \Exception($key.' is not a valid member of a '.__CLASS__.' object');
        }
    }

    public function OffsetExists($key)
    {
        switch( $key ) {
        case 'id':
        case 'order':
        case 'name':
        case 'bgcolor':
        case 'fgcolor':
            if( isset($this->_data[$key]) ) return TRUE;
            return FALSE;

        default:
            throw new \Exception($key.' is not a valid member of a '.__CLASS__.' object');
        }
    }

    public function OffsetUnset($key)
    {
        switch( $key ) {
        case 'id':
        case 'order':
            // thse properties cannot be set this way
            throw new \Exception("The $key member cannot be adjusted this way in a ".__CLASS__);

        case 'name':
            unset($this->_data[$key]);
            $this->_dirty = true;
            break;

        case 'bgcolor':
        case 'fgcolor':
            unset($this->_data[$key]);
            $this->_dirty = true;
            break;

        default:
            throw new \Exception($key.' is not a valid member of a '.__CLASS__.' object');
        }
    }

    public function validate()
    {
        if( !$this['name'] ) throw new \Exception('name cannot be empty in a '.__CLASS__.' object');
        if( isset($this->_data['id']) && $this->_data['id'] < 0 ) throw new \Exception('invalid id in '.__CLASS__);

        // validate that the name does not exist
        $db = cmsms()->GetDb();
        $mod = \cms_utils::get_module(MOD_CGCALENDAR);
        $tmp = null;
        if( isset($this->_data['id']) && $this->_data['id'] > 0 ) {
            $query = 'SELECT category_id FROM '.$mod->categories_table_name.' WHERE category_name = ? AND category_id != ?';
            $tmp = $db->GetOne($query,array($this['name'],$this['id']));
        } else {
            $query = 'SELECT category_id FROM '.$mod->categories_table_name.' WHERE category_name = ?';
            $tmp = $db->GetOne($query,array($this['name']));
        }
        if( $tmp ) throw new \Exception($mod->Lang('error_categoryexists',$this['name']));
    }

    protected function _insert()
    {
        $db = \cmsms()->GetDb();
        $mod = \cms_utils::get_module(MOD_CGCALENDAR);

        // determine a new sequence for categories.
        $this->_data['id'] = $db->GenID($mod->categories_table_name.'_seq');

        // determine a new order
        $query = 'SELECT COALESCE(MAX(category_order),0)+1 FROM '.$mod->categories_table_name;
        $this->_data['order'] = (int)$db->GetOne($query);

        // insert the thing
        $query = 'INSERT INTO '.$mod->categories_table_name.' (category_id,category_name,category_bgcolor,category_fgcolor,category_order) VALUES (?,?,?,?,?)';
        $dbr = $db->Execute($query,array($this['id'],$this['name'],$this['bgcolor'],$this['fgcolor'],$this['order']));
        $mod->SendEvent('CategoryAdded',array('category_id'=>$this['id'],'category_name'=>$this['name'],'category_order'=>$this['order']));
    }

    protected function _update()
    {
        $db = cmsms()->GetDb();
        $mod = \cms_utils::get_module(MOD_CGCALENDAR);

        $query = 'UPDATE '.$mod->categories_table_name.' SET category_name = ?, category_bgcolor = ?, category_fgcolor = ?, category_order = ?
                  WHERE category_id = ?';
        $dbr = $db->Execute($query,array($this['name'],$this['bgcolor'],$this['fgcolor'],$this['order'],$this['id']));
        $mod->SendEvent('CategoryEdited',array('category_id'=>$this['id'],'category_name'=>$this['name'],'category_order'=>$this['order']));
    }

    public function save()
    {
        if( !$this->_dirty ) return;
        $this->validate();
        if( $this['id'] == 0 ) {
            $this->_insert();
        } else {
            $this->_update();
        }
        self::$_cached_categories = null;
        $this->_dirty = null;
    }

    public static function &load($category_id)
    {
        $obj = null;
        $categories = self::get_categories(TRUE);
        if( !isset($categories[$category_id]) ) return $obj;

        $row = $categories[$category_id];
        $obj = new self();
        $obj->_data['id'] = $row['category_id'];
        $obj->_data['name'] = $row['category_name'];
        $obj->_data['bgcolor'] = $row['category_bgcolor'];
        $obj->_data['fgcolor'] = $row['category_fgcolor'];
        $obj->_data['order'] = $row['category_order'];
        return $obj;
    }

    public static function get_categories($by_id = FALSE)
    {
        if( !is_array(self::$_cached_categories) ) {
            $db = cmsms()->GetDb();
            self::$_cached_categories = array();
            $mod = \cms_utils::get_module(MOD_CGCALENDAR);
            $categories_table_name = $mod->categories_table_name;
            $sql = "SELECT * FROM $mod->categories_table_name ORDER BY category_order, category_name";

            self::$_cached_categories = $db->GetArray($sql);
        }

        if( $by_id ) return \cge_array::to_hash(self::$_cached_categories,'category_id');
        return self::$_cached_categories;
    }


    public static function delete($catid)
    {
        $catid = (int)$catid;

        $cat = self::load($catid);
        $db = cmsms()->GetDb();
        $mod = \cms_utils::get_module(MOD_CGCALENDAR);

        $query = 'UPDATE '.$mod->categories_table_name.' SET category_order = category_order - 1 WHERE category_order > ?';
        $db->Execute($query,array($cat['order']));

        $query = 'DELETE FROM '.$mod->events_to_categories_table_name.' WHERE category_id = ?';
        $db->Execute($query,array($catid));

        $query = 'DELETE FROM '.$mod->categories_table_name.' WHERE category_id = ?';
        $db->Execute($query,array($catid));

        $mod->SendEvent('CategoryDeleted',array('category_id'=>$catid,'category_name'=>$cat['name'],'category_order'=>$cat['order']));
    }
} // end of class
?>