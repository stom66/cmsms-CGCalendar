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
if( !$this->CheckPermission('Modify Calendar') ) return;

try {
    if( isset($params['cal_cancel']) ) {
        $this->RedirectToTab($id);
    }

    // get a list of the events (and event names, and wether they are a child event)
    // that are expired.
    $sql = 'SELECT event_id, event_title, event_summary, event_date_start, event_date_end, event_recur_period, event_parent_id FROM '.$this->events_table_name.'
        WHERE (event_date_start < NOW()) OR (event_date_end IS NOT NULL AND event_date_end < NOW())
        ORDER BY event_date_start DESC
        LIMIT 500';
    $list = $db->GetArray($sql);
    if( !count($list) ) {
        $this->SetMessage($this->Lang('msg_noexpiredevents'));
        $this->RedirectToTab($id);
    }

    // convert the list to a hash based on the event id
    $list = \cge_array::to_hash($list,'event_id');

    // now, for all the parent events, get the last child in the future.
    $parent_ids = array();
    $list_future_parents = array(); // event ids that have children in the future.
    foreach( $list as $row ) {
        if( !empty($row['event_recur_period']) && $row['event_recur_period'] != 'none' ) {
            $parent_ids[] = (int) $row['event_id'];
        }
    }
    if( count($parent_ids) ) {
        $sql = 'SELECT t1.event_id,t1.event_parent_id,t1.event_date_start,t1.event_date_end from %table% t1
                JOIN (SELECT event_parent_id,MAX(event_date_start) event_date_start
                      FROM %table%
                      WHERE event_parent_id IN ('.implode(',',$parent_ids).')
                      GROUP BY event_parent_id) t2
                ON t1.event_parent_id = t2.event_parent_id AND t1.event_date_start = t2.event_date_start';
        $sql = str_replace('%table%',$this->events_table_name,$sql);
        $list2 = $db->GetArray($sql);
        array_walk($list2,function($future_record) use (&$list,&$db) {
                $target_event = (int) $future_record['event_parent_id'];
                if( $target_event < 1 ) return;
                if( !isset($list[$target_event]) ) return;
                $ts = $db->UnixTimeStamp($future_record['event_date_start']);
                $list[$target_event]['event_recur_last'] = $ts;
                if( $ts > time() ) $list_future_parents[] = $target_event;
            });
    }

    if( isset($params['cal_submit']) ) {
        try {
            $cal_select = \cge_utils::get_param($params,'cal_select');
            if( !is_array($cal_select) || !count($cal_select) ) throw new \Exception($this->Lang('error_nothingselected'));

            $n = 0;
            foreach( $cal_select as $event_id ) {
                // double check that this event is not a parent with children in the future
                if( in_array($event_id,$list_future_parents) ) continue;
                cgcalendar_utils::delete_event($event_id);
                $n++;
            }
            audit('',$this->GetName(),'Bulk deleted expired events');
            $this->SetMessage($this->Lang('deleted_n',$n));
            $this->RedirectToTab($id);
        }
        catch( \Exception $e ) {
            echo $this->ShowErrors($e->GetMessage());
        }
    }

    $tpl = $smarty->CreateTemplate($this->GetTemplateResource('admin_remove_expired.tpl'));
    $tpl->assign('mod',$this);
    $tpl->assign('actionid',$id);
    $tpl->assign('events',$list);
    $tpl->display();
}
catch( \Exception $e ) {
    echo $this->ShowErrors($e->GetMessage());
}