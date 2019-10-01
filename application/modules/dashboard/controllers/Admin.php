<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends Admin_Controller
{
	private $module = 'admin';
	private $controller = 'dashboard/admin';
	private $model_name = 'mdl_admin';
	
	function __construct() {
		parent::__construct($this->module, $this->controller, $this->model_name);
		$this->load->driver('cache', array('adapter' => 'file'));
	}

	function index(){
		if( ! $this->session->is_admin_logged_in() ){
			redirect('admin','refresh');
		}

		$filters = [];
		
		/* $zone_id = (int) $this->input->post('zone_id');

		if($zone_id) {
			$filters['temp.zone_id'] = $zone_id;
		} */

		$this->data['mainmenu'] = 'dashboard';

		$dashboard_widget_data = $this->cache->get('dashboard_widget_data');

		if(empty($dashboard_widget_data)) {
			$dashboard_widget_data = $this->model->get_dashboard_collection($filters);
		}
		
		$this->data['chemist_count'] = !empty($dashboard_widget_data) ? (int) $dashboard_widget_data['chemist_count'] : 0;
		$this->data['doctor_count'] = !empty($dashboard_widget_data) ? (int) $dashboard_widget_data['doctor_count'] : 0;
		$this->data['asm_count'] = !empty($dashboard_widget_data) ? (int) $dashboard_widget_data['asm_count'] : 0;
		$this->data['zsm_count'] = !empty($dashboard_widget_data) ? (int) $dashboard_widget_data['zsm_count'] : 0;

		$dashboard_table_data = $this->cache->get('dashboard_table_data');

		if(empty($dashboard_table_data)) {
			$dashboard_table_data = $this->model->dashboard_table_collection();
		}

		$this->data['dashboard_table_data'] = $dashboard_table_data;

		$this->cache->save('dashboard_widget_data', $dashboard_widget_data, 300);
		$this->cache->save('dashboard_table_data', $dashboard_table_data, 300);
		$this->data['js'] = ['dashboard.js'];
		
    	$this->set_view($this->data, $this->controller . '/dashboard',  '_admin');
    }
    
    function logout() {
        $session_key = config_item('session_data_key');
		$sessionData = array('user_id'=>'',	'user_name'=>'', 'role'=>'');
		
		$this->session->unset_userdata($session_key, $sessionData);
     	redirect('/admin','refresh');
    }
}
