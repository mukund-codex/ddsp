<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron_notification_request extends MX_Controller
{
	function __construct()
	{
		parent::__construct();
        $this->load->model('mdl_notification_request');
        $this->load->helper(array('notification'));
	}

	public function index()
	{

        /** 
         * Check Cron In Progress
         */
        $cron_in_progress = $this->mdl_notification_request->get_records([],'notification_cron_status',['status']);
        
        if($cron_in_progress[0]->status){
            echo 'Cron Active';
            exit;
        }

        /**
         * Update Cron Status to Active
         */
        
        $this->mdl_notification_request->_update(['c_status_id' => 1],['status' => 1],'notification_cron_status');        
        
        // $notification_request = $this->mdl_notification_request->get_records(['request_status' => 0],'notification_request',[],'',0,0,'device_id,type');
        $notification_request = $this->mdl_notification_request->get_notification_request();
        
        if(!empty($notification_request))
		{	
            $data = array();
            
            foreach ($notification_request as $value) {
                
                $data[$value->device_type][$value->n_req_id]['device_ids'][$value->user_id][] = $value->device_id;
                $data[$value->device_type][$value->n_req_id]['n_req_id'] = $value->n_req_id;
                $data[$value->device_type][$value->n_req_id]['title'] = $value->title;
                $data[$value->device_type][$value->n_req_id]['session_owner'] = $value->session_owner;
                $data[$value->device_type][$value->n_req_id]['insert_id'] = $value->insert_id;
                $data[$value->device_type][$value->n_req_id]['title'] = $value->title;
                $data[$value->device_type][$value->n_req_id]['type'] = $value->type;
                $data[$value->device_type][$value->n_req_id]['image'] = $value->image;
                $data[$value->device_type][$value->n_req_id]['desc'] = $value->desc;
                $data[$value->device_type][$value->n_req_id]['pdf_file'] = $value->pdf_file;
                $data[$value->device_type][$value->n_req_id]['ppt_file'] = $value->ppt_file;
                $data[$value->device_type][$value->n_req_id]['video_file'] = $value->video_file;
                $data[$value->device_type][$value->n_req_id]['file_type'] = $value->file_type;
                $data[$value->device_type][$value->n_req_id]['download_status'] = $value->download_status;
                $data[$value->device_type][$value->n_req_id]['date'] = (!empty($value->date) || $value->date != '0000-00-00') ? $value->date : NULL;

            }
            //echo '<pre>';print_r($data);exit;
            if(count($data)){               
                $device_types = array_keys($data);

                foreach($device_types as $k1 => $v1){

                    foreach($data[$v1] as $req_key => $notf_arr){
                        
                        $device_ids = $notf_arr['device_ids'];
                        
                        foreach ($device_ids as $user_id => $register_user_id) {
                            $register_ids = array_values($register_user_id);
                           
                            if(empty($register_ids)) { continue; } 

                            $request_id = $req_key;
                            $session_owner = $notf_arr['session_owner'];
                            $insert_id = $notf_arr['insert_id'];
                            $title = $notf_arr['title'];
                            $type = $notf_arr['type'];
                            $image = $notf_arr['image'];
                            $desc = $notf_arr['desc'];
                            $pdf_file = $notf_arr['pdf_file'];
                            $ppt_file = $notf_arr['ppt_file'];
                            $video_file = $notf_arr['video_file'];
                            $file_type = $notf_arr['file_type'];
                            $download_status = $notf_arr['download_status'];
                            $date = $notf_arr['date'];
                            $each_device_type = $v1;
                            
                        notification(
                            $register_ids,
                            $session_owner,
                            $insert_id,
                            $title,
                            $type,
                            $image,
                            $desc,
                            $pdf_file,
                            $ppt_file,
                            $video_file,
                            $file_type,
                            $download_status,
                            $date,
                            $each_device_type,
                            $user_id
                        );
                        
                        $this->mdl_notification_request->_delete('n_req_id',[$request_id],'notification_request');
                        
                        }
                                               
    
                    }

                }
                
            }
    
        }

        $this->mdl_notification_request->_update(['c_status_id' => 1],['status' => 0],'notification_cron_status');
        echo 'Success';
	}

}
