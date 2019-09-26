<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_login_reports extends MY_Model {

    function __construct() {
        parent::__construct();
    }
    
    function get_filters() {
        return [
            [
                'field_name' => 'nsm|users_name',
                'field_label' => 'NSM Name'
            ],
            [
                'field_name' => 'nz|national_zone_name',
                'field_label' => 'National Zone'
            ],
            [
                'field_name' => 'zsm|users_name',
                'field_label' => 'ZSM Name'
            ],
            [
                'field_name' => 'z|zone_name',
                'field_label' => 'Zone'
            ],
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
                'field_name' => 'm|users_mobile',
                'field_label' => 'MR Mobile'
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
        
        $sql = "SELECT 
        nsm.users_name as nsm_name, nz.national_zone_name as national_zone,
        zsm.users_name as zsm_name, z.zone_name as zone,
        asm.users_name as asm_name, a.area_name as area,
        m.users_name as mr_name, c.city_name as city,
        m.users_mobile as mr_mobile,
        MAX(at.update_dt) as login_time
        FROM
        manpower m
        LEFT JOIN access_token at ON at.user_id = m.users_id
        LEFT JOIN manpower asm ON asm.users_id = m.users_parent_id
        LEFT JOIN manpower zsm ON zsm.users_id = asm.users_parent_id
        LEFT JOIN manpower nsm ON nsm.users_id = zsm.users_parent_id
        LEFT JOIN city c ON c.city_id = m.users_city_id
        LEFT JOIN area a ON a.area_id = asm.users_area_id
        LEFT JOIN zone z ON z.zone_id = zsm.users_zone_id
        LEFT JOIN national_zone nz ON nz.national_zone_id = nsm.users_national_id";

        $sql .= " WHERE 1 = 1 AND m.users_type = 'MR'";
       
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

        $sql .= " group by m.users_id ";
        $sql .= " ORDER by MAX(at.update_dt) DESC";

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
            $records['NSM Name'] = $rows['nsm_name'];
            $records['National Zone'] = $rows['national_zone'];
            $records['ZBM Name'] = $rows['zsm_name'];
            $records['Zone'] = $rows['zone'];
			$records['ABM Name'] = $rows['asm_name'];
			$records['Area'] = $rows['area'];
            $records['MR Name'] = $rows['mr_name'];
            $records['HQ'] = $rows['city'];            
            $records['MR Mobile'] = $rows['mr_mobile'];            
			$records['Last Login Time'] = $rows['login_time'];
            
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}