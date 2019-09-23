<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_login_reports extends MY_Model {

    function __construct() {
        parent::__construct();
    }
    
    function get_filters() {
        return [
            [],
            [],
            [],
            [],
            [],
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
        
        $sql = "SELECT 
        m.users_name as mr_name, asm.users_name as asm_name,
        zsm.users_name as zsm_name,
        m.users_mobile, m.users_type, m.users_emp_id,
        MAX(at.update_dt) as login_time
        FROM
        manpower m
        LEFT JOIN access_token at ON at.user_id = m.users_id
        JOIN manpower asm ON asm.users_id = m.users_parent_id
        JOIN manpower zsm ON zsm.users_id = asm.users_parent_id";

        $sql .= " WHERE 1 = 1 AND m.users_type = 'MR' AND at.token_status = 'active' ";
       
		if(is_array($rfilters) && count($rfilters) ) {
			$field_filters = $this->get_filters_from($rfilters);
			
            foreach($rfilters as $key=> $value) {
                $value = trim($value);
                if(!in_array($key, $field_filters)) {
                    continue;
                }
               
                if(!empty($value)) {
                    $key = str_replace('|', '.', $key);
                    $value = $this->db->escape_like_str($value);
                    $sql .= " AND $key LIKE '$value%' ";
                }
            }
        }

        $sql .= " group by m.users_id";

        if(! $count) {
            if(!empty($limit)) { $sql .= " LIMIT $offset, $limit"; }        
        }
        
        $q = $this->db->query($sql);
        //echo $sql;
        $collection = (! $count) ? $q->result_array() : $q->num_rows();

		return $collection;
    }	
    
	function _format_data_to_export($data){
		
		$resultant_array = [];
		
		foreach ($data as $rows) {
			$records['MR Name'] = $rows['mr_name'];
			$records['ABM Name'] = $rows['asm_name'];
			$records['ZBM Name'] = $rows['zsm_name'];
			$records['Last Login Time'] = $rows['login_time'];
            
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}