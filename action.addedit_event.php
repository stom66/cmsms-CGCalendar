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

$handle_null_date_ts = function($val)
{
    if( $val ) {
        $db = CmsApp::get_instance()->GetDb();
        $str = $db->DbTimeStamp($val);
        return trim($str,"'");
    }
};

$handle_null_date = function($str)
{
  if( !is_null($str) ) return trim($str,"'");
  return $str;
};

//
// safety checks
//
$feu = $this->GetModuleInstance('FrontEndUsers');
if( !$feu ) {
  echo '<h3><font color="red">'.$this->Lang('error_nofeu')."</font></h3>\n";
  return;
}
else if( !$feu->LoggedIn() ) {
  echo '<h3><font color="red">'.$this->Lang('error_feu_loggedin')."</font></h3>\n";
  return;
}

//
// initialization
//
$feu_uid = $feu->LoggedInId();
$thetemplate = \cge_param::get_string($params,'editeventtemplate');
if( !$thetemplate ) {
    $tpl = CmsLayoutTemplate::load_dflt_by_type('CGCalendar::editevent');
    if( !is_object($tpl) ) {
        audit('',$this->GetName(),'No default editevent template found');
        return;
    }
    $thetemplate = $tpl->get_name();
    unset($tpl);
}
$event_id = \cge_param::get_int($params, 'event_id', -1);
$event = $this->GetEvent($event_id,$feu_uid);
$fields = $this->GetFields();
$categories = $this->GetCategories();
$policy = $this->GetPreference('overlap_policy','all');
$force_category = $this->GetPreference('force_category',0);
$destpage = \cge_param::get_int($params,'return_id',$returnid);
if( !\cge_param::exists($params,'return_id') ) {
    $tmp = $this->GetPreference('frontend_redirectpage','');
    if( $tmp ) {
        $tmp = $this->ProcessTemplateFromData($tmp);
        if( $tmp ) {
            $tmp = $this->resolve_alias_or_id($tmp);
            if( $tmp ) $destpage = $tmp;
        }
    }
}
$status = '';
$message = '';

