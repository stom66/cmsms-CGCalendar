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
$this->SetCurrentTab('defaultadmin');
$message="";
$search = cms_utils::get_search_module();

function handle_null_date_ts($val)
{
    if( $val ) {
        $db = CmsApp::get_instance()->GetDb();
        $str = $db->DbTimeStamp($val);
        return trim($str,"'");
    }
}

function handle_null_date($str)
{
    if( !is_null($str) ) return trim($str,"'");
    return $str;
}

$events_to_categories_table_name = $this->events_to_categories_table_name;
if( isset($params['cancel']) ) {
    $this->SetMessage( $message );
    $this->Redirect($id,'defaultadmin',$returnid);
}

//
// Gather parameters
//
$event_id = (int) cge_utils::get_param($params,'event_id',-1);
$recurring_events = null;
$user_id = get_userid(FALSE) * -1 - 100;
$categories = get_parameter_value($params, 'event_categories',-1);
$event = $this->GetEvent($event_id);
if( $event_id > 0 ) $this->SendEvent('EventEditedPre', ['event_id'=>$event_id,'event'=>$event] );
\cgc_event_utils::get_event_from_params($event,$params,true);
$event_parent_id = $event_id;

// permissions check
if( $event_id < 1 ) {
    // adding
    if( !$this->CheckPermission('Modify Calendar') && !$this->CheckPermission('Add Calendar Events') ) {
        $this->SetError($this->Lang('error_permission'));
        $this->RedirectToAdminTab();
    }
}
else {
    // editing
    if( !$this->CheckPermission('Modify Calendar') &&
        !($this->CheckPermission('Edit My Calendar Events') && $event['event_created_by'] == $user_id) ) {
        $this->SetError($this->Lang('error_permission'));
        $this->RedirectToAdminTab();
    }
}

// Carry through any old values
// just incase a new file was not uploaded
foreach( $params as $k => $v ) {
    if( preg_match('/^upload_field_oldvalue_/', $k) ) {
        $thefield = substr($k,strlen('upload_field_oldvalue_'));
        $params['field_'.$thefield] = $v;
    }
}
// handle deletion
foreach( $params as $k => $v )
{
    if( preg_match('/^remove_field_/', $k) && $v == 1 )
    {
        $thefield = substr($k,strlen('remove_field_'));
        if( isset($params['upload_field_oldvalue_'.$thefield]) )
        {
            $v = $params['upload_field_oldvalue_'.$thefield];
            $config = cmsms()->GetConfig();
            $destDir = $this->GetPreference('uploaddirectory',$config['uploads_path']);
            $dn = cms_join_path($destDir,$v);

            @unlink($dn);
            unset($params['field_'.$thefield]);
        }
    };
}

