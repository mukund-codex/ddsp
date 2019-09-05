<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

abstract class Manpower_Model extends MY_Model {

	private $p_key = 'users_id';
    private $table = 'manpower';
    private $tb_alias = 'm';

	function __construct() {
        parent::__construct($this->table, $this->p_key, $this->tb_alias);
    }
    
    function user_info(int $id, string $role) {
		$id = intval($id);		
        switch (strtoupper($role)) {
            case 'RSM':
                $key = 'r.region_id';
                break;
            case 'ASM':
                $key = 'a.area_id';
                break;
            case 'MR':
                $key = 'c.city_id';
                break;
            case 'ZSM':
                $key = 'z.zone_id';
                break;
            case 'NSM':
                $key = 'nz.national_zone_id';
                break;
            
            default:
                break;
        }

        $collection = $this->get_user_info([$key => $id,'users_type' => $role]);

        return $collection ?? [];
    }
    
    function get_user_info($filters = []){		
		$q = $this->db->select('u.*, zone_name, region_name, area_name')
		->from('manpower u')
		->join('zone z', 'u.users_zone_id = z.zone_id', 'LEFT')
		->join('region r', 'u.users_region_id = r.region_id', 'LEFT')
		->join('area a', 'u.users_area_id = a.area_id', 'LEFT')
		->join('city c', 'u.users_city_id = c.city_id', 'LEFT');
		
		if(sizeof($filters)){
			$q->where($filters);
		}	
		$query = $q->get();
		return $query->row_array();
	}

}