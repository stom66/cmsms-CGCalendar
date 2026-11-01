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

try {
    $feu = $this->GetModuleInstance('FrontEndUsers');
    if( !$feu ) throw new \LogicException('Cannot delete an event from the frontend without being logged in to the FEU module');

    $feu_uid = $feu->LoggedInId();
    if( $feu_uid < 1 ) throw new \LogicException('Cannot delete an event from the frontend without being logged in to the FEU module');

    $event_id = \cge_param::get_int($params,'event_id');
    if( $event_id < 1 ) throw new \LogicException('Invalid event id passed to delete_event action');

    $event = null;
    if( $this->HaveManageRight() ) {
        $event = $this->GetEvent($event_id);
    }
    else {
        $event = $this->GetEvent($event_id,$feu_uid);
        if( $event['event_created_by'] != $feu_uid ) throw new \LogicException('Permission Denied');
    }
    if( $event['event_id'] != $event_id ) throw new \LogicException('Event not found');

    if( isset($params['cal_cancel']) ) {
        $this->RedirectContent($returnid);
    }
    else if( isset($params['cal_submit']) ) {
        $res = cgcalendar_utils::delete_event($event_id);
        $this->RedirectContent($returnid);
    }

    $thetemplate = \cge_param::get_string($params,'deleteeventtemplate');
    if( !$thetemplate ) {
        $tpl = CmsLayoutTemplate::load_dflt_by_type('CGCalendar::deleteevent');
        if( !is_object($tpl) ) {
            audit('',$this->GetName(),'No default delete event template found');
            return;
        }
        $thetemplate = $tpl->get_name();
        unset($tpl);
    }
    $tpl = $smarty->CreateTemplate($this->GetTemplateResource($thetemplate),null,null,$smarty);
    $tpl->assign('formstart',$this->CGCreateFormStart($id,'delete_event',$returnid,$params));
    $tpl->assign('formend',$this->CreateFormEnd());
    $tpl->assign('event',$event);
    $tpl->display();
}
catch( \Exception $e ) {
    echo $this->DisplayErrorMessage($e->GetMessage());
}

#
# EOF
#
