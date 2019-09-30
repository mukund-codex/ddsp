<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_change_image extends MY_Model {

	function __construct() {
		parent::__construct();
	}

	function getDoctorImages() {
		$q = $this->db->select('mr.users_id as mr_id,mr.users_name as mr_name,c.city_name,
		d.doctor_id,d.doctor_name,sp.speciality_name, im.image_id,im.image_name, im.image_path')
		->from('doctor d')
		->join('images im', 'im.doctor_id = d.doctor_id AND im.category = "doctor"')
		->join('speciality sp', 'sp.speciality_id = d.speciality')
		->join('manpower mr', 'mr.users_id = d.users_id')
		->join('city c', 'c.city_id = mr.users_city_id');

		return $q->get()->result();
	}

	function getChemistImages() {
		$q = $this->db->select('mr.users_id as mr_id,mr.users_name as mr_name,c.city_name,
		ch.chemist_id, ch.chemist_name, im.image_id,im.image_name, im.image_path')
		->from('chemist ch')
		->join('images im', 'im.chemist_id = ch.chemist_id AND im.category = "chemist"')
		->join('manpower mr', 'mr.users_id = ch.users_id')
		->join('city c', 'c.city_id = mr.users_city_id');
		
		return $q->get()->result();
	}
}
