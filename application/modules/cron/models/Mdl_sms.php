<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_sms extends MY_Model {

	public $p_key = 'sms_data_id';
	public $table = 'sms_data';

	function __construct() {
		parent::__construct($this->table);
	}

	function get_collection() {

		$yesterday = date('Y/m/d',strtotime("-1 days"));

    	$q = $this->db->select('COUNT(DISTINCT ch.chemist_id) total_chemist, 
		COUNT(DISTINCT d.doctor_id) total_doctor,
		m.users_id,m.users_name, m.users_mobile,
		c.city_name,a.area_name,r.region_name,z.zone_name,nz.national_zone_name, us.users_name as asm_name, zs.users_name as zsm_name,
		zs.users_mobile as zsm_mobile')

		->from('manpower m')
		->join('city c', 'c.city_id = m.users_city_id')
		->join('area a', 'a.area_id = c.area_id')
		->join('region r','r.region_id = a.region_id')
		->join('zone z','z.zone_id = r.zone_id')
		->join('national_zone nz','nz.national_zone_id = z.national_zone_id')
		->join('manpower us','us.users_id = m.users_parent_id')
		->join('manpower rs','rs.users_id = us.users_parent_id')
		->join('manpower zs','zs.users_id = rs.users_parent_id')
		->join('chemist ch','ch.users_id = m.users_id')
		->join('doctor d','d.users_id = m.users_id')
		->where('DATE(ch.insert_dt)', '2019-09-11')
		->group_by('us.users_id');

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