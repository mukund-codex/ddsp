<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if (!function_exists('bitly_url')) {

	function bitly_url($url, ...$params){
		if(empty($url)) { return false; }
		
        $curl_url = "http://api.bit.ly/shorten?version=2.0.1&longUrl=$url&login=techizer&apiKey=R_5fb5675508684417864ed5e88edf2a12&format=json&history=1";
        
        $ch = curl_init(); 		
		$timeout = 30; 		
        curl_setopt($ch,CURLOPT_URL,$curl_url); 
          		
		curl_setopt($ch,CURLOPT_MAXREDIRS,10); 		
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 		
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET"); 		
		curl_setopt($ch,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1); 		
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout); 		
		$data = curl_exec($ch); 		
		curl_close($ch); 

		$obj = json_decode($data, true);  
        return $obj["results"]["$url"]["shortUrl"];
	}
}