<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_summary_report extends MY_Model {

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
        
        $sql = "SELECT 
        temp.zsm_name, temp.zone, temp.asm_name, temp.area, 
        MAX(temp.chemist_count) chemist_count, 
        MAX(temp.total_reps) total_reps, MAX(temp.no_of_days) no_of_days,
        (MAX(temp.chemist_count)/ (MAX(temp.total_reps) * MAX(temp.no_of_days))) chemist_avg,
        MAX(temp.doctor_count) doctor_count, 
        MAX(temp.asm_count) asm_count, MAX(temp.zsm_count) zsm_count
        FROM (
        SELECT 
        zsm.users_name AS zsm_name, z.zone_name AS zone,
        asm.users_id AS asm_id, asm.users_name AS asm_name, a.area_name AS area, 
        NULL AS chemist_count, COUNT(d.doctor_id) AS doctor_count, 
        SUM(IF(d.asm_status = 'approve', 1, 0)) AS asm_count, SUM(IF(d.zsm_status = 'approve', 1, 0)) AS zsm_count,
        NULL as total_reps, NULL as no_of_days
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

        $sql .= " GROUP BY asm.users_id 
        UNION ALL
        SELECT 
        zsm.users_name AS zsm_name, z.zone_name AS zone,
        asm.users_id AS asm_id, asm.users_name AS asm_name, a.area_name AS AREA, 
        COUNT(ch.chemist_id) chemist_count, 
        NULL AS doctor_count, NULL AS asm_count, NULL AS zsm_count,
        NULL as total_reps, 
        DATEDIFF(MAX(ch.insert_dt),MIN(ch.insert_dt)) no_of_days
        FROM
        chemist ch
        JOIN manpower mr ON mr.users_id = ch.users_id
        JOIN manpower asm ON asm.users_id = mr.users_parent_id
        JOIN manpower zsm ON zsm.users_id = asm.users_parent_id
        JOIN zone z ON z.zone_id = zsm.users_zone_id
        JOIN area a ON a.area_id = asm.users_area_id
        WHERE 1= 1 ";

        if(is_array($rfilters) && count($rfilters) ) {
            $field_filters = $this->get_filters_from($rfilters);
            
            foreach($rfilters as $key=> $value) {
                $value = trim($value);
                if(!in_array($key, $field_filters)) {
                    continue;
                }

                if($key == 'from_date' && !empty($value)) {
                    $sql .= " AND DATE(ch.insert_dt) >= '".date('Y-m-d', strtotime($value))."' ";
                    continue;
                }

                if($key == 'to_date' && !empty($value)) {
                    $sql .= " AND DATE(ch.insert_dt) <= '".date('Y-m-d', strtotime($value))."' ";
                    continue;
                }
            }
        }

        $sql .= " GROUP BY asm.users_id
        UNION ALL
        SELECT 
        zsm.users_name AS zsm_name, z.zone_name AS zone,
        asm.users_id AS asm_id, asm.users_name AS asm_name, a.area_name AS area,
        NULL as chemist_count, NULL AS doctor_count, NULL AS asm_count, NULL AS zsm_count,
        COUNT(temp.mr_id) as total_reps, NULL as no_of_days
        FROM 
        (
                SELECT mr.users_id as mr_id, mr.users_name as mr_name, mr.users_parent_id as mr_parent, MAX(at.insert_dt) max_date, MIN(at.insert_dt) min_date
                FROM
                manpower mr
                JOIN access_token at ON at.user_id = mr.users_id
                GROUP BY mr.users_id
        )temp
        JOIN manpower asm ON asm.users_id = temp.mr_parent
        JOIN manpower zsm ON zsm.users_id = asm.users_parent_id
        JOIN zone z ON z.zone_id = zsm.users_zone_id
        JOIN area a ON a.area_id = asm.users_area_id
        GROUP BY asm.users_id) temp";

		if(is_array($rfilters) && count($rfilters) ) {
			$field_filters = $this->get_filters_from($rfilters);
			
            foreach($rfilters as $key=> $value) {
                $value = trim($value);
                if(!in_array($key, $field_filters)) {
                    continue;
                }
               
                if(!empty($value) && !in_array($key, $field_filters)) {
                    $key = str_replace('|', '.', $key);
                    $value = $this->db->escape_like_str($value);
                    $sql .= " AND $key LIKE '$value%' ";
                }
            }
        }

        $sql .= " GROUP BY temp.asm_id ";

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
			$records['No. of Reps'] = $rows['total_reps'];
			$records['No. of Days'] = $rows['no_of_days'];
			$records['Chemist Average'] = $rows['chemist_avg'];
			$records['Total Doctor Count'] = $rows['doctor_count'];
			$records['ABM Approved Count'] = $rows['asm_count'];
			$records['ZBM Approved Count'] = $rows['zsm_count'];
            
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}