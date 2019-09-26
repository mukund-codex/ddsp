<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_molecule_wise_report extends MY_Model {

    function __construct() {
        parent::__construct();
    }
    
    function get_filters() {
        return [
            [
                'field_name'=>'zsm_name',
                'field_label'=> 'ZBM',
            ],
            [
                'field_name'=>'zone',
                'field_label'=> 'Zone',
            ],
            [
                'field_name'=>'asm_name',
                'field_label'=> 'ABM',
            ],
            [
                'field_name'=>'area',
                'field_label'=> 'Area',
            ],
            [
                'field_name'=>'mr_name',
                'field_label'=> 'MR Name',
            ],
            [
                'field_name'=>'city',
                'field_label'=> 'HQ',
            ],
            [
                'field_name'=>'chemist_name',
                'field_label'=> 'Chemist Name',
            ],
            [
                'field_name'=>'chemist_address',
                'field_label'=> 'Chemist Address',
            ],
            [
                'field_name'=>'doctor_name',
                'field_label'=> 'Doctor Name',
            ],
            [
                'field_name'=>'doctor_address',
                'field_label'=> 'Doctor Address',
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
        
        $molecule_data = $this->get_molecule_list();
        $query = '';
        foreach($molecule_data as $key => $molecule){
            $molecule_name = $molecule->molecule_name;
            //echo $molecule_name."<br>";
            $query .= "SUM(IF(molecule_name = '".$molecule_name."', 1, 0)) AS '".$molecule_name."', ";
        }
        $query = rtrim($query, ", ");

        $sql = "SELECT
            zsm_name, zone, asm_name, area, mr_name, city,
            chemist_name, chemist_address, chemist_date, doctor_name, doctor_address,
            ".$query."
        FROM
        (
            SELECT
                zsm.users_name as zsm_name, z.zone_name as zone,
                asm.users_name as asm_name, a.area_name as area,
                mr.users_name as mr_name, c.city_name as city,
                ch.chemist_name, ch.address as chemist_address, ch.insert_dt as chemist_date,
                d.doctor_id, d.doctor_name, d.address as doctor_address, 
                um.molecule_id, um.category_id, um.molecule,
                m.molecule_id AS 'm_molecule_id', m.molecule_name, m.category_id AS 'm_molecule_cat'		
            FROM doctor d
            LEFT JOIN users_molecule um ON d.doctor_id = um.doctor_id 
            LEFT JOIN molecule m ON um.molecule = m.molecule_id
            JOIN chemist ch ON ch.chemist_id = um.chemist_id
            JOIN manpower mr ON ch.users_id = mr.users_id
            JOIN manpower asm ON asm.users_id = mr.users_parent_id
            JOIN manpower zsm ON zsm.users_id = asm.users_parent_id
            JOIN zone z ON z.zone_id = zsm.users_zone_id
            JOIN area a ON a.area_id = asm.users_area_id
            JOIN city c ON c.city_id = mr.users_city_id
        ) temp";

        $sql .= " WHERE 1 = 1 ";
       
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

        $sql .= " group by doctor_id ";
        $sql .= " ORDER by chemist_date DESC";

        if(! $count) {
            if(!empty($limit)) { $sql .= " LIMIT $offset, $limit"; }        
        }
        
        $q = $this->db->query($sql);
        //echo $sql;exit;
        $collection = (! $count) ? $q->result_array() : $q->num_rows();

		return $collection;
    }	

    function get_molecule_list(){
        $q = $this->db->select('molecule_id ,molecule_name')

		->from('molecule');

		//print_r($this->db->get_compiled_select());exit;
		$collection = $q->get()->result();
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
            $molecule_data = $this->get_molecule_list();
            foreach ($molecule_data as $key => $molecule){
                $molecule_name = $molecule->molecule_name;
                $records[$molecule_name] = $rows[$molecule_name];
            }            
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}