<?php
class Module extends Api_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('api/mdl_module','model');
		$this->load->library('form_validation');
        $this->load->library('common');
    }
    
    function communication(){

        // If the token is valid, send Communication List to APP
		// Else return the error message to the APP

		/**
		 * @api {post} /api/module/communication Communication
		 * @apiName communication
         * @apiGroup Module
		 * @apiSuccess {Number} code HTTP Status Code.
		 * @apiSuccess {String} message  Associated Message.
		 * @apiSuccess {Object} data  Communication List Object With Token
		 * @apiSuccess {Object} error  Error if Any.
		 *
		 * @apiSuccessExample Success-Response:
			*     HTTP/1.1 200 OK
			*     {
			*		"message": "Communication List",
			*		"error": "",
			*		"code": 200,
			*		"data": {
			*			"data": [
			*			    {
            *                   "c_id": "64",
            *                   "title": "Test",
            *                   "description": "Test Description",
            *                   "time": "Updated 1 hour ago",  
            *                    "media": {
            *                       "image": [
            *                           {
            *                               "file_id": "17",
            *                               "file_path": "uploads/communication/images/7b481b5e0774236676295aa21e934b8e.jpg"
            *                           },
            *                           {
            *                               "file_id": "18",
            *                               "file_path": "uploads/communication/images/dc1b75bc24d57c2a8dac3992f19c8b2f.jpg"
            *                           }
            *                       ],
            *                       "document": [
            *                           {
            *                               "file_id": "19",
            *                               "file_path": "uploads/communication/documents/6932742918e4cd88a972327900d36477.pdf"
            *                           }
            *                       ]
            *                   }
            *               }
            *           ],                 
			*		}
			*	}
            */
                
            $user_id = $this->id;
            $get_communication_list = $this->model->get_records([], 'communication');
            $data = [];
            $media_data = [];
    
            if(count($get_communication_list) > 0)  {
                foreach ($get_communication_list as $key => $value) {
                    $input_data['c_id'] = $value->c_id;
                    $input_data['title'] = $value->title;
                    $input_data['description'] = $value->description;
                    $input_data['time'] = $this->common->time2str( strtotime($value->insert_dt));
                    
                    $get_media = $this->model->get_records(['c_id' => $value->c_id], 'communication_media');
                    if(count($get_media) > 0){
                        foreach($get_media as $key => $media){
                            $input_media = [];
                            $input_media['file_id'] = $media->media_id;
                            $input_media['file_path'] = base_url($media->media);                            
                            
                            $media_data[$media->media_type][] = (object) $input_media;
                            $input_data['media'] = $media_data;
                        }   
                    }
                }

                array_push($data, $input_data);                
            }

            $this->response['code'] = 200;
            $this->response['data'] = ['posts' => $data];
            $this->response['message'] = empty($data) ? "No Data Found" : "Communication List";
            $this->sendResponse();

    }

    function about(){

        // If the token is valid, send About Content to APP
		// Else return the error message to the APP

		/**
		 * @api {post} /api/module/about about
         * @apiName about
         * @apiGroup Module
		 *
		 * @apiSuccess {Number} code HTTP Status Code.
		 * @apiSuccess {String} message  Associated Message.
		 * @apiSuccess {Object} data  About Content Object With Token
		 * @apiSuccess {Object} error  Error if Any.
		 *
		 * @apiSuccessExample Success-Response:
			*     HTTP/1.1 200 OK
			*     {
            *           "message": "About",
            *           "error": "",
            *           "code": 200,
            *           "data": {
            *               "data": [
            *                   {  
            *                       "about": "asdasdasdasdsadas"
            *                   }
            *               ],
            *           "request_id": 1567667611.252315
            *           }
            *       }
            */

        $user_id = $this->id;
        $get_about = $this->model->get_records([], 'about', ['about'], '', 1);

        $about = count($get_about) > 0 ? $get_about[0]->about : '';

        $this->response['code'] = 200;
        $this->response['data'] = [
            "about" => $about,
        ];
        
        $this->response['message'] = empty($about) ? "No Data Found" : "About";
        $this->sendResponse();

    }

    function dashboard(){

        // If the token is valid, send Cound of Chemist, doctor, Speciality wises count and About Content to APP
		// Else return the error message to the APP

		/**
		 * @api {post} /api/module/dashboard dashboard
         * @apiName dashboard
         * @apiGroup Module
		 *
		 * @apiSuccess {Number} code HTTP Status Code.
		 * @apiSuccess {String} message  Associated Message.
		 * @apiSuccess {Object} data  About Content Object With Token
		 * @apiSuccess {Object} error  Error if Any.
		 *
		 * @apiSuccessExample Success-Response:
			*     HTTP/1.1 200 OK
			*     {
            *       "message": "OK",
            *       "error": "",
            *       "code": 200,
            *       "data": {
            *       "slider": [
            *            {
            *                "title": "Chemist",
            *                "count": "15",
            *                "image": "http://aux.iconspalace.com/uploads/file-person-icon-128.png"
            *            },
            *            {
            *                "title": "Doctor",
            *                "count": "15",
            *                "image": "http://aux.iconspalace.com/uploads/file-person-icon-128.png"
            *            }
            *       ],
            *       "specialitygrid": [
            *           {
            *               "speciality_id": "1",
            *               "speciality_name": "Derma",
            *               "count": "0",
            *               "color": "#DCF4FE",
            *               "image": "http://aux.iconspalace.com/uploads/file-person-icon-128.png"
            *           },
            *       ],
            *       "about": "asdasdasdasdsadas",
            *       "request_id": 1567842916.9668
            *       }
            *   }
            */

        $user_id = $this->id;
        $data = $this->model->get_collection($user_id);
        
        $countdata = [];
        $chemistData = [];
        $doctorData = [];
        
        $ccount['title'] = 'Chemist';
        $ccount['count'] = ($data) ? $data[0]['chemist_count'] : 0;
        $ccount['image'] = 'http://aux.iconspalace.com/uploads/file-person-icon-128.png';
        

        $dcount['title'] = 'Doctor';
        $dcount['count'] = ($data) ? $data[0]['doctor_count'] : 0;            
        $dcount['image'] = 'http://aux.iconspalace.com/uploads/file-person-icon-128.png';
        
        array_push($countdata, $ccount, $dcount);
        
        $specialtygrid = [];
        $specialtydata = [];

        $speciality = $this->model->get_records([], 'speciality');
        if(count($speciality) > 0){
            foreach($speciality as $value){
                $speciality_data['speciality_id'] = $value->speciality_id;
                $speciality_data['speciality_name'] = $value->speciality_name;
                
                $speciality = $this->model->get_speciality_count($speciality_data['speciality_id'], $user_id);
                $speciality_data['count'] = $speciality[0]['doctor_count'];

                if($speciality_data['speciality_name'] == 'Derma'){
                    $speciality_data['color'] = '#DCF4FE';
                }elseif($speciality_data['speciality_name'] == 'CP'){
                    $speciality_data['color'] = '#F9E8E7';
                }elseif($speciality_data['speciality_name'] == 'GP'){
                    $speciality_data['color'] = '#FFFBD3';
                }elseif($speciality_data['speciality_name'] == 'Gynae'){
                    $speciality_data['color'] = '#FFE2F7';
                }else{
                    $speciality_data['color'] = '';
                }
                
                $speciality_data['image'] = 'http://aux.iconspalace.com/uploads/file-person-icon-128.png';

                array_push($specialtydata, $speciality_data);
            }
            
        }

        $get_about = $this->model->get_records([], 'about', ['about'], '', 1);
        $about = count($get_about) > 0 ? $get_about[0]->about : '';
        
        $this->response['code'] = 200;
        $this->response['data'] = [
            "slider" => $countdata,
            "specialitygrid" => $specialtydata,
            "about" => $about,
        ];
        $this->sendResponse();

    }

    function speciality(){

		// Get Speciality Type for Validating
		// If records are present, return the Specaility List on the basis of type to the APP
		// Else return the error message to the APP

		/**
		 * @api {post} /api/module/speciality Speciality
		 * @apiName speciality
		 * @apiGroup Module
		 *
		 * @apiParam {String {derma, other}}  specialityType Speciality Type.
		 *
		 * @apiSuccess {Number} code HTTP Status Code.
		 * @apiSuccess {String} message  Associated Message.
		 * @apiSuccess {Object} data  Employee Record Object With Token
		 * @apiSuccess {Object} error  Error if Any.
		 *
		 * @apiSuccessExample Success-Response:
			*     HTTP/1.1 200 OK
			*       {
            *           "message": "Speciality List",
            *           "error": "",
            *           "code": 200,
            *           "data": {
            *           "speciality": [
            *               {
            *                   "speciality_id": "2",
            *                   "speciality_name": "CP"
            *               },
            *               {
            *                   "speciality_id": "3",
            *                   "speciality_name": "GP"
            *               },
            *               {
            *                   "speciality_id": "4",
            *                   "speciality_name": "Gynae"
            *               }
            *           ],
            *           "request_id": 1567834956.298453
        *           }
            *   }
			*/

        $user_id = $this->id;
        $specialityType = $this->input_data['specialityType'];
        
        if($specialityType == 'derma'){
            $speciality_data = $this->model->get_records(['speciality_name' => 'Derma'], 'speciality');
        }else{
            $speciality_data = $this->model->get_records(['speciality_name !=' => 'Derma'], 'speciality');            
        }

        $data = [];

        if(count($speciality_data) > 0){
            foreach($speciality_data as $key => $value){
                $specialitydata['speciality_id'] = $value->speciality_id;
                $specialitydata['speciality_name'] = $value->speciality_name;
                array_push($data, $specialitydata);
            }
            
        }

        $this->response['code'] = 200;
        $this->response['data'] = [
            "speciality" => $data,
        ];
        $this->response['message'] = empty($speciality_data) ? "No Data Found" : "Speciality List";
        $this->sendResponse();

    }

    function state_molecule(){

        // If the token is valid, send State, City and Molecule data to APP
		// Else return the error message to the APP

		/**
		 * @api {post} /api/module/state_molecule State_Molecule
		 * @apiName state_molecule
         * @apiGroup Module
		 * @apiSuccess {Number} code HTTP Status Code.
		 * @apiSuccess {String} message  Associated Message.
		 * @apiSuccess {Object} data  About Content Object With Token
		 * @apiSuccess {Object} error  Error if Any.
		 *
		 * @apiSuccessExample Success-Response:
			*     HTTP/1.1 200 OK
			*     {
            *           "message": "About",
            *           "error": "",
            *           "code": 200,
            *           "data": {
            *               "data": [
            *                   {
            *                        "id": "1",
            *                        "name": "Andaman & Nicobar Islands",
            *                        "city": [
            *                            {
            *                                "id": "2",
            *                                "name": "Port Blair"
            *                            }
            *                         ]
            *                    },
            *               ],
            *               "molecule": [
            *                   {
            *                       "id": "11",
            *                       "name": "Molecule 1",
            *                       "brand": [
            *                           {
            *                               "brand_id": "6",
            *                               "brand_name": "Brand 2",
            *                               "sku": [
            *                                   {
            *                                       "sku_id": "12",
            *                                       "sku": "GGWP1234"
            *                                   },
            *                                   {
            *                                       "sku_id": "13",
            *                                       "sku": "GGWP1235"
            *                                   }
            *                               ],
            *                               "isSku": true
            *                           },
            *                       ]
            *                   },
            *                   {
            *                       "id": "12",
            *                       "name": "Molecule 2",
            *                       "brand": [
            *                           {
            *                               "brand_id": "10",
            *                               "brand_name": "Brand M2 1",
            *                               "sku": [],
            *                               "isSku": false
            *                           }
            *                       ]
            *                   }
            *               ],
            *               "request_id": 1567673072.714553
            *           }
            *       }
            */

        $user_id = $this->id;

        $data = [];
        $moleculedata = [];

        $get_state = $this->model->get_records([], 'state');
       
        if(count($get_state) > 0){
            foreach($get_state as $key => $value){
                $citydata = [];
                $input_data['id'] = $value->id;
                $input_data['name'] = $value->state;
                
                $get_city = $this->model->get_records(['state_id' => $value->id], 'cities');
                if(count($get_city) > 0){
                    foreach($get_city as $key => $city){
                        $city_data = [];
                        $city_data['id'] = $city->city_id;
                        $city_data['name'] = $city->city_name;
                        array_push($citydata, $city_data);
                    }
                    $input_data['city'] = $citydata;
                }
                
                array_push($data, $input_data);
            }
            
            
        }

        $get_molecule = $this->model->get_records([], 'molecule');
        if(count($get_molecule) > 0){
            foreach($get_molecule as $key => $molecule){
                $brandData = [];
                $molecules_data['id'] = $molecule->molecule_id;
                $molecules_data['name'] = $molecule->molecule_name;
                
                $get_brand = $this->model->get_records(['molecule_id' => $molecule->molecule_id], 'brand');
                if(count($get_brand) > 0){
                    foreach($get_brand as $key => $brand){
                        $skuData = [];
                        $brand_data = [];
                        $brand_data['id'] = $brand->brand_id;
                        $brand_data['name'] = $brand->brand_name;                        
                        $brand_data['other'] = 'no';

                        $get_sku = $this->model->get_records(['brand_id' => $brand->brand_id], 'sku');
                        if(count($get_sku) > 0){
                            foreach($get_sku as $key => $sku){
                                $sku_data= [];
                                $sku_data['id'] = $sku->sku_id;
                                $sku_data['name'] = $sku->sku;
                                array_push($skuData, $sku_data);
                            }
                            $brand_data['sku'] = $skuData;
                            $brand_data['isSku'] = TRUE;
                            
                        }else{
                            $brand_data['sku'] = [];
                            $brand_data['isSku'] = FALSE;
                        }

                        array_push($brandData, $brand_data);

                        $molecules_data['brand'] = $brandData;

                    }
                }

                array_push($moleculedata, $molecules_data);
            }
           
        }

        $this->response['code'] = 200;
        $this->response['data'] = [
            "state" => $data,
            "molecule" => $moleculedata,
        ];
        $this->response['message'] = empty($data) ? "No Data Found" : "List";
        $this->sendResponse();

    }

    function imageupload(){

        // Add Images To the System
		/**
		* @api {post} /api/module/imageupload Upload Images
		* @apiName imageupload
		* @apiGroup Module
		*

		*
		* @apiParam {String} id Doctor/Chemist ID
		* @apiParam {String {chemist, doctor}} category Category
		* @apiParam {File} images Doctor/Chemist Images(JPEG, JPG, PNG)

		* @apiSuccess {Number} code HTTP Status Code.
		* @apiSuccess {String} message  Associated Message.
		* @apiSuccess {Object} data  Doctor Record Object With Token
		* @apiSuccess {Object} error  Error if Any.
		*
		* @apiSuccessExample Success-Response:
		*     HTTP/1.1 200 OK
		*     {
        *           "message": "Images Uploaded",
        *           "error": "",
        *           "code": 200,
        *           "data": {
        *               "request_id": 1568004052.413862
        *           }
        *      }
		*/

        $user_id = $this->id;

        $category = $this->input->post('category');

        if(sizeof($_FILES['images']['name']) <= 0){
            $this->response['code'] = 400;
            $this->response['message'] = 'Atleast upload 1 image.';
            $this->sendResponse();
            return;
        }

        if(sizeof($_FILES['images']['name']) > 3){
            $this->response['code'] = 400;
            $this->response['message'] = 'Only upload 3 images.';
            $this->sendResponse();
            return;
        }

        $path = 'uploads/doctorImages/';

        if($category == 'chemist'){
            $path = 'uploads/chemistImages/';
        }
        

        if(!empty($_FILES['images'])) {
			
			$this->load->helper('upload_media');
			$is_file_upload = upload_media('images', $path, ['jpeg', 'png', 'jpg'], 10000000);
	
			if(array_key_exists('error', $is_file_upload)) {
				$this->response['code'] = 400;
				$this->response['message'] = $is_file_upload['error'];
				$this->error = array('message'=> $is_file_upload['error']);
				$this->sendResponse();
				return;
            }
            
           /*  echo '<pre>';
            print_r($is_file_upload);exit; */

            foreach($is_file_upload as $upload){
                
                if($category == 'chemist'){
                    $data['chemist_id'] = $this->input->post('id');
                }else{
                    $data['doctor_id'] = $this->input->post('id');
                }
                $data['category'] = $category;
                $data['image_name'] = $upload['file_name'];
                $data['image_path'] = $upload['full_path'];

                $id = $this->model->_insert($data, 'images');
            }

            $this->response['code'] = 200;
            $this->response['message'] = 'Images Uploaded Successfully';
            $this->sendResponse();
            return;

		}

    }

    function adddetails(){
        $user_id = $this->id;

        $chemist = $this->input_data['chemist'];

        $error = [];

        if(empty($chemist)) {
            $error['error']['chemist'] = 'Empty Chemist Data';
        }

        $chemist_name = trim($chemist['name']);
        $chemist_address = trim($chemist['address']);
        $chemist_state = trim($chemist['state']);
        $chemist_city = trim($chemist['city']);
        $chemist_pincode = trim($chemist['pincode']);

        if(empty($chemist_name)) {
            $error['error']['chemist']['chemist_name'] = "Required";
        }

        if(empty($chemist_address)){
            $error['error']['chemist']['chemist_address'] = "Required";
        }

        if(empty($chemist_state)){
            $error['error']['chemist']['chemist_state'] = "Required";
        }

        if(empty($chemist_city)){
            $error['error']['chemist']['chemist_city'] = "Required";
        }

        $chemiststate = $this->model->get_records(['id' => $chemist_state], 'state');
        if(empty($chemistState)){
            $error['error']['chemist']['chemist_state'] = 'Invalid State';
        }

        $chemistcity = $this->model->get_records(['city_id' => $chemist_city, 'state_id' => $chemist_state], 'cities');
        if(empty($chemistcity)){
            $error['error']['chemist']['chemist_city'] = 'Invalid City';
        }

        $doctors = $chemist['doctor'];
        
        if(count($doctors) <= 0) {
            $error['error']['chemist']['doctor'] = 'Empty Doctor Data';
        }

        foreach ($doctors as $k1 => $doctor) {
            $doctor_name = $doctor['name'];
            $doctor_speciality = $doctor['speciality'];
            $doctor_address = $doctor['address'];
            $doctor_state = $doctor['state'];
            $doctor_city = $doctor['city'];
            $doctor_pincode = $doctor['pincode'];

            if(empty($doctor_name)) {
                $error['error']['chemist']['doctor'][$k1]['doctor_name'] = "Required";
            }

            if(empty($doctor_speciality)){
                $error['error']['chemist']['doctor'][$k1]['doctor_speciality'] = "Required";
            }

            $specialityData = $this->model->get_records(['speciality_id' => $doctor_speciality], 'speciality');
            if(empty($specialityData)){
                $error['error']['chemist']['doctor'][$k1]['doctor_speciality'] = 'Invalid Speciality';
            }

            $doctorstate = $this->model->get_records(['id' => $doctor_state], 'state');
            if(empty($chemistState)){
                $error['error']['chemist']['doctor'][$k1]['doctor_state'] = 'Invalid State';
            }

            $doctorcity = $this->model->get_records(['city_id' => $doctor_city, 'state_id' => $doctor_state], 'cities');
            if(empty($chemistcity)){
                $error['error']['chemist']['doctor'][$k1]['doctor_city'] = 'Invalid City';
            }

            $potential = $doctor['potential'];

            if(count($potential) <= 0) {
                $error['error']['chemist']['doctor'][$k1]['potential'] = 'Empty Potential Data';

            }

            foreach ($potential as $k2 => $molecule) {
                $molecule_id = $molecule['molecule'];

                if(empty($molecule_id)) {
                    $error['error']['status'] = FALSE;
                    $error['error']['chemist']['doctor'][$k1]['potential'][$k2]['molecule'] = "Molecule Required";
                    continue;
                }

                $moleculedata = $this->model->get_records(['molecule_id' => $molecule_id], 'molecule');
                if(empty($moleculedata)){
                    $error['error']['chemist']['doctor'][$k1]['potential'][$k2]['molecule'] = 'Invalid Molecule';
                }

                $brands = $molecule['brand'];

                if(count($brands) <= 0) {
                    $error['error']['chemist']['doctor'][$k1]['potential'][$k2]['brand']['message'] = 'Empty Brand Data';
                }

                foreach ($brands as $k3 => $brand) {
                    $brand_id = isset($brand['id']) ? $brand['id'] : '';
                    $isSKU = $brand['isSKU'];
                    $other = $brand['other'];
                    $brand_rxn = isset($brand['rxn']) ? $brand['rxn'] : '';
                    $brand_name = isset($brand['name']) ? $brand['name'] : '';
                    $skus = isset($brand['sku']) ? $brand['sku'] : '';;

                    // validate
                    $brand_error = $error['error']['chemist']['doctor'][$k1]['potential'][$k2]['brand'];

                    if($isSKU) {
                        if(empty($brand_id)) {
                            $brand_error[$k3]['brand_id'] = $brand_id;
                            $brand_error[$k3]['message'] = 'Required';
                        }

                        $branddata = $this->model->get_records(['brand_id' => $brand_id, 'molecule_id' => $molecule_id], 'brand');
                        if(empty($branddata)){
                            $brand_error[$k3]['brand_id'] = $brand_id;
                            $brand_error[$k3]['message'] = 'Invalid Brand';
                        }

                        if(count($skus) <= 0) {
                            $brand_error[$k3]['sku'] = 'Empty SKU Data';
                        }

                        foreach ($skus as $k4 => $sku) {
                            $sku_id = $sku['id'];
                            $sku_rxn = $sku['rxn'];

                            if(empty($sku_id)) {
                                $error['error']['chemist']['doctor'][$k1]['potential'][$k2]['brand'][$k3]['sku'][$k4]['sku_id'] = 'Required';
                            }

                            $skudata = $this->model->get_records(['sku_id' => $sku_id, 'brand_id' => $brand_id], 'sku');
                            if(empty($skudata)){
                                $error['error']['chemist']['doctor'][$k1]['potential'][$k2]['brand'][$k3]['sku'][$k4]['sku_id'] = $sku_id;
                                $error['error']['chemist']['doctor'][$k1]['potential'][$k2]['brand'][$k3]['sku'][$k4]['message'] = 'Invalid SKU';
                            }

                        }
                    } else {
                        if($other === 'yes') {
                            if(empty($brand_name) || empty($brand_rxn)) {
                                $error['error']['chemist']['doctor'][$k1]['potential'][$k2]['brand'][$k3]['brand_name'] = 'Required';
                            }
                        } else if($other === 'no') {
                            if(empty($brand_id) || empty($brand_rxn)) {
                                $error['error']['chemist']['doctor'][$k1]['potential'][$k2]['brand'][$k3]['rxn'] = 'Required';
                            }
                        }
                    }
                }
            }

        }

        if(count($error) <= 0){

            $chemistdata = [];
            $doctordata = [];
            $moleculedata = [];
            $branddata = [];
            $skudata = [];
            $error = [];

            $chemistdata['chemist_name'] = trim($this->input_data['chemist']['name']);

            $chemistdata['address'] = trim($this->input_data['chemist']['address']);
            $chemistdata['state'] = trim($this->input_data['chemist']['state']);
            $chemistdata['city'] = trim($this->input_data['chemist']['city']);
            $chemistdata['pincode'] = trim($this->input_data['chemist']['pincode']);
            $chemistdata['users_id'] = $user_id;
            
            $chemist_id = $this->model->_insert($chemistdata, 'chemist');

            foreach($this->input_data['chemist']['doctor'] as $doctor){
                $doctordata['chemist_id'] = $chemist_id;
                $doctordata['users_id'] = $user_id;
                $doctordata['doctor_name'] = $doctor['name'];
                $doctordata['speciality'] = $doctor['speciality'];
                $doctordata['address'] = $doctor['address'];
                $doctordata['state'] = $doctor['state'];
                $doctordata['city'] = $doctor['city'];
                $doctordata['pincode'] = $doctor['pincode'];

                $doctor_id = $this->model->_insert($doctordata, 'doctor');
                
                foreach($doctor['potential'] as $molecule){
                    $moleculedata['chemist_id'] = $chemist_id;
                    $moleculedata['doctor_id'] = $doctor_id;
                    $moleculedata['users_id'] = $user_id;
                    $moleculedata['molecule'] = $molecule['molecule'];

                    $molecule_id = $this->model->_insert($moleculedata, 'users_molecule');

                    foreach($molecule['brand'] as $brand){

                        $branddata['chemist_id'] = $chemist_id;
                        $branddata['doctor_id'] = $doctor_id;
                        $branddata['users_id'] = $user_id;
                        $branddata['molecule_id'] = $molecule_id;
                        $branddata['brand_id'] = $brand['other'] == 'no' ? $brand['id'] : NULL;
                        $branddata['brand_name'] = $brand['other'] == 'yes' ? $brand['name'] : NULL;
                        $branddata['issku'] = $brand['isSKU'];
                        $branddata['other'] = $brand['other'];
                        $branddata['rxn'] = $brand['isSKU'] == FALSE ? $brand['rxn'] : '';

                        $brand_id = $this->model->_insert($branddata, 'users_brand');

                        if($brand['isSKU'] == TRUE){
                            foreach($brand['sku'] as $sku){
                                $skudata['chemist_id'] = $chemist_id;
                                $skudata['doctor_id'] = $doctor_id;
                                $skudata['molecule_id'] = $molecule_id;
                                $skudata['brand_id'] = $brand_id;
                                $skudata['users_id'] = $user_id;
                                $skudata['sku_id'] = $sku['id'];
                                $skudata['rxn'] = $sku['rxn'];
        
                                $sku_id = $this->model->_insert($skudata, 'users_sku');
        
                            }
                        }                    

                    }

                }

            }

            $this->response['code'] = 200;
            $this->response['message'] = "Data Added Successfully";
            $this->sendResponse();
        }

        $this->response['code'] = 400;
        $this->response['message'] = $error;
        $this->sendResponse();
        
    }

}