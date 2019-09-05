<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if (!function_exists('send_email')) {

	function send_email($to = [], $subject, $content, $attachment = []){
		$ci = &get_instance();
		$path = 'uploads/';
		
		//$message =  $this->load->view($data['viewFile'], $data, TRUE);
		$ci->load->library('email');

		$mail = $ci->email
		        ->from('info@rssdiwheel.techizertech.in', 'RSSDI')
		        ->to($to)
		        ->subject($subject)
		        ->message($content);
		        //->cc(['softech@techizerindia.com']);
		        //->bcc(['poonam.ipca@gmail.com']);
		        //->attach('uploads/Overall_Flow__WCC2017.pdf');

		if(sizeof($attachment)) : 
			foreach ($attachment as $file) {
				$loc = $path . $file;
				$mail->attach($loc);
			}
		endif;
		//print_r($attachment);exit;
		$message = date('Y-m-d h:i:s') . " MAIL SENDING TO Mail to :: " . json_encode($to) . " :: Subject :: " . $subject . " :: Files :: " . json_encode($attachment) . PHP_EOL;
		error_log($message, 3, APPPATH . 'logs/email_log' . date('Y-m-d') . ".log");
		
		$result = $mail->send();

		if($result)
			return TRUE;
		else
			return FALSE;
	}	
}