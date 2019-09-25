<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_chemist_list extends MY_Model {

    function __construct() {
        parent::__construct();
    }
    
    function get_filters() {
        return [
            [
                'field_name' => 'asm|users_name',
                'field_label' => 'ASM Name'
            ],
            [
                'field_name' => 'a|area_name',
                'field_label' => 'Area'
            ],
            [
                'field_name' => 'm|users_name',
                'field_label' => 'MR Name'
            ],
            [
                'field_name' => 'c|city_name',
                'field_label' => 'HQ'
            ],
            [
                'field_name' => 'ch|chemist_name',
                'field_label' => 'Chemist Name'
            ],
            [
                'field_name' => 'ch|address',
                'field_label' => 'Chemist Location'
            ]
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
        asm.users_name as asm_name, a.area_name as area,
        m.users_name as mr_name, c.city_name as city,
        ch.chemist_name as chemist_name, ch.address as chemist_location
        FROM chemist ch
        JOIN manpower m ON m.users_id = ch.users_id
        JOIN city c ON c.city_id = m.users_city_id
        JOIN manpower asm ON asm.users_id = m.users_parent_id
        JOIN area a ON a.area_id = asm.users_area_id";

        $sql .= " WHERE 1 = 1";
       
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

        //$sql .= " group by m.users_id";

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
			$records['ABM Name'] = $rows['asm_name'];
			$records['Area'] = $rows['area'];
            $records['MR Name'] = $rows['mr_name'];
            $records['HQ'] = $rows['city'];            
            $records['Chemist Name'] = $rows['chemist_name'];            
			$records['Chemist Location'] = $rows['chemist_location'];
            
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}