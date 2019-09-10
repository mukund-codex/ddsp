<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_sms extends MY_Model {

	public $p_key = 'sms_data_id';
	public $table = 'sms_data';

	function __construct() {
		parent::__construct($this->table);
	}

	function get_collection() {

    	$q = $this->db->select('
		d.*, div.*
    	')
		->from('doctor d')
		->join('divisions div', 'd.division_id = div.division_id');

		//print_r($this->db->get_compiled_select());exit;
		$collection = $q->get()->result();
		return $collection;
	}

	function getPatientsForTemplate() {
		$q = $this->db->select('
			patient.*, doctor.doc_name, doctor_health_tips_translate.message, language.language_name
		')
		->from('patient')
		->join('language', 'language.language_code = patient.lang_code')
		->join('doctor', 'doctor.doc_id = patient.doctor_id')
		->join('doctor_health_tips', 'doctor_health_tips.doctor_id = doctor.doc_id')
		->join('doctor_health_tips_translate', 'doctor_health_tips_translate.doc_ht_id = doctor_health_tips.doc_ht_id and language.language_id = doctor_health_tips_translate.language_id')
		->where('doctor.is_deleted',0)
		->group_by('patient.patient_id');
		$collection = $q->get()->result();
		return $collection;
	}

	function get_patient_collection($month,$year,$doctor_ids = [],$patient_mobiles = [])
	{
		$q = $this->db->select('
			p.patient_name,p.patient_mobile,dht.doctor_ivr')
		->from('doctor_health_tips dht')
		->join('patient p', 'p.doctor_id = dht.doctor_id')
		->join('doctor d', 'd.doc_id = p.doctor_id');

		if(!empty($month)){
			$q->where('dht.doctor_month',$month);
		}
		
		if(!empty($year)){
			$q->where('dht.doctor_year',$year);
		}

		if(count($doctor_ids)){
			$this->db->where_in('d.doc_id', $doctor_ids);
		}

		if(count($patient_mobiles)){
			$this->db->where_in('p.patient_mobile', $patient_mobiles);
		}

		// print_r($this->db->get_compiled_select());exit;
		$collection = $q->get()->result();
		
		return $collection;
	}

	
	function sms_data_link() {
		$current_date = date("Y-m-d H:i:00");

		$smssendData = $this->model->get_records(['sms_date_time'=>$current_date,'is_processed'=>0], 'sms_data');
		
		if(count($smssendData) > 0) {
			foreach($smssendData as $dataRecord) {
				$upID = $dataRecord->sms_data_id;

				$data = array();
				$data['is_processed'] = 1;
				$status = (int) $this->_update(['sms_data_id' => $upID], $data,'sms_data');
			}		

			foreach($smssendData as $dataRecord) {
				$thID = $dataRecord->therapy_id;
				$smsTemplate = $dataRecord->message;
				$doctorData = $this->model->get_records(['therapy_id'=>$thID,'unsubscribe'=>0], 'doctor');
				
				foreach($doctorData as $doc) {
					$message = str_replace('{docid}',$doc->doc_id,$smsTemplate);
					send_sms($doc->mobile, trim($message), 'PDF Info');
				}
			}
			$response['status'] = TRUE;
		} else {
			$response['status'] = FALSE;
		}
		
		
		return $response;
	}
}