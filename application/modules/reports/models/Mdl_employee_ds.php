<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_employee_ds extends MY_Model {

    function __construct() {
        parent::__construct();
    }
    
     function get_filters() {
        return [
            [],
            [],
            [
                'field_name'=>'users_name',
                'field_label'=> 'Employee',
            ],
            [
                'field_name'=>'users_type',
                'field_label'=> 'Designation',
            ],
            [
                'field_name'=>'users_emp_id',
                'field_label'=> 'EMP ID',
            ],
            [
                'field_name'=>'zone_name',
                'field_label'=> 'Zone',
            ],
            [
                'field_name'=>'region_name',
                'field_label'=> 'Region',
            ],
            [
                'field_name'=>'area_name',
                'field_label'=> 'Area',
            ],
            [
                'field_name'=>'city_name',
                'field_label'=> 'City',
            ],
            [],
            [],
            [],
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

	function get_collection($count = FALSE, $f_filters = [], $rfilters, $limit = 0, $offset = 0 ) {
        $sql = "SELECT ";
        
        if(!$count) {
            $sql .= " mgr_name, mgr_type, users_name, users_type, users_emp_id, zone_name, region_name, area_name, city_name, 
            COUNT(DISTINCT doctor_id) AS doctor_count,
            SUM(IF(share_type = 'W', 1, 0)) AS whatsapp_count,
            SUM(IF(share_type = 'D', 1, 0)) AS download_count ";
        } else {
            
            $sql .= " COUNT(users_name) AS total_count ";
        }

		$sql .= "
            
        FROM (
            SELECT
                m.users_id, m.users_name, m.users_mobile,
                m.users_type, m.users_emp_id, mgr.users_name as mgr_name, mgr.users_type as mgr_type,
                z.zone_name, r.region_name, a.area_name, c.city_name,
                d.doctor_id, 
                s.share_type
                
            FROM manpower m 
            LEFT JOIN doctor d ON m.users_id = d.insert_user_id
            LEFT JOIN shared s ON d.doctor_id = s.doctor_id AND s.insert_user_id = m.users_id
            JOIN city c ON m.users_city_id = c.city_id
            JOIN `area` a ON a.area_id = c.area_id
            JOIN region r ON r.region_id = a.region_id
            JOIN zone z ON z.zone_id = r.zone_id
            JOIN manpower mgr ON mgr.users_id = m.users_parent_id
            WHERE m.users_type = 'MR'

            UNION ALL

            SELECT
                m.users_id, m.users_name, m.users_mobile,
                m.users_type, m.users_emp_id, mgr.users_name as mgr_name, mgr.users_type as mgr_type,
                z.zone_name, r.region_name, a.area_name, NULL,
                d.doctor_id, 
                s.share_type
            FROM manpower m 
            LEFT JOIN doctor d ON m.users_id = d.insert_user_id
            LEFT JOIN shared s ON d.doctor_id = s.doctor_id AND s.insert_user_id = m.users_id
            JOIN `area` a ON m.users_area_id = a.area_id
            JOIN region r ON r.region_id = a.region_id
            JOIN zone z ON z.zone_id = r.zone_id
            JOIN manpower mgr ON mgr.users_id = m.users_parent_id
            WHERE m.users_type = 'ASM'

            UNION ALL
            
            SELECT
                m.users_id, m.users_name, m.users_mobile,
                m.users_type, m.users_emp_id, mgr.users_name as mgr_name, mgr.users_type as mgr_type,
                z.zone_name, r.region_name, NULL, NULL,
                d.doctor_id, 
                s.share_type
            FROM manpower m 
            LEFT JOIN doctor d ON m.users_id = d.insert_user_id
            LEFT JOIN shared s ON d.doctor_id = s.doctor_id AND s.insert_user_id = m.users_id
            JOIN region r ON m.users_region_id = r.region_id
            JOIN zone z ON z.zone_id = r.zone_id 
            JOIN manpower mgr ON mgr.users_id = m.users_parent_id
            WHERE m.users_type = 'RSM') users_collection WHERE 1=1 ";

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

        $sql .= " GROUP BY users_collection.users_id ";

        if(! $count) {
            if(!empty($limit)) { $sql .= " LIMIT $offset, $limit"; }        
        }
        
        $q = $this->db->query($sql);
        // echo $this->db->last_query(); die();
        $collection = (! $count) ? $q->result_array() : $q->num_rows();
		return $collection;
    }	
    
	function _format_data_to_export($data){
		
		$resultant_array = [];
		
		foreach ($data as $rows) {
			$records['Manager'] = $rows['mgr_name'];
            $records['Manager Type'] = $rows['mgr_type'];
			$records['Employee'] = $rows['users_name'];
            $records['Designation'] = $rows['users_type'];
			$records['EMP ID'] = $rows['users_emp_id'];
			$records['Zone'] = $rows['zone_name'];
			$records['Region'] = $rows['region_name'];
			$records['Area'] = $rows['area_name'];
            $records['City'] = $rows['city_name'];
            
			$records['Doctor Count'] = $rows['doctor_count'];
			$records['Whatsapp Count'] = $rows['whatsapp_count'];
			$records['Download Count'] = $rows['download_count'];
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}