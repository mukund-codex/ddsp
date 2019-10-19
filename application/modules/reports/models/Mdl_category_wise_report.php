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
        
        $sql = "select 
        zsm.users_name as zsm_name, z.zone_name as zone, 
        asm.users_name as asm_name, a.area_name as area,
        mr.users_name as mr_name, c.city_name as city,
        chemist.chemist_name, chemist.address as chemist_address,
        doctor.doctor_name, doctor.address as doctor_address,

        SUM(ifnull(case when (category.category_id = 1 AND users_molecule.category_id = 1 AND users_brand.molecule_id = users_molecule.molecule_id) 
        THEN users_brand.rxn END, 0)) as hyper,
        SUM(ifnull(case when (category.category_id = 2 AND users_molecule.category_id = 2 AND users_brand.molecule_id = users_molecule.molecule_id) 
        THEN users_brand.rxn END, 0)) as acne,
        SUM(ifnull(case when (category.category_id = 3 AND users_molecule.category_id = 3 AND users_brand.molecule_id = users_molecule.molecule_id) 
        THEN users_brand.rxn END, 0)) as anti,
        chemist.insert_dt as chemist_date

        from doctor
        JOIN manpower mr on mr.users_id = doctor.users_id
        JOIN manpower asm on asm.users_id = mr.users_parent_id
        JOIN manpower zsm on zsm.users_id = asm.users_parent_id
        JOIN chemist on chemist.users_id = mr.users_id
        JOIN zone z ON z.zone_id = zsm.users_zone_id
        JOIN area a ON a.area_id = asm.users_area_id
        JOIN city c ON c.city_id = mr.users_city_id

        JOIN users_molecule on users_molecule.doctor_id = doctor.doctor_id
        and users_molecule.users_id = users_molecule.users_id
        and users_molecule.chemist_id = users_molecule.chemist_id

        JOIN category on category.category_id = users_molecule.category_id
        JOIN users_brand on users_brand.molecule_id = users_molecule.molecule_id
        and users_brand.doctor_id = doctor.doctor_id
        and users_brand.chemist_id = chemist.chemist_id
        and users_brand.users_id = mr.users_id";

        $sql .= " WHERE 1 = 1 AND mr.users_type = 'MR' ";
       
		if(is_array($rfilters) && count($rfilters) ) {
			$field_filters = $this->get_filters_from($rfilters);
			
            foreach($rfilters as $key=> $value) {
                $value = trim($value);
                if(!in_array($key, $field_filters)) {
                    continue;
                }

                if($key == 'from_date' && !empty($value)) {
					$sql .= " AND DATE(chemist_date) >= '".date('Y-m-d', strtotime($value))."' ";
                    continue;
                }

                if($key == 'to_date' && !empty($value)) {
					$sql .= " AND DATE(chemist_date) <= '".date('Y-m-d', strtotime($value))."' ";
                    continue;
                }
               
                if(!empty($value) && !in_array($key, ['from_date', 'to_date'])) {
                    $key = str_replace('|', '.', $key);
                    $value = $this->db->escape_like_str($value);
                    $sql .= " AND $key LIKE '%$value%' ";
                }
            }
        }

        $role = $this->session->get_field_from_session('role', 'user');
		if(!empty($role)){
			$id = $this->session->get_field_from_session('user_id', 'user');
			if($role == 'ASM'){
				$sql .= "AND asm.users_id = '".$id."'";
			}
			if($role == 'ZSM'){
				$sql .= "AND zsm.users_id = '".$id."'";
			}
		}

        $sql .= " group by doctor.doctor_id";
        $sql .= " order by chemist_date DESC ";

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