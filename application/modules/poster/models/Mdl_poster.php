<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use GDText\Box;
use GDText\Color;

class Mdl_poster extends MY_Model {
    private $template;
    private $rgb;
    private $font;

    private $name_size;
    private $message_size;
    
    private $thumb_size;
    private $thumb_x_location;
    private $thumb_y_location;

    private $name_y_location;
    private $message_y_location;

	function __construct() {
        parent::__construct();

        $this->template = 'assets/resources/templates/Doctor-Template-Quintessential.jpg';
        $this->rgb = [0,0,0];
        $this->font = 'assets/resources/fonts/Quintessential-Regular.ttf';
        
        $this->name_size = 70;
        $this->message_size = 60;
        $this->thumb_size = 575;

        $this->thumb_x_location = 1465;
        $this->thumb_y_location = 1100;

        $this->name_y_location = 1830;
        $this->message_y_location = 1970;

    }
    
    function initialize($config = []) {
        if(array_key_exists('template', $config)) {
            $this->template = $config['templage'];
        }
        if(array_key_exists('rgb', $config)) {
            $this->rgb = $config['rgb'];
        }
        if(array_key_exists('font', $config)) {
            $this->font = $config['font'];
        }
        if(array_key_exists('name_size', $config)) {
            $this->name_size = $config['name_size'];
        }
        if(array_key_exists('message_size', $config)) {
            $this->message_size = $config['message_size'];
        }
        if(array_key_exists('thumb_size', $config)) {
            $this->thumb_size = $config['thumb_size'];
        }
        if(array_key_exists('thumb_x_location', $config)) {
            $this->thumb_x_location = $config['thumb_x_location'];
        }
        if(array_key_exists('thumb_y_location', $config)) {
            $this->thumb_y_location = $config['thumb_y_location'];
        }
        if(array_key_exists('name_y_location', $config)) {
            $this->name_y_location = $config['name_y_location'];
        }
        if(array_key_exists('message_y_location', $config)) {
            $this->message_y_location = $config['message_y_location'];
        }
    }
    
	function generate($data = [], $crop_photo) {
        if(! is_array($data)) {
            return FALSE;
        }

        $doctor_file = $crop_photo;
        $doctor_name = $data['doctor'];
        $doctor_file_name = url_title($doctor_name, '-', TRUE);

        $this->load->library('Image');
        $doctor_photo = new Image($doctor_file);

        $doctor_photo->resize($this->thumb_size, $this->thumb_size);
        $thumb = $doctor_photo->getImage();

        $info = getimagesize ($this->template);
        
        $mime = $info['mime'];
        $imgWidth = $info[0];

        if ($mime == 'image/png') {
            $poster = imagecreatefrompng ($this->template);
        } elseif ($mime == 'image/jpeg') {
            $poster = imagecreatefromjpeg ($this->template);
        }
        
        list($width, $height) = $info; 

        $doctor_name = $data['doctor'];
        $message = $data['message'];
        
        list($r, $g, $b) = $this->rgb;
        $color = imagecolorallocate($poster, $r, $g, $b);
        
        // For Writing Text
        list($x, $y) = $this->boxCenter($poster, $doctor_name, $this->font, $this->name_size);
        imagettftext($poster, $this->name_size, 0, $x, $this->name_y_location, $color, $this->font, $doctor_name);

        $im = $poster;
        $im2 = $thumb;

        imagecopy($im, $im2, $this->thumb_x_location, $this->thumb_y_location, 0, 0, $this->thumb_size, $this->thumb_size);

        if(!file_exists('uploads/posters/')) {
            mkdir('uploads/posters', 0775);
        }
        
        $box = new Box($poster);
        //$box->enableDebug();
        $box->setFontFace($this->font); 
        $box->setFontColor(new Color(0, 0, 0));
        $box->setFontSize($this->message_size);
        $box->setBox(600, $this->message_y_location, 2312, 370);
        $box->setTextAlign('center', 'top');
        $box->draw($message);

        $random = rand(1000, 100000);
        if ($mime == 'image/png') {
            $poster_name = "uploads/posters/$doctor_file_name-$random.png";
            imagepng($poster, $poster_name);
        }elseif ($mime == 'image/jpeg') {
            $poster_name = "uploads/posters/$doctor_file_name-$random.jpg";
            imagejpeg($poster, $poster_name);
        }

        imagedestroy($poster);
        
        return $poster_name;
	}
	
	function boxCenter($image, $text, $font, $size, $angle = 0) {
        $xi = imagesx($image);
        $yi = imagesy($image);
        // echo $font; die();
        $box = imagettfbbox($size, $angle, $font, $text);

        $xr = abs(max($box[2], $box[4]));
        $yr = abs(max($box[5], $box[7]));

        $x = intval(($xi - $xr) / 2);
        $y = intval(($yi + $yr) / 2);

        return array($x, $y);
    }
}