//
// Error Handling
//
if( $event['event_date_end_ut'] != NULL &&
    $event['event_date_end_ut'] < $event['event_date_start_ut'] ) {
    $this->SetError( $this->Lang('error_invalid_dates') );
    $params['event_recur_weekdays'] = implode(',',$params['event_recur_weekdays']);
    $params['event_recur_monthdays'] = implode(',',$params['event_recur_monthdays']);
    if( isset($params['event_categories']) && $params['event_categories]'] ) $params['event_categories'] = serialize($params['event_categories']);
    $params['do_get_from_params'] = 1;
    $this->Redirect($id,'admin_add_event',$returnid,$params);
}
if($event['event_recur_period'] != 'none' &&
   $event['event_date_recur_end_ut'] != NULL &&
   ($event['event_date_recur_end_ut'] < $event['event_date_start_ut'] ||
    ($event['event_date_end_ut'] != NULL &&
     $event['event_date_recur_end_ut'] < $event['event_date_end_ut'])) ) {
    $this->SetError( $this->Lang('error_invalid_dates') );
    $params['event_recur_weekdays'] = implode(',',$event['event_recur_weekdays']);
    $params['event_recur_monthdays'] = implode(',',$event['event_recur_monthdays']);
    if( isset($params['event_categories']) && $params['event_categories'] ) $params['event_categories'] = serialize($params['event_categories']);
    $params['do_get_from_params'] = 1;
    $this->Redirect($id,'admin_add_event',$returnid,$params);
}
if ($event['event_recur_period'] == 'monthly' &&
    !empty($event['event_recur_monthdays']) && count($event['event_recur_monthdays']) > 1 && $event['event_recur_monthdays'][0] == 'specified' ) {
    $this->SetError( $this->Lang('error_invalid_recur_monthly_freq') );
    $params['event_recur_weekdays'] = implode(',',$event['event_recur_weekdays']);
    $params['event_recur_monthdays'] = implode(',',$event['event_recur_monthdays']);
    if( isset($params['event_categories']) && $params['event_categories'] ) $params['event_categories'] = serialize($params['event_categories']);
    $params['do_get_from_params'] = 1;
    $this->Redirect($id,'admin_add_event',$returnid,$params);
}
if( empty($event['event_title']) ) {
    $this->SetError( $this->Lang('error_noeventname') );
    $params['event_recur_weekdays'] = implode(',',$event['event_recur_weekdays']);
    $params['event_recur_monthdays'] = implode(',',$event['event_recur_monthdays']);
    if( isset($params['event_categories']) && $params['event_categories'] ) $params['event_categories'] = serialize($params['event_categories']);
    $params['do_get_from_params'] = 1;
    $this->Redirect($id,'admin_add_event',$returnid,$params);
}
if( ($categories == -1) && $this->GetPreference('force_category') == 1) {
    $this->SetError( $this->Lang('error_nocategory') );
    $params['event_recur_weekdays'] = implode(',',$event['event_recur_weekdays']);
    $params['event_recur_monthdays'] = implode(',',$event['event_recur_monthdays']);
    if( isset($params['event_categories']) && $params['event_categories'] ) $params['event_categories'] = serialize($params['event_categories']);
    $params['do_get_from_params'] = 1;
    $this->Redirect($id,'admin_add_event',$returnid,$params);
}
if( $event['event_recur_period'] != 'none' ) {
    // calculate recurring events
    $recurring_events = \cgc_event_utils::calculate_recurring_events($event['event_date_start_ut'],
                                                                     $event['event_date_end_ut'],
                                                                     $event['event_recur_period'],
                                                                     $event['event_date_recur_end_ut'],
                                                                     $event['event_recur_interval'],
                                                                     $event['event_recur_count'],
                                                                     $event['event_recur_weekdays'],
                                                                     $event['event_recur_monthdays']);
    for( $i = 0, $n = count($recurring_events); $i < $n; $i++ ) {
        $recurring_events[$i]['event_all_day'] = $event['event_all_day'];
    }

    // check recurring events for overlap
    $overlap = \cgc_event_utils::check_overlapping_events($event['event_date_start_ut'],$event['event_date_end_ut'],$recurring_events);
    if( $overlap === true ) {
        $this->SetError( $this->Lang('error_eventoverlap') );
        $params['event_recur_weekdays'] = implode(',',$params['event_recur_weekdays']);
        $params['event_recur_monthdays'] = implode(',',$params['event_recur_monthdays']);
        if( isset($params['event_categories']) && $params['event_categories'] ) $params['event_categories'] = serialize($params['event_categories']);
        $params['do_get_from_params'] = 1;
        $this->Redirect($id,'admin_add_event',$returnid,$params);
    }
}


//
// check for conflicting events.
//
$policy = $this->GetPreference('overlap_policy','all');
if( $policy != 'all' ) {
    $conflict = \cgc_event_utils::check_db_for_conflicts($event,$event['event_id'],$policy, $event['event_allows_overlap']);
    if( $conflict )  {
        // this is the parent event... we always display an error message if
        // this parent event conflicts with another event.
        $this->SetError( $this->Lang('error_event_conflict') );
        $params['event_recur_weekdays'] = implode(',',$params['event_recur_weekdays']);
        $params['event_recur_monthdays'] = implode(',',$params['event_recur_monthdays']);
        if( isset($params['event_categories']) && $params['event_categories'] ) $params['event_categories'] = serialize($params['event_categories']);
        $params['do_get_from_params'] = 1;
        $this->Redirect($id,'admin_add_event',$returnid,$params);
    }

    // now check any child events
    if( $event['event_recur_period'] != 'none' && !empty($recurring_events) ) {
        $new_recurring = array();
        foreach( $recurring_events as $oneevent ) {
            $conflict = \cgc_event_utils::check_db_for_conflicts($oneevent,$event['event_id'],$policy,$event['event_allows_overlap']);
            if( $conflict ) {
                if( $this->GetPreference('overlap_action','error') == 'error' ) {
                    $this->SetError( $this->Lang( 'error_event_conflict') );
                    $params['event_recur_weekdays'] = implode(',',$params['event_recur_weekdays']);
                    $params['event_recur_monthdays'] = implode(',',$params['event_recur_monthdays']);
                    if( isset($params['event_categories']) && $params['event_categories'] ) $params['event_categories'] = serialize($params['event_categories']);
                    $params['do_get_from_params'] = 1;
                    $this->Redirect($id,'admin_add_event',$returnid,$params);
                }
                else {
                    // silently remove this event.
                    continue;
                }
            }
            $new_recurring[] = $oneevent;
        }
        $recurring_events = $new_recurring;
     }
}



//
// Fix up the event so it's suitable for the database
//

