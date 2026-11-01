<?php
namespace CGCalendar;

class EventStorage
{
    private $_mod;
    private $_db;

    public function __construct(\CGCalendar $mod, $db)
    {
        $this->_mod = $mod;
        $this->_db = $db;
    }

    protected function to_datestr( $unixtime )
    {
        if( is_null($unixtime) || $unixtime < 1 ) return;
        $out = $this->_db->DbTimeStamp( $unixtime );
        return trim($out,"'");
    }

    protected function update_single_event( Event $event )
    {
        // does not handle updating children on recurring events.
        $event_id = $event['event_id'];
        $db = $this->_db;
        $sql = 'UPDATE '.self::events_table().'
                SET event_title = ?, event_summary = ?, event_details = ?, event_date_start = ?, event_date_end = ?,
                    event_parent_id = ?, event_recur_period = ?, event_date_recur_end = ?, event_recur_nevents = ?,
                    event_recur_interval = ?, event_recur_weekdays = ?, event_recur_monthdays = ?, event_modified_date = NOW(),
                    event_allows_overlap = ?, event_all_day = ?, event_status = ?
                WHERE event_id = ?';
        $dbr = $db->Execute( $sql, [ $event['event_title'], $event['event_summary'], $event['event_details'],
                                     $this->to_datestr($event['event_date_start_ut']), $this->to_datestr($event['event_date_end_ut']),
                                     $event['event_parent_id'], $event['event_recur_period'], $this->to_datestr($event['event_date_recur_end']),
                                     $event['event_recur_nevents'], $event['event_recur_interval'],
                                     implode(',',$event['event_recur_weekdays']), implode(',',$event['event_recur_monthdays']),
                                     $event['event_allows_overlap'], $event['event_all_day'], $event['event_status'],
                                     $event_id
                                 ]);
        if( !$dbr ) {
            throw new \cg_sql_error('Database Error: '.$db->ErrorMsg());
        }

        // delete existing field entries for this event
        $sql = 'DELETE FROM '.self::event_fields_table().' WHERE event_id = ?';
        $db->Execute( $sql, [ $event_id ] );

        if( count($event['fields']) ) {
            // insert new field entries
            $sql = 'INSERT INTO '.self::event_fields_table().' (event_id,field_name,field_value) VALUES (?,?,?)';
            foreach( $event['fields'] as $field_name => $field_value ) {
                $db->Execute( $sql, [ $event_id, $field_name, $field_value ] );
            }
        }

        // delete existing category entries for this event
        $sql = 'DELETE FROM '.self::event_categories_table().' WHERE event_id = ?';
        $db->Execute( $sql, [ $event_id ] );

        if( count($event['categories']) ) {
            $sql = 'INSERT INTO '.self::event_categories_table().' (event_id, category_id) VALUES (?,?)';
            foreach( $event['categories'] as $category_id ) {
                $db->Execute( $sql, [ $event_id, $category_id ] );
            }
        }
    }

    protected function insert_single_event( Event $event )
    {
        // todo: transaction

        $db = $this->_db;
        $event_id = $db->GenID(self::events_table()."_seq");

        // does not handle recurring events.
        $sql = "INSERT INTO " . self::events_table()."
                (event_id, event_title, event_summary, event_details,
     	        event_date_start, event_date_end, event_parent_id,
    	        event_recur_period, event_date_recur_end, event_created_by,
                event_recur_nevents, event_recur_interval,
                event_recur_weekdays, event_recur_monthdays,
                event_allows_overlap,event_all_day,event_status,
    	        event_create_date, event_modified_date)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())";
        $dbr = $db->Execute( $sql, [ $event_id, $event['event_title'], $event['event_summary'], $event['event_details'],
                                     $this->to_datestr($event['event_date_start_ut']), $this->to_datestr($event['event_date_end_ut']), $event['event_parent_id'],
                                     $event['event_recur_period'], $this->to_datestr($event['event_date_recur_end_ut']), $event['event_created_by'],
                                     $event['event_recur_nevents'], $event['event_recur_interval'],
                                     implode(',',$event['event_recur_weekdays']), implode(',',$event['event_recur_monthdays']),
                                     $event['event_allows_overlap'], $event['event_all_day'], $event['event_status']
                                 ]);
        if( !$dbr ) throw new \cg_sql_error('Database Error: '.$db->ErrorMsg());

