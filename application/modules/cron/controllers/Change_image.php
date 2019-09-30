<?php 
class Change_image extends Generic_Controller{

    private $model_name = 'mdl_change_image';

	function __construct()
	{
		parent::__construct();
		$this->load->model($this->model_name, 'model');
    }
    
    // New Doctor/Chemist Image Name:: MR + HQ + CHEMIST/DOCTOR + RANDOM
    function change_doctor_images(){

        $i = 0;
        $j = 0;
        echo "Doctor Image Rename Started\n";

        $getDoctorImages = $this->model->getDoctorImages();

        foreach ($getDoctorImages as $doc) {
            $mr_id = $doc->mr_id;
            $mr_name = $doc->mr_name;
            $city_name = $doc->city_name;
            $doctor_id = $doc->doctor_id;
            $doctor_name = $doc->doctor_name;
            $speciality_name = $doc->speciality_name;
            $image_id = $doc->image_id;
            $old_image_name = $doc->image_name;
            $old_image_path = $doc->image_path;

            if(empty($mr_name) || empty($city_name) || empty($doctor_name) || 
                    empty($old_image_name) || empty($old_image_path) || empty($image_id)) 
            {
                echo "\n========================\n";
                echo "Invalid Data \n";
                echo "\n========================\n";
                $j++;
                continue;
            }

            if(! file_exists($old_image_name)) {
                echo "\n========================\n";
                echo "$old_image_name \n";
                echo "Image File Required \n";
                echo "\n========================\n";
                $j++;
                continue;
            }

            $dirname = pathinfo($old_image_name, PATHINFO_DIRNAME);
            $ext = pathinfo($old_image_name, PATHINFO_EXTENSION);
                        
            $random = rand(1111,9999);
            $file_name = $mr_name .'_'.$city_name.'_'.$doctor_name.'_'.$random;
            
            $modified_file_name = url_title($file_name, '_', TRUE);
            $new_file_name = $dirname .'/'.$modified_file_name.'.'.$ext;
            $new_image_path = getcwd().'/'.$new_file_name;

            if(rename($old_image_name, $new_file_name)) {
                $data = [];
                $data['image_name'] = $new_file_name;
                $data['image_path'] = $new_image_path;

                $is_file_renamed = $this->model->_update(
                    ['image_id' => $image_id],
                    $data,
                    'images'
                );

                if($is_file_renamed) {
                    echo "\n========================\n";
                    echo "File Updated \n";
                    echo "$old_image_name => $new_file_name \n";
                    echo "$old_image_path => $new_image_path \n";
                    echo "\n========================\n";
                    $i++;
                }
            } else {
                echo "\n========================\n";
                echo "File Not Updated \n";
                echo "$old_image_name => $new_file_name \n";
                echo "$old_image_path => $new_image_path \n";
                echo "\n========================\n";
                $j++;
            }
        }

        echo "Success \n";
        echo "Images Updated :: $i \n";
        echo "Images Not Updated :: $j \n";
        echo "\n========================\n";
        exit;
    }

    // New Doctor/Chemist Image Name:: MR + HQ + CHEMIST/DOCTOR + RANDOM
    function change_chemist_images(){

        $i = 0;
        $j = 0;
        echo "Chemist Image Rename Started\n";

        $getChemistImages = $this->model->getChemistImages();

        foreach ($getChemistImages as $chem) {
            $mr_id = $chem->mr_id;
            $mr_name = $chem->mr_name;
            $city_name = $chem->city_name;
            $chemist_id = $chem->chemist_id;
            $chemist_name = $chem->chemist_name;
            $image_id = $chem->image_id;
            $old_image_name = $chem->image_name;
            $old_image_path = $chem->image_path;

            if(empty($mr_name) || empty($city_name) || empty($chemist_name) || 
                    empty($old_image_name) || empty($old_image_path) || empty($image_id)) 
            {
                echo "\n========================\n";
                echo "Invalid Data \n";
                echo "\n========================\n";
                $j++;
                continue;
            }

            if(! file_exists($old_image_name)) {
                echo "\n========================\n";
                echo "$old_image_name \n";
                echo "Image File Required \n";
                echo "\n========================\n";
                $j++;
                continue;
            }

            $dirname = pathinfo($old_image_name, PATHINFO_DIRNAME);
            $ext = pathinfo($old_image_name, PATHINFO_EXTENSION);
                        
            $random = rand(1111,9999);
            $file_name = $mr_name .'_'.$city_name.'_'.$chemist_name.'_'.$random;
            
            $modified_file_name = url_title($file_name, '_', TRUE);
            $new_file_name = $dirname .'/'.$modified_file_name.'.'.$ext;
            $new_image_path = getcwd().'/'.$new_file_name;

            if(rename($old_image_name, $new_file_name)) {
                $data = [];
                $data['image_name'] = $new_file_name;
                $data['image_path'] = $new_image_path;

                $is_file_renamed = $this->model->_update(
                    ['image_id' => $image_id],
                    $data,
                    'images'
                );

                if($is_file_renamed) {
                    echo "\n========================\n";
                    echo "File Updated \n";
                    echo "$old_image_name => $new_file_name \n";
                    echo "$old_image_path => $new_image_path \n";
                    echo "\n========================\n";
                    $i++;
                }
            } else {
                echo "\n========================\n";
                echo "File Not Updated \n";
                echo "$old_image_name => $new_file_name \n";
                echo "$old_image_path => $new_image_path \n";
                echo "\n========================\n";
                $j++;
            }
        }        
        echo "Success \n";
        echo "Images Updated :: $i \n";
        echo "Images Not Updated :: $j \n";
        echo "\n========================\n";
        exit;
    }
}