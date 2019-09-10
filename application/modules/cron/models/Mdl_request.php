<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_request extends MY_Model {

	public $p_key = 'sms_queue_id';
	public $table = 'sms_queue';

	function __construct() {
		parent::__construct($this->table);
	}

	function is_cron_active(){
		$cron_record = $this->get_records(['status'=> 1], 'sms_cron_status', ['status']);
		return (count($cron_record)) ? TRUE : FALSE;
	}

	function get_doctors($sfilters = [], $limit = 0, $offset = 0) {
		$q = $this->db->select('sd.sms_data_id, sd.message, sd.sms_date_time, sd.is_processed,
		d.division_name, d.sender_id, doc.name, doc.mobile')
        ->from('sms_data sd')
        ->join('divisions d', 'd.division_id = sd.division_id')
        ->join('doctor doc', 'doc.division_id = d.division_id')
        ->where('sd.is_processed', 0);
        
		if(sizeof($sfilters)) { 
            
            foreach ($sfilters as $key=>$value) { 
                $q->where("$key", $value); 
			}
		}
        
		if(!empty($limit)) { $q->limit($limit, $offset); }        
        //echo $this->db->get_compiled_select(); die();
        $collection = $q->get()->result_array();
		return $collection;
	}
}
?>