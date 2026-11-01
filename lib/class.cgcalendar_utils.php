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
final class cgcalendar_utils
{
    private function __construct() {}

    static public function get_locale_dates()
    {
        $out = array();
        $out['monthNames'] = array();
        $out['monthNamesShort'] = array();
        for( $i = 0; $i < 12; $i++ ) {
            $out['monthNames'][] = strftime('%B',mktime(1,0,0,$i+1,1,2000));
            $out['monthNamesShort'][] = strftime('%b',mktime(1,0,0,$i+1,1,2000));
        }

        $mod = cms_utils::get_module(MOD_CGCALENDAR);
        $fmt = 'last sunday + %d days';
        $out['dayNames'] = array();
        $out['dayNamesShort'] = array();
        $curday = (int)date('w');
        for( $i = 0; $i < 7; $i++ ) {
            $out['dayNames'][] = strftime('%A',strtotime(sprintf($fmt,$i)));
            $out['dayNamesShort'][] = strftime('%a',strtotime(sprintf($fmt,$i)));
        }
        return $out;
    }

    static public function get_uid_by_username($username,$check_admin = false)
    {
        if( $check_admin ) {
            $userops = cmsms()->GetUserOperations();
            $user = $userops->LoadUserByUsername($username);
            if( is_object($user) ) return $user->id * -1 - 100;
        }

        $feu = cge_utils::get_module('FrontEndUsers');
        if( !$feu ) return FALSE;

        $info = $feu->GetUserInfoByName($username);
        if( !is_array($info) ) return FALSE;
        if( $info[0] == FALSE ) return FALSE;
        return $info[1]['id'];
    }


    static public function get_username($uid)
    {
        if( $uid < -100 ) {
            // an admin user
            $userops = cmsms()->GetUserOperations();
            $user = $userops->LoadUserByID($uid * -1 - 100);
            if( !$user ) return FALSE;
            return $user->username;
        }
        else if( $uid > 0 ) {
            $feu = cge_utils::get_module('FrontEndUsers');
            if( !$feu ) return FALSE;

            $userinfo = $feu->GetUserInfo($uid);
            if( !is_array($userinfo) ) return FALSE;

            if( $userinfo[0] != TRUE ) return FALSE;
            return $userinfo[1]['username'];
        }

        return FALSE;
    }

    public static function expand_events($eventids,$returnid,$parameters,$limit = 10000,$startoffset = 0)
    {
        if( !is_array($eventids) || count($eventids) < 1 ) return FALSE;

        $module = cge_utils::get_module(MOD_CGCALENDAR);
        $gCms = CmsApp::get_instance();
        $db = $gCms->GetDb();

        $events_to_categories_table_name = $module->events_to_categories_table_name;
        $categories_table_name = $module->categories_table_name;
        $event_field_values_table_name = $module->event_field_values_table_name;

        $userops = $gCms->GetUserOperations();
        $tmp = $userops->LoadUsers();
        $users = array();
        foreach ($tmp as $oneuser) {
            $users[$oneuser->id] = $oneuser;
        }

        $query = 'SELECT * FROM '.$module->events_table_name.' WHERE event_id IN ('.implode(',',$eventids).')
                  ORDER BY event_date_start ASC';
        $rs = $db->SelectLimit($query,$limit,$startoffset);
        $feu_users = array();
        while( !$rs->EOF() ) {
            $row = $rs->fields;
            $uid = $row['event_created_by'];
            if( $uid < 1 && !in_array($uid,$feu_users) ) $feu_users[] = $uid;
            $rs->MoveNext();
        }
        $rs->MoveFirst();

        $events = array();
        while($rs && ($row = $rs->FetchRow())) {
            $titleSEO = munge_string_to_url($row['event_title']);
            $destpage = $module->GetPreference('defaultcalendarpage',-1);
            $destpage = ($destpage>0) ? $destpage : $returnid;
            //$destpage =$detailpage!=''?$detailpage:$destpage;
            $prefix = $module->GetPreference('url_prefix'); if( !$prefix ) $prefix = 'calendar';
            $prettyurl = sprintf($prefix."/%d/%d-%s", $destpage, $row['event_id'], $titleSEO);
            $parms = array();
            $parms['event_id'] = $row['event_id'];
            $parms['display'] = 'event';
            if( isset($parameters['eventtemplate']) ) $parms['eventtemplate'] = $parameters['eventtemplate'];
            $url = $module->CreateLink('cntnt01', 'default',$destpage, $contents='', $parms, '', true, '', '', '', $prettyurl);
            $row['url'] = $url;
            $row['author'] = $users[$row['event_created_by']]->username;
            $row['authorname'] = $users[$row['event_created_by']]->firstname.' '.$users[$row['event_created_by']]->lastname;

            // Begin categories retrieval
            {
                // Build the sql to retrieve the categories for this event.
                $sql = "SELECT category_name FROM $events_to_categories_table_name
	        INNER JOIN $categories_table_name ON  $events_to_categories_table_name.category_id = $categories_table_name.category_id
	        WHERE event_id = ?";
                $crs = $db->Execute($sql,array($row['event_id'])); // Get the field values
                $categories = array();
                $categories_temp = array();
                if ($crs) {
                    // make sure there are results and assign to the $categories array
                    $categories_temp = $crs->GetArray();
                    foreach($categories_temp as $category) {
                        $category_name = $category['category_name'];
                        $categories[$category_name] = '1';
                    }
                }
                // Attach the custom fields to the event
                $row['categories'] = $categories;
            }
            // End categories retrieval

            // Begin custom fields retrieval
            {
                // Build the sql to retrieve the field values for this event.
                $sql = "SELECT field_name,field_value FROM $event_field_values_table_name WHERE event_id = ?";
                $frs = $db->Execute($sql,array($row['event_id'])); // Get the field values
                $fields = array();
                $fields_temp = array();
                if ($frs) {
                    // make sure there are results and assign to the $fields array
                    $fields_temp = $frs->GetArray();
                    foreach($fields_temp as $field) {
                        $field_name = $field['field_name'];
                        $field_value = $field['field_value'];
                        $fields[$field_name] = $field_value;
                    }
                }
                // Attach the custom fields to the event
                $row['fields'] = $fields;
            }
            // End custom fields retrieval

            // and add it to the list of completed, expanded events.
            $events[] = $row;
        }
        if( $rs ) $rs->Close();

        return $events;
    }

