<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if (!function_exists('notification')) {

  function notification($registrationIds,$user_id,$id,$title,$type,$image = '',$desc = '',$pdf_file = '',$ppt_file= '',$video_file= '',$file_type= '',$download_status= '',$date = '', $device_type = false,$doc_id = '')
  {
    $chunk = array_chunk($registrationIds,1000,true);
    fcm_push($chunk[0],$user_id,$id,$title,$type,$image,$desc,$pdf_file,$ppt_file,$video_file,$file_type,$download_status,$date, $device_type,$doc_id);
  }

  /**
   * Gets Result Array with Key (Register Ids) <=> Value (Notification Result) Pair
   * @param register_ids[] Registration Ids  List Of Registration Ids
   * @param notification_result Result Json registration result
   * 
   * @return registration_result[] Registration with result
   */
  function get_notification_status($register_ids,$notification_json_result){
    $notification_result = json_decode($notification_json_result,TRUE);
    if(count($notification_result) < 0 || !is_array($notification_result)){ return; }
    $keys1  = [];
    foreach ($notification_result["results"] as $key => $value) {
        if(array_key_exists("error", $value) || array_key_exists("message_id",$value)){
            foreach ($value as $k1 => $v1) {
                array_push($keys1,$v1);
            }
        }
    }   
    return array_combine($register_ids,$keys1);
  }

  function fcm_push($registration_ids,$user_id,$id,$title,$type,$image,$desc,$pdf_file,$ppt_file,$video_file,$file_type,$download_status,$date = '',$device_type = false, $doc_id = '')
  {
    
    $log_file = APPPATH . 'logs/notification_log' . date('Y-m-d') . ".log";

    if(! $device_type){
      error_log("NOTIFICATION CALLED USING EMPTY DEVICE TYPE FOR Registeration IDs :: " . json_encode($registration_ids) . PHP_EOL, 3, $log_file);
      return;
    }

    // Fields Length Fixes
    $title = mb_strimwidth($title,0,255,'...');
    $desc = mb_strimwidth($desc,0,1152,'...');

    $ci =& get_instance();
    $ci->load->database();

    //$ci->load->model('api/mdl_doctor');
    $base_url = $ci->config->base_url();
    // Set POST variables
    $url = 'https://fcm.googleapis.com/fcm/send';

    $fields = [];
    if($device_type) {

      $notification_data = array(
        "body"            => $desc, 
        'title'           => $title, 
        'birthday_icon'   => $image,
        'icon'            => $image,
        'type'            => $type,
        'id'              => $id,
        'pdf_file'        => $pdf_file,
        'ppt_file'        => $ppt_file,
        'video_file'      => $video_file,
        'file_type'       => $file_type,
        'download_status' => $download_status,
        'date'            => $date,
      );

      switch (strtolower($device_type)) {
        case 'android':          
          $fields['registration_ids'] = (array)$registration_ids;
          // $fields['notification'] = $notification_data;
          $fields['data'] = $notification_data;
          $fields['priority'] = 'high';
          break;

        case 'ios':
          $fields['registration_ids'] = (array)$registration_ids;
          $fields['notification'] = $notification_data;
          $fields['mutable_content'] = TRUE;
          $fields['content_available'] = TRUE;
          break;
        
        default:
          error_log("NOTIFICATION CALLED USING :: " . $device_type . " :: DEVICE TYPE FOR Registeration IDs :: " . json_encode($registration_ids) . PHP_EOL, 3, $log_file);
          break;
      }
    }

   //  echo "<pre>";
   //  print_r($fields);exit;
        
    $headers = array('Authorization: key='.API_ACCESS_KEY,'Content-Type: application/json');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    curl_close($ch);
    
    $log = 'message: ' . json_encode($fields) . ' :: response: ' . $result . PHP_EOL;
    error_log($log, 3, $log_file);

    if ($result === FALSE) {
      return;
    }else{

      $registration_log_ids = get_notification_status($registration_ids,$result);

      foreach ($registration_log_ids as $key => $value) {
          $data = [
            'insert_user_id'  =>  $user_id,
            'user_id'          =>  $user_id,
            'register_id'     =>  $key,
            'status'          =>  $value,
            'id'              =>  $id,
            'title'           =>  $title,
            'type'            =>  $type,
            'image'           =>  $image,
            'desc'            =>  $desc,
            'pdf_file'        =>  $pdf_file,
            'ppt_file'        =>  $ppt_file,
            'video_file'      =>  $video_file,
            'file_type'       =>  $file_type,
            'download_status' =>  $download_status,
            'date'            =>  $date,
            'insert_dt'       =>  date('Y-m-d H:i:s')
          ];

        $ci->db->insert('notification_log', $data);
      }
    }
    }
  }

 ?>
