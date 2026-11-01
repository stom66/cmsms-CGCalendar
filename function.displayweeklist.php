<?php
if( !isset($gCms) ) exit;

$sessionkey = null;
$year = date('Y');
$week = date('W');
$detailpage = $this->GetPreference('defaultcalendarpage',$returnid);
if( $detailpage < 1 ) $detailpage = $returnid;
if (isset($params['detailpage'])) {
  $tmp = $this->resolve_alias_or_id($params['detailpage']);
  if( $tmp > 0 ) $detailpage = $tmp;
}
$inline = (isset($params['inline']))?(int)$params['inline']:0;
$first_day_of_week = $this->GetPreference('firstdayofweek',1);
$category = html_entity_decode(html_entity_decode(get_parameter_value($params, 'category')));
$limit = get_parameter_value($params, 'limit', 1000);
$limit = min(1000,$limit);
$reverse = get_parameter_value($params, 'reverse', false);
$use_session = get_parameter_value($params, 'use_session');

if( $use_session ) {
  $sessionkey = $use_session.cgcalendar_utils::get_sessionkey($params);
  $year = (int)$this->session_get($sessionkey.'year',$year);
  $week = (int)$this->session_get($sessionkey.'week',$week);
}

$year = (int)get_parameter_value($params, 'year', $year);
$week = (int)get_parameter_value($params, 'week', $week);

if( $sessionkey ) {
  $this->session_put($sessionkey.'year',$year);
  $this->session_put($sessionkey.'week',$week);
  $inline = true;
}

// fix up week and year
if( $week < 1 ) {
  $week = 53;
  $year--;
}
else if( $week > 53 ) {
  $week = 1;
  $year++;
}

// build query
$fmt = '%04d-W%02d-%d';
if( $first_day_of_week == 1 ) {
  $sday = 1;
  $eday = 7;
}
else {
  $sday = 0;
  $eday = 6;
}
$start_ut = strtotime(sprintf($fmt,$year,$week,$sday));
$end_ut = strtotime(sprintf($fmt,$year,$week,$eday));

$rs = $db->SelectLimit(cgcalendar_utils::get_query($start_ut,$end_ut,$category,$reverse),$limit);

// get the list of event id's.
$customfields = null;
$event_ids = array();
while( $rs && !$rs->EOF() ) {
  $event_ids[] = $rs->fields['event_id'];
  $rs->MoveNext();
}

$loader = new cgc_event_loader($id,$event_ids,$detailpage,$reverse);
if( isset($params['eventtemplate']) ) $loader->set_event_template($params['eventtemplate']);
$events = $loader->get_events();

// build navigation.
$navigation = array();
$parms = $params;
unset($parms['returnid'],$parms['page'],$parms['action']);
$parms['display'] = 'weeklist';
$parms['year'] = $year;
$parms['week'] = ($reverse)?$week-1:$week+1;
$navigation['next'] = $this->CreateURL($id,'default',$returnid,$parms,$inline); // deprecated
$navigation['ni_next'] = $this->CreateURL($id, 'default', $returnid, $parms, false );
$navigation['in_next'] = $this->CreateURL($id, 'default', $returnid, $parms, true );
$parms['week'] = ($reverse)?$week+1:$week-1;
$navigation['prev'] = $this->CreateURL($id,'default',$returnid,$parms,$inline); // deprecated
$navigation['ni_prev'] = $this->CreateURL($id, 'default', $returnid, $parms, false );
$navigation['in_prev'] = $this->CreateURL($id, 'default', $returnid, $parms, true );
$parms['week'] = date('W');
$parms['year'] = date('Y');
$navigation['current'] = $this->CreateURL($id,'default',$returnid,$parms,$inline); // deprecated
$navigation['ni_current'] = $this->CreateURL($id, 'default', $returnid, $parms, false );
$navigation['in_current'] = $this->CreateURL($id, 'default', $returnid, $parms, true );

$day_names = $this->GetDayNames();
$day_short_names = $this->GetDayShortNames();

// assign to Smarty
$thetemplate = $this->GetPreference(CGCALENDAR_PREF_DFLTLIST_TEMPLATE);
$thetemplate = \cge_param::get_string($params,'listtemplate',$thetemplate);
$tpl = $this->CreateSmartyTemplate($thetemplate,'list_');
$tpl->assign('start_date_ut',$start_ut);
$tpl->assign('end_date_ut',$end_ut);
$tpl->assign('day_names', $day_names);
$tpl->assign('day_short_names', $day_short_names);
$tpl->assign('events', $events);
$tpl->assign('year', $year);
$tpl->assign('week',$week);
$tpl->assign('lang', $this->GetLabels());
$tpl->assign('navigation', $navigation);
$tpl->assign('file_location',cgcalendar_utils::get_upload_location());
$tpl->display();

?>