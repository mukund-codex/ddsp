<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Rxn extends User_Controller
{
	private $module = 'rxn';
    private $model_name = 'mdl_rxn';
    private $controller = 'rxn';
    private $settings = [
        'permissions'=> ['add','edit','remove','download','upload'],
    ];
    
    private $scripts = ['doctor.js'];    

	function __construct() {
        $user_role = $this->session->get_field_from_session('role');
        
        parent::__construct( 
            $this->module, 
            $this->controller, 
            $this->model_name,
            $this->settings,    
            $this->scripts,
            ['jCrop','fancybox']
        );

        $this->set_defaults();
    }

	function options(){
		$this->session->is_Ajax_and_logged_in();

		$limit = $this->dropdownlength;
		$page = intval($_POST['page']) - 1;
		$page = ($page <= 0) ? 0 : $page;

		$s_term = (isset($_POST['search'])) ? $this->db->escape_like_str($_POST['search']) : '';
		$id = (isset($_POST['id'])) ? (int) $this->input->post('id') : 0;

		if($id){ $filters['region_id'] = $id; }

		$new = array(); $json['results'] = array();

		$_options = $this->model->get_options($s_term, 'area_name', $filters, $page * $limit, $limit);
		$_opt_count = count($this->model->get_options($s_term, 'area_name', $filters));

		foreach($_options as $option){
			$new['id'] = $option->area_id;
			$new['text'] = $option->area_name;

			array_push($json['results'], $new);
		}
		
		$more = ($_opt_count > count($_options)) ? TRUE : FALSE;
		$json['pagination']['more'] = $more;

		echo json_encode($json);
    }

	function uploadcsv(){
        
		$this->session->is_Ajax_and_admin_logged_in();
		/*upload csv file */
        
		if(! is_uploaded_file($_FILES['csvfile']['tmp_name'])){
			echo json_encode(['errors'=> ['csvfile'=> '<label class="error">Please Upload CSV file</label>']]); exit;
		}

		if(!in_array($_FILES['csvfile']['type'], array('application/vnd.ms-excel', 'application/csv', 'text/csv')) ){
			echo json_encode(['errors'=> ['csvfile'=> '<label class="error">Only .CSV files allowed</label>']]); exit;
		}

		$file = $_FILES['csvfile']['tmp_name'];
		$handle = fopen($file, "r");
		$cnt = 0; $newrows = 0;
        
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE){
            
			if(! $cnt){
                $cnt++; continue;
            }

			if(count($data) !== 3) { continue; }            
			
			$brand_name = trim($data[0]);

			$brand = $this->model->get_or_records(['brand_name'=> $brand_name], 'brand', ['brand_id'], '', 1);

			if(empty($brand)){ continue; }

			$brand_id = $brand[0]->brand_id;

			$sku = trim($data[1]);

			if(!empty($sku)){
				if(!ctype_alnum($sku)){
					continue;
				}

				$skuData = $this->model->get_or_records(['sku' => $sku, 'brand_id' => $brand_id], 'sku', ['sku_id'], '', 1);

				$sku_id = $skuData[0]->sku_id;

			}else{
				$sku_id = '';
			}
			
			$rxn = trim($data[2]);         

			/* $record = $this->model->get_or_records(['sku'=> $sku], 'sku', ['sku_id'], '', 1);
			if(count($record)) {
				continue;
			} */

			$insert['brand_id'] = $brand_id;
			$insert['sku_id'] = $sku_id;
			$insert['rxn'] = $rxn;

            $this->model->_insert($insert);

            $newrows++;
		}

		fclose($handle);

		echo json_encode(['newrows'=> "$newrows record(s) added successfully"]);
	}
}
