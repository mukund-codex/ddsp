<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_category_wise_report extends MY_Model {

    function __construct() {
        parent::__construct();
    }
    
    function get_filters() {
        return [
            [
                'field_name'=>'zsm|users_name',
                'field_label'=> 'ZBM',
            ],
            [
                'field_name'=>'z|zone_name',
                'field_label'=> 'Zone',
            ],
            [
                'field_name'=>'asm|users_name',
                'field_label'=> 'ABM',
            ],
            [
                'field_name'=>'a|area_name',
                'field_label'=> 'Area',
            ],
            [
                'field_name'=>'mr|users_name',
                'field_label'=> 'MR Name',
            ],
            [
                'field_name'=>'c|city_name',
                'field_label'=> 'HQ',
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
        zsm.users_name as zsm_name, z.zone_name as zone,
        asm.users_name as asm_name, a.area_name as area,
        mr.users_name as mr_name, c.city_name as city, temp.chemist_date,
        temp.chemist_name, temp.chemist_address, temp.doctor_name, 
        temp.doctor_address, SUM(temp.hyper) hyper, 
        SUM(temp.acne) acne, SUM(temp.anti) anti
        FROM
        (
        SELECT 
        ch.users_id,ch.chemist_id, ch.chemist_name, ch.address AS chemist_address, ch.insert_dt as chemist_date,
        d.doctor_id, d.doctor_name, d.address AS doctor_address,
        cat.category_id, cat.category_name,
        IF(cat.category_id = 1, 1,0) AS hyper, 
        IF(cat.category_id = 2, 1,0) AS acne, 
        IF(cat.category_id = 3, 1,0) AS anti
        FROM 
        chemist ch
        JOIN doctor d ON d.chemist_id = ch.chemist_id
        JOIN users_molecule um ON um.doctor_id = d.doctor_id
        JOIN category cat ON cat.category_id = um.category_id
        ) temp
        JOIN manpower mr ON mr.users_id = temp.users_id
        JOIN manpower asm ON asm.users_id = mr.users_parent_id
        JOIN manpower zsm ON zsm.users_id = asm.users_parent_id
        JOIN zone z ON z.zone_id = zsm.users_zone_id
        JOIN area a ON a.area_id = asm.users_area_id
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
					$sql .= " AND DATE(temp.chemist_date) >= '".date('Y-m-d', strtotime($value))."' ";
                    continue;
                }

                if($key == 'to_date' && !empty($value)) {
					$sql .= " AND DATE(temp.chemist_date) <= '".date('Y-m-d', strtotime($value))."' ";
                    continue;
                }
               
                if(!empty($value)) {
                    $key = str_replace('|', '.', $key);
                    $value = $this->db->escape_like_str($value);
                    $sql .= " AND $key LIKE '$value%' ";
                }
            }
        }

        $sql .= " group by temp.doctor_id";

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
			$records['ZBM'] = $rows['zsm_name'];
			$records['Zone'] = $rows['zone'];
			$records['ABM'] = $rows['asm_name'];
			$records['Area'] = $rows['area'];
			$records['MR Name'] = $rows['mr_name'];
			$records['HQ'] = $rows['city'];
			$records['AntiFungal'] = $rows['anti'];
			$records['Acne Light'] = $rows['acne'];
			$records['Hyper-Pigmentation'] = $rows['hyper'];
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}