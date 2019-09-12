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
            $get_communication_list = $this->model->get_records([], 'communication', [], 'c_id desc');
           
            $data = [];
            
    
            if(count($get_communication_list) > 0)  {
                foreach ($get_communication_list as $key => $value) {
                    $media_data = [];
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
                        }   
                    }
                    if(empty($media_data)){
                        $input_data['media'] = [
                            "image" => [],
                            "document" => [],
                        ];
                    }else{
                        $input_data['media'] = $media_data;
                    }
                    
                    array_push($data, $input_data); 
                }
                              
            }

            $this->response['code'] = 200;
            $this->response['data'] = ['posts' => $data];
            $this->response['message'] = empty($data) ? "No Data Found" : "Communication List";
            $this->sendResponse();
            return;

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
        return;

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
        $ccount['image'] = base_url('assets/images/dashboardImages/chemist.png');
        

        $dcount['title'] = 'Doctor';
        $dcount['count'] = ($data) ? $data[0]['doctor_count'] : 0;            
        $dcount['image'] = base_url('assets/images/dashboardImages/doctor.png');
        
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
                    $speciality_data['image'] = base_url('assets/images/dashboardImages/derma.png');
                }elseif($speciality_data['speciality_name'] == 'CP'){
                    $speciality_data['color'] = '#F9E8E7';
                    $speciality_data['image'] = base_url('assets/images/dashboardImages/cp.png');
                }elseif($speciality_data['speciality_name'] == 'GP'){
                    $speciality_data['color'] = '#FFFBD3';
                    $speciality_data['image'] = base_url('assets/images/dashboardImages/gp.png');
                }elseif($speciality_data['speciality_name'] == 'Gynae'){
                    $speciality_data['color'] = '#FFE2F7';
                    $speciality_data['image'] = base_url('assets/images/dashboardImages/gyne.png');
                }else{
                    $speciality_data['color'] = '';
                    $speciality_data['image'] = '';
                }
                
                

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
        return;

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
        return;

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
                $input_data = [];
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
                }
                $input_data['city'] = $citydata;
                
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
        return;

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

        $category = !empty($this->input->post('category')) ? strtolower($this->input->post('category')) : '';
        $doctor_chemist_id  = (int) $this->input->post('id');

        if(!array_key_exists('images', $_FILES) || ! is_array($_FILES['images']['name']) ) {
            $this->response['code'] = 400;
            $this->response['message'] = 'Invalid key. Expecting Images Array.';
            $this->sendResponse();
            return;
        }

        if(count($_FILES['images']['name']) <= 0 || count($_FILES['images']['name']) > 3 ) {
            $this->response['code'] = 400;
            $this->response['message'] = 'Min 1 Image and Max 3 Images Required';
            $this->sendResponse();
            return;
        }

        if(! in_array($category, ['chemist', 'doctor'])) {
            $this->response['code'] = 400;
            $this->response['message'] = 'Invalid Category';
            $this->sendResponse();
            return;
        }

        $chemist_doctor = [];
        $chemist_doctor['users_id'] = $user_id;

        $is_max_fileupload = [];
        $is_max_fileupload['category'] = $category;
        $is_max_fileupload['users_id'] = $user_id;

        if($category === 'chemist') {
            $chemist_doctor['chemist_id'] = $doctor_chemist_id;
            $is_doctor_chemist_exist = $this->model->get_records($chemist_doctor, 'chemist', ['chemist_id']);

            $is_max_fileupload['chemist_id'] = $doctor_chemist_id;    
    
        } else if($category === 'doctor') {
            $chemist_doctor['doctor_id'] = $doctor_chemist_id;
            $is_doctor_chemist_exist = $this->model->get_records($chemist_doctor, 'doctor', ['doctor_id']);

            $is_max_fileupload['doctor_id'] = $doctor_chemist_id;
        }
        
        if(count($is_doctor_chemist_exist) < 0) {
            $this->response['code'] = 400;
            $this->response['message'] = "Invalid $category";
            $this->sendResponse();
            return;
        }


        $is_chemist_max_uploaded = $this->model->get_records($is_max_fileupload, 'images', ['image_id']);

        $total_count = count($_FILES['images']['name']) + count($is_chemist_max_uploaded);

        if($total_count > 3) {
            $this->response['code'] = 400;
            $this->response['message'] = "Maximum 3 Images Uploaded";
            $this->sendResponse();
            return;
        }

        
        $path = ($category == 'doctor') ? 'uploads/doctorImages' : 'uploads/chemistImages';

        $this->load->helper('upload_media');
        $is_file_upload = upload_media('images', $path, ['jpeg', 'png', 'jpg'], 10000000);

        if(array_key_exists('error', $is_file_upload)) {
            $this->response['code'] = 400;
            $this->response['message'] = $is_file_upload['error'];
            $this->error = array('message'=> $is_file_upload['error']);
            $this->sendResponse();
            return;
        }

        $data = [];

        foreach($is_file_upload as $upload){
            $file_upload = [];
                
            if($category == 'chemist'){
                $file_upload['chemist_id'] = $doctor_chemist_id;
            }else{
                $file_upload['doctor_id'] = $doctor_chemist_id;
            }
            $file_upload['users_id'] = $user_id;
            $file_upload['category'] = $category;
            $file_upload['image_name'] = $upload['file_name'];
            $file_upload['image_path'] = $upload['full_path'];
            array_push($data, $file_upload);            
        }

        if(count($data)) {
            $is_files = $this->model->_insert_batch($data, 'images');
        }

        $this->response['code'] = 200;
        $this->response['message'] = 'Images Uploaded Successfully';
        $this->sendResponse();
        return;
    }

    function adddetails(){
        
        // Add Chemist & Doctor To the System
       /**
       * @api {post} /api/module/adddetails Add Chemist/Doctor
       * @apiName adddetails
       * @apiGroup Module
       *

       * @apiParam {Object} chemist
       * @apiParam {String} chemist.name Chemist Name
       * @apiParam {String} chemist.address Chemist Address
       * @apiParam {Number} chemist.state Chemist State
       * @apiParam {Number} chemist.city Chemist City
       * @apiParam {Number} chemist.pincode Chemist Pincode
       *
       * @apiParam {Object[]} chemist.doctor
       * @apiParam {Number} chemist.doctor.id Doctor ID
       * @apiParam {String} chemist.doctor.name Doctor Name
       * @apiParam {Number} chemist.doctor.speciality Doctor Speciality
       * @apiParam {String} chemist.doctor.address Doctor Address
       * @apiParam {Number} chemist.doctor.state Doctor State
       * @apiParam {Number} chemist.doctor.city Doctor City
       * @apiParam {Number} chemist.doctor.pincode Doctor Pincode
       * @apiParam {String {yes, no}} chemist.doctor.other Other Doctor
       *
       * @apiParam {Object[]} chemist.doctor.potential Potential/Molecule List
       * @apiParam {Object} chemist.doctor.potential.molecule 
       * @apiParam {Number} chemist.doctor.potential.molecule.molecule Molecule ID
       * @apiParam {Object[]} chemist.doctor.potential.molecule.brand Brand List
       * @apiParam {Number} chemist.doctor.potential.molecule.brand.id Brand ID
       * @apiParam {Boolean} chemist.doctor.potential.molecule.brand.isSku isSku
       * @apiParam {Number} chemist.doctor.potential.molecule.brand.rxn RXN
       * @apiParam {String} chemist.doctor.potential.molecule.brand.name Name
       * @apiParam {String {yes, no}} chemist.doctor.potential.molecule.brand.other Other
       *
       * @apiParam {Object[]} chemist.doctor.potential.molecule.brand.sku SKU List
       * @apiParam {Number} chemist.doctor.potential.molecule.brand.sku.id SKU ID
       * @apiParam {Number} chemist.doctor.potential.molecule.brand.sku.rxn RXN
       *
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
       *
       * @apiErrorExample {json} Error-Response:
       *     HTTP/1.1 400 Bad Request
       *     {
       *       {
       *            "message": "Please enter the chemist name.",
       *            "error": "",
       *            "code": 400,
       *            "data": {
       *                "request_id": 1568111057.487055
       *            }
       *        }
       *    }
       */

        $user_id = $this->id;

        $is_error = $this->validate_details();
        
        if($is_error) {
            $this->response = $is_error;
            $this->sendResponse();
            return;
        }
        
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

            if($doctor['other'] == 'no'){
                $doc_id = $doctor['id'];
                $doctorrecords = $this->model->get_records(['doctor_id' => $doctor_id], 'doctor', ['doctor_id']);
                if(!empty($doctorrecords)){
                    $doctor_id = $doctorrecords[0]->doctor_id;
                } 
            }else{
                $doctor_id = $this->model->_insert($doctordata, 'doctor');
            }

            foreach($doctor['potential'] as $molecule){
                $moleculedata['chemist_id'] = $chemist_id;
                $moleculedata['doctor_id'] = $doctor_id;
                $moleculedata['users_id'] = $user_id;
                $moleculedata['molecule'] = $molecule['id'];

                $molecule_id = $this->model->_insert($moleculedata, 'users_molecule');

                foreach($molecule['brand'] as $brand){

                    $branddata['chemist_id'] = $chemist_id;
                    $branddata['doctor_id'] = $doctor_id;
                    $branddata['users_id'] = $user_id;
                    $branddata['molecule_id'] = $molecule_id;
                    $branddata['brand_id'] = $brand['other'] == 'no' ? $brand['id'] : NULL;
                    $branddata['brand_name'] = $brand['other'] == 'yes' ? $brand['name'] : NULL;
                    $branddata['issku'] = $brand['isSku'];
                    $branddata['other'] = $brand['other'];
                    $branddata['rxn'] = $brand['isSku'] == FALSE ? $brand['rxn'] : '';

                    $brand_id = $this->model->_insert($branddata, 'users_brand');

                    if($brand['isSku'] == TRUE){
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
        return;       
       
    }

   function validate_details(){
        $chemist = isset($this->input_data['chemist']) ? $this->input_data['chemist'] : '';

        $error = [];

        if(empty($chemist)) {
            $this->response['code'] = 400;
            $this->response['message'] = 'Please enter the chemist details.';
            return $this->response;
        }

        $chemist_name = isset($chemist['name']) ? trim($chemist['name']) : '';
        $chemist_address = isset($chemist['address']) ? trim($chemist['address']) : '';
        $chemist_state = isset($chemist['state']) ? trim($chemist['state']) : '';
        $chemist_city = isset($chemist['city']) ? trim($chemist['city']) : '';
        $chemist_pincode = isset($chemist['pincode']) ? trim($chemist['pincode']) : '';

        if(empty($chemist_name) || !array_key_exists('name', $chemist)) {
            $this->response['code'] = 400;
            $this->response['message'] = 'Please enter the chemist name.';
            return $this->response;
        }

        if(empty($chemist_address)){
            $this->response['code'] = 400;
            $this->response['message'] = 'Please enter the chemist address.';
            return $this->response;
        }

        if(empty($chemist_state)){
            $this->response['code'] = 400;
            $this->response['message'] = 'Please enter the chemist state.';
            return $this->response;
        }

        if(empty($chemist_city)){
            $this->response['code'] = 400;
            $this->response['message'] = 'Please enter the chemist city.';
            return $this->response;
        }

        if(empty($chemist_pincode) || strlen($chemist_pincode) != 6){ 
            $this->response['code'] = 400;
            $this->response['message'] = 'Please enter correct chemist Pincode.';
            return $this->response;
        }

        $chemistState = $this->model->get_records(['id' => $chemist_state], 'state');
        if(empty($chemistState)){
            $this->response['code'] = 400;
            $this->response['message'] = 'Please enter the correct chemist state.';
            return $this->response;
        }

        $chemistCity = $this->model->get_records(['city_id' => $chemist_city, 'state_id' => $chemist_state], 'cities');
        if(empty($chemistCity)){
            $this->response['code'] = 400;
            $this->response['message'] = 'Please enter the  correct chemist city.';
            return $this->response;
        }

        $doctors = $chemist['doctor'];
        
        if(count($doctors) <= 0) {
            $this->response['code'] = 400;
            $this->response['message'] = 'Please enter the Doctor details of '.$chemist_name;
            return $this->response;
        }

        foreach ($doctors as $k1 => $doctor) {
            $doctor_name = isset($doctor['name']) ? $doctor['name'] : '';
            $doctor_speciality = isset($doctor['speciality']) ? $doctor['speciality'] : '';
            $doctor_address = isset($doctor['address']) ? $doctor['address'] : '';
            $doctor_state = isset($doctor['state']) ? $doctor['state'] : '';
            $doctor_city = isset($doctor['city']) ? $doctor['city'] : '';
            $doctor_pincode = isset($doctor['pincode']) ? $doctor['pincode'] : '';
            $doctor_other = isset($doctor['other']) ? $doctor['other'] : ''; 
           
            if(empty($doctor_address)){
                $this->response['code'] = 400;
                $this->response['message'] = "Please enter Address of Doctor $doctor_name";
                return $this->response;
            }

            if(empty($doctor_other)){
                $this->response['code'] = 400;
                $this->response['message'] = 'Please enter the Other Doctor.';
                return $this->response;
            }

            if($doctor_other == 'yes'){                
                if(empty($doctor_name)) {
                    $this->response['code'] = 400;
                    $this->response['message'] = 'Please enter the Doctor name.';
                    return $this->response;
                }
            }else{
                $doctor_id = isset($doctor['id']) ? $doctor['id'] : '';
                if(empty($doctor_id)){
                    $this->response['code'] = 400;
                    $this->response['message'] = 'Please select valid Doctor.';
                    return $this->response;
                }
                $doctorrecords = $this->model->get_records(['doctor_id' => $doctor_id], 'doctor');
                if(empty($doctorrecords)){
                    $this->response['code'] = 400;
                    $this->response['message'] = 'Please enter the Valid Doctor name.';
                    return $this->response;
                }
            }

            if(empty($doctor_speciality)){
                $this->response['code'] = 400;
                $this->response['message'] = "Please enter Speciality for Doctor $doctor_name";
                return $this->response;
            }

            if(empty($doctor_pincode) || strlen($doctor_pincode) != 6){
                $this->response['code'] = 400;
                $this->response['message'] = "Please enter correct Pincode for Doctor $doctor_name";
                return $this->response;
            }

            $specialityData = $this->model->get_records(['speciality_id' => $doctor_speciality], 'speciality');
            if(empty($specialityData)){
                $this->response['code'] = 400;
                $this->response['message'] = "Please enter correct Speciality for Doctor $doctor_name";
                return $this->response;
            }

            $doctorstate = $this->model->get_records(['id' => $doctor_state], 'state');
            if(empty($doctorstate)){
                $this->response['code'] = 400;
                $this->response['message'] = "Please enter correct State for Doctor $doctor_name";
                return $this->response;
            }

            $doctorcity = $this->model->get_records(['city_id' => $doctor_city, 'state_id' => $doctor_state], 'cities');
            if(empty($doctorcity)){
                $this->response['code'] = 400;
                $this->response['message'] = "Please enter correct city for Doctor $doctor_name";
                return $this->response;
            }

            $potential = $doctor['potential'];

            if(count($potential) <= 0) {
                $this->response['code'] = 400;
                $this->response['message'] = "Please enter Potential Detials for Doctor $doctor_name";
                return $this->response;

            }

            foreach ($potential as $k2 => $molecule) {
                $molecule_id = isset($molecule['id']) ? $molecule['id'] : '';

                if(empty($molecule_id)) {
                    $this->response['code'] = 400;
                    $this->response['message'] = "Please enter Molecule for Doctor $doctor_name";
                    return $this->response;
                }

                $moleculedata = $this->model->get_records(['molecule_id' => $molecule_id], 'molecule');
                if(empty($moleculedata)){
                    $this->response['code'] = 400;
                    $this->response['message'] = "Please enter correct Molecule for Doctor $doctor_name";
                    return $this->response;
                }
                $molcule_name = isset($moleculedata[0]->molecule_name) ? $moleculedata[0]->molecule_name : '';

                $brands = $molecule['brand'];

                if(count($brands) <= 0) {
                    $this->response['code'] = 400;
                    $this->response['message'] = "Please enter Brand Detials for $molcule_name for Doctor $doctor_name";
                    return $this->response;
                }

                foreach ($brands as $k3 => $brand) {
                    $brand_id = isset($brand['id']) ? $brand['id'] : '';
                    $isSKU = isset($brand['isSku']) ? (bool) $brand['isSku'] : '';
                    $other = isset($brand['other']) ? $brand['other'] : '';
                    $brand_rxn = isset($brand['rxn']) ? $brand['rxn'] : '';
                    $brand_name = isset($brand['name']) ? $brand['name'] : '';
                    $skus = isset($brand['sku']) ? $brand['sku'] : '';

                    /* if(empty($isSKU)) {
                        $this->response['code'] = 400;
                        $this->response['message'] = "Please enter correct Brand details for $molcule_name for Doctor $doctor_name";
                        return $this->response;
                    } */

                    if(empty($other) || !in_array($other, ['yes','no'])) {
                        $this->response['code'] = 400;
                        $this->response['message'] = "Please enter correct Brand details for $molcule_name for Doctor $doctor_name";
                        return $this->response;
                    }

                    // validate

                    if($isSKU) {
                        if(empty($brand_id)) {
                            $this->response['code'] = 400;
                            $this->response['message'] = "Please enter Brand for $molcule_name for Doctor $doctor_name";
                            return $this->response;
                        }

                        $branddata = $this->model->get_records(['brand_id' => $brand_id, 'molecule_id' => $molecule_id], 'brand');
                        if(empty($branddata)){
                            $this->response['code'] = 400;
                            $this->response['message'] = "Please enter Valid Brand for $molcule_name for Doctor $doctor_name";
                            return $this->response;
                        }

                        $brand_name = isset($branddata[0]->brand_name) ? $branddata[0]->brand_name : '';

                        if(count($skus) <= 0) {
                            $this->response['code'] = 400;
                            $this->response['message'] = "Please enter SKU for $brand_name of $molcule_name for Doctor $doctor_name";
                            return $this->response;
                        }

                        foreach ($skus as $k4 => $sku) {
                            $sku_id = isset($sku['id']) ? $sku['id'] : '';
                            $sku_rxn = isset($sku['rxn']) ? $sku['rxn'] : '';

                            if(empty($sku_id)) {
                                $this->response['code'] = 400;
                                $this->response['message'] = "Please enter SKU for $brand_name of $molcule_name for Doctor $doctor_name";
                                return $this->response;
                            }

                            $skudata = $this->model->get_records(['sku_id' => $sku_id, 'brand_id' => $brand_id], 'sku');
                            if(empty($skudata)){
                                $this->response['code'] = 400;
                                $this->response['message'] = "Please enter valid SKU for $brand_name of $molcule_name for Doctor $doctor_name";
                                return $this->response;
                            }

                            $sku_name = isset($skudata['sku']) ? $skudata['sku'] : '';

                            if(empty($sku_rxn)) {
                                $this->response['code'] = 400;
                                $this->response['message'] = "Please enter RXN $sku_name of $brand_name of $molcule_name for Doctor $doctor_name";
                                return $this->response;
                            }


                        }
                    } else {
                        if($other === 'yes') {
                            if(empty($brand_name)) {
                                $this->response['code'] = 400;
                                $this->response['message'] = "Please enter Brand Name of $molcule_name for Doctor $doctor_name";
                                return $this->response;
                            }
                            if(empty($brand_rxn)) {
                                $this->response['code'] = 400;
                                $this->response['message'] = "Please enter RXN for $brand_name of $molcule_name for Doctor $doctor_name";
                                return $this->response;
                            }
                        } else if($other === 'no') {    
                            if(empty($brand_id)) {
                                $this->response['code'] = 400;
                                $this->response['message'] = "Please enter correct Brand Name of $molcule_name for Doctor $doctor_name";
                                return $this->response;
                            }

                            $brandrecords = $this->model->geT_records(['brand_id' => $brand_id], 'brand');
                            if(empty($brandrecords)){
                                $this->response['code'] = 400;
                                $this->response['message'] = "Please enter correct Brand Name of $molcule_name for Doctor $doctor_name";
                                return $this->response;
                            }

                            $brand_name = isset($brandrecords[0]->brand_name) ? $brandrecords[0]->brand_name : '';

                            if(empty($brand_rxn)) {
                                $this->response['code'] = 400;
                                $this->response['message'] = "Please enter RXN for $brand_name of $molcule_name for Doctor $doctor_name";
                                return $this->response;
                            }
                        }
                    }

                }
            }

        }
        
   }

}