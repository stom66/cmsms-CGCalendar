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

class cgc_event_utils
{
    private function __construct() {}

    static public function get_event_from_params(&$event,$params,$is_edit = false)
    {
        $gCms = CmsApp::get_instance();
        $db = $gCms->GetDb();
        $mod = \cms_utils::get_module(MOD_CGCALENDAR);

        if( isset($params['event_id']) )  $event['event_id'] =  (int)$params['event_id'];
        if( isset($params['event_parent_id']) )  $event['event_parent_id'] = (int)$params['event_parent_id'];
        if( isset($params['removefromparent']) && $params['removefromparent'] ) $event['event_parent_id'] = -1;
        if( isset($params['event_title']) )  $event['event_title'] = trim($params['event_title']);
        if( isset($params['event_summary']) )  $event['event_summary'] = trim($params['event_summary']);
        if( isset($params['event_details']) )  $event['event_details'] = trim($params['event_details']);
        if( isset($params['event_allows_overlap']) ) {
            $event['event_allows_overlap'] = (int)$params['event_allows_overlap'];
        }
        else {
            $tmp = $mod->GetPreference('overlap_policy','all');
            $event['event_allows_overlap'] = ($tmp == 'all')?1:0;
        }

        $event['event_status'] = $mod->GetPreference('dflt_status','D');
        if( isset($params['event_status']) ) {
            switch( $params['event_status'] ) {
            case 'D':
            case 'P':
                $event['event_status'] = $params['event_status'];
                break;
            }
        }

        $all_day_event = 0;
        if( isset($params['all_day_event']) )  $all_day_event = (int)$params['all_day_event'];
        $event['event_all_day'] = $all_day_event;

        $start_hour = 0;
        $start_minute = 0;
        if( isset($params['startdate_Month']) ) {
            $start_hour = $params['startdate_Hour'];
            $start_minute = $params['startdate_Minute'];
            if( isset($params['startdate_Meridian']) ) {
                if( $params['startdate_Meridian'] == 'am' && $start_hour >= 12 ) {
                    $start_hour -= 12;
                }
                else if( $params['startdate_Meridian'] == 'pm' && $start_hour < 12 ) {
                    $start_hour += 12;
                }
            }
            $event['event_date_start_ut'] = mktime($start_hour,$start_minute,0,$params['startdate_Month'],$params['startdate_Day'],
                                                   $params['startdate_Year']);
            $event['event_date_start'] = $db->DbTimeStamp($event['event_date_start_ut']);
        }

        $event['event_date_end'] = NULL;
        $event['event_date_end_ut'] = NULL;
        if( isset($params['enddate_valid']) && $params['enddate_valid'] && isset($params['enddate_Day']) ) {
            $end_month = $params['enddate_Month'];
            $end_day = $params['enddate_Day'];
            $end_year = $params['enddate_Year'];
            $end_hour = $params['enddate_Hour'];
            $end_minute = $params['enddate_Minute'];
            if( isset($params['enddate_Meridian']) && $params['enddate_Meridian'] == 'am' && $end_hour >= 12 ) {
                $end_hour -= 12;
            }
            else if( isset($params['enddate_Meridian']) && $params['enddate_Meridian'] == 'pm' && $end_hour < 12 ) {
                $end_hour += 12;
            }
            $event['event_date_end_ut'] = mktime($end_hour,$end_minute,0,$end_month,$end_day,$end_year);
            $event['event_date_end'] = $db->DbTimeStamp($event['event_date_end_ut']);
        }

        if( isset($params['event_recur_period']) ) $event['event_recur_period'] = trim($params['event_recur_period']);
        if( $event['event_recur_period'] != 'none' ) {
            if( isset($params['event_recur_interval']) ) $event['event_recur_interval'] = (int)$params['event_recur_interval'];
            if( isset($params['event_recur_weekdays']) ) {
                if( is_array($params['event_recur_weekdays']) ) {
                    $event['event_recur_weekdays'] = $params['event_recur_weekdays'];
                }
                else {
                    $event['event_recur_weekdays'] = explode(',',$params['event_recur_weekdays']);
                }
            }
            if( isset($params['event_recur_monthdays']) ) {
                if( is_array($params['event_recur_monthdays']) ) {
                    $event['event_recur_monthdays'] = $params['event_recur_monthdays'];
                }
                else {
                    $event['event_recur_monthdays'] = explode(',',$params['event_recur_monthdays']);
                }
            } else {
                $event['event_recur_monthdays'] = ['specified'];
            }
            if( isset($params['event_recur_count']) )  $event['event_recur_count'] = (int)$params['event_recur_count'];
            if( isset($params['event_recur_date_Month']) && $params['event_recur_period'] != 'none' ) {
                $event['event_date_recur_end_ut'] = mktime(0,0,0,
                                                           $params['event_recur_date_Month'],$params['event_recur_date_Day'],
                                                           $params['event_recur_date_Year']);
                $event['event_date_recur_end'] = $db->DbTimeStamp($event['event_date_recur_end_ut']);
            }
            else if( isset($params['event_recur_date_ut']) ) {
                // handle a single input field in the form of a unix timestamp.
                $event['event_date_recur_end'] = $db->DbTimeStamp($params['event_recur_date_ut']);
                $event['event_date_recur_end_ut'] = $params['event_recur_date_ut'];
            }
            else {
                $event['event_date_recur_end'] = NULL;
                $event['event_date_recur_end_ut'] = NULL;
            }
        }

        if( isset($params['event_categories']) ) {
            if( is_string($params['event_categories']) ) $params['event_categories'] = unserialize($params['event_categories']);
            if( is_array($params['event_categories']) ) {
                // todo: make sure all values are ints, and keys are category id's.
                $event['categories'] = $params['event_categories'];
            }
        }
    }

