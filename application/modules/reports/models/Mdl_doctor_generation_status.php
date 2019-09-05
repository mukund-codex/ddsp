<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_doctor_generation_status extends MY_Model {

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
            [
                'field_name'=>'doctor',
                'field_label'=> 'Doctor',
            ],
            [
                'field_name'=>'mobile',
                'field_label'=> 'Mobile',
            ],
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

	function get_collection($count = FALSE, $f_filters = [], $rfilters ='', $limit = 0, $offset = 0 ) {
        
        $sql = "SELECT ";

        if(!$count) {
            $sql .= " users_name, users_id, users_type, users_emp_id, mgr_name, mgr_type,
                zone_name, region_name, area_name, city_name, 
                doctor_id, doctor, mobile,
                IF( SUM(IF(share_type = 'W', 1, 0)) = 0, 'NO', 'YES' ) AS 'share_status',
                IF( SUM(IF(share_type = 'D', 1, 0)) = 0, 'NO', 'YES' ) AS 'download_status' ";
        } 
        else {
            $sql .= " COUNT(users_name) AS total_count ";
        } 

		$sql .= "                
            FROM (
                SELECT
                    m.users_name, m.users_id, m.users_type, m.users_emp_id, mgr.users_name as mgr_name, mgr.users_type as mgr_type,
                    m.users_zone_id, m.users_region_id, m.users_area_id, m.users_city_id, 
                    d.doctor_id, d.doctor, d.mobile, s.share_type
                FROM doctor d
                JOIN manpower m ON d.insert_user_id = m.users_id
				LEFT JOIN shared s ON s.doctor_id = d.doctor_id
				JOIN manpower mgr ON mgr.users_id = m.users_parent_id
            ) collection
            LEFT JOIN zone z ON z.zone_id = users_zone_id
            LEFT JOIN region r ON r.region_id = users_region_id
            LEFT JOIN `area` a ON a.area_id = users_area_id
            LEFT JOIN city c ON c.city_id = users_city_id " ;
        $sql .= " WHERE 1 = 1 ";
            
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

        $sql .= " GROUP BY doctor_id ";

        if(! $count) {
            if(!empty($limit)) { $sql .= " LIMIT $offset, $limit"; }        
        }
        
        $q = $this->db->query($sql);
        
        $collection = (! $count) ? $q->result_array() : $q->num_rows();

		return $collection;
    }	
    
	function _format_data_to_export($data){
		
		$resultant_array = [];
		
		foreach ($data as $rows) {
			$records['Name'] = $rows['users_name'];
			$records['Designation'] = $rows['users_type'];
			$records['EMP ID'] = $rows['users_emp_id'];
			$records['Zone'] = $rows['zone_name'];
			$records['Region'] = $rows['region_name'];
			$records['Area'] = $rows['area_name'];
			$records['City'] = $rows['city_name'];
			$records['Doctor'] = $rows['doctor'];
			$records['Doctor Mobile'] = $rows['mobile'];
			$records['Download Status'] = $rows['download_status'];
			$records['Share Status'] = $rows['share_status'];
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}