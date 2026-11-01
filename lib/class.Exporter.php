<?php
namespace CGCalendar;

class Exporter
{
    private $_mod;
    private $_settings;
    private $_category_list;
    private $_field_list;
    private $_tempnam;
    private $_fh;
    private $_limit = 200;
    private $_offset = 0;
    private $_total_rows;
    private $_field_cache;
    private $_category_cache;

    public function __construct( \CGCalendar $mod, ExportSettings $settings )
    {
        $this->_mod = $mod;
        $this->_settings = $settings;

        $categories = category::get_categories();
        if( count($categories) ) {
            foreach( $categories as $rec ) {
                $this->_category_list[$rec['category_id']] = $rec['category_name'];
            }
        }

        $fields = $mod->GetFields();
        $this->_field_list = \cge_array::to_hash($fields,'field_name');
        $this->_tempnam = tempnam(TMP_CACHE_LOCATION,'cal_');
    }

    public function __destruct()
    {
        $this->finish_writing();
        if( $this->_tempnam ) unlink($this->_tempnam);
    }

    protected function write_line($in)
    {
        if( !$this->_fh ) $this->_fh = fopen($this->_tempnam,'w');
        if( !$this->_fh ) throw new \RuntimeException('Could not create a temporary file for writing');
        if( !endswith( $in, "\n") && !endswith( $in, "\r" ) ) $in .= "\n";
        fputs($this->_fh,$in);
    }

    protected function finish_writing()
    {
        if( !$this->_fh ) return;
        fclose($this->_fh);
        $this->_fh = null;
    }

    protected function to_dbtimestamp($in)
    {
        if( !is_int($in) ) $in = strtotime($in);
        $out = $this->_mod->GetDb()->DbTimeStamp( $in );
        return trim($out,"'");
    }

    private function preload($rs)
    {
        $quoted_field_names = function() {
            static $res = FALSE;
            if( $res === FALSE ) {
                $res = [];
                $field_names = array_keys($this->_field_list);
                if( $field_names ) {
                    foreach( $field_names as $one ) {
                        $res[] = '"'.$one.'"';
                    }
                }
            }
            return $res;
        };

        $event_ids = null;
        while( !$rs->EOF() ) {
            $event_ids[] = (int) $rs->fields['event_id'];
            $rs->MoveNext();
        }
        $rs->MoveFirst();

        $this->_field_cache = $this->_category_cache = null;
        if( !count($event_ids) ) return;
        $db = $this->_mod->GetDb();

        // cache fields
        if( count($this->_field_list) ) {
            $sql = 'SELECT * FROM '.$this->_mod->event_field_values_table_name.' WHERE event_id IN (%A%) AND field_name IN (%B%) ORDER BY event_id, field_name';
            $sql = str_replace( '%A%', implode(',',$event_ids), $sql );
            $sql = str_replace( '%B%', implode(',',$quoted_field_names()), $sql );
            $list = $db->GetArray($sql);
            if( is_array($list) && count($list) ) $this->_field_cache = $list;
        }

        // cache categories
        if( count($this->_category_list) ) {
            $sql = 'SELECT * FROM '.$this->_mod->events_to_categories_table_name.' WHERE event_id in (%A%) ORDER BY event_id, category_id';
            $sql = str_replace( '%A%', implode(',',$event_ids), $sql );
            $list = $db->GetArray($sql);
            if( is_array($list) && count($list) ) $this->_category_cache = $list;
        }
    }