    static public function is_event_conflicted($event,$policy)
    {
        $gCms = CmsApp::get_instance();
        $db = $gCms->GetDb();

        $event_id = (isset($event['event_id'])) ? $event['event_id'] : -1;
        $str = '';
        if( $policy == 'individual') $str .= ' AND event_allows_overlap = 0';
        if( $event_id != -1) $str .= " AND event_id != $event_id";

        $dbr = '';
        if( $event['event_date_end'] == NULL ) {
            $start = '';
            if( isset($event['event_date_start']) ) {
                $start = $event['event_date_start'];
            }
            else if( isset($event['event_date_start_ut']) ) {
                // assume unix timestamp
                $start = $db->DbTimeStamp($event['event_date_start_ut']);
            }
            else if( isset($event['start']) ) {
                // assume unix timestamp
                $start = $db->DbTimeStamp($event['start']);
            }

            $query = 'SELECT event_id FROM '.$module->events_table_name." WHERE ($start BETWEEN event_date_start and event_date_end)";
            $dbr = $db->GetOne($query.$str);
        }
        else {
            $start = '';
            if( isset($event['event_date_start']) ) {
                $start = $event['event_date_start'];
            }
            else if( isset($event['event_date_start_ut']) ) {
                // assume unix timestamp
                $start = $db->DbTimeStamp($event['event_date_start_ut']);
            }
            else if( isset($event['start']) ) {
                // assume unix timestamp
                $start = $db->DbTimeStamp($event['start']);
            }

            $end = '';
            if( isset($event['event_date_end']) ) {
                $end = $event['event_date_end'];
            }
            else if( isset($event['event_date_end_ut']) ) {
                // assume unix timestamp
                $end = $db->DbTimeStamp($event['event_date_end_ut']);
            }
            else if( isset($event['end']) ) {
                // assume unix timestamp
                $end = $db->DbTimeStamp($event['end']);
            }

            $query = 'SELECT event_id FROM '.$module->events_table_name."
                      WHERE (($start BETWEEN event_date_start and event_date_end)
                      OR ($end BETWEEN event_date_start and event_date_end))";
            $dbr = $db->GetOne($query.$str, array(trim($start,'"'),trim($end,'"')));
        }

        if( $dbr ) return TRUE;
        return FALSE;
    }

    protected static function nth_weekday($nth,$weekday,$fromdate)
    {
        $month = date('m',$fromdate);
        $year = date('Y',$fromdate);
        $hours = date('H',$fromdate);
        $minutes = date('i',$fromdate);
        $seconds = date('s',$fromdate);
        $ndays = -1;

        $nth = strtolower($nth);
        switch( $nth ) {
        case 'first':
            $nth = 1;
            break;
        case 'second':
            $nth = 2;
            break;
        case 'third':
            $nth = 3;
            break;
        case 'fourth':
            $nth = 4;
            break;
        case 'last':
            $nth = -1;
            break;
        default:
            $nth = (int)$nth;
            break;
        }
        if( $nth == -1 ) {
            $days_in_month = date('t',$fromdate);
            $tmp1 = mktime(0,0,0,$month,$days_in_month,$year);
            $weekday_of_last = date('w',$tmp1);

            if( $weekday_of_last < $weekday ) {
                //$ndays = $days_in_month - $weekday_of_last - $weekday - 1;
                $ndays = $days_in_month - $weekday_of_last + $weekday - 7;
            }
            else {
                $ndays = $days_in_month - $weekday_of_last + $weekday;
            }
        }
        else {
            $nth = max($nth,1);
            $nth = min($nth,4);

            // get weekday of first of the month
            $tmp1 = mktime(0,0,0,$month,1,$year);
            $weekday_of_first = date('w',$tmp1);

            if( $weekday_of_first <= $weekday ) {
                $ndays = ($weekday - $weekday_of_first + 1) + ($nth - 1) * 7;
            }
            else {
                $ndays = (7 - $weekday_of_first + $weekday + 1) + ($nth - 1) * 7;
            }
        }

        $date = mktime($hours,$minutes,$seconds,$month,$ndays,$year);
        return $date;
    }

