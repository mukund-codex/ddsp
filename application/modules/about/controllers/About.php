<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class About extends User_Controller
{
	private $module = 'about';
    private $model_name = 'mdl_about';
    private $controller = 'about';
    private $settings = [
		'permissions'=> ['edit' ,'download'],
		'filters'=>[
			'date_filters'=>false,
		],		
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
	
	

}