    private function generate_query()
    {
        $db = $this->_mod->GetDb();
        $sql = 'SELECT SQL_CALC_FOUND_ROWS E.* FROM '.$this->_mod->events_table_name.' E';
        $where = $joins = $parms = null;
        $where[] = 'E.event_status = ?';
        $parms[] = 'P';
        if( $this->_settings->fromdate ) {
            switch( $this->_settings->datesel ) {
            case 'start':
                $where[] = 'E.event_date_start >= ?';
                $parms[] = $this->to_dbtimestamp( $this->_settings->fromdate );
                break;
            case 'created':
                $where[] = 'E.event_create_date >= ?';
                $parms[] = $this->to_dbtimestamp( $this->_settings->fromdate );
                break;
            case 'modified':
            default:
                $where[] = 'E.event_modified_date >= ?';
                $parms[] = $this->to_dbtimestamp( $this->_settings->fromdate );
                break;
            }
        }

        if( $this->_settings->category ) {
            $joins[] = 'LEFT JOIN '.$this->_mod->events_to_categories_table_name.' C ON E.event_id = C.event_id';
            $where[] = 'C.category_id = ?';
            $parms[] = $this->_settings->category;
        }

        if( count($joins) ) $sql .= ' '.implode(' ',$joins);
        if( count($where) ) $sql .= ' WHERE '.implode(' AND ',$where);
        $sql .= ' ORDER BY E.event_create_date ASC';
        $rs = $db->SelectLimit($sql,$this->_limit,(int) $this->_offset,$parms);
        if( !$this->_total_rows ) $this->_total_rows = (int) $db->GetOne('SELECT FOUND_ROWS()');
        if( !$this->_total_rows ) return;

        $this->preload($rs);
        return $rs;
    }

    protected function generate_map()
    {
        $cols = [ 'start time','end time','title','summary','description' ];
        if( count($this->_category_list) ) $cols[] = 'category';
        if( count($this->_field_list) ) {
            $field_names = array_keys($this->_field_list);
            foreach( $field_names as $one ) {
                $cols[] = $one;
            }
        }
        return $cols;
    }

    protected function generate_header( array $map )
    {
        return \cge_array::implode_quoted($map,$this->_settings->delimiter);
    }

    protected function generate_record( $rs )
    {
        // get a record for each event (as an array)
        // the category element is a ; separated string of category names
        // the fields element is an associative array of field_names and field values
        $in = $rs->fields;
        $out['start time'] = $in['event_date_start'];
        $out['end time'] = $in['event_date_end'];
        $out['title'] = $in['event_title'];
        $out['summary'] = $in['event_summary'];
        $out['description'] = $in['event_details'];
        $out['category'] = null;
        $out['fields'] = null;
        if( $this->_category_cache ) {
            $member_cats = null;
            foreach( $this->_category_cache as $cat_row ) {
                if( $cat_row['event_id'] < $in['event_id'] ) continue;
                if( $cat_row['event_id'] > $in['event_id'] ) break;

                $member_cats[] = $this->_category_list[$cat_row['category_id']];
            }
            if( count($member_cats) ) $out['category'] = implode(';',$member_cats);
        }

        if( $this->_field_cache ) {
            foreach( $this->_field_cache as $field_row ) {
                if( $field_row['event_id'] < $in['event_id'] ) continue;
                if( $field_row['event_id'] > $in['event_id'] ) break;

                $out['fields'][$field_row['field_name']] = $field_row['field_value'];
            }
        }
        return $out;
    }

    protected function generate_line( $map, $rec )
    {
        // given the map, and a record, generate a text line
        $out = null;
        foreach( $map as $col ) {
            $val = null;
            if( isset($rec[$col]) ) {
                $val = $rec[$col];
            }
            else if( isset($rec['fields']) && isset($rec['fields'][$col]) ) {
                $val = $rec['fields'][$col];
            }
            $out[] = $val;
        }
        $line = \cge_array::implode_quoted($out,$this->_settings->delimiter);
        $line = rtrim($line,$this->_settings->delimiter);
        return $line;
    }

    public function generate()
    {
        $map = $this->generate_map();
        do {
            $rs = $this->generate_query();
            if( $this->_total_rows == 0 ) break;
            if( $rs && $this->_offset == 0 ) {
                $this->write_line( $this->generate_header( $map ) );
            }

            while( !$rs->EOF() ) {
                $rec = $this->generate_record( $rs );
                $line = $this->generate_line( $map, $rec );
                $this->write_line( $line );
                $rs->MoveNext();
            }
            $this->_offset += $this->_limit; // not necessarily correct, but should work
        } while( $this->_offset < $this->_total_rows );

        $this->finish_writing();
        if( $this->_total_rows > 0 ) {
            \cge_utils::send_file_and_exit($this->_tempnam,65535,'text/csv','CGCalendar Export.csv');
        }

    }
}