    public static function next_ymd($date_str)
    {
        list($yy,$mm,$dd) = self::to_ymd($date_str);
        if( $dd == '' ) {
            if( $mm == '' ) {
                // next year
                return (int)$yy+1;
            }
            else {
                // next month
                $mm = (int)$mm+1;
                if( $mm == 13 ) {
                    $yy = (int)$yy+1;
                    $mm = 1;
                }
                return "$yy-$mm";
            }
        }
        else {
            // next day.
            $dd = (int)$dd+1;
            $ut = strtotime($date_str);
            $lastday = date('t',$ut);
            if( $dd > $lastday ) {
                $dd = 1;
                $mm = (int)$mm+1;
                if( $mm == 13 ) {
                    $yy = (int)$yy+1;
                    $mm = 1;
                }
            }
            return "$yy-$mm-$dd";
        }
    }


    public static function prev_ymd($date_str)
    {
        list($yy,$mm,$dd) = self::to_ymd($date_str);
        if( $dd == '' ) {
            if( $mm == '' ) {
                // prev year
                return (int)$yy-1;
            }
            else {
                // prev month
                $mm = (int)$mm-1;
                if( $mm < 1 ) {
                    // wrapping years
                    $yy = (int)$yy-1;
                    $mm = 12;
                }
                return "$yy-$mm";
            }
        }
        else {
            // prev day.
            $dd = (int)$dd-1;
            if( $dd < 1 ) {
                $mm = (int)$mm-1;
                if( $mm < 1 ) {
                    // wrapping years
                    $yy = (int)$yy-1;
                    $mm = 12;
                    $dd = 31;
                }
                else {
                    $dd = date('t',strtotime("$yy-$mm-2"));
                }
            }
            return "$yy-$mm-$dd";
        }
    }


    public static function to_ymd($str)
    {
        $tmp = explode(' ',$str);
        $tmp = $tmp[0];
        $tmp = explode('-',$tmp);
        for( $i = count($tmp); $i < 3; $i++ ) {
            $tmp[$i] = '';
        }
        return $tmp;
    }


    public static function get_query($start,$end,$category,$reverse,$limit = null)
    {
        // suitable only for frontend displays
        // never returns unpublished events.
        $module = cms_utils::get_module('CGCalendar');
        $sorting = ($reverse == true ? 'DESC' : 'ASC');
        $events_table_name = $module->events_table_name;
        $categories_table_name = $module->categories_table_name;
        $events_to_categories_table_name = $module->events_to_categories_table_name;

        $db = cmsms()->GetDb();
        if( is_int($start) ) $start = $db->DbTimeStamp($start);
        if( is_int($end) ) $end = $db->DbTimeStamp($end);

        $sql = "SELECT DISTINCT E.event_id FROM $events_table_name E\n";

        if( $category ) {
            $sql .= "INNER JOIN $events_to_categories_table_name EC ON E.event_id = EC.event_id
	                 INNER JOIN $categories_table_name C ON EC.category_id = C.category_id";
        }

        if( $start ) {
            if( $end ) {
                // start date and end date
                $sql .= " WHERE ((E.event_date_start BETWEEN $start AND $end) OR (E.event_date_end BETWEEN $start AND $end)
                          OR    (E.event_date_start <= $start AND E.event_date_end > $end))";
            } else {
                // start date, but no end date
                // prolly an upcomming list.
                $sql .= " WHERE ((E.event_date_start >= $start) OR (E.event_date_end > $start))";
            }
        } else if( $end ) {
            // end date, but no start date.
            // prolly a past list
            $sql .= " WHERE ((E.event_date_start < $end) OR (E.event_date_end < $end AND E.event_date_end IS NOT NULL))";
        }

