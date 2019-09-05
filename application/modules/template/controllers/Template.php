<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Template extends MX_Controller {

	function __construct() {
		parent::__construct();
		/* $this->load->library('pwa');
		Pwa::init(); */
		// $this->load->library('sendgrid');
		// Sendgrid::fetch_mails(50);

	}

	function _admin($data){
		$data['role'] = $role = $this->session->get_field_from_session('role');

		if( ! in_array($role, ['SA', 'A'])){
			show_error('You do not have permission to access content of the page', 403, 'Unauthorized Access');
		}

		$this->load->view('admin', $data);		
	}

	function _ho($data) {
		$data['role'] = $role = $this->session->get_field_from_session('role', 'user');

		if( ! in_array($role, ['HO'])) {
			show_error('You do not have permission to access content of the page', 403, 'Unauthorized Access');
		}

		$this->load->view('ho', $data);
	}

	function _user($data) {
		$data['user_role'] = $role = $this->session->get_field_from_session('role', 'user');

		if( ! in_array($role, ['ZSM','RSM','ASM','MR', 'HO'])) {
			show_error('You do not have permission to access content of the page', 403, 'Unauthorized Access');
		}

		$this->load->view('user', $data);
	}

	function _login($data){
		$this->load->view('login', $data);
    }
    
	function _one_page($data){
		$this->load->view('one-page', $data);
	}

	function _ho_login($data) {
		$this->load->view('hologin', $data);
	}


}