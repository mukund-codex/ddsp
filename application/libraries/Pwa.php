<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set("Asia/Kolkata");

class Pwa {
    protected static $_ci;
	
	protected static $_icons = "icons";

	protected static $_icons_full_dir = APPPATH. "../icons";
	
	protected static $_db_table = "pwa";
	
	protected static $_manifest_file_name = APPPATH ."../manifest.json";

	function __construct()
	{
		self::$_ci = & get_instance();
		log_message('debug', 'PWA Initiated');
    }
    
    public static function init() {
        self::generate_manifest();
	}
	
	public static function generate_manifest() {
		if(! self::$_ci->db->table_exists('pwa')) { 
            show_error('PWA Table Not Found');
        }

        $data = self::$_ci->db->select('*')->from(self::$_db_table)->limit(1)->get()->row_array();
        
        if(! count($data)) { 
            show_error('PWA Values not defined');
		}
		
		$image = $data['image'] ?? NULL;
		if(empty($image) || ! file_exists($image)) {
			show_error('PWA Image Not Found');
		}

		$myfile = fopen(self::$_manifest_file_name, "a+") or die("Unable to open file!");

		if(!file_exists(self::$_icons_full_dir)) {
			mkdir(self::$_icons_full_dir, 0777, TRUE);
		}

		$resized_img = [];
		$image_resize = [72,96,128,144,152,192,384,512];
		foreach ($image_resize as $value) {
			$new_img = self::resize_image(
				$image, 
				$value, 
				$value, 
				self::$_icons_full_dir);
			
			array_push($resized_img, $new_img);				
		}

		$data['related_applications'] = (array)$data['related_applications'];
		$data['prefer_related_applications'] = (bool)$data['prefer_related_applications'];
		unset($data['image']);
		$data = array_merge($data, ['icons' => $resized_img]);

		$txt = json_encode($data, JSON_PRETTY_PRINT);

		file_put_contents(self::$_manifest_file_name, "");
		fwrite($myfile, $txt);
		fclose($myfile);
	}
    
    public static function resize_image($filename, $new_width, $new_height, $file_path) {
		list($width, $height) = getimagesize($filename);
		$image_p = imagecreatetruecolor($new_width, $new_height);
		$image = imagecreatefrompng($filename); 
		
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		
		$new_file_name = $file_path.'/icon-'.$new_width.'x'.$new_height.'.png';

		$data['src'] = self::$_icons. '/icon-'.$new_width.'x'.$new_height.'.png';
		$data['sizes'] = $new_width.'x'.$new_height;
		$data['type'] = 'image/png';
		imagepng($image_p, $new_file_name, 9);		
		return $data;
	}
}