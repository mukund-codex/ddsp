<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if (!function_exists('send_voice_call')) {

	function send_voice_call($to, $voice_id = '',$msg_for = ''){
		$ci = &get_instance();
        $ci->load->database();
        
        // $url1 = "http://obd.solutionsinfini.com/api/v1/?api_key=54c3f83cfc3ece41716676e9bdd5e0b6&method=voice.status&format=json&mobile=$to";
		$url ="https://obd.solutionsinfini.com/api/v1/?api_key=54c3f83cfc3ece41716676e9bdd5e0b6&method=voice.call&play=".$voice_id.".sound&numbers=".$to."";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		$pos = strpos($output,"GID=");
		$is_success = ($pos == true) ? 1 : 0;
        curl_close($ch);
    
		// $is_success = 1;
		// $output = '';
      
		$data = [
				'msg_for' => $msg_for,
				'voice_id' => $voice_id,
				'mobile' => $to,
				'is_success' => $is_success,
				'output' => $output,
				'insertdatetime' => date("Y-m-d H:i:s")
			];

		$ci->db->insert('voice_log', $data);

		return true;
    }
}