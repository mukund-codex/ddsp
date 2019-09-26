<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_zone_wise_doctor extends MY_Model {

    function __construct() {
        parent::__construct();
    }
    
    function get_filters() {
        return [
            [
                'field_name'=>'temp|zsm_name',
                'field_label'=> 'ZBM',
            ],
            [
                'field_name'=>'temp|zone',
                'field_label'=> 'Zone',
            ],
            [
                'field_name'=>'temp|asm_name',
                'field_label'=> 'ABM',
            ],
            [
                'field_name'=>'temp|area',
                'field_label'=> 'Area',
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
        
        $sql = "SELECT temp.zsm_name, temp.zone, temp.asm_name, temp.area, 
        MAX(temp.chemist_count) chemist_count,
        MAX(temp.doctor_count) doctor_count, MAX(temp.asm_count) asm_count, MAX(temp.zsm_count) zsm_count
        FROM ( 
        SELECT 
        zsm.users_name as zsm_name, z.zone_name as zone,
        asm.users_id as asm_id, asm.users_name as asm_name, a.area_name as area,
        NULL as chemist_count, COUNT(d.doctor_id) as doctor_count, 
        SUM(IF(d.asm_status = 'approve', 1, 0)) as asm_count,
        SUM(IF(d.zsm_status = 'approve', 1, 0)) as zsm_count
        FROM
        doctor d
        JOIN manpower mr ON mr.users_id = d.users_id
        JOIN manpower asm ON asm.users_id = mr.users_parent_id
        JOIN manpower zsm ON zsm.users_id = asm.users_parent_id
        JOIN zone z ON z.zone_id = zsm.users_zone_id
        JOIN area a ON a.area_id = asm.users_area_id
        WHERE 1 =1 ";

        if(is_array($rfilters) && count($rfilters) ) {
            $field_filters = $this->get_filters_from($rfilters);
            
            foreach($rfilters as $key=> $value) {
                $value = trim($value);
                if(!in_array($key, $field_filters)) {
                    continue;
                }

                if($key == 'from_date' && !empty($value)) {
                    $sql .= " AND DATE(d.insert_dt) >= '".date('Y-m-d', strtotime($value))."' ";
                    continue;
                }

                if($key == 'to_date' && !empty($value)) {
                    $sql .= " AND DATE(d.insert_dt) <= '".date('Y-m-d', strtotime($value))."' ";
                    continue;
                }
            }
        }
        
        $sql .=" GROUP BY asm.users_id
        
        
        UNION ALL
        
        SELECT 
        zsm.users_name as zsm_name, z.zone_name as zone,
        asm.users_id as asm_id, asm.users_name as asm_name, a.area_name as area,
        COUNT(ch.chemist_id) chemist_count, NULL as doctor_count, NULL as asm_count, NULL as zsm_count
        FROM
        chemist ch
        JOIN manpower mr ON mr.users_id = ch.users_id
        JOIN manpower asm ON asm.users_id = mr.users_parent_id
        JOIN manpower zsm ON zsm.users_id = asm.users_parent_id
        JOIN zone z ON z.zone_id = zsm.users_zone_id
        JOIN area a ON a.area_id = asm.users_area_id
        WHERE 1 =1 ";

        if(is_array($rfilters) && count($rfilters) ) {
            $field_filters = $this->get_filters_from($rfilters);
            
            foreach($rfilters as $key=> $value) {
                $value = trim($value);
                if(!in_array($key, $field_filters)) {
                    continue;
                }

                if($key == 'from_date' && !empty($value)) {
                    $sql .= " AND DATE(ch.insert_dt) >= '".date('Y-m-d', strtotime($value))."' ";
                }

                if($key == 'to_date' && !empty($value)) {
                    $sql .= " AND DATE(ch.insert_dt) <= '".date('Y-m-d', strtotime($value))."' ";
                }
            }
        }

        $sql.= " GROUP BY asm.users_id
        ) temp";

        $sql .= " WHERE 1 = 1 ";
       
		if(is_array($rfilters) && count($rfilters) ) {
			$field_filters = $this->get_filters_from($rfilters);
            
            //echo '<pre>';print_r($field_filters);exit;

            foreach($rfilters as $key=> $value) {
                $value = trim($value);
                /* if(in_array($key, ['from_date', 'to_date'])) {
                    continue;
                } */
               
                if(!empty($value) && !in_array($key, ['from_date', 'to_date'])) {
                    $key = str_replace('|', '.', $key);
                    $value = $this->db->escape_like_str($value);
                    $sql .= " AND $key LIKE '%$value%' ";
                }
            }
        }

        $sql .= " GROUP BY temp.asm_id ";
        $sql .= " ORDER BY temp.zone";

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
			$records['ZBM'] = $rows['zsm_name'];
			$records['Zone'] = $rows['zone'];
			$records['ABM'] = $rows['asm_name'];
			$records['Area'] = $rows['area'];
			$records['Chemist Count'] = $rows['chemist_count'];
			$records['Doctor Count'] = $rows['doctor_count'];
			$records['ABM Approved Count'] = $rows['asm_count'];
			$records['ZBM Approved Count'] = $rows['zsm_count'];			
            
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}