<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_troubleshoot_report extends MY_Model {

    function __construct() {
        parent::__construct();
    }
    
    function get_filters() {
        return [
            [
                'field_name' => 'mr|users_name',
                'field_label' => 'MR Name'
            ],
            [
                'field_name' => 'c|city_name',
                'field_label' => 'HQ'
            ],
            [
                'field_name' => 'ts|message',
                'field_label' => 'Message'
            ],
        ];
    }

    function get_filters_from($filters) {
        $new_filters = array_column($this->get_filters(), 'field_name');
        
        if(array_key_exists('from_date', $filters))  {
            array_push($new_filters, 'from_date');
        }

        if(array_key_exists('to_date', $filters))  {
            array_push($new_filters, 'to_date');
        }

        return $new_filters;
    }

	function get_collection($count = FALSE, $f_filters = [], $rfilters ='', $limit = 0, $offset = 0 ) {
        
        $sql = "select
        ts.troubleshoot_id, mr.users_name as mr_name, c.city_name as city,
        ts.message as message, ts.image as images,
        ts.insert_dt as date
        from 
        troubleshoot ts
        JOIN manpower mr on mr.users_id = ts.users_id
        JOIN city c ON c.city_id = mr.users_city_id";

        $sql .= " WHERE 1 = 1 ";
		
		if(is_array($rfilters) && count($rfilters) ) {
			$field_filters = $this->get_filters_from($rfilters);
			
            foreach($rfilters as $key=> $value) {
                $value = trim($value);
                if(!in_array($key, $field_filters)) {
                    continue;
                }
				
				if($key == 'from_date' && !empty($value)) {
					$sql .= " AND DATE(ts.insert_dt) >= '".date('Y-m-d', strtotime($value))."' ";
                    continue;
                }

                if($key == 'to_date' && !empty($value)) {
					$sql .= " AND DATE(ts.insert_dt) <= '".date('Y-m-d', strtotime($value))."' ";
                    continue;
                }

                if(!empty($value) && !in_array($key, ['from_date', 'to_date'])) {
                    $key = str_replace('|', '.', $key);
                    $value = $this->db->escape_like_str($value);
                    $sql .= " AND $key LIKE '%$value%' ";
                }
            }
        }

        if(! $count) {
            if(!empty($limit)) { $sql .= " LIMIT $offset, $limit"; }        
        }
        
        $q = $this->db->query($sql);
        //echo $sql;exit;
        $collection = (! $count) ? $q->result_array() : $q->num_rows();

		return $collection;
    }		
    
	function _format_data_to_export($data){
		
		$resultant_array = [];
		
		foreach ($data as $rows) {			
			$records['MR Name'] = $rows['mr_name'];
			$records['HQ'] = $rows['city'];
			$records['Message'] = $rows['message'];
			$records['Date'] = $rows['date'];
			
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}