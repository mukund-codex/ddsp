<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_notification_request extends MY_Model {

	public $p_key = 'n_req_id';
	public $table = 'notification_request';

	function __construct() {
		parent::__construct($this->table);
	}

	function get_notification_request(){
        $query = "SELECT nrd.user_id, nrd.device_id,nrd.device_type,nr.* 
		FROM notification_request_devices nrd
		JOIN notification_request nr ON nr.n_req_id = nrd.request_id";

        $collection = $this->db->query($query)->result();
        return $collection;
	}

}
