<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_module extends MY_Model{

	public function __construct(){
		parent::__construct();
    }
    
    function get_collection($user_id) {
        $q = $this->db->select('COUNT(DISTINCT ch.chemist_id) chemist_count, 
		COUNT(DISTINCT d.doctor_id) doctor_count')
		->from('manpower m')
		->join('chemist ch', 'ch.users_id = m.users_id') 
		->join('doctor d', 'd.users_id = m.users_id')        
		->where('m.users_id', $user_id)
		->group_by('m.users_id');

		$collection = $q->get()->result_array();
		return $collection;
	}
	
	function get_speciality_count($speciality_id, $user_id){
		$q = $this->db->select('count(doctor_id) as doctor_count')
		->from('doctor')
		->where('speciality', $speciality_id)
		->where('users_id', $user_id);

		//echo $this->db->get_compiled_select();exit;

		$collection = $q->get()->result_array();
		return $collection;

	}	
	
	function get_mr_hq($user_id){
		$q = $this->db->select('mr.users_id, mr.users_name as mr_name, c.city_name as city')
		->from('manpower mr')
		->join('city c','c.city_id = mr.users_city_id')
		->where('mr.users_id', $user_id);

		$collection = $q->get()->result_array();
		return $collection;

	}
}