// Add input controls for each of the custom fields that are defined.
if( $fields ) {
    $tmp = array();
    foreach($fields as $field) {
        $field_values = $event['fields'];
        $field_name = $field['field_name'];
        $field_type = $field['field_type'];
        $field_value = "";
        if( isset($field_values[$field_name]) ) $field_value = $field_values[$field_name];

        // Replace spaces with underbars for the form parameter (a bit of an ikky hack I know,
        // but I can well imagine folk defining custom fields with spaces in the name)
        // Perhaps it would have been better if custom fields also had a numeric key - hohum live and learn!

        $obj = new StdClass();
        $obj->name = $field['field_name'];
        $obj->safename =  str_replace ( " ", "_", $field_name );
        $obj->searchable = $field['field_searchable'];
        $obj->value = $field_value;
        $obj->type = $field_type;
        if( isset($field['field_extra']) ) $obj->extra = $field['field_extra'];
        switch( $field_type ) {
        case 0: // the normal text field
            $obj->field = $this->CreateInputText($id, 'cal_field_'.$obj->safename, $field_value, 50, 255);
            break;

        case 1:
            // an upload field type
            // display a label with the current value, and then an upload field
            $obj->value = $field_value;
            $obj->field = $this->CreateFileUploadInput($id,'cal_field_'.$obj->safename,'',50).
                $this->CreateInputHidden($id,'cal_upload_field_'.$obj->safename,$field_name).
                $this->CreateInputHidden($id,'cal_upload_field_oldvalue_'.$obj->safename,$field_value);
            break;

        case 2:
            // text area
            $obj->field = $this->CreateTextArea(false,$id,$field_value,'cal_field_'.$obj->safename);
            break;

        case 3:
            // company directory entry.
            $cdmod = cms_utils::get_module('CompanyDirectory');
            if( !$cdmod ) {
                // have field type, but no companydirectory module.
                continue 2;
            }
            // get a list of the companies that we want to display
            {
                $query = 'SELECT id,company_name FROM '.cms_db_prefix().'module_compdir_companies ORDER BY company_name';
                $dbr = $db->GetArray($query);
                if( $dbr ) {
                    $tmp2 = array($this->Lang('none')=>-1);
                    for( $i = 0; $i < count($dbr); $i++ ) {
                        $tmp2[$dbr[$i]['company_name']] = $dbr[$i]['id'];
                    }
                    $obj->field = $this->CreateInputDropdown($id,'cal_field_'.$obj->safename,$tmp2,-1,$field_value);
                }
            }
            break;

        case 4:
            // file attachment
            $obj->field = null;
            $dir = $this->GetPreference('uploaddirectory');
            if( !$dir ) $dir = $config['uploads_path'];
            $pattern = explode(',',$this->GetPreference('uploadfiletypes','jpg,jpeg,gif,png'));
            $excludes = array('_*','.??*');
            foreach( $pattern as &$one ) {
                $one = '*.'.$one;
            }
            $files = cge_dir::recursive_glob($dir,$pattern,'FILES',$excludes);
            if( !is_array($files) || count($files) == 0 ) continue 2;

            $out = array($this->Lang('none')=>-1);
            foreach( $files as $one ) {
                $one = substr($one,strlen($dir));
                if( startswith($one,'/') ) $one = substr($one,1);
                $out[$one] = $one;
            }
            $obj->field = $this->CreateInputDropdown($id,'cal_field_'.$obj->safename,$out,-1,$field_value);
            break;

        case 5:
            // checkbox
            $obj->field = $this->CreateInputHidden($id,'cal_field_'.$obj->safename,0);
            $obj->field .= $this->CreateInputCheckbox($id,'cal_field_'.$obj->safename,1,$field_value);
            break;

        case 6:
            // dropdown
            $obj->field = null;
            if( !empty($field['field_extra']['ddopts']) ) {
                $opts = \cge_array::explode_with_key($field['field_extra']['ddopts'],'=',"\n");
                if( is_array($opts) && count($opts)) $obj->field = $this->CreateInputDropdown($id,'cal_field_'.$obj->safename,array_flip($opts),-1,$field_value);
            }
            break;
        }
        $tmp[$obj->safename] = $obj;
    }
    if( count($tmp) ) $fields = $tmp;
}

if( $categories ) {
    $dflt_cat = $this->GetPreference('default_category','');
    $tmp = array();
    foreach( $categories as $category ) {
        $obj = new StdClass();
        $obj->id = $category['category_id'];
        $obj->name = $category['category_name'];
        $obj->checked = 0;
        if( $obj->id == $dflt_cat && $event_id == -1 ) {
            $obj->checked = 1;
        }
        else if( in_array($obj->id,$event['categories']) ) {
            $obj->checked = 1;
        }
        $obj->field =
            $this->CreateInputHidden($id,'cal_event_categories['.$obj->id.']',0).
            $this->CreateInputCheckbox($id,'cal_event_categories['.$obj->id.']',1,$obj->checked,"id='category".$obj->id."'");
        $tmp[$obj->id] = $obj;
    }
    $categories = $tmp;
}