//
// Process any file uploads that may have occurred
//
if( isset( $_FILES ) ) {
    $fldname = '';
    $thefield = '';
    foreach( $params as $k => $v ) {
        // see if we're expecting an upload
        if( preg_match('/^upload_field_/', $k) ) {
            // we are it appears
            $thefield = substr($k,strlen('upload_field_'));
            $fldname = $id.'field_'.$thefield;

            if( !isset($_FILES[$fldname]) || empty($_FILES[$fldname]['tmp_name']) ) continue;

            $error = '';
            $filename = $this->HandleUpload($fldname,$error);
            if( $filename === false ) {
                // an error occurred
                $this->SetError( $error );
                $this->Redirect($id,'defaultadmin',$returnid );
                return;
            }

            $params['field_'.$thefield] = $filename;
        }
    }
}

//
// Process the insert or update
//
$do_insert = true;
if($event['event_id'] > -1) {
    $do_insert = false;
    // update
    $sql = "UPDATE " . $this->events_table_name . "
               SET event_title = ?, event_summary = ?, event_details = ?, event_date_start = ?, event_date_end = ?, event_parent_id = ?,
                   event_recur_period = ?, event_date_recur_end = ?, event_recur_nevents = ?,
                   event_recur_interval = ?,event_recur_weekdays = ?,event_recur_monthdays = ?,event_modified_date = NOW(),
                   event_allows_overlap = ?, event_all_day = ?, event_status = ?
	     WHERE event_id = ?";
    $dbr =  $db->Execute($sql,
                         array($event['event_title'],
                               $event['event_summary'],
                               $event['event_details'],
                               trim($event['event_date_start'],"'"),
                               handle_null_date($event['event_date_end']),
                               $event['event_parent_id'],
                               $event['event_recur_period'],
                               handle_null_date($event['event_date_recur_end']),
                               $event['event_recur_count'],
                               $event['event_recur_interval'],
                               implode(',',$event['event_recur_weekdays']),
                               implode(',',$event['event_recur_monthdays']),
                               $event['event_allows_overlap'],
                               $event['event_all_day'],
                               $event['event_status'],
                               $event['event_id']));
    if( !$dbr ) {
        echo "FATAL ERROR:<br/>";
        echo "QUERY = ".$db->sql."<br/>";
        echo "ERROR = ".$db->ErrorMsg()."<br/><br/>";
        die();
    }

    if( isset($params['update_children']) ) {
        //here we also delete any and all children, and all sub-tables that may have been filled with child data.
        $result=$db->Execute("SELECT event_id FROM " . $this->events_table_name . " WHERE event_parent_id = ?",array($event['event_id']));
        $db->Execute("DELETE FROM " . $this->events_table_name . " WHERE event_parent_id = ?",array($event['event_id']));

        while ($row = $result->FetchRow()) {
            //We clear these as they will be added later on

            if( $search ) {
                $search->DeleteWords($this->GetName(), $row['event_id']);
            }

            //delete from categories
            $db->Execute("DELETE FROM " . $this->events_to_categories_table_name . " WHERE event_id = ? LIMIT 1",array($row['event_id']));
            //delete from custom fields
            $db->Execute("DELETE FROM " . $this->event_field_values_table_name . " WHERE event_id = ? LIMIT 1",array($row['event_id']));
        }
    }

}
//otherwise we are just adding a new event
else {
    $event['event_id'] = $db->GenID($this->events_table_name . "_seq");

    $sql = "INSERT INTO " . $this->events_table_name . "
           (event_id, event_title, event_summary, event_details,
  	        event_date_start, event_date_end, event_parent_id,
   	        event_recur_period, event_date_recur_end, event_created_by,
            event_recur_nevents, event_recur_interval, event_recur_weekdays,
            event_recur_monthdays, event_allows_overlap,event_all_day,event_status,
	        event_create_date, event_modified_date)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())";

    $dbr=$db->Execute($sql,
                      array($event['event_id'],
                            $event['event_title'],
                            $event['event_summary'],
                            $event['event_details'],
                            trim($event['event_date_start'],"'"),
                            handle_null_date($event['event_date_end']),
                            $event['event_parent_id'],
                            $event['event_recur_period'],
                            handle_null_date($event['event_date_recur_end']),
                            $user_id,
                            $event['event_recur_count'],
                            $event['event_recur_interval'],
                            implode(',',$event['event_recur_weekdays']),
                            implode(',',$event['event_recur_monthdays']),
                            $event['event_allows_overlap'],
                            $event['event_all_day'],
                            $event['event_status']));
    if( !$dbr ) {
        echo "FATAL ERROR:<br/>";
        echo "QUERY = ".$db->sql."<br/>";
        echo "ERROR = ".$db->ErrorMsg()."<br/><br/>";
        die();
    }
}


// delete current events_to_categories records for this event
$sql = "DELETE FROM " . $this->events_to_categories_table_name . " WHERE event_id = ?";
$result=$db->Execute($sql,array($event['event_id']));

