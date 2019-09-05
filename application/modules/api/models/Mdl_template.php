<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

use GDText\Box;
use GDText\Color;

class Mdl_template extends MY_Model{

	public function __construct(){
        parent::__construct();

		$this->template = 'assets/resources/templates/Doctor-Template-Quintessential.jpg';
        $this->rgb = [0,0,0];
        $this->font_bold = 'assets/resources/fonts/Calibri-Bold.TTF';
        $this->font = 'assets/resources/fonts/Calibri-Regular.ttf';
        $this->load->library('s3');
        $this->load->config('s3');
        /* 
        $this->name_size = 70;
        $this->message_size = 60;
        $this->thumb_size = 575;

        $this->thumb_x_location = 1465;
        $this->thumb_y_location = 1100;

        $this->name_y_location = 1830;
        $this->message_y_location = 1970; */
    }

	function generate($data = [], $crop_photo, $name_size, $message_size, $thumb_size, $thumb_x_location, $thumb_y_location, $name_y_location, $message_y_location, $template_image,$name_x_location,$name_height, $name_width, $speciality_size, $speciality_x_location, $speciality_y_location, $speciality_width, $speciality_height,$address_size, $address_x_location, $address_y_location, $address_width, $address_height, $directory_location) {

        $s3_directory = explode('/', $directory_location);
        $path_name = $s3_directory[1]. "/";

        $bucket = $this->config->item('s3_bucket_name');
        if(! is_array($data)) {
            return FALSE;
        }

        $doctor_file = $crop_photo;
        $doctor_name = $data['doctor'];
        $doctor_file_name = url_title($doctor_name, '-', TRUE);
        $this->load->library('Image');
        $doctor_photo = new Image($doctor_file);

        $doctor_photo->resize($thumb_size, $thumb_size);
        $thumb = $doctor_photo->getImage();

        $info = getimagesize ($template_image);

        $mime = $info['mime'];
        $imgWidth = $info[0];

        if ($mime == 'image/png') {
            $poster = imagecreatefrompng ($template_image);
        } elseif ($mime == 'image/jpeg') {
            $poster = imagecreatefromjpeg ($template_image);
        }
        
        list($width, $height) = $info; 

        $doctor_name = $data['doctor'];
        $address = $data['clinic_address'];
        $speciality = $data['doctor_speciality'];
        // $message = $data['message'];
        
        list($r, $g, $b) = $this->rgb;
        $color = imagecolorallocate($poster, $r, $g, $b);
        
        // For Writing Text
/*         list($x, $y) = $this->boxCenter($poster, $doctor_name, $this->font, $name_size);
        imagettftext($poster, $name_size, 0, $x, $name_y_location, $color, $this->font, $doctor_name); */

        $im = $poster;
        $im2 = $thumb;

        imagecopy($im, $im2, $thumb_x_location, $thumb_y_location, 0, 0, 1093, $thumb_size);

        if(!file_exists("$directory_location")) {
            mkdir("$directory_location", 0775);
        }
        
        // Name Section
        $box = new Box($poster);
        // $box->enableDebug();
        $box->setFontFace($this->font_bold);
        $box->setFontColor(new Color(40, 116, 173));
        $box->setFontSize($name_size);
        $box->setBox($name_x_location, $name_y_location, $name_height , $name_width);
        $box->setTextAlign('left', 'top');
        $box->draw($doctor_name);

        // Speciality Section
        $box_speciality = new Box($poster);
        $box_speciality->setFontFace($this->font_bold);
        $box_speciality->setFontColor(new Color(40, 116, 173));
        $box_speciality->setFontSize($speciality_size);
        $box_speciality->setBox($speciality_x_location, $speciality_y_location, $speciality_width , $speciality_height);
        $box_speciality->setTextAlign('left', 'top');
        $box_speciality->draw($speciality);

        // Address Section
        $box_address = new Box($poster);
        $box_address->setFontFace($this->font);
        $box_address->setFontColor(new Color(0, 0, 0));
        $box_address->setFontSize($address_size);
        $box_address->setBox(1496, 900, 1770, 94);
        $box_address->setTextAlign('left', 'top');
        $box_address->draw($address);

        // Done. Distroy the Image
        $random = rand(1000, 100000);
        
        if ($mime == 'image/png') {
            $s3_dir_path = "";
            $s3_file_name = "$doctor_file_name-$random.png";
            $poster_name = "$directory_location/$doctor_file_name-$random.png";
            imagejpeg($poster, $poster_name);
            $output_uri = trim($s3_dir_path)."$path_name$doctor_file_name-$random.png";
            $output_file_url = $poster_name;
            $result = S3::putObjectFile($output_file_url, $bucket, $output_uri, S3::ACL_PUBLIC_READ,['Content-Disposition'=> 'attachment; filename="'.$doctor_file_name.'-'.$random.'.png"']);
            unlink($poster_name);

        }elseif ($mime == 'image/jpeg') {
            $s3_dir_path = "";
            $s3_file_name = "$doctor_file_name-$random.png";
            $poster_name = "$directory_location/$doctor_file_name-$random.jpg";
            imagejpeg($poster, $poster_name);
            $output_uri = trim($s3_dir_path)."$path_name$doctor_file_name-$random.jpg";
            $output_file_url = $poster_name;
            $result = S3::putObjectFile($output_file_url, $bucket, $output_uri, S3::ACL_PUBLIC_READ,['Content-Disposition'=> 'attachment; filename="'.$doctor_file_name.'-'.$random.'.jpg"']);
            unlink($poster_name);
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
