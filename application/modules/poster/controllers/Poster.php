<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Poster extends Generic_Controller
{
    public $data = [];

	function __construct() {
		parent::__construct();
    }

    function save() {
        $this->load->helper('upload_media');
        $response = upload_media('doctor_photo', 'uploads/doctors', ['jpg', 'png', 'jpeg']);

        if(array_key_exists('status', $response)) {
            echo json_encode(['status'=> FALSE, 'error' => ['doctor_photo' => '<label class="error">'.$response['error']. '</label>']]); die();
        }

        echo json_encode([
            'status'=> TRUE, 
            'path'=> base_url($response[0]['file_name']), 
            'filename'=> $response[0]['raw_name'] . $response[0]['file_ext']
        ]); die();
    }
}