<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: Skeleton (c) 2008
#      by Robert Allen (akrabat) and
#         Robert Campbell (calguy1000@cmsmadesimple.org)
#  An addon module for CMS Made Simple to allow displaying calendars,
#  and management and display of time based events.
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

// get the parameters
$detailpage = $this->GetPreference('defaultcalendarpage',$returnid);
if( $detailpage < 1 ) $detailpage = $returnid;
if (isset($params['detailpage'])) {
    $tmp = $this->resolve_alias_or_id($params['detailpage']);
    if( $tmp > 0 ) $detailpage = $tmp;
}

$category = '';
$page = 1;
$expr = '>=';
$reverse = false;
$sessionkey = null;
$inline = 0;
$categories_table_name = $this->categories_table_name;
$event_field_values_table_name = $this->event_field_values_table_name;
$events_to_categories_table_name = $this->events_to_categories_table_name;
$events_table_name = $this->events_table_name;
$use_session = get_parameter_value($params,'use_session');
if( $use_session ) {
    // we use a unique key, based on most parameters (except the ones that can change)
    // and only extract items from the session that can change (like dates, or page numbers).
    $inline = 1;
    $sessionkey = $use_session.cgcalendar_utils::get_sessionkey($params);
    $page = $this->session_get($sessionkey.'page',$page);
}
$category = html_entity_decode(html_entity_decode(get_parameter_value($params, 'category', $category)));
$pastitems = get_parameter_value($params, 'pastitems', 0);
$page = get_parameter_value($params,'page',$page);
if( $pastitems ) {
    $expr = '<';
    $reverse = true;
}
$limit = get_parameter_value($params, 'limit', 1000);
$inline = get_parameter_value($params,'inline',$inline);
$reverse = get_parameter_value($params, 'reverse', $reverse);
if( $sessionkey ) {
    //this remembers the page number for the next time we visit this page.
    $this->session_put($sessionkey.'page',$page);
}

// build the query
$sql2 = "SELECT E.event_id FROM $events_table_name E\n";
if(!empty($category)) {
    $sql2 .= "INNER JOIN $events_to_categories_table_name EC ON E.event_id = EC.event_id
              INNER JOIN $categories_table_name C ON EC.category_id = C.category_id";
}

$sorting = ($reverse == 'true' ? 'DESC' : 'ASC');
$start = $db->DbTimeStamp(time());
if( isset($params['displayforday']) ) $start = $db->DbTimeStamp(mktime(00,00,30));

$txt = "WHERE (E.event_date_start $expr $start OR (E.event_date_end $expr $start AND COALESCE(E.event_date_end,'****') != '****'))";
if ($pastitems) {
    $txt = "WHERE ( (E.event_date_start $expr $start AND COALESCE(E.event_date_end,'****') = '****' ) OR
                  (E.event_date_end $expr $start AND COALESCE(E.event_date_end,'****') != '****' ) )";
}
$sql2 .= ' '.$txt;
$where = ' AND ';

if(!empty($category)) {
    $cats = explode(',', $category);

    $tmp = array();
    foreach( $cats as $cat ) {
        $cat = $db->qstr(trim($cat));
        $tmp[] = "(C.category_name LIKE $cat)";
    }
    $txt = '('.implode(' OR ',$tmp).')';
    $sql2 .= ' AND '.$txt;
}

if( isset($params['unique_only']) && $params['unique_only'] ) $sql2 .= " GROUP BY E.event_title";

$sql1 = 'SELECT COUNT(*) FROM ('.$sql2.') AS sub';
$sql2 .= " ORDER BY E.event_date_start $sorting";

// get the number of articles
$events = null;
$navigation = null;
$count = $db->GetOne($sql1);
if( !$count ) {
  $tmp = $db->ErrorMsg();
  if( !empty($tmp) ) echo "DEBUG: query failed: ".$db->sql."<br/>".$db->ErrorMsg();
}
if( $count ) {
    $numpages = (int)($count / $limit);
    if( $count % $limit ) $numpages++;
    $offset = ($page - 1) * $limit;

    // get the data
    $rs = false;
    $rs = $db->SelectLimit($sql2, $limit,$offset);
    $event_ids = array();
    while( $rs && !$rs->EOF() ) {
        $event_ids[] = (int)$rs->fields['event_id'];
        $rs->MoveNext();
    }

    $loader = new cgc_event_loader($id,$event_ids,$detailpage,$reverse);
    if( isset($params['eventtemplate']) ) $loader->set_event_template($params['eventtemplate']);
    $events = $loader->get_events();

    // navigation
    $navigation = array();
    $parms = $params;
    unset($parms['returnid'],$parms['page']);
    if( $reverse ) {
        if( $page < $numpages ) {
            $parms['page'] = $page + 1;
            $navigation['prev'] = $this->CreateURL($id, 'default', $returnid, $parms, $inline ); // deprecated
            $navigation['ni_prev'] = $this->CreateURL($id, 'default', $returnid, $parms, false );
            $navigation['in_prev'] = $this->CreateURL($id, 'default', $returnid, $parms, true );
        }
        if( $page > 1 ) {
            $parms['page'] = $page - 1;
            $navigation['next'] = $this->CreateURL($id, 'default', $returnid, $parms, $inline ); // deprecated
            $navigation['ni_next'] = $this->CreateURL($id, 'default', $returnid, $parms, false );
            $navigation['in_next'] = $this->CreateURL($id, 'default', $returnid, $parms, true );
        }
    }
    else {
        if( $page < $numpages ) {
            $parms['page'] = $page + 1;
            $navigation['next'] = $this->CreateURL($id, 'default', $returnid, $parms, $inline ); // deprecated
            $navigation['ni_next'] = $this->CreateURL($id, 'default', $returnid, $parms, false );
            $navigation['in_next'] = $this->CreateURL($id, 'default', $returnid, $parms, true );
        }
        if( $page > 1 ) {
            $parms['page'] = $page - 1;
            $navigation['prev'] = $this->CreateURL($id, 'default', $returnid, $parms, $inline ); // deprecated
            $navigation['ni_prev'] = $this->CreateURL($id, 'default', $returnid, $parms, false );
            $navigation['in_prev'] = $this->CreateURL($id, 'default', $returnid, $parms, true );
        }
    }
}

$lang = $this->GetLabels();

// assign to Smarty
$thetemplate = $this->GetPreference(CGCALENDAR_PREF_DFLTUPCOMINGLIST_TEMPLATE);
if (isset($params['upcominglisttemplate'])) $thetemplate = $params['upcominglisttemplate'];
$tpl = $this->CreateSmartyTemplate($thetemplate,'upcominglist_');
if( count($events) && count($navigation) )  $tpl->assign('navigation',$navigation);
$tpl->assign('day_names',$this->GetDayNames());
$tpl->assign('day_short_names',$this->GetDayShortNames());
$tpl->assign('events', $events);
$tpl->assign('lang', $lang);
$tpl->assign('pastitems', $pastitems);
$tpl->assign('file_location',cgcalendar_utils::get_upload_location());
$tpl->display();

?>