    public static function check_overlapping_events($event_start_ut,$event_end_ut,$child_events)
    {
        $tmp = array();
        $tmp[] = array('start'=>$event_start_ut,'end'=>$event_end_ut);
        $tmp = array_merge($tmp,$child_events);

        // trivial check, if the end date is null or empty
        // there is no chance that events can overlap.
        if(empty($event_end_ut) ) return false;

        for( $i = 1; $i < count($tmp); $i++ ) {
            $prev =& $tmp[$i-1];
            $cur =& $tmp[$i];
            if( ($cur['start'] >= $prev['start'] && $cur['start'] <= $prev['end']) ||
                ($cur['end'] >= $prev['start'] && $cur['end'] <= $prev['end']) ) {
                return true;
            }
        }

        return false;
    }

    public static function check_db_for_conflicts($event,$event_id,$policy,$event_allows_overlap)
    {
        $db = \CmsApp::get_instance()->GetDb();
        $module = \cms_utils::get_module(MOD_CGCALENDAR);

        $str = '';
        if( $policy == 'individual' && $event_allows_overlap) $str .= ' AND event_allows_overlap = 0';
        if( $event_id != '' && $event_id != -1) $str .= " AND event_id != $event_id";

        $dbr = '';
        if( $event['event_date_end'] == NULL ) {
            $start = '';
            if( isset($event['event_date_start']) ) {
                $start = $event['event_date_start'];
            }
            else if( isset($event['start']) ) {
                // assume unix timestamp
                $start = $db->DbTimeStamp($event['start']);
            }

            $query = 'SELECT event_id FROM '.$module->events_table_name." WHERE ($start BETWEEN event_date_start and event_date_end)";
            $dbr = $db->GetOne($query.$str);
        }
        else {
            $start = '';
            if( isset($event['event_date_start']) ) {
                $start = $event['event_date_start'];
            }
            else if( isset($event['start']) ) {
                // assume unix timestamp
                $start = $db->DbTimeStamp($event['start']);
            }

            $end = '';
            if( isset($event['event_date_end']) ) {
                $end = $event['event_date_end'];
            }
            else if( isset($event['end']) ) {
                // assume unix timestamp
                $end = $db->DbTimeStamp($event['end']);
            }

            $query = 'SELECT event_id FROM '.$module->events_table_name."
                      WHERE (($start BETWEEN event_date_start and event_date_end)  OR ($end BETWEEN event_date_start and event_date_end))";
            $dbr = $db->GetOne($query.$str);
        }
        if( $dbr ) return true;
        return false;
    }

