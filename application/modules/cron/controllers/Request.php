<?php 
class Request extends Generic_Controller{

    private $model_name = 'mdl_request';

	function __construct()
	{
		parent::__construct();
		$this->load->model($this->model_name, 'model');
    }
    
    function process_bulk_sms(){
        $current_datetime = date('Y-m-d H:i:00');

        $this->load->helper('send_sms');
        $is_cron_active = $this->model->is_cron_active();

        if($is_cron_active){
            echo 'SMS Sending already in progress'; 
            die();
        }

        $update = $this->model->_update(['status'=> 0], ['status'=> 1], 'sms_cron_status'); 

        if(!$update){
            echo 'Unable to start the cron, please check the conditions'; 
            die();
        }

        $sms_collection = $this->model->get_doctors();
        
        if(count($sms_collection)){
            
            foreach ($sms_collection as $sms) {
                
                $sms_data_id = $sms['sms_data_id'];
                $name = $sms['name'];
                $mobile = $sms['mobile'];
				$division_name = $sms['division_name'];
				$sender_id = $sms['sender_id'];
                $sms_type = $division_name; 
                $message = $sms['message']; 
                $sms_date_time = $sms['sms_date_time']; 

                if(empty($name) || empty($mobile) || empty($sender_id) || empty($message) || empty($sms_date_time) ) {
                    continue;
                }

                if(strtotime($current_datetime) == strtotime($sms_date_time)) {
                    $sms_status = send_sms($mobile, $message, $sms_type, '', '', $sender_id);
                    
                    if($sms_status){
                        $update = $this->model->_update(['sms_data_id' => $sms_data_id], ['is_processed' => 1], 'sms_data');
                    }else{
                        echo 'SMS sending failed for ' . $sms_data_id;
                    }
                }

            }
        }
        $this->model->_update(['status'=> 1], ['status'=> 0], 'sms_cron_status');

        echo 'Success';
        exit;
    }
}