<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_module extends MY_Model{

	public function __construct(){
		parent::__construct();
    }
    
    function get_collection($user_id) {
        $q = $this->db->select('count(c.chemist_id) as chemist_count, count(d.doctor_id) as doctor_count')
		->from('chemist c')
		->join('doctor d', 'c.chemist_id = d.chemist_id')        
		->where('c.users_id', $user_id);

		//echo $this->db->get_compiled_select(); die();

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
}