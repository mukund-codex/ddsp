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
		 *
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
		 * @api {post} /api/module/communication Communication
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

    function state_molecule(){

        // If the token is valid, send State, City and Molecule data to APP
		// Else return the error message to the APP

		/**
		 * @api {post} /api/module/communication Communication
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

}