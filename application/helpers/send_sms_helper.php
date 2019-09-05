<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if (!function_exists('send_sms')) {

	function send_sms($to, $message = '', $msg_for = '',$user_id = '', $desig = ''){

		$ci = &get_instance();
		$path = 'uploads/';
		
		$ci->load->database();

		$sender_id = "PHARMA";
		$provider = '';

		switch($provider){
			case 'quicksmart':
				$username = 'Techizer';
				$key = '92Ay3difxPE2';
				
				$master_url = 'http://quicksmart.in/api/pushsms?user=' . $username . '&authkey=' . $key . '&sender=' . $sender_id;
				$is_english = is_english($message);
				$url = $master_url . '&mobile=' . $to . '&text=' . urlencode($message) . '&rpt=1';
				if (! $is_english){
					$url .= '&type=1';
				}
				
				// echo $url;				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$output = curl_exec($ch);
				$pos = strpos($output,"STATUS:OK");
				$is_success = ($pos == true) ? 1 : 0;
				curl_close($ch);

				$set = array(
					"smslog_msg_for" => $msg_for,
					"smslog_user_id" => $user_id,
					"smslog_mobile" => $to,
					"smslog_designation" => $desig,
					"smslog_message" => $message,
					"smslog_is_success" => $is_success,
					"smslog_output" => $output
				);
				
				$ci->load->model('common_model');
				$ci->common_model->insert_data_query('smslog',$set);
				break;
			case 'meru': 
				break;
			default:
				$msg = urlencode($message);
			
				$url = "http://alerts.sinfini.com/api/web2sms.php?workingkey=79205ve7suw5bj1odtr5&sender=".$sender_id."&to=$to&message=$msg";
		
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$output = curl_exec($ch);
		
				$pos = strpos($output,"GID=");
				$is_success = ($pos == true) ? 1 : 0;
				curl_close($ch); 
				
				$data = [
					'mobile' => $to,
					'message' => $message,
					'msg_for' => $msg_for,
					'is_success' => $is_success,
					'output' => $output,
					'insertdatetime' => date("Y-m-d H:i:s")
				];
	
				$ci->db->insert('sms_log', $data);   
				break;
		}

		$logmessage = date('Y-m-d h:i:s')." :: msg_for ::".$msg_for.":: result ::".$output.":: msg ::".$message. PHP_EOL;
		error_log($logmessage, 3, APPPATH . 'logs/sms_log' . date('Y-m-d') . ".log");
		
		
		return true; 
	}	
}
if (!function_exists('is_english')) {
	function is_english($str) {
		if (strlen($str) != strlen(utf8_decode($str))) {
			return false;
		} else {
			return true;
		}
	}
}