// update events_to_categories
if( is_array($categories) && !empty($categories) ) {
    foreach($categories as $category_id) {
        $category_id = intval($category_id);
        if($category_id > 0) {
            $sql = "INSERT INTO " . $this->events_to_categories_table_name . " (category_id, event_id) VALUES (?,?)";
            $db->Execute($sql,array($category_id,$event['event_id']));
            //echo $sql;
        }
    }
}

$fieldtext = $searchtext = null;
// Clear out all the custom field values for this event, and add the new ones from the form
{
    $fields = $this->GetFields();
    // Delete the custom field values first
    {
        $sql = "DELETE FROM " . $this->event_field_values_table_name . " WHERE event_id = ?";
        $result=$db->Execute($sql,array($event['event_id']));
    }

    $customfieldsvalues = array();
    foreach ($fields as $field) {
        $fieldname = $field['field_name'];
        // Replace spaces with underbars for the form parameter (a bit of an ikky hack I know, but I can well imagine folk defining custom fields with spaces in the name) Perhaps it would have been better if custom fields also had a numeric key - hohum live and learn!
        $safefieldname =  str_replace( ".", "_", $fieldname );
        $safefieldname =  'field_'.str_replace ( " ", "_", $safefieldname );
        $field_value = get_parameter_value($params, $safefieldname, '');

        $sql = "INSERT INTO " . $this->event_field_values_table_name . " (field_name, event_id, field_value) VALUES (?,?,?)";

        if ($field_value) {
            $result=$db->Execute($sql,array($fieldname,$event['event_id'],$field_value));
        }

        $customfieldsvalues[] = array($fieldname,$event['event_id'],$field_value);
        if( $field['field_searchable'] ) $fieldtext .= $field_value.' ';
    }
}

//////////////////////////////////////////////////////////////
// Update Search
/////////////////////////////////////////////////////////////
if( $search != FALSE ) {
    if( $event['event_status'] == 'P' ) {
        $searchtext = "{$event['event_title']} {$event['event_summary']} {$event['event_details']}";
        $searchtext .= ' '.$fieldtext;
        $search->AddWords($this->GetName(), $event['event_id'], 'event', $searchtext, NULL);
    }
    else {
        $search->DeleteWords($this->GetName(), $event['event_id']);
    }
}


//////////////////////////////////////////////////////////////
// Add recurring events
/////////////////////////////////////////////////////////////
if( ($event['event_recur_period'] != 'none') && !empty($recurring_events) &&
    (isset($params['update_children']) || ($do_insert == true) ) ) {
    $sql = "INSERT INTO " . $this->events_table_name . "
             (event_id
              ,event_title
              ,event_summary
              ,event_details
	          ,event_date_start
              ,event_date_end
              ,event_parent_id
              ,event_created_by
              ,event_allows_overlap
              ,event_all_day
              ,event_status
    	      ,event_create_date, event_modified_date)
              VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())";
    foreach( $recurring_events as $oneevent ) {
        $child_event_id = $db->GenID($this->events_table_name . "_seq");
        $dbr = $db->Execute($sql,
                            array($child_event_id,
                                  $event['event_title'],
                                  $event['event_summary'],
                                  $event['event_details'],
                                  trim($db->DbTimeStamp($oneevent['start']),"'"),
                                  handle_null_date_ts($oneevent['end']),
                                  $event['event_id'],
                                  $user_id,
                                  $event['event_allows_overlap'],
                                  $event['event_all_day'],
                                  $event['event_status']));

        if( $dbr ) {
            if( $search && $event['event_status'] == 'P' && $searchtext ) {
                $search->AddWords( $this->GetName(), $child_event_id, 'event', $searchtext, NULL );
            }
            if( !empty($categories) ) {
                foreach($categories as $category_id) {
                    $category_id = intval($category_id);
                    if($category_id > 0) {
                        $sql2 = "INSERT INTO " . $this->events_to_categories_table_name . " (category_id, event_id) VALUES (?,?)";
                        $db->Execute($sql2,array($category_id,$child_event_id));
                    }
                }
            }
        }
    }
}

$eventname = 'EventAdded';
$parms = array();
$parms['event'] = $event; // the whole array.
$parms['event_title'] = $event['event_title'];
$parms['event_summary'] = $event['event_summary'];
$parms['event_details'] = $event['event_details'];
$parms['event_date_start'] = $event['event_date_start'];
$parms['event_date_end'] = $event['event_date_end'];
$parms['event_created_by'] = $user_id;
$parms['event_id'] = $event['event_id'];
if( !$do_insert ) $eventname = 'EventEdited';
$this->SendEvent( $eventname, $parms );

$message=$this->Lang("eventupdated");

//
// done
//
$this->SetMessage( $message );
$this->Redirect($id,'defaultadmin',$returnid);

// EOF
