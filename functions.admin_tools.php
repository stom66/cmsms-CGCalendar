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

function calendar_isValidFilename(&$mod,$filename)
{
  $file_name = trim ($filename);
  $extension = strtolower (strrchr ($file_name, "."));

  $valid_extensions = explode(',',strtolower($mod->GetPreference('uploadfiletypes','jpg,jpeg,gif,png')));
  $count = count($valid_extensions);

  if( !$file_name ) return false;
  if( $count == 0 ) return true;
  foreach( $valid_extensions as $oneextension ) {
    $fc = substr($oneextension, 0, 1);
    if( $fc != '.' ) $oneextension = '.'.$oneextension;
    if( $oneextension == $extension ) return true;
  }
  return false;
}

function calendar_HandleUpload(&$mod,$fldname,&$error)
{
  $config = cmsms()->GetConfig();
  $destDir = $mod->GetPreference('uploaddirectory');
  if( !$destDir ) $destDir = $config['uploads_path'];

  if( !isset($_FILES) || !isset($_FILES[$fldname]) || !$_FILES[$fldname]['name'] ) {
    $error = $mod->Lang('error_noupload');
    return false;
  }

  $file =& $_FILES[$fldname];
  if( !isset($file['type']) ) $file['type'] = '';
  if( !isset($file['name']) ) $file['name'] = '';
  if( !isset($file['size']) ) $file['size'] = '';
  //$file['name'] = preg_replace('/[^a-zA-Z0-9\.\$\%\'\`\-\@\{\}\~\!\#\(\)\&\_\^]/', '', str_replace (array (' ', '%20'), array ('_', '_'), $file['name']));
  $file['name'] = mb_ereg_replace('[^\w\s\d\.\-_~,;:\%\@\$\'\{\}\#\&\[\]\(\]]', '', str_replace (array (' ', '%20'), array ('_', '_'), $file['name']));
  if( !$mod->isValidFilename( $file['name'] ) ) {
    $error = $mod->Lang('error_invalidfilename');
    return false;
  }

  if( $file['error'] != 0 || $file['size'] == '' || $file['size'] == 0 ) {
    $error = $mod->Lang('error_problemwithupload');
    return false;
  }

  $destname = $file['name'];
  $destfilespec = cms_join_path( $destDir, $destname );
  if( file_exists( $destfilespec ) ) {
    if( $mod->GetPreference('uploadunique',1) == 1 ) {
      $i = 0;
      for( $i = 0;  $i < 100; $i++ ) {
	$destname = $file['name'];
	if( $i > 0 ) $destname = $i.'_'.$file['name'];
	$destfilespec = cms_join_path( $destDir, $destname );
	if( !file_exists( $destfilespec ) ) break;
      }
      if( $i == 100 ) {
	$error = $mod->Lang('error_fileexists');
	return false;
      }
    }
    else {
      $error = $mod->Lang('error_fileexists');
      return false;
    }
  }

  if( !@copy($file['tmp_name'], $destfilespec) ) {
    $error = $mod->Lang('error_filecopyfailed');
    return false;
  }

  return $destname;
}

#
# EOF
#
?>