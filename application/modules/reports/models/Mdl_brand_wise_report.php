<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_brand_wise_report extends MY_Model {

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
            ],
            [
                'field_name'=>'ch|chemist_name',
                'field_label'=> 'Chemist Name',
            ],
            [
                'field_name'=>'ch|address',
                'field_label'=> 'Chemist Address',
            ],
            [
                'field_name'=>'dr|doctor_name',
                'field_label'=> 'Doctor Name',
            ],
            [
                'field_name'=>'dr|address',
                'field_label'=> 'Doctor Address',
            ],
            [
                'field_name'=>'m|molecule_name',
                'field_label'=> 'Molecule',
            ],
            [
                'field_name'=>'b|brand_name',
                'field_label'=> 'Brand',
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
        zsm.users_name as zsm_name,z.zone_name as zone,
        asm.users_name as asm_name, a.area_name as area,
        mr.users_name as mr_name, c.city_name as city,
        ch.chemist_name as chemist_name, ch.address as chemist_address,
        dr.doctor_name as doctor_name, dr.address as doctor_address,
        m.molecule_name, 
        IF(b.brand_name is NULL, ub.brand_name, b.brand_name) as brand_name,
        ub.rxn
        FROM users_molecule um
        JOIN users_brand ub ON um.molecule_id = ub.molecule_id
        LEFT JOIN brand b ON b.brand_id = ub.brand_id
        LEFT JOIN manpower mr ON mr.users_id = um.users_id
        LEFT JOIN molecule m ON m.molecule_id = um.molecule
        JOIN manpower asm ON asm.users_id = mr.users_parent_id
        JOIN manpower zsm ON zsm.users_id = asm.users_parent_id
        JOIN zone z ON zsm.users_zone_id = z.zone_id
        JOIN area a ON asm.users_area_id = a.area_id
        JOIN city c ON mr.users_city_id = c.city_id
        JOIN doctor dr ON dr.doctor_id = um.doctor_id
        JOIN chemist ch ON ch.chemist_id = dr.chemist_id";

        $sql .= " WHERE 1 = 1 ";
       
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
               
                if(!empty($value)) {
                    $key = str_replace('|', '.', $key);
                    $value = $this->db->escape_like_str($value);
                    $sql .= " AND $key LIKE '$value%' ";
                }
            }
        }

        //$sql .= " group by temp.doctor_id";
        $sql .= " ORDER by ch.insert_dt DESC";

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
			$records['Chemist Name'] = $rows['chemist_name'];
			$records['Chemist Address'] = $rows['chemist_address'];
			$records['Doctor Name'] = $rows['doctor_name'];
			$records['Doctor Address'] = $rows['doctor_address'];
			$records['Molecule'] = $rows['molecule_name'];
            $records['Brand'] = $rows['brand_name'];
            $records['RXN'] = $rows['rxn'];
            
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}