        if( count($event['fields']) ) {
            // insert new field entries
            $sql = 'INSERT INTO '.self::event_fields_table().' (event_id,field_name,field_value) VALUES (?,?,?)';
            foreach( $event['fields'] as $field_name => $field_value ) {
                $db->Execute( $sql, [ $event_id, $field_name, $field_value ] );
            }
        }

        if( count($event['categories']) ) {
            $sql = 'INSERT INTO '.self::event_categories_table().' (event_id, category_id) VALUES (?,?)';
            foreach( $event['categories'] as $category_id ) {
                $db->Execute( $sql, [ $event_id, $category_id ] );
            }
        }

        return $event_id;
    }

    public function save_single_event( Event $event )
    {
        // saves a single event, does not effect children
        if( $event['event_id'] ) {
            $this->update_single_event( $event );
        } else {
            $this->insert_single_event( $event );
        }
    }

    public function simple_update_recurring_event( Event $event )
    {
        // if this is a recurring event,  then we update the fields, categories, and details
        // of all existing child events... but do not effect the start and end dates or recur details.
        if( !$event['event_id'] ) throw new \LogicException('Cannot call '.__METHOD__.' on a new event');

        // todo: transaction.
        $this->update_single_event( $event );
        if( !$event['event_recur_period'] || $event['event_recur_period'] == 'none' ) return;

        // now gotta update the children
        $event_id = $event['event_id'];

        // first, get the list of child event id's
        $db = $this->_db;
        $sql = 'SELECT event_id FROM '.self::events_table().' WHERE event_parent_id = ? ORDER BY event_id';
        $child_event_ids = $db->GetCol( $sql, [ $event_id ] );
        if( !count($child_event_ids) ) {
            trigger_error("CGCalendar event $event_id has no child events");
            audit($event_id,'CGCalendar','A supposed recurring event has no child events');
            return;
        }
        foreach( $child_event_ids as &$one ) {
            $one = (int) $one;
        }
        $idliststr = implode(',',$child_event_ids);

        // update the primary record.
        $sql = 'UPDATE '.self::events_table().' SET event_title = ?, event_summary = ?, event_details = ?, event_status = ?,
                 event_modified_date = NOW()
                WHERE event_parent_id = ?';
        $dbr = $db->Execute( $sql, [ $event['event_title'], $event['event_summary'], $event['event_details'], $event['event_status'], $event_id ] );
        if( !$dbr ) throw new \cg_sql_error('Database Error: '.$db->ErrorMsg());

        $sql = 'DELETE FROM '.self::event_fields_table()." WHERE event_id IN ($idliststr)";
        $db->Execute( $sql );

        // delete existing category entries for this event
        $sql = 'DELETE FROM '.self::event_categories_table()." WHERE event_id IN ($idliststr)";
        $db->Execute( $sql );

        foreach( $child_event_ids as $child_event_id ) {
            if( count($event['fields']) ) {
                // insert new field entries
                $sql = 'INSERT INTO '.self::event_fields_table().' (event_id,field_name,field_value) VALUES (?,?,?)';
                foreach( $event['fields'] as $field_name => $field_value ) {
                    $db->Execute( $sql, [ $child_event_id, $field_name, $field_value ] );
                }
            }

            if( count($event['categories']) ) {
                $sql = 'INSERT INTO '.self::event_categories_table().' (event_id, category_id) VALUES (?,?)';
                foreach( $event['categories'] as $category_id ) {
                    $db->Execute( $sql, [ $child_event_id, $category_id ] );
                }
            }
        }
    }

    protected function cleanup_on_load( array $params )
    {
        // converts the timestamps to uts.
        if( ($val = \cge_param::get_string($params,'event_date_start')) ) {
            $params['event_date_start_ut'] = strtotime($val);
            unset($params['event_date_start']);
        }
        if( ($val = \cge_param::get_string($params,'event_date_end')) ) {
            $params['event_date_end_ut'] = strtotime($val);
            unset($params['event_date_end']);
        }
        if( ($val = \cge_param::get_string($params,'event_date_recur_end')) ) {
            $params['event_date_recur_end_ut'] = strtotime($val);
            unset($params['event_date_recur_end']);
        }
        if( ($val = \cge_param::get_string($params,'event_create_date')) ) {
            $params['event_create_date_ut'] = strtotime($val);
            unset($params['event_create_date']);
        }
        if( ($val = \cge_param::get_string($params,'event_modified_date')) ) {
            $params['event_modified_date_ut'] = strtotime($val);
            unset($params['event_modified_date']);
        }
        $params['event_recur_weekdays'] = $params['event_recur_monthdays'] = [];
        if( ($weekdays = \cge_param::get_string($params,'event_recur_weekdays')) ) {
            $params['event_recur_weekdays'] = explode(',',$event_recur_weekdays);
        }
        if( ($monthdays = \cge_param::get_string($params,'event_recur_monthdays')) ) {
            $params['event_recur_monthdays'] = explode(',',$event_recur_monthdays);
        }
        return $params;
    }

    public function load_by_id( $event_id )
    {
        $event_id = (int) $event_id;

        $out = $this->load_list( [ $event_id ] );
        if( is_array($out) && count($out) == 1 ) return $out[0];
    }

    public function load_list( array $in_idlist )
    {
        // cleanup idlist
        $idlist = [];
        foreach( $in_idlist as $one ) {
            $one = (int) $one;
            if( $one > 0 && !in_array($one,$idlist) ) $idlist[] = $one;
        }
        $idliststr = implode(',',$idlist);

        // get all the rows

        $db = $this->_db;
        $sql = 'SELECT * FROM '.self::events_table()." WHERE event_id IN ($idliststr) ORDER BY event_id";
        $event_rows = $db->GetArray($sql);
        if( !count($event_rows) ) return;
        $idlist = \cge_array::extract_field( $event_rows, 'event_id' );
        foreach( $idlist as &$one ) {
            $one = (int) $one;
        }

        // get matching fields
        $sql = 'SELECT * FROM '.self::event_fields_table()." WHERE event_id IN ($idliststr) ORDER BY event_id,field_name";
        $field_rows = $db->GetArray($sql);

        // get matching categories
        $sql = 'SELECT * FROM '.self::event_categories_table()." WHERE event_id IN ($idliststr) ORDER BY event_id";
        $cat_rows = $db->GetArray($sql);

        // organize the data
        $event_objs = [];
        foreach( $event_rows as $event_arr ) {
            // if we have fields
            $event_arr = $this->cleanup_on_load( $event_arr );
            $event_id = $event_arr['event_id'];
            if( count($field_rows) ) {
                foreach( $field_rows as $field_row ) {
                    if( $field_row['event_id'] < $event_id ) continue;
                    if( $field_row['event_id'] > $event_id ) break;
                    if( !isset($event_arr['fields']) ) $event_arr['fields'] = [];
                    $event_arr['fields'][$field_row['field_name']] = $field_row['field_value'];
                }
            }

            // if we have categories.
            if( count($cat_rows) ) {
                foreach( $cat_rows as $cat_row ) {
                    if( $cat_row['event_id'] < $event_id ) continue;
                    if( $cat_row['event_id'] > $event_id ) break;
                    if( !isset($event_arr['categories']) ) $event_arr['categories'] = [];
                    $event_arr['categories'][] = $cat_row['category_id'];
                }
            }

            // now we create the event object.
            $event_obj = new Event( $event_arr );
            $event_objs[] = $event_obj;
        }
        return $event_objs;
    }

    public static function events_table()
    {
        return CMS_DB_PREFIX.'module_cgcalendar_events';
    }

    public static function event_categories_table()
    {
        return CMS_DB_PREFIX.'module_cgcalendar_events_to_categories';
    }

    public static function event_fields_table()
    {
        return CMS_DB_PREFIX.'module_cgcalendar_event_field_values';
    }
}
