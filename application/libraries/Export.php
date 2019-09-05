<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set("Asia/Kolkata");
/**
 * DPCP Trainer Class
 *
 * Library managing all the common calls
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Jimish Desai
 */
class Export {
	protected $_ci; // CodeIgniter instance
	public $cModel;
	public $qModel;

	function __construct($url = '')
	{
		$this->_ci = & get_instance();
		log_message('debug', 'cURL Class Initialized');
	}

	function download_send_headers($filename) {
		// disable caching
		$now = gmdate("D, d M Y H:i:s");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/xls");
	}

	function array2csv_old(array &$array) {
		if (count($array) == 0) {
			return null;
		}
		ob_start();
		$df = fopen("php://output", 'w');
		fputcsv($df, array_keys(reset($array)));
		foreach ($array as $row) {
			fputcsv($df, $row);
		}
		fclose($df);
		return ob_get_clean();
	}

	function array2csv(array &$array) {
		$flag = false;
		foreach($array as $row) {
		    if(!$flag) {
		      // display field/column names as first row
		      echo implode("\t", array_keys($row)) . "\n";
		      $flag = true;
		    }

		    /*echo __NAMESPACE__ . '\cleanData';*/
		    array_walk($row, array($this, 'cleanData'));
		    echo implode("\t", array_values($row)) . "\n";
		}
	}

	function cleanData(&$str){
	    $str = preg_replace("/\t/", "\\t", $str);
	    $str = preg_replace("/\r?\n/", "\\n", $str);
	    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
	}
}
/* 	Class Dpcp Ends Here
End of library dpcp.php */
