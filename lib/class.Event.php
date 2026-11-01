<?php
namespace CGCalendar;


// uses array access so that the object represents itself as an array to preserve backwards compat
class Event implements \ArrayAccess
{
    // readonly 'event_date_start', 'event_date_end' 'event_date_recur_end' (but fill from params can reading these and setting variables. handle these)
    private $_data = ['event_id'=>null,'event_title'=>null,'event_summary'=>null,'event_details'=>null,
                      'event_date_start_ut'=>null,'event_date_end_ut'=>null,'event_parent_id'=>null,'event_recur_period'=>null,
                      'event_date_recur_end_ut'=>null, 'event_created_by'=>null, 'event_create_date_ut'=>null, 'event_modified_date_ut'=>null,
                      'event_recur_nevents'=>null, 'event_recur_interval'=>null, 'event_recur_weekdays'=>[], 'event_recur_monthdays'=>[],
                      'event_allows_overlap'=>null, 'event_all_day'=>null, 'event_status'=>null ];
    private $_categories = [];
    private $_fields = [];

    public function __construct( array $params = null )
    {
        if( is_array($params) ) $this->fill_from_db( $params );
    }

    public function OffsetGet($key)
    {
        $not_null_dbtime = function( $in ) {
            if( is_int($in) ) return \cge_utils::db_time( $in );
        };

        switch( $key ) {
        case 'event_id':
        case 'event_parent_id':
        case 'event_created_by':
        case 'event_recur_nevents':
        case 'event_recur_interval':
            return (int) $this->_data[$key];

        case 'event_recurs': // convenience
            return (bool) ($this->_data['event_recur_period'] && $this->_data['event_recur_period'] != 'none');

        case 'event_recur_count': // convenience and compatibility
            return (int) $this->_data['event_recur_nevents'];

        case 'event_allows_overlap':
        case 'event_all_day':
            return (bool) $this->_data[$key];

        case 'all_day_event': // convenience and compatibility
            return (bool) $this->_data['event_all_day'];

        case 'event_date_start_ut':
            return (int) $this->_data[$key];

        case 'event_date_end_ut':
        case 'event_date_recur_end_ut':
        case 'event_create_date_ut':
        case 'event_modified_date_ut':
            if( $this->_data[$key] ) return (int) $this->_data[$key];
            return;

        case 'event_date_start':
            return $not_null_dbtime( $this->_data['event_date_start_ut'] );

        case 'event_date_recur_end':
            return $not_null_dbtime( $this->_data['event_date_recur_end_ut'] );

        case 'event_date_end':
            return $not_null_dbtime( $this->_data['event_date_end_ut'] );

        case 'event_create_date':
            return $not_null_dbtime( $this->_data['event_create_date_ut'] );

        case 'event_modified_date':
            return $not_null_dbtime( $this->_data['event_modified_date_ut'] );

        case 'event_title':
        case 'event_summary':
        case 'event_details':
        case 'event_recur_period':
        case 'event_status':
            return trim($this->_data[$key]);

        case 'event_recur_weekdays':
        case 'event_recur_monthdays':
            // this is an int[]
            return $this->_data[$key];

        case 'fields':
            return $this->_fields;

        case 'categories':
            return $this->_categories;

        default:
            throw new \LogicException("$key is not a gettable property of ".get_class($this));
        }
    }

    public function OffsetSet($key,$val)
    {
        $explode2 = function($delim,$in_str) {
            // explode a string by every second ,
            $tmp = explode($delim,$in_str);
            $out = [];
            while( $tmp ) {
                $out[] = implode($delim,array_splice($tmp,0,2));
            }
            return $out;
        };

        switch( $key ) {
        case 'event_parent_id':
        case 'event_created_by':
        case 'event_recur_nevents':
        case 'event_recur_interval':
            $this->_data[$key] = (int) $val;
            break;

        case 'event_recur_count': // compatibility
            $this->_data['event_recur_nevents'] = (int) $val;
            break;

        case 'event_allows_overlap':
        case 'event_all_day':
            $this->_data[$key] = (bool) $val;
            break;

        case 'all_day_event': // compatiblity
            $this->_data['event_all_day'] = (bool) $val;
            break;

        case 'event_date_recur_end_ut':
        case 'event_date_end_ut':
            if( !is_null($val) ) $val = trim($val);
            $this->_data[$key] = $val;
            break;

        case 'event_date_start_ut':
            $this->_data[$key] = (int) $val;
            break;

        case 'event_title':
        case 'event_summary':
        case 'event_details':
        case 'event_recur_period':
            $this->_data[$key] = trim($val);
            break;

        case 'event_recur_weekdays':
        case 'event_recur_monthdays':
            if( is_string($val) ) {
                $this->_data[$key] = $explode2(',',$val);
            } else {
                $this->_data[$key] = $val; // array ?
            }
            break;

        case 'event_status':
            $this->_data[$key] = trim($val)[0];
            break;

        default:
            throw new \LogicException("$key is not a settable property of ".get_class($this));
        }
    }

    public function OffsetExists( $key )
    {
        if( $key == 'fields' || $key == 'categories' ) return true;
        return array_key_exists($key,$this->_data);
    }