//
// Process the form
//
if( isset($params['cal_cancel']) ) {
    $this->RedirectContent($destpage);
}
else if( isset($params['cal_submit']) ) {

    if( !class_exists('CGCalEventException') ) {
        class CGCalEventException extends \Exception {}
    }

    $tmp = array();
    foreach( $params as $key => $value ) {
        if( startswith($key,'cal_') ) $key = substr($key,strlen('cal_'));
        switch( $key ) {
        case 'event_title':
        case 'event_summary':
            $value = filter_var(trim($value),FILTER_SANITIZE_STRING);
            break;

        case 'event_recur_date':
            $key = 'event_recur_date_ut';
            $value = strtotime($value);
            break;

        case 'event_details':
            $value = html_entity_decode(trim($value));
            $value = cge_utils::clean_input_html($value);
            break;
        }
        $tmp[$key] = $value;
    }
    $params = $tmp;
    unset($tmp);

    try {
        if( $event_id > 0 ) $this->SendEvent('EventEditedPre', ['event_id'=>$event_id,'event'=>$event] );
        // todo: send event before modify if we have an event id.
        \cgc_event_utils::get_event_from_params($event,$params,true);
        if( isset($event['categories']) ) {
            foreach( $event['categories'] as $cat_id => $flag ) {
                if( isset($categories[$cat_id]) ) {
                    $categories[$cat_id]->checked = (int) $flag;
                }
            }
        }

        // check for data quality
        if( $event['event_date_end_ut'] != NULL && $event['event_date_end_ut'] < $event['event_date_start_ut'] ) {
            throw new \CGCalEventException('error_invalid_dates');
        }
        if( empty($event['event_title']) ) throw new \CGCalEventException('error_noeventname');
        if( !isset($params['event_categories']) || empty($params['event_categories']) )  throw new \CGCalEventException('error_nocategory');

        // todo: check recurring event details
        // check event for conflicts.
        if( $policy != 'all' ) {
            if( cgc_event_utils::is_event_conflicted($event,$policy) ) throw new \CGCalEventException('error_event_conflict');
        }

        $recurring_events = null;
        if( $event['event_parent_id'] < 1 && $event['event_recur_period'] != 'none' ) {
            $recurring_events = \cgc_event_utils::calculate_recurring_events($event['event_date_start_ut'],
                                                                             $event['event_date_end_ut'],
                                                                             $event['event_recur_period'],
                                                                             $event['event_date_recur_end_ut'],
                                                                             $event['event_recur_interval'],
                                                                             $event['event_recur_count'],
                                                                             $event['event_recur_weekdays'],
                                                                             $event['event_recur_monthdays']);
            if( !count($recurring_events) ) throw new \CGCalEventException('error_recurring_data');

            for( $i = 0, $n = count($recurring_events); $i < $n; $i++ ) {
                $recurring_events[$i]['event_all_day'] = $event['event_all_day'];
            }

            $overlap = \cgc_event_utils::check_overlapping_events($event['event_date_start_ut'],$event['event_date_end_ut'],$recurring_events);
            if( $overlap === true ) throw new \CGCalEventException('error_eventoverlap');

            if( $policy != 'all' ) {
                // check child events for conflicts.
                $new_recurring = array();
                foreach( $recurring_events as $oneevent ) {
                    $conflict = \cgc_event_utils::check_db_for_conflicts($oneevent,$event['event_id'],$policy,$event['event_allows_overlap']);
                    if( $conflict ) {
                        if( $this->GetPreference('overlap_action','error') == 'error' ) {
                            throw new \CGCalEventException('error_event_conflict');
                        }
                        else {
                            // silently remove this event.
                            continue;
                        }
                    }
                    $new_recurring[] = $oneevent;
                }
                $recurring_events = $new_recurring;
                if( !count($recurring_events) ) throw new \CGCalEventException('error_recurring_data');
            }
        }

        // handle any file fields...
        if( isset($_FILES) ) {
            foreach( $params as $key => $value ) {
                if( !startswith($key,'upload_field_') ) continue;

                $thefield = substr($key,strlen('upload_field_'));
                $fldname = $id.'cal_field_'.$thefield;
                if( !isset($_FILES[$fldname]) || empty($_FILES[$fldname]['tmp_name']) ) continue;

                $error = '';
                $filename = $this->HandleUpload($fldname,$error);
                if( $filename === false ) {
                    $status = 'error_upload';
                    $message = $error;
                    break;
                }
                $params['field_'.$thefield] = $filename;
            }
        }

        // merge field values back into the fields array
        foreach( $params as $key => $value ) {
            if( !startswith($key,'field_') ) continue;
            $fieldname = substr($key,strlen('field_'));
            switch( $fields[$fieldname]->type ) {
            case 0:
                $value = filter_var(trim($value),FILTER_SANITIZE_STRING);
                break;
            case 2:
                $value = html_entity_decode(trim($value));
                $value = cge_utils::clean_input_html($value);
                break;
            }
            $fields[$fieldname]->value = $value;
        }

        // merge category values back into the categories array
        if( isset($params['event_categories']) ) {
            foreach( $categories as &$one ) {
                $one->checked = 0;
                if( isset($params['event_categories'][$one->id]) ) $one->checked = $params['event_categories'][$one->id];
            }
        }

        // ready to insert or update
        $child_ids = null;
        $fieldtext = '';
        $do_insert = false;
        $dbr = null;

        if( $event['event_id'] > 0 ) {
            //
            //  it's an update to an existing event
            //

            // get the child id's of this event if any.
            $sql = 'SELECT event_id FROM '.$this->events_table_name.' WHERE event_parent_id = ?';
            $child_ids = $db->GetCol($sql,array($event['event_id']));

            // delete all of the child events.
            $tmp = array((int) $event['event_id']);
            if( count($child_ids) ) $tmp = array_merge($tmp,$child_ids);
            $children_str = '('.implode(',',$tmp).')';
            $sql = 'DELETE FROM '.$this->event_field_values_table_name.' WHERE event_id IN '.$children_str;
            $db->Execute($sql);
            $sql = 'DELETE FROM '.$this->events_to_categories_table_name.' WHERE event_id IN '.$children_str;
            $db->Execute($sql);
            $sql = 'DELETE FROM '.$this->events_table_name.' WHERE event_parent_id = ?';
            $db->Execute($sql,array($event['event_id']));

            // do the update
            $sql = "UPDATE " . $this->events_table_name . "
                  SET event_title = ?, event_summary = ?, event_details = ?, event_date_start = ?, event_date_end = ?, event_recur_period = ?, event_date_recur_end = ?,
                  event_recur_nevents = ?, event_recur_interval = ?, event_recur_weekdays = ?, event_recur_monthdays = ?, event_modified_date = NOW(),
                  event_allows_overlap = ?, event_all_day = ?
	              WHERE event_id = ?";
            $dbr =  $db->Execute($sql,
                                 array($event['event_title'],
                                       $event['event_summary'],
                                       $event['event_details'],
                                       trim($event['event_date_start'],"'"),
                                       $handle_null_date($event['event_date_end']),
                                       $event['event_recur_period'],
                                       $handle_null_date($event['event_date_recur_end']),
                                       $event['event_recur_count'],
                                       $event['event_recur_interval'],
                                       implode(',',$event['event_recur_weekdays']),
                                       implode(',',$event['event_recur_monthdays']),
                                       $event['event_allows_overlap'],
                                       $event['event_all_day'],
                                       $event['event_id']));
        }
        else {
            //
            // we are inserting a new record
            //
            $do_insert = true;

            $event['event_id'] = $db->GenID($this->events_table_name . "_seq");

            $sql = "INSERT INTO " . $this->events_table_name . "
                  (event_id,event_title,event_summary,event_details
	              ,event_date_start,event_date_end,event_parent_id
	              ,event_recur_period, event_date_recur_end, event_created_by
                  ,event_recur_nevents, event_recur_interval, event_recur_weekdays
                  ,event_recur_monthdays, event_allows_overlap, event_all_day, event_status
	              ,event_create_date, event_modified_date)
                  VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())";
            $dbr=$db->Execute($sql,
                              array($event['event_id'],
                                    $event['event_title'],
                                    $event['event_summary'],
                                    $event['event_details'],
                                    trim($event['event_date_start'],"'"),
                                    $handle_null_date($event['event_date_end']),
                                    $event['event_parent_id'],
                                    $event['event_recur_period'],
                                    $handle_null_date($event['event_date_recur_end']),
                                    $feu_uid,
                                    $event['event_recur_count'],
                                    $event['event_recur_interval'],
                                    implode(',',$event['event_recur_weekdays']),
                                    implode(',',$event['event_recur_monthdays']),
                                    $event['event_allows_overlap'],
                                    $event['event_all_day'],
				    $event['event_status']));
        }

        if( !$dbr ) throw new \CGCalEventException('error_dberror');

        // insert field definitions
        $query = 'INSERT INTO '.$this->event_field_values_table_name.' (field_name, event_id, field_value) VALUES (?,?,?)';

        foreach( $fields as $field ) {
            $fieldname = $field->name;
            if( !empty($field->value) ) {
                if( $field->searchable ) $fieldtext .= $field->value.' ';
                $db->Execute($query,array($field->name,$event['event_id'],$field->value));
            }
        }

        // insert category values
        $query = 'INSERT INTO '.$this->events_to_categories_table_name.' (category_id,event_id) VALUES (?,?)';
        foreach( $categories as &$one ) {
            if( !$one->checked ) continue;
            if( $one->id <= 0 ) continue;

            $db->Execute($query,array((int) $one->id,(int) $event['event_id']));
        }

        if( count($recurring_events) ) {
            // add the recurring events.
            $sql = "INSERT INTO " . $this->events_table_name . "(event_id,event_title,event_summary,event_details,event_date_start,event_date_end,event_parent_id
                    ,event_created_by,event_allows_overlap,event_all_day,event_create_date, event_modified_date)
                    VALUES (?,?,?,?,?,?,?,?,?,?,NOW(),NOW())";
            foreach( $recurring_events as $oneevent ) {
                $child_event_id = $db->GenID($this->events_table_name . "_seq");
                $db->Execute($sql,
                             array($child_event_id,
                                   $event['event_title'],
                                   $event['event_summary'],
                                   $event['event_details'],
                                   trim($db->DbTimeStamp($oneevent['start']),"'"),
                                   $handle_null_date_ts($oneevent['end']),
                                   $event['event_id'],
                                   $feu_uid,
                                   $event['event_allows_overlap'],
                                   $event['event_all_day']));

                if(is_array($categories) && (count($categories) > 0)) {
                    debug_to_log($categories,'categories');
                    $sql2 = "INSERT INTO " . $this->events_to_categories_table_name . " (category_id, event_id) VALUES (?,?)";
                    foreach($categories as &$one) {
                        if( !$one->checked ) continue;
                        $category_id = (int) $one->id;
                        if($category_id > 0) {
                            $db->Execute($sql2,array($category_id,(int) $child_event_id));
                            debug_to_log($db->sql);
                        }
                    }
                }

                // note, we do not do field values for recurring events here.
                // the various views can read field values from parent events.
            }
        }

        // send a single event
        $eventname = 'EventAdded';
        $parms = array();
        $parms['event_title'] = $event['event_title'];
        $parms['event_summary'] = $event['event_summary'];
        $parms['event_details'] = $event['event_details'];
        $parms['event_date_start'] = $event['event_date_start'];
        $parms['event_date_end'] = $event['event_date_end'];
        $parms['event_id'] = $event['event_id'];
        if( !$do_insert ) $eventname = 'EventEdited';
        $this->SendEvent( $eventname, $parms );

        // update the search index
        $search = cms_utils::get_search_module();
        if( $search != FALSE ) {
            $text = "{$event['event_title']} {$event['event_summary']} {$event['event_details']}";
            $text .= ' '.$fieldtext;
            $search->AddWords($this->GetName(), $event['event_id'], 'event', $text, NULL);
        }

        // and done
        $this->RedirectContent($destpage);
    }
    catch( \CGCalEventException $e ) {
        $status = $e->GetMessage();
        $message = $this->Lang($status);
    }
    catch( \Exception $e ) {
        $status = 'error';
        $message = $e->GetMessage();
    }
} // if submit

