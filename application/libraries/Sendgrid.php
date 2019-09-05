<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * SendGrid Class
 *
 * Library managing all the abm related calls
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Amey
 */
class Sendgrid {
	protected $_ci;

	function __construct()
	{
		$this->_ci = & get_instance();
	}

	static function fetch_mails($limit = 10) {
		$url = "https://api.sendgrid.com/v3/messages?limit=$limit";
		
		$headers = [
			"Authorization: Bearer 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",
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$output = curl_exec($ch);

		echo '<pre>';
		print_r(json_decode($output, TRUE));
		exit;
		curl_close($ch); 

		
	}
}
