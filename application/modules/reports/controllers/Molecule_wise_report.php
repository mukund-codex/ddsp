<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Molecule_wise_report extends Reports_Controller
{   
	private $module = 'molecule_wise_report';
	private $controller = 'reports/molecule_wise_report';
    private $model_name = 'mdl_molecule_wise_report';
    private $columns = ['ZBM', 'Zone', 'ABM', 'Area', 'MR Name', 'HQ', 'Chemist Name', 'Chemist Address', 'Doctor Name', 'Doctor Address'];
    
	function __construct() {

		parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            $this->columns,
        );
    }

    function index(){

        $columns = ['ZBM', 'Zone', 'ABM', 'Area', 'MR Name', 'HQ', 'Chemist Name', 'Chemist Address', 'Doctor Name', 'Doctor Address'];

        $moleculedata = $this->model->get_molecule_list();

        foreach($moleculedata as $key => $molecule){
            $molecule_name = $molecule->molecule_name;
            array_push($columns,$molecule_name);
        }

		if( ! $this->session->is_logged_in() ){
			show_error("Forbidden", 403);
		}

		$sfilters = array();

        $offset = (int) $this->input->post('page');

        $post_array = $this->input->post();
        unset($post_array['page']);
        unset($post_array['search']);       
		
		$this->data['collection'] = $this->model->get_collection($count = FALSE, $sfilters, $post_array, $this->perPage, $offset);
        $totalRec = $this->model->get_collection($count = TRUE, $sfilters, $post_array);
        $this->paginate($this->data['controller'], $totalRec, 4);
		$this->data['plugins'] = ['paginate','fancybox'];
        
        /* columns for list */
        $table_columns = $columns;
        $this->data['all_action'] = FALSE;

        $this->set_defaults([
            'listing_url'=> $this->controller . '/index', 
            'download_url'=> $this->controller . '/download' ,
            'module_title'=> $this->module . ' Report'
        ]);
        $this->set_view_columns($table_columns);
        /* END columns */
        $records_view = $this->data['controller'].'/records';
        
        $role = $this->session->get_field_from_session('role');

        $this->data['permissions'] = ['download'];
        
        $template = ( in_array($role, ['SA', 'A'])) ? '_admin' : '_user';

        $filter_columns = $this->model->get_filters();
        
        $this->data['show_filters'] = TRUE;
        $this->data['date_filters'] = TRUE;
        
        $this->set_view_columns($table_columns, [], $filter_columns);

        if($this->input->post('search') == TRUE) {
			$this->load->view($records_view, $this->data);
        } else {
            $this->data['records_view'] = $records_view;
			$this->set_view($this->data, 'template/components/container/lists', $template);
		}
	}
    
}