//
// build the form
//
$tpl = $smarty->CreateTemplate($this->GetTemplateResource($thetemplate),null,null,$smarty);
$tpl->assign('event',$event);
if( !empty($status) ) $tpl->assign('status',$status);
if( !empty($message) ) $tpl->assign('message',$message);
if( $fields ) $tpl->assign('fields',$fields);
if( $categories ) $tpl->assign('categories',$categories);

// stuff for recurring events
$recur_days = array();
$recur_days['specified']        = $this->Lang('specified_date');
$recur_days['1,0']  = $this->Lang('first_sunday');
$recur_days['1,1']  = $this->Lang('first_monday');
$recur_days['1,2']  = $this->Lang('first_tuesday');
$recur_days['1,3']  = $this->Lang('first_wednesday');
$recur_days['1,4']  = $this->Lang('first_thursday');
$recur_days['1,5']  = $this->Lang('first_friday');
$recur_days['1,6']  = $this->Lang('first_saturday');
$recur_days['2,0']  = $this->Lang('second_sunday');
$recur_days['2,1']  = $this->Lang('second_monday');
$recur_days['2,2']  = $this->Lang('second_tuesday');
$recur_days['2,3']  = $this->Lang('second_wednesday');
$recur_days['2,4']  = $this->Lang('second_thursday');
$recur_days['2,5']  = $this->Lang('second_friday');
$recur_days['2,6']  = $this->Lang('second_saturday');
$recur_days['3,0']  = $this->Lang('third_sunday');
$recur_days['3,1']  = $this->Lang('third_monday');
$recur_days['3,2']  = $this->Lang('third_tuesday');
$recur_days['3,3']  = $this->Lang('third_wednesday');
$recur_days['3,4']  = $this->Lang('third_thursday');
$recur_days['3,5']  = $this->Lang('third_friday');
$recur_days['3,6']  = $this->Lang('third_saturday');
$recur_days['4,0']  = $this->Lang('fourth_sunday');
$recur_days['4,1']  = $this->Lang('fourth_monday');
$recur_days['4,2']  = $this->Lang('fourth_tuesday');
$recur_days['4,3']  = $this->Lang('fourth_wednesday');
$recur_days['4,4']  = $this->Lang('fourth_thursday');
$recur_days['4,5']  = $this->Lang('fourth_friday');
$recur_days['4,6']  = $this->Lang('fourth_saturday');
$recur_days['-1,0'] = $this->Lang('last_sunday');
$recur_days['-1,1'] = $this->Lang('last_monday');
$recur_days['-1,2'] = $this->Lang('last_tuesday');
$recur_days['-1,3'] = $this->Lang('last_wednesday');
$recur_days['-1,4'] = $this->Lang('last_thursday');
$recur_days['-1,5'] = $this->Lang('last_friday');
$recur_days['-1,6'] = $this->Lang('last_saturday');
$tpl->assign('recur_days',$recur_days);

