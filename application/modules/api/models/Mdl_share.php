<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_share extends MY_Model {

	public $p_key = 'gif_id';
	public $table = 'gifs';

	function __construct() {
		parent::__construct($this->table);
	}

	function share($doctor_id, $whatsapp){

		if(isset($_POST['id'])){
			$gif_id = (int) $this->input->post('id');
			$insert_user_id = Modules::run('users/_get_session_owner', 'U');
			$response = $this->_insert(
				[
					'gif_id'=> $gif_id, 
					'insert_user_id'=> $insert_user_id,
					'share_type'=> 'W'
				], 
				'shared');

			$status = ($response) ? TRUE : FALSE;
			return ['status'=> TRUE];
		}

		return ['msg'=> 'Permission Denied!', 'status'=> FALSE ];
	}
}