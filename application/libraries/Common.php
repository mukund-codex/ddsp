<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * NDEP ABM Class
 *
 * Library managing all the abm related calls
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Amey
 */
class Common {
	protected $_ci; // CodeIgniter instance
	public $cModel;
	public $qModel;

	function __construct()
	{
		$this->_ci = & get_instance();
	}

	function time2str($ts,$str='') {
        if(!ctype_digit($ts)) {
            $ts = strtotime($ts);
        }
        $diff = time() - $ts;
        if($diff == 0) {
            return trim($str.' now');
        } elseif($diff > 0) {
            $day_diff = floor($diff / 86400);
            if($day_diff == 0) {
                if($diff < 60) return trim($str.' just now');
                if($diff < 120) return trim($str.' 1 minute ago');
                if($diff < 3600) return trim($str.' '.floor($diff / 60) . ' minutes ago');
                if($diff < 7200) return trim($str.' 1 hour ago');
                if($diff < 86400) return trim($str.' '.floor($diff / 3600) . ' hours ago');
            }
            if($day_diff == 1) { return trim($str.' Yesterday'); }
            if($day_diff < 7) { return trim($str.' '.$day_diff . ' days ago'); }
            if($day_diff < 31) { return trim($str.' '.ceil($day_diff / 7) . ' weeks ago'); }
            if($day_diff < 60) { return trim($str.' last month'); }
            return trim($str.' '.date('M j, Y', $ts));
        } else {
            $diff = abs($diff);
            $day_diff = floor($diff / 86400);
            if($day_diff == 0) {
                if($diff < 120) { return trim($str.'  a minute'); }
                if($diff < 3600) { return trim($str.' ' . floor($diff / 60) . ' minutes'); }
                if($diff < 7200) { return trim($str.' an hour'); }
                if($diff < 86400) { return trim($str.' ' . floor($diff / 3600) . ' hours ago'); }
            }
            if($day_diff == 1) { return trim($str.' Tomorrow'); }
            if($day_diff < 4) { return trim($str.' '.date('l', $ts)); }
            if($day_diff < 7 + (7 - date('w'))) { return trim($str.' next week'); }
            if(ceil($day_diff / 7) < 4) { return trim($str.' in ' . ceil($day_diff / 7) . ' weeks'); }
            if(date('n', $ts) == date('n') + 1) { return trim($str.' next month'); }
            return trim($str.' '.date('M j, Y', $ts));
        }
    }

	public function array_find($needle, array $haystack, $column = null) {
        $keyArray = array();
        if(is_array($haystack[0]) === true) { 
            foreach (array_column($haystack, $column) as $key => $value) {
                if (strpos(strtolower($value), strtolower($needle)) !== false) {
                    $keyArray[] = $key;

                }
            }
        } else {
            foreach ($haystack as $key => $value) { // for normal array
                if (strpos(strtolower($value), strtolower($needle)) !== false) {
                    $keyArray[] = $key;
                }
            }
        }
        
        if(empty($keyArray)) {
            return false;
        }
        if(count($keyArray) == 1) {
            return $keyArray[0];
        } else {
            return $keyArray;
        }
	}
	
	function generate_random_string($length=12) {
		$str = "";
		  $characters = array_merge(range('a','z'), range('0','9'));
		  $max = count($characters) - 1;
		  for ($i = 0; $i < $length; $i++) {
			  $rand = mt_rand(0, $max);
			  $str .= $characters[$rand];
		  }
		  return $str;
	}
}