$weekdays = $this->GetDayNames();
$tpl->assign('input_weekdays', $this->CreateInputSelectList($id,'event_recur_weekdays[]',array_flip($weekdays),$event['event_recur_weekdays'],7));
$tpl->assign('repeat_str',$this->Lang('plural_'.$event['event_recur_period']));
$tpl->assign('weekdays',$weekdays);
$nums = array();
$nums[-1] = $this->Lang('unlimited');
for( $i = 0; $i < 50; $i++ ) {
    $nums[$i+1] = $i+1;
}
$nums[100] = 100;
$nums[250] = 250;
$tpl->assign('maxiters',$nums);
$nums = array();
for( $i = 0; $i < 30; $i++ ) {
    $nums[$i+1] = $i+1;
}
$tpl->assign('nums',$nums);
$tpl->assign('recur_options',
             array('none'=>$this->Lang('no'),
                   'daily'=>$this->Lang('daily'),
                   'weekly'=>$this->Lang('weekly'),
                   'monthly'=>$this->Lang('monthly'),
                   'yearly'=>$this->Lang('yearly')));
$plurals = array('daily'=>$this->Lang('plural_daily'),
                   'weekly'=>$this->Lang('plural_weekly'),
                   'monthly'=>$this->Lang('plural_monthly'),
                   'yearly'=>$this->Lang('plural_yearly')
    );
$tpl->assign('plurals_json',json_encode($plurals));

// other stuff..
$parms = array();
$parms['event_id'] = $event_id;
if( isset($params['editeventtemplate']) ) $parms['editeventtemplate'] = $params['editeventtemplate'];
if( isset($params['return_id']) ) $parms['return_id'] = $params['return_id'];
$tpl->assign('formstart',$this->CGCreateFormStart($id,'addedit_event',$returnid,$parms,false,'post','multipart/form-data'));
$tpl->assign('formend',$this->CreateFormEnd());
$current_year = date('Y');
$start = $current_year - $this->GetPreference('showpastyears',2);
$end = $current_year + $this->GetPreference('showfutureyears',10);
$tpl->assign('start_year',$start);
$tpl->assign('end_year',$end);
$tpl->display();

#
# EOF
#
?>
