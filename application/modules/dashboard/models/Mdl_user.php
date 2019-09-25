<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_user extends MY_Model {

	private $p_key = '';
	private $table = '';

	function __construct() {
		parent::__construct($this->table, $this->p_key);
	}
	
	function get_chemist_count(){
		
		$sql = "SELECT 
		COUNT(DISTINCT(ch.chemist_name)) as chemist_count
		FROM chemist ch
		JOIN manpower mr ON mr.users_id = ch.users_id
		LEFT JOIN manpower asm ON asm.users_id = mr.users_parent_id";

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

	function get_approved_doctor_count(){
		
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
				$sql .= "AND d.asm_status = 'approve' AND asm.users_id = '".$id."'";
			}
			if($role == 'ZSM'){
				$sql .= "AND d.zsm_status = 'approve' AND asm.users_parent_id = '".$id."'";
			}
		}

		$q = $this->db->query($sql);
        //echo $sql;exit;
        $collection = $q->result_array();

		return $collection;

	}

	function get_disapproved_doctor_count(){
		
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
				$sql .= "AND d.asm_status = 'disapprove' AND asm.users_id = '".$id."'";
			}
			if($role == 'ZSM'){
				$sql .= "AND d.zsm_status = 'disapprove' AND asm.users_parent_id = '".$id."'";
			}
		}

		$q = $this->db->query($sql);
        //echo $sql;exit;
        $collection = $q->result_array();

		return $collection;

	}

}