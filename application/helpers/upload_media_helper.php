<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if (!function_exists('upload_media')) {

    /**
     * Validate Input Paramaters
     */
    function validateParams(...$params)
    {
        list($field_name, $local_storage_path, $to_s3 , $s3_storage_path, $allowed_types, $max_file_size) = $params;

        if(! isset($_FILES[$field_name]) || empty($_FILES[$field_name]['name'])) {
			return ['status' => FALSE, 'error' => 'Invalid Input File'];
		}
		
		if($to_s3) {
			if(empty($s3_storage_path)) {
				return ['status' => FALSE, 'error' => 'Invalid S3 Storage Path'];
			}
		} else {
			if(empty($local_storage_path)) {
				return ['status' => FALSE, 'error' => 'Invalid Local Storage Path'];
			}
			if(! file_exists($local_storage_path)) {
                mkdir($local_storage_path, 0755, true);
			}
		}

		$global_allowed_types = ['hqx','cpt','csv','bin','dms','lha','lzh','exe','class','psd','so',
		'sea','dll','oda','pdf','ai','eps','ps','smi','smil','mif','xls','ppt','pptx','wbxml','wmlc',
		'dcr','dir','dxr','dvi','gtar','gz','gzip','php','php4','php3','phtml','phps','js','swf','sit',
		'tar','tgz','z','xhtml','xht','zip','rar','mid','midi','mpga','mp2','mp3','aif','aiff','aifc',
		'ram','rm','rpm','ra','rv','wav','bmp','gif','jpeg','jpg','jpe','png','tiff','tif','css','html',
		'htm','shtml','txt','text','log','rtx','rtf','xml','xsl','mpeg','mpg','mpe','qt','mov','avi',
		'movie','doc','docx','dot','dotx','xlsx','word','xl','eml','json','pem','p10','p12','p7a','p7c',
		'p7m','p7r','p7s','crt','crl','der','kdb','pgp','gpg','sst','csr','rsa','cer','3g2','3gp','mp4',
		'm4a','f4v','webm','aac','m4u','m3u','xspf','vlc','wmv','au','ac3','flac','ogg','kmz','kml','ics',
		'ical','zsh','7zip','cdr','wma','jar','svg','vcf'];

		$is_validate_types = array_diff($allowed_types, $global_allowed_types);

		if (! is_array($allowed_types) || count($is_validate_types) > 0) {
			return ['status' => FALSE, 'error' => 'Invalid Allowed Types'];
		}

		if(is_array($_FILES[$field_name]['name'])) {
			$is_validate_files = array_diff(array_map(function ($file_name){
				return pathinfo($file_name, PATHINFO_EXTENSION);
			}, $_FILES[$field_name]['name']), $allowed_types);
            
			if(count($is_validate_files) > 0) {
				return ['status' => FALSE, 'error' => 'Invalid Files. Allowed File types are '. implode(',', $allowed_types)];
			}
		} else {
			$check_allowed_file = in_array(pathinfo($_FILES[$field_name]['name'], PATHINFO_EXTENSION), $allowed_types);
			if(! $check_allowed_file) {
				return ['status' => FALSE, 'error' => 'Invalid File. Allowed File types are '. implode(',', $allowed_types)];
			}
		}
    }


    /**
     * Upload Files in Local/S3 Storage
     * 
     * @param string $field_name
     * @param string $local_storage_path
     * @param bool $to_s3
     * @param string $s3_storage_path
     * @param array $allowed_types
     * @param int $max_file_size
     * 
     * @return array
     * 
     */
	function upload_media(string $field_name, string $local_storage_path = '', array $allowed_types, int $max_file_size = 10000, bool $to_s3 = false, string $s3_storage_path = ''): array{
        $ci = &get_instance();
        $ci->load->library('upload');
        
        // Validate Input Parameters
        $validate_params = validateParams($field_name, $local_storage_path, $to_s3, $s3_storage_path, $allowed_types, $max_file_size);
        if($validate_params) {return $validate_params;}
        //END

        $allowed_types = implode('|', $allowed_types);
        
        $filesCount =  is_array($_FILES["$field_name"]['name']) ? count($_FILES["$field_name"]['name']) : 1;
                
        if($to_s3 !== true){
            $config['upload_path'] = $local_storage_path;
            $media_path = $local_storage_path;
            $config['s3'] = false;
        }else{
            $media_path = $s3_storage_path;
            $config['s3'] = true;
        }

        $config['allowed_types'] = $allowed_types;
        $config['max_size']	= (int) $max_file_size;
        $config['overwrite'] = FALSE;
        $config['encrypt_name'] = TRUE;
        $config['file_ext_tolower'] = TRUE;
        $config['remove_spaces'] = TRUE;

        $ci->upload->initialize($config);
        $file_response = [];

        for($i = 0; $i < (int) $filesCount; $i++){ 
            $_FILES['userfile']['name']     = is_array($_FILES[$field_name]['name']) ? $_FILES[$field_name]['name'][$i] : $_FILES[$field_name]['name'];
            $_FILES['userfile']['type']     = is_array($_FILES[$field_name]['type']) ? $_FILES[$field_name]['type'][$i] : $_FILES[$field_name]['type'];
            $_FILES['userfile']['tmp_name'] = is_array($_FILES[$field_name]['tmp_name']) ? $_FILES[$field_name]['tmp_name'][$i] : $_FILES[$field_name]['tmp_name'];
            $_FILES['userfile']['error']    = is_array($_FILES[$field_name]['error']) ? $_FILES[$field_name]['error'][$i] : $_FILES[$field_name]['error'];
            $_FILES['userfile']['size']     = is_array($_FILES[$field_name]['size']) ? $_FILES[$field_name]['size'][$i] : $_FILES[$field_name]['size'];
            
            $upload_callback = ($to_s3 === true) ? $ci->upload->do_s3_upload($s3_storage_path) : $ci->upload->do_upload();

            if ( ! $upload_callback){
                return  array('status' => FALSE, 'error' => $ci->upload->display_errors());
            }else{
                $data = $ci->upload->data();                

                $response_file_data = [];
                $response_file_data['file_name'] = trim($media_path,'/').'/'.$data['file_name'];
                $response_file_data['file_type'] = $data['file_type'];
                $response_file_data['full_path'] = $data['full_path'];
                $response_file_data['file_path'] = $data['file_path'] ?? NULL;
                $response_file_data['raw_name'] = $data['raw_name'];
                $response_file_data['orig_name'] = $data['orig_name'];
                $response_file_data['client_name'] = $data['client_name'];
                $response_file_data['file_ext'] = $data['file_ext'];
                $response_file_data['file_size'] = $data['file_size'];
                $response_file_data['is_image'] = $data['is_image'] ?? NULL;
                $response_file_data['image_width'] = $data['image_width'] ?? NULL;
                $response_file_data['image_height'] = $data['image_height'] ?? NULL;
                $response_file_data['image_type'] = $data['image_type'] ?? NULL;
                $response_file_data['image_size_str'] = $data['image_size_str'] ?? NULL;
                array_push($file_response, $response_file_data);
            }
        }
        unset($config);
        return $file_response;
	}
}