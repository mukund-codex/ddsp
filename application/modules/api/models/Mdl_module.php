<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_module extends MY_Model{

	public function __construct(){
		parent::__construct();
    }
    
    function get_collection() {
        $q = $this->db->select('c.* ')
		->from('communication c');        

		$collection = $q->get()->result_array();
		return $collection;
    }
}