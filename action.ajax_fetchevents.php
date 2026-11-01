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

$start = strtotime(\cge_param::get_string($_REQUEST,'start'));
$end = strtotime(\cge_param::get_string($_REQUEST,'end'));
$showchildren = (int)cge_param::get_bool($params,'showchildren',0);
$category_str = cge_param::get_string($params,'category');
$keyword = trim(cge_param::get_string($params,'keyword'));
$when = strtolower(trim(cge_param::get_string($params,'when','any')));
$limit = cge_param::get_int($params,'limit',1000); // 1000 is more than enough.
$detailpage = (int) cge_param::get_int($params,'detailpage',$returnid);
if( $detailpage < 1 ) $detailpage = $returnid;
$eventtemplate = cge_param::get_string($params,'eventtemplate');
$editpage = cge_param::get_string($params,'editpage');
$editeventtemplate = cge_param::get_string($params,'editeventtemplate');

// resolve the categories.
$categories = array();
if( $category_str ) {
    $cat_map = array();
    $tmp = \CGCalendar\category::get_categories();
    foreach( $tmp as $one ) {
        $cat_map[$one['category_name']] = (int) $one['category_id'];
    }

    $list = explode(',',$category_str);
    foreach( $list as $one ) {
        $one = trim($one);
        if( is_numeric($one) && (int) $one > 0 ) {
            if( !in_array($one,$categories) ) $categories[] = (int) $one;
        }
        else if( isset($cat_map[$one]) ) {
            $categories[] = $cat_map[$one];
        }
    }
}

// resolve the editpage
$editpage = $returnid;
{
    $tmp = $this->GetPreference('frontend_redirectpage','');
    if( $tmp ) {
        $tmp = $this->ProcessTemplateFromData($tmp);
        $editpage = $this->resolve_alias_or_id($tmp,$editpage);
    }
    $tmp = \cge_param::get_string($params,'editpage');
    if( $tmp ) {
        $editpage = $this->resolve_alias_or_id($tmp,$editpage);
    }
}
$feu = \cms_utils::get_module('FrontEndUsers');
$feu_uid = null;
if( $feu ) $feu_uid = $feu->LoggedInId();

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
if( $gCms->is_frontend_request() ) {
    $where[] = 'E.event_status = ?';
    $parms[] = 'P';
}
if( count($categories) ) {
    $query .= ' LEFT JOIN '.$this->events_to_categories_table_name.' EC ON E.event_id = EC.event_id';
    $where[] = 'EC.category_id in ('.implode(',',$categories).')';
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
while( !$rs->EOF ) {
    $list1[] = $rs->fields;
    $rs->MoveNext();
}
unset($rs);

$outlist = array();
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
    foreach( $list1 as $rec ) {
        try {
            $out = new stdClass;
            $out->id = $rec['event_id'];
            $out->title = $rec['event_title'];
            $out->summary = $rec['event_summary'];
            $out->details = $rec['event_details'];
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
                $out->title .= " (".$this->Lang('child').")";
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
            $out->start = $rec['event_date_start'];
            $out->end = $rec['event_date_end'];
            $out->allDay = ($rec['event_all_day'])?true:false;
            if( $out->allDay ) {
                $ts = $db->UnixTimeStamp( $rec['event_date_end'] );
                $out->end = strftime('%Y-%m-%d 00:00:00', strtotime( '+1 day', $ts) );
            }

            $parms = array('event_id'=>$rec['event_id']);
            $parms['display'] = 'event';
            if( $eventtemplate ) $parms['eventtemplate'] = $eventtemplate;
            $out->detail_url = $this->create_url('cntnt01','default',$detailpage,$parms);
            $out->detail_url = str_replace('&amp;','&',$out->detail_url);

            if( $feu_uid && $rec['event_created_by'] == $feu_uid ) {
                $parms = array('event_id'=>$rec['event_id']);
                if( $editeventtemplate ) $parms['editeventtemplate'] = $editeventtemplate;
                $parms['return_id'] = $returnid;
                $out->edit_url = $this->create_url('cntnt01','addedit_event',$editpage,$parms);
                $out->edit_url = str_replace('&amp;','&',$out->edit_url);
            }
            $outlist[] = $out;

        }
        catch( Exception $e ) {
            debug_to_log(__FILE__);
            debug_to_log($e->GetMessage());
        }
    }

}
cge_utils::send_ajax_and_exit($outlist);