    public function OffsetUnset( $key ) {} /* do nothing */

    protected function fill_from_db( array $params )
    {
        if( ($fields = \cge_utils::get_param($params,'fields') ) ) {
            if( is_array($fields) && count($fields) )  $this->_fields = $fields;
            unset($params['fields']);
        }
        if( ($categories = \cge_utils::get_param($params,'categories') ) ) {
            if( is_array($categories) && count($categories) )  $this->_categories = $categories;
            unset($params['categories']);
        }

        foreach( array_keys($this->_data) as $key ) {
            if( isset($params[$key]) ) $this->_data[$key] = $params[$key];
        }
    }

    public function fill_from_params( $db, array $params )
    {
        // this is for form processing
        // todo: event id  ??
        if( ($val = \cge_param::get_int($params,'event_parent_id')) ) $this['event_parent_id'] = $val;
        if( \cge_param::exists($params,'removefromparent') ) $this['event_parent_id'] = -1;
        if( ($val = \cge_param::get_string($params,'event_title')) ) $this['event_title'] = $val;
        if( ($val = \cge_param::get_string($params,'event_summary')) ) $this['event_summary'] = $val;
        if( ($val = trim(\cge_utils::get_param($params,'event_details'))) ) $this['event_details'] = $val;
        if( ($val = \cge_param::get_bool($params,'event_allows_overlap')) ) $this['event_allows_overlap'] = $val;
        if( ($val = \cge_param::get_string($params,'event_status')) ) $this['event_status'] = $val;
        $this['event_all_day'] = \cge_param::get_bool($params,'all_day_event');

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
            $time = mktime($start_hour,$start_minute,0,$params['startdate_Month'],$params['startdate_Day'],
                                                   $params['startdate_Year']);
            $this['event_date_start'] = $db->DbTimeStamp($time);
        }

        $event['event_date_end'] = null;
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
            $time = mktime($end_hour,$end_minute,0,$end_month,$end_day,$end_year);
            $this['event_date_end'] = $db->DbTimeStamp($time);
        }

        if( ($val = \cge_param::get_string($params,'event_recur_period')) ) $this['event_recur_period'] = $val;
        if( $this['event_recur_period'] != 'none' ) {
            if( ($val = \cge_param::get_int($params,'event_recur_interval')) ) $this['event_recur_interval'] = $val;
            if( ($val = \cge_param::get_int($params,'event_recur_count')) ) $this['event_recur_count'] = $val;
            if( ($val = \cge_utils::get_param($params,'event_recur_weekdays')) ) {
                if( is_array($val) ) {
                    $this['event_recur_weekdays'] = $val;
                } else {
                    $this['event_recur_weekdays'] = explode(',',$val);
                }
            }
            if( ($val = \cge_utils::get_param($params,'event_recur_monthdays')) ) {
                if( is_array($val) ) {
                    $this['event_recur_monthdays'] = $val;
                } else {
                    $this['event_recur_monthdays'] = explode(',',$val);
                }
            }
            if( \cge_param::exists($params,'event_recur_date_Month') ) {
                $time = mktime(0,0,0,
                               \cge_param::get_int($params,'event_recur_date_Month'),
                               \cge_param::get_int($params,'event_recur_date_Day'),
                               \cge_param::get_int($params,'event_recur_date_Year')
                    );
                $event['event_date_recur_end'] = $db->DbTimeStamp($time);
            } else if( ($val = \cge_param::get_int($params,'event_date_recur_ut')) ) {
                $event['event_date_recur_end'] = $db->DbTimeStamp($val);
            } else {
                $event['event_date_recur_end'] = null;
            }
        }

        if( ($val = \cge_param::get_string($params,'event_categories') ) ) {
            $list = unserialize( $val );
            if( is_array( $list ) ) {
                $this->set_categories( $list );
            } else {
                // clear all categories;
                $this->set_categories( [] );
            }
        }

        // todo: handle fields.
    }

    public function has_field( $field_name )
    {
        return isset($this->_fields[$field_name]);
    }

    public function set_field_value( $field_name, $val )
    {
        $field_name = trim($field_name);
        if( !$field_name ) throw new \LogicException("invalid field name passed to ".__METHOD__);
        if( !is_null($val) ) $val = trim($val);
        if( is_null($val) ) {
            if( isset($this->_fields[$field_name]) ) unset($this->_fields[$field_name]);
        }
        else {
            $this->_fields[$field_name] = $val;
        }
    }

    public function has_category( $catid )
    {
        $catid = (int) $catid;
        if( $catid < 1 ) throw new \LogicException("Invalid argument passed to ".__METHOD__);
        return in_array( $catid, $this->_categories );
    }

    public function set_categories( array $in )
    {
        foreach( $in as $one ) {
            if( (int) $one < 1 ) throw new \LogicException('Invalid category array passed to '.__METHOD__);
        }
        $this->_categories = [];
    }

    public function set_category( $catid )
    {
        $catid = (int) $catid;
        if( $catid < 1 ) throw new \LogicException("Invalid argument passed to ".__METHOD__);
        if( !$this->has_category( $catid ) ) $this->_categories[] = $catid;
    }
}