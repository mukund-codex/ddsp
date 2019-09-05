<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_livetracker extends MY_Model {
    
    private $column_list = ['Name', 'Mobile','Message','Poster', 'Photo','Date'];

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

	function get_collection($count = FALSE, $f_filters = [], $rfilters = [], $limit = 0, $offset = 0 ) {

        $sql = "SELECT ";
        
        $sql .= (!$count) ? " * " : " COUNT(users_name) AS total_count ";

        $sql .= " 
            FROM (
            SELECT
                m.users_name, m.users_type, m.users_emp_id, mgr.users_name as mgr_name, mgr.users_type as mgr_type,
                c.city_name, a.area_name, r.region_name, z.zone_name, 
                d.doctor, d.mobile, d.message, IF(d.poster IS NOT NULL, 'YES', 'NO') AS poster_status, d.insert_dt
            FROM doctor d
            JOIN manpower m ON 1=1
                AND d.insert_user_id = m.users_id AND m.users_type = 'MR'
            JOIN city c ON m.users_city_id = c.city_id
            JOIN `area` a ON a.area_id = c.area_id
            JOIN region r ON r.region_id = a.region_id
            JOIN zone z ON z.zone_id = r.zone_id
            JOIN manpower mgr ON mgr.users_id = m.users_parent_id

            UNION 

            SELECT
                m.users_name, m.users_type, m.users_emp_id, mgr.users_name as mgr_name, mgr.users_type as mgr_type,
                NULL, a.area_name, r.region_name, z.zone_name, 
                d.doctor, d.mobile, d.message, IF(d.poster IS NOT NULL, 'YES', 'NO') AS poster_status, d.insert_dt
            FROM doctor d
            JOIN manpower m ON 1=1
                AND d.insert_user_id = m.users_id AND m.users_type = 'ASM'
            JOIN `area` a ON m.users_area_id = a.area_id
            JOIN region r ON r.region_id = a.region_id
            JOIN zone z ON z.zone_id = r.zone_id
            JOIN manpower mgr ON mgr.users_id = m.users_parent_id

            UNION 

            SELECT
                m.users_name, m.users_type, m.users_emp_id, mgr.users_name as mgr_name, mgr.users_type as mgr_type,
                NULL, NULL, r.region_name, z.zone_name, 
                d.doctor, d.mobile, d.message, IF(d.poster IS NOT NULL, 'YES', 'NO') AS poster_status, d.insert_dt
            FROM doctor d
            JOIN manpower m ON 1=1
                AND d.insert_user_id = m.users_id AND m.users_type = 'RSM'
            JOIN region r ON m.users_region_id = r.region_id
            JOIN zone z ON z.zone_id = r.zone_id 
            JOIN manpower mgr ON mgr.users_id = m.users_parent_id
        ) collection WHERE 1=1 ";

		/* if(sizeof($sfilters)) { 
            
            foreach ($sfilters as $key=>$value) { 
                $q->where("$key", $value); 
			}
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
            $records['Manager'] = $rows['mgr_name'];
            $records['Manager Type'] = $rows['mgr_type'];
            $records['Employee'] = $rows['users_name'];
            $records['Designation'] = $rows['users_type'];
			$records['EMP ID'] = $rows['users_emp_id'];
			$records['Zone'] = $rows['zone_name'];
			$records['Region'] = $rows['region_name'];
			$records['Area'] = $rows['area_name'];
			$records['City'] = $rows['city_name'];
            
			$records['Doctor'] = $rows['doctor'];
			$records['Mobile'] = $rows['mobile'];
            $records['Image'] = $rows['poster_status'];
            
            $message =$rows['message'];
            $records['Message'] = $message;
            
			$records['Date'] = $rows['insert_dt'];
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}