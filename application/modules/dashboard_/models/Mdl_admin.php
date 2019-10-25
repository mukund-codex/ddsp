<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_admin extends MY_Model {

	private $p_key = '';
	private $table = '';

	function __construct() {
		parent::__construct($this->table, $this->p_key);
	}

	function getDashboardCounts() {
		$sql = "SELECT zsm_id,zsm_name, zone,zone_id,asm_id,asm_name,area, sum(chemist_count) as chemist_count, sum(doctor_count) as doctor_count, asm_name
		FROM (
			SELECT 
					temp.zsm_id, temp.zsm_name, temp.zone_id, temp.zone, temp.asm_id, temp.asm_name, temp.area, 
					MAX(temp.chemist_count) chemist_count, MAX(temp.doctor_count) doctor_count, 
					MAX(temp.asm_count) asm_count, MAX(temp.zsm_count) zsm_count
					FROM (
					SELECT 
					zsm.users_id as zsm_id, zsm.users_name AS zsm_name, z.zone_id as zone_id, z.zone_name AS zone,
					asm.users_id as asm_id, asm.users_name AS asm_name, a.area_name AS AREA, NULL AS chemist_count, 
					COUNT(d.doctor_id) AS doctor_count, SUM(IF(d.asm_status = 'approve', 1, 0)) AS asm_count, 
					SUM(IF(d.zsm_status = 'approve', 1, 0)) AS zsm_count
					FROM doctor d
					JOIN manpower mr ON mr.users_id = d.users_id
					JOIN manpower asm ON asm.users_id = mr.users_parent_id
					JOIN manpower zsm ON zsm.users_id = asm.users_parent_id
					JOIN zone z ON z.zone_id = zsm.users_zone_id
					JOIN area a ON a.area_id = asm.users_area_id
					WHERE 1 =1 
					GROUP BY asm.users_id 
					UNION ALL
					SELECT 
					zsm.users_id as zsm_id, zsm.users_name AS zsm_name, z.zone_id as zone_id, z.zone_name AS zone,
					asm.users_id as asm_id, asm.users_name AS asm_name, a.area_name AS AREA, COUNT(ch.chemist_id) chemist_count, 
					NULL AS doctor_count, NULL AS asm_count, NULL AS zsm_count
					FROM chemist ch
					JOIN manpower mr ON mr.users_id = ch.users_id
					JOIN manpower asm ON asm.users_id = mr.users_parent_id
					JOIN manpower zsm ON zsm.users_id = asm.users_parent_id
					JOIN zone z ON z.zone_id = zsm.users_zone_id
					JOIN area a ON a.area_id = asm.users_area_id
					WHERE 1 =1 
					GROUP BY asm.users_id
					) temp
					WHERE 1 = 1
					GROUP BY temp.asm_id
					ORDER BY temp.zone) as temp2";

			$query = $this->db->query($sql);
			return $query->result();

	}
	
	function get_chemist_count(){
		
		$sql = "SELECT 
		COUNT(DISTINCT(ch.chemist_name)) as chemist_count
		FROM chemist ch
		JOIN manpower mr ON mr.users_id = ch.users_id
		LEFT JOIN manpower asm ON asm.users_id = mr.users_parent_id
		LEFT JOIN manpower zsm ON asm.users_id = asm.users_parent_id";

		$sql .= " WHERE 1 = 1 ";
				
		$role = $this->session->get_field_from_session('role', 'user');
		if(!empty($role)){
			$id = $this->session->get_field_from_session('user_id', 'user');
			if($role == 'ASM'){
				$sql .= "AND asm.users_id = '".$id."'";
			}
			if($role == 'ZSM'){
				$sql .= "AND asm.users_parent_id = '".$id."'";
			}
		}

		$q = $this->db->query($sql);
        //echo $sql;exit;
        $collection = $q->result_array();

		return $collection;

	}

	function get_doctor_count(){
		
		$sql = "SELECT 
		COUNT(DISTINCT(d.doctor_name)) AS doctor_count
		FROM doctor d
		JOIN manpower mr ON mr.users_id = d.users_id
		LEFT JOIN manpower asm ON asm.users_id = mr.users_parent_id
		LEFT JOIN manpower zsm ON asm.users_id = asm.users_parent_id";

		$sql .= " WHERE 1 = 1 ";
				
		$role = $this->session->get_field_from_session('role', 'user');
		if(!empty($role)){
			$id = $this->session->get_field_from_session('user_id', 'user');
			if($role == 'ASM'){
				$sql .= "AND asm.users_id = '".$id."'";
			}
			if($role == 'ZSM'){
				$sql .= "AND asm.users_parent_id = '".$id."'";
			}
		}

		$q = $this->db->query($sql);
        //echo $sql;exit;
        $collection = $q->result_array();

		return $collection;

	}

}