<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_admin extends MY_Model {

	private $p_key = '';
	private $table = '';

	function __construct() {
		parent::__construct($this->table, $this->p_key);
	}

	function get_dashboard_collection($sfilters = []) {		
		$sql = "SELECT 
		SUM(temp.chemist_count) chemist_count, SUM(temp.doctor_count) doctor_count, 
		SUM(temp.asm_count) asm_count, SUM(temp.zsm_count) zsm_count
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
		GROUP BY z.zone_id
		) temp 
		WHERE 1 = 1 ";
		
		if(sizeof($sfilters)) { 
            foreach ($sfilters as $key => $value) { 
                $sql .= " AND $key = $value "; 
			}
		}

		$user_role = $this->session->get_field_from_session('role','user');
		if(in_array($user_role, ['ASM'])) {
			$user_id = $this->session->get_field_from_session('user_id', 'user');
			$sql .= ' AND temp.asm_id = '.$user_id.' ';
		}
		if(in_array($user_role, ['ZSM'])) {
			$user_id = $this->session->get_field_from_session('user_id', 'user');
			$sql .= ' AND temp.zsm_id = '.$user_id.' ';
		}

		$q = $this->db->query($sql);
		
        $collection = $q->row_array();
		return $collection;
	}
	
	function dashboard_table_collection()
	{
		$sql = "SELECT 
		temp.zsm_name, temp.zone, 
		MAX(temp.chemist_count) chemist_count, MAX(temp.total_reps) total_reps, 
		MAX(temp.no_of_days) no_of_days, (MAX(temp.chemist_count)/ (MAX(temp.total_reps) * MAX(temp.no_of_days))) chemist_avg, 
		MAX(temp.doctor_count) doctor_count, MAX(temp.asm_count) asm_count, MAX(temp.zsm_count) zsm_count
		FROM (
		SELECT 
		zsm.users_name AS zsm_name, z.zone_name AS zone, 
		NULL AS chemist_count, COUNT(d.doctor_name) AS doctor_count, 
		SUM(IF(d.asm_status = 'approve', 1, 0)) AS asm_count, 
		SUM(IF(d.zsm_status = 'approve', 1, 0)) AS zsm_count, 
		NULL AS total_reps, NULL AS no_of_days
		FROM doctor d
		LEFT JOIN manpower mr ON mr.users_id = d.users_id
		LEFT JOIN manpower asm ON asm.users_id = mr.users_parent_id
		LEFT JOIN manpower zsm ON zsm.users_id = asm.users_parent_id
		LEFT JOIN zone z ON z.zone_id = zsm.users_zone_id
		LEFT JOIN area a ON a.area_id = asm.users_area_id
		WHERE 1 =1
		GROUP BY z.zone_id 
		UNION ALL
		SELECT 
		zsm.users_name AS zsm_name, z.zone_name AS zone, 
		COUNT(ch.chemist_id) chemist_count, NULL AS doctor_count, NULL AS asm_count, 
		NULL AS zsm_count, NULL AS total_reps, 
		DATEDIFF(MAX(ch.insert_dt), MIN(ch.insert_dt)) no_of_days
		FROM chemist ch
		LEFT JOIN manpower mr ON mr.users_id = ch.users_id
		LEFT JOIN manpower asm ON asm.users_id = mr.users_parent_id
		LEFT JOIN manpower zsm ON zsm.users_id = asm.users_parent_id
		LEFT JOIN zone z ON z.zone_id = zsm.users_zone_id
		LEFT JOIN area a ON a.area_id = asm.users_area_id
		WHERE 1= 1
		GROUP BY z.zone_id 
		UNION ALL
		SELECT 
		zsm.users_name AS zsm_name, z.zone_name AS zone, 
		NULL AS chemist_count, NULL AS doctor_count, NULL AS asm_count, NULL AS zsm_count, 
		COUNT(temp.mr_id) AS total_reps, NULL AS no_of_days
		FROM (
		SELECT 
		mr.users_id AS mr_id, mr.users_name AS mr_name, 
		mr.users_parent_id AS mr_parent, MAX(att.insert_dt) max_date, 
		MIN(att.insert_dt) min_date
		FROM manpower mr
		JOIN access_token att ON att.user_id = mr.users_id
		GROUP BY mr.users_id)temp
		LEFT JOIN manpower asm ON asm.users_id = temp.mr_parent
		LEFT JOIN manpower zsm ON zsm.users_id = asm.users_parent_id
		LEFT JOIN zone z ON z.zone_id = zsm.users_zone_id
		LEFT JOIN area a ON a.area_id = asm.users_area_id
		GROUP BY z.zone_id) temp
		WHERE 1 = 1
		GROUP BY temp.zone";

		$q = $this->db->query($sql);
				
		$collection = $q->result_array();
		return $collection;

	}



}