<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_admin extends MY_Model {

	private $p_key = '';
	private $table = '';

	function __construct() {
		parent::__construct($this->table, $this->p_key);
    }
}