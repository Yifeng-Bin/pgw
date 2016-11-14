<?php
function admin_log($log_info,$submit_content ='',$user_id = NULL){
    $data = array(
        'log_time' => gmtime(),
        'log_info' => $log_info,
        'log_format_time' => local_date("Y-m-d H:i:s"),
        'submit_content' => $submit_content,
        'ip_address' => get_client_ip(),
    );
    if($user_id === NULL){
      $data['user_id'] = defined('ADMIN_USER_ID') ? ADMIN_USER_ID : 0;
    }else{
         $data['user_id'] =   intval($user_id);
    }
    M("AdminLog")->add($data);
}
?>