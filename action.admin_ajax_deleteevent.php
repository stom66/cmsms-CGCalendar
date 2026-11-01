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
if( !$this->CheckPermission('Modify Calendar') && !$this->CheckPermission('Add Calendar Events') &&
    !$this->CheckPermission('Edit My Calendar Events') ) exit;

$userid = null;
if( !$this->CheckPermission('Modify Calendar') ) $userid = get_userid(FALSE) * -1 - 100;

$result = new stdClass;
$result->status = 0;
$result->msg = $this->Lang('error_unknown');

try {
    $event_id = (int)cge_utils::get_param($params,'event_id');
    $delete_children = (int)cge_utils::get_param($params,'delete_children',0);

    // get the event (make sure it's valid, and we have permission to delete it, AND that it is not a parent)
    $query = 'SELECT * FROM '.$this->events_table_name.' WHERE event_id = ?';
    $parms = array($event_id);
    if( !is_null($userid) ) {
        $query .= ' AND event_created_by = ?';
        $parms[] = $userid;
    }

    $event = $db->GetRow($query,$parms);
    if( !$event ) throw new Exception($this->Lang('error_eventnotfound'));

    if( !$delete_children ) {
        // now make sure it's not a parent (don't wanna leave orphaned events lying around)
        $query = 'SELECT event_id FROM '.$this->events_table_name.' WHERE event_parent_id = ?';
        $tmp = $db->GetOne($query,array($event_id));
        if( $tmp > 0 ) throw new Exception($this->Lang('error_deleteparentevent'));
    }

    // woot, we can delete the thing.
    cgcalendar_utils::delete_event($event_id);
    $result->status = 1;
}
catch( Exception $e ) {
    $result->msg = $e->GetMessage();
    \cge_utils::log_exception( $e );
}

echo json_encode($result);
exit;

?>