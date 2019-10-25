<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_user extends MY_Model {

	private $p_key = '';
	private $table = '';

	function __construct() {
		parent::__construct($this->table, $this->p_key);
	}
	
	function get_chemist_count(){
		
		$sql = "SELECT 
		COUNT(ch.chemist_name) as chemist_count
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
    
    function chemist_graph_count($from_date = false, $to_date = false) {
		
		$where = " WHERE 1 = 1 ";
		if($from_date) {
			$where .= " AND date(c.insert_dt) >='".$from_date."'";
		}
		if($to_date) {
			$where .= " AND date(c.insert_dt) <='".$to_date."'";
		}

        $sql = "SELECT
            'Greater than 15' AS 'key', SUM(IF(chemist_count > 15, 1, 0)) AS 'value'
        FROM (
            SELECT
                c.users_id AS mr, COUNT(c.chemist_id) AS chemist_count
            FROM chemist c
			$where
            GROUP BY c.users_id
        ) temp

        UNION

        SELECT
            'Exactly 15' AS 'key', SUM(IF(chemist_count = 15, 1, 0)) AS 'value'
        FROM (
            SELECT
                c.users_id AS mr, COUNT(c.chemist_id) AS chemist_count
            FROM chemist c
			$where
            GROUP BY c.users_id
        ) temp

        UNION 

        SELECT
            'Less than 15' AS 'key', SUM(IF(chemist_count < 15, 1, 0)) AS 'value'
        FROM (
            SELECT
                c.users_id AS mr, COUNT(c.chemist_id) AS chemist_count
            FROM chemist c
			$where
            GROUP BY c.users_id
        ) temp";

        return $this->db->query($sql)->result_array();
    }

	function get_doctor_count(){
		
		$sql = "SELECT 
		COUNT(d.doctor_name) AS doctor_count
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
		COUNT(d.doctor_name) AS doctor_count
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
		COUNT(d.doctor_name) AS doctor_count
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

	function get_chemist_count_data($zone_id, $from_date = "", $to_date = "") {
		$where = "";
		if($from_date) {
			$where .= " AND date(chemist.insert_dt) >='".$from_date."'";
		}
		if($to_date) {
			$where .= " AND date(chemist.insert_dt) <='".$to_date."'";
		}

		$q = $this->db->query("select sum(less_than_15) as count_less, sum(greater_15) as count_greater, sum(equal_15) as count_equal, asm.users_id, asm.users_name 
								from 
								(select mr.users_id as mr_id, mr.users_parent_id as mr_parent_id,
								case when (count(chemist.chemist_id) < 15) THEN 1 ELSE 0 END as less_than_15,
								case when (count(chemist.chemist_id) = 15) THEN 1 ELSE 0 END as equal_15,
								case when (count(chemist.chemist_id) > 15) THEN 1 ELSE 0 END as greater_15,

								chemist.*, mr.users_name as asm_name
								from manpower mr
								LEFT JOIN chemist on mr.users_id = chemist.users_id
								$where
								group by mr.users_id) as temp
								JOIN manpower asm on asm.users_id = temp.mr_parent_id
								JOIN zone on zone.zone_id = asm.users_zone_id
								where zone.zone_id = $zone_id
								group by asm.users_id");
		$collection = $q->result_array();
		return $collection;
	}

}