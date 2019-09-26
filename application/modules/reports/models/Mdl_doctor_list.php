<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_doctor_list extends MY_Model {

    function __construct() {
        parent::__construct();
    }
    
    function get_filters() {
        return [
			[
                'field_name' => 'temp2|zsm_name',
                'field_label' => 'ZSM Name'
            ],
            [
                'field_name' => 'temp2|zone',
                'field_label' => 'Zone'
            ],
            [
                'field_name' => 'temp2|asm_name',
                'field_label' => 'ASM Name'
            ],
            [
                'field_name' => 'temp2|area',
                'field_label' => 'Area'
            ],
            [
                'field_name' => 'temp2|mr_name',
                'field_label' => 'MR Name'
            ],
            [
                'field_name' => 'temp2|city',
                'field_label' => 'HQ'
            ],
            [
                'field_name' => 'temp2|doctor_name',
                'field_label' => 'Doctor Name'
			],			
			[
                'field_name' => 'temp2|type',
                'field_label' => 'Type'
			],
            [
                'field_name' => 'temp2|speciality',
                'field_label' => 'Speciality'
			],
			[
                'field_name' => 'temp2|asm_status',
                'field_label' => 'ABM Status'
			],
			[
                'field_name' => 'temp2|zsm_status',
                'field_label' => 'ZBM Status'
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
		temp2.* ,
		GROUP_CONCAT(im.image_name) images
		FROM
		(
		SELECT 
		temp.asm_id, temp.zsm_id,
		temp.zsm_name,temp.asm_name, temp.mr_name, 
		temp.zone, temp.area, temp.city,
		temp.doctor_id, temp.doctor_name, temp.doctor_date, temp.speciality, temp.type, temp.asm_status, temp.zsm_status,
		SUM(temp.hyper) hyper, SUM(temp.acne) acne, SUM(temp.anti) anti
		
		FROM (
		SELECT
		mr.users_name as mr_name,
		asm.users_id as asm_id, asm.users_name as asm_name, zsm.users_id as zsm_id, zsm.users_name as zsm_name,
		z.zone_name as zone, a.area_name as area, c.city_name as city,
		d.doctor_id, d.doctor_name, d.insert_dt as doctor_date, d.asm_status as asm_status, d.zsm_status as zsm_status,
		sp.speciality_name as speciality, spc.category_name as type,
		cat.category_id,cat.category_name,
		b.brand_name as other_name, ub.brand_name,
		IF(cat.category_id = 1,SUM(ub.rxn),0) as hyper,
		IF(cat.category_id = 2,SUM(ub.rxn),0) as acne,
		IF(cat.category_id = 3,SUM(ub.rxn),0) as anti
		FROM
		doctor d
		JOIN speciality sp ON sp.speciality_id = d.speciality
		LEFT JOIN speciality_category spc ON spc.sc_id = d.speciality_category
		JOIN users_molecule um ON um.doctor_id = d.doctor_id
		JOIN category cat ON cat.category_id = um.category_id
		JOIN users_brand ub ON ub.molecule_id = um.molecule_id
		LEFT JOIN brand b ON b.brand_id = ub.brand_id
		JOIN manpower mr ON mr.users_id = d.users_id
		JOIN manpower asm ON asm.users_id = mr.users_parent_id
		JOIN manpower zsm ON zsm.users_id = asm.users_parent_id
		JOIN zone z ON z.zone_id = zsm.users_zone_id
		JOIN area a ON a.area_id = asm.users_area_id
		JOIN city c ON c.city_id = mr.users_city_id
		GROUP BY ub.id
		)temp
		GROUP BY temp.doctor_id
		) temp2
		LEFT JOIN images im ON im.doctor_id = temp2.doctor_id AND im.category = 'doctor'";

        $sql .= " WHERE 1 = 1 ";
		
		$role = $this->session->get_field_from_session('role', 'user');
		if(!empty($role)){
			$id = $this->session->get_field_from_session('user_id', 'user');
			if($role == 'ASM'){
				$sql .= "AND temp2.asm_id = '".$id."'";
			}
			if($role == 'ZSM'){
				$sql .= "AND temp2.zsm_id = '".$id."'";
			}
		}
		

		if(is_array($rfilters) && count($rfilters) ) {
			$field_filters = $this->get_filters_from($rfilters);
			
            foreach($rfilters as $key=> $value) {
                $value = trim($value);
                if(!in_array($key, $field_filters)) {
                    continue;
                }
				
				if($key == 'from_date' && !empty($value)) {
					$sql .= " AND DATE(temp2.doctor_date) >= '".date('Y-m-d', strtotime($value))."' ";
                    continue;
                }

                if($key == 'to_date' && !empty($value)) {
					$sql .= " AND DATE(temp2.doctor_date) <= '".date('Y-m-d', strtotime($value))."' ";
                    continue;
                }

                if(!empty($value)) {
                    $key = str_replace('|', '.', $key);
                    $value = $this->db->escape_like_str($value);
                    $sql .= " AND $key LIKE '%$value%' ";
                }
            }
        }

		$sql .= " GROUP BY temp2.doctor_id ";
		$sql .= " ORDER BY temp2.doctor_date DESC";

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
			$user_role = $this->session->get_field_from_session('role','user');		
			if(empty($user_role) || $user_role == 'HO') {  
				$records['ZBM Name'] = $rows['zsm_name'];
				$records['Zone'] = $rows['zone'];
			}
			
			$records['ABM Name'] = $rows['asm_name'];
			$records['Area'] = $rows['area'];
			$records['MR Name'] = $rows['mr_name'];
			$records['HQ City'] = $rows['city'];
			$records['Doctor Name'] = $rows['doctor_name'];
			$records['Type'] = $rows['type'];
			$records['Speciality'] = $rows['speciality'];
			$records['ABM Status'] = ucfirst($rows['asm_status']);
			$records['ZBM Status'] = ucfirst($rows['zsm_status']);
			$images = "";
			if(!empty($rows['images'])){
				$rx_files = explode(',', $rows['images']);
				if(count($rx_files)){
					foreach ($rx_files as $key => $value){
						if(file_exists($value)){
							$ext = pathinfo($value, PATHINFO_EXTENSION);
							$images .= base_url($value).", ";
						}
					}
					$records['Images'] = rtrim($images, ', ');
				}
			}
			
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}