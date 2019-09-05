<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_region_wise extends MY_Model {

    function __construct() {
        parent::__construct();
    }
    
    function get_filters() {
        return [
            [
                'field_name'=>'users_name',
                'field_label'=> 'RSM Name',
            ],
            [
                'field_name'=>'region_name',
                'field_label'=> 'Region',
            ],
            []
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
        
        $sql = "SELECT ";

        if(!$count) {
            $sql .= " users_name, users_type, region_name, doctor_count ";
        } 
        else {
            $sql .= " COUNT(users_name) AS total_count ";
        } 

		$sql .= "                
            FROM (
                SELECT
                    region_id, region_name, 
                    COUNT(doctor_id) AS doctor_count 
                    FROM region r
                    JOIN manpower m ON r.region_id = m.users_region_id
                    JOIN doctor d ON d.insert_user_id = m.users_id
                    GROUP BY r.region_id 
            ) collection 
            JOIN manpower mp ON mp.users_region_id = collection.region_id AND mp.users_type = 'RSM' " ;
                    
        $sql .= " WHERE 1=1 ";
            
        /* if(sizeof($f_filters)) { 
			foreach ($f_filters as $key=>$value) { $sql .= "AND $key = $value "; }
		} */

		if(is_array($rfilters) && count($rfilters) ) {
			$field_filters = $this->get_filters_from($rfilters);
			
            foreach($rfilters as $key=> $value) {
                $value = trim($value);
                if(!in_array($key, $field_filters)) {
                    continue;
                }
                
                if(!empty($value)) {
                    $value = $this->db->escape_like_str($value);
                    $sql .= " AND $key LIKE '$value%' ";
                }
            }
        }

        if(! $count) {
            if(!empty($limit)) { $sql .= " LIMIT $offset, $limit"; }        
        }
        
        $q = $this->db->query($sql);
        
        $collection = (! $count) ? $q->result_array() : $q->row_array()['total_count'];

		return $collection;
    }	
    
	function _format_data_to_export($data){
		
		$resultant_array = [];
		
		foreach ($data as $rows) {
			$records['Name'] = $rows['users_name'];
			$records['Region'] = $rows['region_name'];
			$records['Doctor Count'] = $rows['doctor_count'];
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}