    public static function calculate_recurring_events($event_start_ut, $event_end_ut,$event_recur_period,$event_recur_end_ut,$event_recur_interval,
                                                      $event_recur_count,$event_recur_weekdays,$event_recur_monthdays)
    {
        // adjust event_recur_end_ut to be at 23:59:59 of the same day
        $event_recur_end_ut = strtotime( '23:59:59', $event_recur_end_ut );
        $results = array();
        $weekdays = array('sunday','monday','tuesday','wednesday','thursday','friday','saturday');
        $date = $event_start_ut;
        $deltat = $event_end_ut - $event_start_ut;
        if( (int)$event_end_ut == 0 ) $deltat = 0;

        $period = 'day';
        $datefmtstr = "+%d %s";
        $hour = (int)strftime('%H',$date);
        $minute = (int)strftime('%M',$date);
        if( $event_recur_count < 0 ) $event_recur_count = 1000000;
        $mod = \cms_utils::get_module(MOD_CGCALENDAR);
        $firstdayofweek = $mod->GetPreference('firstdayofweek');

        switch( $event_recur_period ) {
        case 'daily':
            $period = 'days';
            break;

        case 'weekly':
            $datefmtstr = "+%d %s %s %d:%d";
            $period = 'weeks';
            // this loop handles event in the same week as the start event
            if( !count($event_recur_weekdays) ) {
                $event_recur_weekdays = array(date('w',$event_start_ut));
            }
            $date = $event_start_ut;
            foreach( $event_recur_weekdays as $wd ) {
                if( count($results) >= $event_recur_count ) break;
                $str = sprintf($datefmtstr,0,$period,$weekdays[$wd+$firstdayofweek],$hour,$minute);
                $tmp = strtotime($str,$date);
                if( $tmp <= $event_start_ut ) continue;

                $tmpa = array();
                $tmpa['start'] = $tmp;
                if( $deltat == 0 ) {
                    $tmpa['end'] = NULL;
                }
                else {
                    $tmpa['end'] = $tmp + $deltat;
                }

                // here we check for an overlap
                // with the start and end dates
                // and ignore anything that might overlap
                $tmp2 = array($tmpa);
                $test = \cgc_event_utils::check_overlapping_events($event_start_ut,$event_end_ut,$tmp2);
                if( $test == false ) $results[] = $tmpa;
            }
            break;

        case 'monthly':
            $period = 'months';
            if( $event_recur_monthdays[0] == 'specified' ) {
                $event_recur_period = 'monthly_specified';
            }
            else {
                $datefmtstr = "%s %s %d %d:%d";
                // this loop handles events in the same month as the start event
                $month = date('M',$date);
                $year = date('Y',$date);
                foreach( $event_recur_monthdays as $md ) {
                    if( count($results) >= $event_recur_count ) break;
                    list($nth,$wd) = explode(',',$md,2);
                    $tmp = self::nth_weekday($nth,$wd,$date);
                    //$str = sprintf($datefmtstr,$md,$month,$year,$hour,$minute);
                    //$tmp = strtotime($str,$date);
                    if( $tmp <= $event_start_ut ) continue;
                    $results[] = $tmp;
                }
                $tmp2 = strtotime(sprintf('+%d months',$event_recur_interval),$date);
                if (date('d',$tmp2)>28) $tmp2 = strtotime('-4 days',$tmp2);
                $date = $tmp2;
            }
            break;

        case 'yearly':
            $period = 'years';
            break;
        }

        // handle the recurring nature of things.
        while( count($results) < $event_recur_count && $date <= $event_recur_end_ut ) {
            switch( $event_recur_period ) {
            case 'weekly':
                // handle each weekday
                $tmp = '';
                foreach( $event_recur_weekdays as $wd ) {
                    if( count($results) >= $event_recur_count ) break;
                    $str = sprintf($datefmtstr,$event_recur_interval,$period,$weekdays[$wd+$firstdayofweek],$hour,$minute);
                    $tmp = strtotime($str,$date);
                    if( $tmp > $event_recur_end_ut ) break;
                    $tmpa = array();
                    $tmpa['start'] = $tmp;
                    if( $deltat == 0 ) {
                        $tmpa['end'] = NULL;
                    }
                    else {
                        $tmpa['end'] = $tmp + $deltat;
                    }
                    $tmpa['start_a'] = strftime('%x %X', $tmpa['start']);
                    $results[] = $tmpa;
                }
                $date = $tmp;
                break;

            case 'monthly':
                $tmp = '';
                $month = date('M',$date);
                $year = date('Y',$date);
                foreach( $event_recur_monthdays as $md ) {
                    if( count($results) >= $event_recur_count ) break;
                    list($nth,$wd) = explode(',',$md,2);
                    $tmp = self::nth_weekday($nth,$wd,$date);
                    //$str = sprintf($datefmtstr,$md,$month,$year,$hour,$minute);
                    //$tmp = strtotime($str,$date);
                    if( $tmp <= $event_start_ut ) continue;

                    $tmpa = array();
                    $tmpa['start'] = $tmp;
                    if( $deltat == 0 ) {
                        $tmpa['end'] = NULL;
                    }
                    else {
                        $tmpa['end'] = $tmp + $deltat;
                    }
                    $results[] = $tmpa;
                }

                $tmp2 = strtotime(sprintf('+%d months',$event_recur_interval),$date);
                if (date('d',$tmp2)>28)	$tmp2 = strtotime('-4 days',$tmp2);
                $date = $tmp2;
                break;

            default:
                // normal incrementing
                $tmp = strtotime(sprintf($datefmtstr,$event_recur_interval,$period),$date);
                $tmpa = array();
                $tmpa['start'] = $tmp;
                if( $deltat == 0 ) {
                    $tmpa['end'] = NULL;
                }
                else {
                    $tmpa['end'] = $tmp + $deltat;
                }
                $results[] = $tmpa;
                $date = $tmp;
                break;
            } // switch
        } // while

        return $results;
    }
} // end of class

#
# EOF
#
