<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_lists extends MY_Model{

	public function __construct(){
		parent::__construct();
    }
    
    function get_collection($user_id, $role) {
        $q = $this->db->select('
			doctor.doctor_id, doctor.doctor_name, doctor_poster.poster, doctor.photo,
			doctor_poster.s3_url,speciality.speciality_name,speciality.speciality_id,
			ifnull(doctor.clinic_address, "") as clinic_address, doctor.doctor_email
    	')
		->from('doctor')
		->join('doctor_poster', 'doctor_poster.doctor_id = doctor.doctor_id', 'left')
		->join('speciality', 'speciality.speciality_id = doctor.speciality_id')
		->join('manpower mr', 'mr.users_id = doctor.doctor_mr_id and mr.is_deleted = 0')
		->join('manpower asm', 'asm.users_id = mr.users_parent_id and asm.is_deleted = 0', 'left')
        ->join('zone z', 'mr.users_zone_id = z.zone_id and z.is_deleted = 0')
        ->join('region r', 'mr.users_region_id = r.region_id and r.is_deleted = 0')
        ->join('area a', 'mr.users_area_id = a.area_id and a.is_deleted = 0')
		->join('city c', 'mr.users_city_id = c.city_id and c.is_deleted = 0');
		$role = strtolower($role);

		$q->where("$role".".users_id", $user_id);
		$q->where("doctor.is_deleted", 0);

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