        if( $category ) {
            $cats = explode(',', $category);
            $tmp = array();
            foreach($cats as $cat) {
                $cat = trim($cat);
                if( strpos('*',$cat) !== FALSE ) {
                    $cat = str_replace('*','%',$cat);
                    $tmp[] = "C.category_name LIKE '$cat'";
                }
                else if( strpos('%',$cat) !== FALSE ) {
                    $tmp[] = "C.category_name LIKE '$cat'";
                }
                else if( is_numeric($cat) ) {
                    $tmp[] = 'C.category_id = '.(int)$cat;
                }
                else {
                    $tmp[] = "C.category_name = '$cat'";
                }
            }
            $sql .= ' AND ('.implode(' OR ',$tmp).')';
        }
        $sql .= " AND E.event_status = 'P'";  // only published events
        $sql .= " ORDER BY E.event_date_start $sorting";
        if( $limit ) $sql .= ' LIMIT '.max(1,(int) $limit);
        return $sql;
    }

    public static function delete_event($event_id)
    {
        $event_id = (int)$event_id;
        $db = CmsApp::get_instance()->GetDb();
        $mod = cms_utils::get_module(MOD_CGCALENDAR);
        $events_table_name = $mod->events_table_name;
        $events_to_categories_table_name = $mod->events_to_categories_table_name;
        $event_field_values_table_name = $mod->event_field_values_table_name;
        if( $event_id < 1 ) return FALSE;

        $query = 'SELECT * FROM '.$events_table_name.' WHERE event_id = ? OR event_parent_id = ? ORDER BY event_parent_id DESC';
        $rows = $db->GetArray($query,array($event_id,$event_id));

        if( CmsApp::get_instance()->is_frontend_request() ) {
            // userid has to match the owner
            $feu = cms_utils::get_module('FrontEndUsers');
            if( !$feu ) return;
            $userid = $feu->LoggedInId();

            foreach( $rows as $row ) {
                if( !$mod->HaveManageRight() && $row['event_created_by'] != $userid ) {
                    // oops, we can't delete anything
                    audit($event_id,$mod->GetName(),"FEU user $userid attempted to delete an event owned by somebody else");
                    return FALSE;
                }
            }
        }
        else {
            // admin request.
            if( !$mod->CheckPermission('Modify Calendar') ) {
                // userid has to match the owner.. and we have to have permission to edit my events
                if( !$mod->CheckPermission('Edit My Calendar Events') ) {
                    $userid = get_userid(FALSE) * -1 - 100;
                    foreach( $rows as $row ) {
                        if( $row['event_created_by'] != $userid ) {
                            // oops. we can't delete this event.
                            audit($event_id,$mod->GetName(),"Admin user $userid attempted to delete an event owned by somebody else");
                            return FALSE;
                        }
                    }
                }
            }
        }

        $query1 = 'DELETE FROM '.$event_field_values_table_name.' WHERE event_id = ?';
        $query2 = 'DELETE FROM '.$events_to_categories_table_name.' WHERE event_id = ?';
        $query3 = 'DELETE FROM '.$events_table_name.' WHERE event_parent_id = ?';
        $query4 = 'DELETE FROM '.$events_table_name.' WHERE event_id = ?';
        foreach( $rows as $one ) {
            $db->Execute($query1,array($one['event_id']));
            $db->Execute($query2,array($one['event_id']));
        }
        $db->Execute($query1,array($event_id));
        $db->Execute($query2,array($event_id));
        $db->Execute($query3,array($event_id));
        $db->Execute($query4,array($event_id));

        $mod->SendEvent('EventDeleted',array('event_id'=>$event_id));

        $cgcal = cms_utils::get_module('CGCalendar');
        $search = cms_utils::get_search_module();
        if( $search ) $search->DeleteWords($cgcal->GetName(),$event_id);

        return TRUE;
    }

    public static function get_upload_location()
    {
        $mod = \cms_utils::get_module(MOD_CGCALENDAR);
        $config = \cms_config::get_instance();
        $origdir = $dir = trim($mod->GetPreference('uploaddirectory'));
        if( !$origdir ) $origdir = $dir = $config['uploads_path'];
        if( !is_dir($dir) ) $dir = cms_join_path($config['root_path'],$origdir);
        if( !is_dir($dir) ) $dir = cms_join_path($config['uploads_path'],$origdir);
        if( is_dir($dir) ) {
            $rp1 = realpath($config['root_path']);
            $rp2 = realpath($dir);
            if( startswith($rp2,$rp1) ) {
                $url = str_replace($config['root_path'],$config['root_url'],$dir);
                return $url;
            }
        }
    }

    public static function find_layout_template($params,$paramname,$typename)
    {
        $paramname = (string) $paramname;
        $typename = (string) $typename;
        if( !is_array($params) || !($thetemplate = \cge_param::get_string($params,$paramname)) ) {
            $tpl = CmsLayoutTemplate::load_dflt_by_type($typename);
            if( !is_object($tpl) ) {
                audit('',$this->GetName(),'No default '.$typename.' template found');
                return;
            }
            $thetemplate = $tpl->get_name();
            unset($tpl);
        }
        return $thetemplate;
    }
} // end of class

#
# EOF
#
?>
