<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_user extends MY_Model{

	public function __construct(){
		parent::__construct();
	}

	function getUserRecords($username, $password) {
		$q = $this->db->select('m.users_emp_id, m.users_password, m.users_mobile, ifnull(m.users_email, "") as users_email, m.users_name, m.users_id as userId, city.city_name, area.area_name, m.users_type, parent.users_name as reporting_manager')
		->from('manpower m')
		->join('manpower parent', 'parent.users_id = m.users_parent_id')
		->join('city', 'city.city_id = m.users_city_id', 'left')
		->join('area', 'area.area_id = m.users_area_id', 'left');

		$q->where('m.users_emp_id', $username);
		$q->where('m.users_password', $password);

		$q->where('(m.users_type = "MR")', NULL);
		return $q->get()->result();
	}

}
