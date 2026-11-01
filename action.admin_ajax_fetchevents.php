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
if( !$this->CheckPermission('Modify Calendar') &&
    !$this->CheckPermission('Add Calendar Events') &&
    !$this->CheckPermission('Edit My Calendar Events') ) exit;

$start = cge_param::get_string($_REQUEST,'start');
$end = cge_param::get_string($_REQUEST,'end');
$showchildren = (int)cge_utils::get_param($params,'showchildren',0);
$category = (int)cge_utils::get_param($params,'category',-1);
$keyword = trim(cge_utils::get_param($params,'keyword'));
$when = strtolower(trim(cge_utils::get_param($params,'when','any')));
$filter = \cge_param::get_string($params,'filter','all');

// when can only be used when start/end are not specified.
$limit = cge_utils::get_param($params,'limit',1000); // 1000 is more than enough.
$st_ds = $db->DbTimeStamp($start);
$en_ds = $db->DbTimeStamp($end);

if( $start < 1 || $end < 1 ) {
    switch( $when ) {
    case 'past':
        $start = 1;
        $end = time();
        break;

    case 'future':
        $start = time();
        $end = 2147483648; // max unix time
        break;

    case 'any':
        break;
    }
}
if( $start > $end ) {
    $c = $start;
    $start = $end;
    $end = $c;
}

$where = array();
$parms = array();
// get all of the events
$query = 'SELECT E.* FROM '.$this->events_table_name.' E';
if( $category > 0 ) {
    $query .= ' LEFT JOIN '.$this->events_to_categories_table_name.' EC ON E.event_id = EC.event_id';
    $where[] = 'EC.category_id = ?';
    $parms[] = $category;
}
switch( $filter ) {
case 'published':
    $where[] = 'E.event_status = ?';
    $parms[] = 'P';
    break;
case 'draft':
    $where[] = 'E.event_status = ?';
    $parms[] = 'D';
    break;
case 'all':
default:
}
if( $start && $end ) {
    $where[] = '((E.event_date_start BETWEEN ? AND ?) OR (E.event_date_end BETWEEN ? AND ?))';
    $parms[] = trim($db->DbTimeStamp($start),"'");
    $parms[] = trim($db->DbTimeStamp($end),"'");
    $parms[] = trim($db->DbTimeStamp($start),"'");
    $parms[] = trim($db->DbTimeStamp($end),"'");
}
if( !$showchildren ) $where[] = 'COALESCE(E.event_parent_id,0) < 1';
if( $keyword ) {
    $keyword = strip_tags($keyword);
    $t_words = preg_split('/[\s,]+/',$keyword);
    $words = [];
    foreach( $t_words as $word ) {
        $word = trim($word);
        if( strlen($word) < 3) continue;  // ignore any one or 2 character words.
        if( $word && !in_array($word,$words) ) $words[] = $word;
    }
    if( count($words) ) {
        $arr = [];
        $fmt = "E.event_title LIKE '%s' OR E.event_summary LIKE '%s' OR E.event_details LIKE '%s'";
        foreach( $words as $word ) {
            $word = '%'.$word.'%';
            $arr[] = sprintf($fmt,$word,$word,$word);
        }
        $where[] = '('.implode(' OR ',$arr).')';
    }
}

// put the query together
if( count($where) ) $query .= ' WHERE ' . implode(' AND ',$where);

$rs = $db->SelectLimit($query,$limit,0,$parms);
$list1 = array();
if( $rs ) {
    while( !$rs->EOF ) {
        $list1[] = $rs->fields;
        $rs->MoveNext();
    }
}
unset($rs);
if( count($list1) ) {
    // get the categorie(s) for these events
    $idlist = cge_array::extract_field($list1,'event_id');
    $query = 'SELECT * FROM '.$this->events_to_categories_table_name.' WHERE event_id IN ('.implode(',',$idlist).') ORDER BY event_id,category_id';
    $tmp = $db->GetArray($query);
    $category_list = array();
    if( is_array($tmp) && count($tmp) ) {
        foreach( $tmp as $rec ) {
            $category_list[$rec['event_id']][] = $rec['category_id'];
        }
    }

    // now convert this into something compatible with fullcalendar
    $outlist = array();
    foreach( $list1 as $rec ) {
        try {
            $out = new stdClass;
            $out->id = $rec['event_id'];
            $out->title = $rec['event_title'];
            $out->summary = $rec['event_summary'];
            $out->status = $rec['event_status'];
            $out->canedit = 1;
            $out->candelete = 0;

            if( isset($category_list[$out->id]) && count($category_list[$out->id]) >= 1 ) {
                // this event is in only one category
                // we can set the color and textcolor
                $category = \CGCalendar\category::load($category_list[$out->id][0]);
                $out->category = $category['name'];
                $out->color = $category['bgcolor'];
                $out->textColor = $category['fgcolor'];
            }

            if( $rec['event_parent_id'] > 1 ) {
                // is a child event
                $out->child = $rec['event_parent_id'];
                $out->candelete = 1;
            }
            else if( $rec['event_recur_period'] && $rec['event_recur_period'] != 'none' ) {
                // is a parent event of a recurring event
                $out->title .= " (".$this->Lang('repeats').')';
                $out->recurs = 1;
            }
            else {
                $out->candelete = 1;
            }

            if( $out->status == 'D' ) {
                $out->title .= ' ('.$this->Lang('draft').')';
                $out->textColor = '#666A6D';
            }

            //$out->start = $db->UnixTimeStamp($rec['event_date_start']);
            //$out->end = $db->UnixTimeStamp($rec['event_date_end']);
            $out->start = $rec['event_date_start'];
            $out->end = $rec['event_date_end'];
            $out->allDay = ($rec['event_all_day'])?true:false;
            if( $out->allDay ) {
                // because of the way FullCalendar works we adjust the end date of all day events to be
                // at midnite on the following day.
                $ts = $db->UnixTimeStamp( $rec['event_date_end'] );
                $out->end = strftime('%Y-%m-%d 00:00:00', strtotime( '+1 day', $ts) );
            }

            $out->edit_url = $this->create_url($id,'admin_add_event',$returnid,array('event_id'=>$rec['event_id']));
            $out->edit_url = str_replace('&amp;','&',$out->edit_url);
            $outlist[] = $out;
        }
        catch( Exception $e ) {
            \cge_utils::log_exception( $e );
        }
    }

    $out = json_encode($outlist);
    echo $out;
}
exit;

?>
