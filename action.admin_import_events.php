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

function _fgets($fh,$num = 4096)
{
  if( !$fh ) return;
  $pos1 = ftell($fh);

  $line = fgets($fh,$num);
  if( strpos($line,"\r") === FALSE ) return $line;

  // line is probably a crappy mac line.
  $len1 = strlen($line);
  $pos = strpos($line,"\r");

  $line = substr($line,0,$pos);
  fseek($fh,($len1 - $pos -1 ) * -1,SEEK_CUR);
  return $line;
}

function _getline($fh)
{
  if( !$fh ) return;
  $line = _fgets($fh);
  $pos = strpos($line,'//');
  if( $pos !== FALSE ) {
    $tmp = strpos($line,'://');
    if( $tmp === FALSE || $tmp != $pos - 1 ) $line = substr($line,0,$pos);
  }
  $line = trim($line);
  return $line;
}

function _map_field($fieldmap,$data,$fldname)
{
  for( $i = 0; $i < count($fieldmap); $i++ ) {
    if( strtolower($fieldmap[$i]) == strtolower($fldname) ) return $data[$i];
  }
}

$this->SetCurrentTab('defaultadmin');
if( isset($params['cancel']) ) $this->RedirectToTab($id);
if (!isset($_FILES[$id.'csv_file'])) {
  $this->SetError($this->Lang('error_csvfilenotfound'));
  $this->RedirectToTab($id);
}
else {
  $delimiter = (isset($params['delimiter']))?trim($params['delimiter']):$this->GetPreference('import_delimiter',',');
  $this->SetPreference('import_delimiter',$delimiter);

  $user_id = get_userid();

  // get a category map.
  $sql = 'SELECT category_id, category_name FROM ' . $this->categories_table_name;
  $rs = $db->Execute($sql);
  $cat = array();
  while( $rs && $row = $rs->FetchRow()) {
    $cat[$row['category_name']]=$row['category_id'];
  }

  // get a field map.
  $fields = array();
  {
    $tmp = $this->GetFields();
    if( is_array($tmp) && count($tmp) )	$fields = cge_array::to_hash($tmp,'field_name');
  }

  // open the file
  $handle = fopen($_FILES[$id.'csv_file']['tmp_name'], "r");
  if ($handle === false) {
    $this->SetError('error_cantopenfile');
    $this->RedirectToTab($id);
  }

  // get the first line.
  $linenum = 1;
  $line = _getline($handle);
  if( feof($handle) || $line == '' ) {
    $this->SetError($this->Lang('error_csvinvalidfirstline'));
    $this->RedirectToTab($id);
  }

  // parse the first line into a field map.
  $fieldmap = cge_array::smart_explode($line,$delimiter);
  for( $i = 0; $i < count($fieldmap); $i++ ) {
    $fieldmap[$i] = trim($fieldmap[$i]);
  }

  // check for required fields.
  $required_fields = array('start time','end time','title');
  $optional_fields = array('category','summary','description');
  foreach( $required_fields as $reqd ) {
      $found = false;
      foreach( $fieldmap as $fld ) {
          if( strtolower($fld) == $reqd ) {
              $found = true;
              break;
          }
      }
      if( !$found ) {
          $this->SetError($this->Lang('error_csvfirstlinefldnotfound',$reqd));
          $this->RedirectToTab($id);
      }
  }

  // now check for fields of type 'file' and for unknown fields.
  if( 0 && count($fields) ) {   // commented out.
      foreach( $fieldmap as $one ) {
          if( in_array(strtolower($one),$required_fields) ) continue;
          if( in_array(strtolower($one),$optional_fields) ) continue;

          $fnd = false;
          foreach( $fields as $name => $rec ) {
              if( $one == $name ) {
                  $fnd = true;
                  if( $rec['field_type'] == 1 ) {
                      $this->SetError($this->Lang('error_csvfirstlinefilefld'));
                      $this->RedirectToTab($id);
                  }
                  break;
              }
          }
          if( !$fnd ) {
              $this->SetError($this->Lang('error_csvfirstlineinvalidfield',$one));
              $this->RedirectToTab($id);
          }
      }
  }

  // now begin importing.
  $errors = array();
  $imported = 0;
  while( !feof($handle) ) {
      $event_status = 'P'; // exported events are only published events.
      $start_time = null; $end_time = null; $all_day = false; $start_time_ts = null; $end_time_ts = null;
      $line = _getline($handle);
      $linenum++;
      if( !$line ) continue;
      $data = cge_array::smart_explode($line,$delimiter);

      $event_id = $db->GenID($this->events_table_name . "_seq");

      $tmp = _map_field($fieldmap,$data,'start time');
      if( $tmp == 'NULL' || $tmp == '' ) {
          // no start time is an error.
          $errors[] = $this->Lang('error_csv_insert',$linenum).' 1';
      }
      else {
          $start_time_ts = strtotime($tmp);
          if( !$start_time_ts ) $start_time_ts = $db->UnixTimeStamp($tmp);
          if( !$start_time_ts ) {
              $errors[] = $this->Lang('error_csv_insert',$linenum).' 1a';
              continue;
          }
      }

      $tmp = _map_field($fieldmap,$data,'all day');
      if( $tmp ) {
          $all_day = 1;
      }

      $tmp = _map_field($fieldmap,$data,'end time');
      if( strtolower($tmp) == 'all day' ) {
          // full day, use start date, but 23:59 end time.
          $all_day = 1;
      }
      else if( $tmp == 'NULL' || $tmp == '' ) {
          // no end time.
          $end_time_ts = null;
      }
      else {
          $end_time_ts = strtotime($tmp,$start_time_ts);
          if( !$end_time_ts ) $end_time_ts = $db->UnixTimeStamp($tmp);
      }
      if( $end_time_ts && $end_time_ts < $start_time_ts ) {
          $errors[] = $this->Lang('error_csv_insert',$linenum).' 2';
          continue;
      }
      // todo: check for too long of an event..

      $start_time = trim($db->DbTimeStamp($start_time_ts),"'");
      if( $end_time_ts ) $end_time = trim($db->DbTimeStamp($end_time_ts),"'");

      $title = _map_field($fieldmap,$data,'title');
      $description = _map_field($fieldmap,$data,'description');

      if( $title == '' ) {
          $errors[] = $this->Lang('error_csv_insert',$linenum).' 2a';
          continue;
      }

      $sql = "INSERT INTO " . $this->events_table_name . " (
               event_id
              ,event_status
              ,event_title
              ,event_summary
              ,event_details
              ,event_parent_id
              ,event_date_start
              ,event_date_end
              ,event_all_day
              ,event_allows_overlap
              ,event_created_by
              ,event_create_date
              ,event_modified_date
              ) VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())";

      // insert the event record.
      $parms = array($event_id
                     ,$event_status
                     ,$title
                     ,_map_field($fieldmap,$data,'summary')
                     ,$description
                     ,-1
                     ,$start_time
                     ,$end_time
                     ,$all_day
                     ,1
                     ,$user_id
          );
      $dbr = $db->Execute( $sql, $parms );
      if( !$dbr ) {
          $errors[] = $this->Lang('error_csv_insert',$linenum).' 3';
          continue;
      }

      // insert the categorie(s)
      $val = _map_field($fieldmap,$data,'category');
      if( $val ) {
          $categories = explode(';',$val);
          $_bad = false;
          foreach( $categories as $one ) {
              $one = trim($one);
              if( isset($cat[$one]) ) {
                  $dbr = $db->Execute('INSERT INTO '.$this->events_to_categories_table_name.
                                      ' (category_id, event_id) VALUES (?,?)',array($cat[$one],$event_id));
                  if( !$dbr ) {
                      // delete the inserted record
                      $query = 'DELETE FROM '.$this->events_table_name.' WHERE event_id = ?';
                      $db->Execute($query,array($event_id));

                      // delete any other categories.
                      $query = 'DELETE FROM '.$this->events_to_categories_table_name.' WHERE event_id = ?';
                      $db->Execute($query,array($event_id));

                      $errors[] = $this->Lang('error_csv_insert',$linenum).' 4';
                      $_bad = true;
                      break;
                  }
              }
          }
          if( $_bad ) continue;
      }

      // insert the fields.
      $_bad = false;
      $query = 'INSERT INTO '.$this->event_field_values_table_name.' (field_name,event_id,field_value) VALUES (?,?,?)';
      for( $i = 0; $i < count($fieldmap); $i++ ) {
          $fldname = $fieldmap[$i];
          if( in_array($fldname,$required_fields) ) continue;
          if( in_array($fldname,$optional_fields) ) continue;
          if( !isset($fields[$fldname]) ) continue; // an unknown field.
          if( $fields[$fldname]['field_type'] == 1 ) continue; // file type.

          $value = null;
          if( isset($data[$i]) ) $value = $data[$i];
          if( empty($value) ) continue;

          $dbr = $db->Execute($query,array($fldname,$event_id,$value));
          if( !$dbr ) {
              // delete the inserted record
              $query = 'DELETE FROM '.$this->events_table_name.' WHERE event_id = ?';
              $db->Execute($query,array($event_id));

              // delete the categories
              $query = 'DELETE FROM '.$this->events_to_categories_table_name.' WHERE event_id = ?';
              $db->Execute($query,array($event_id));

              $errors[] = $this->Lang('error_csv_insert',$linenum).' 5';
              $_bad = true;
              break;
          }
      }
      if( $_bad ) continue;

      $imported++;
  } // not end of file.

  fclose($handle);

  // do final report.
  $smarty->assign('imported',$imported);
  if( count($errors) ) $smarty->assign('errors',$errors);
  $smarty->assign('linenum',$linenum);
  echo $this->ProcessTemplate('admin_import.tpl');
} // else.

?>
