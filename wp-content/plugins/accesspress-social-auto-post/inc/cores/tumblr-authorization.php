<?php

global $wpdb;

$account_id = $_POST['account_id'];
$table_name = $wpdb->prefix . "asap_accounts";
$account_row = $wpdb->get_row("SELECT * FROM $table_name WHERE account_id = $account_id", 'ARRAY_A');
$account_details = unserialize($account_row['account_details']);
//$this->print_array($account_details);
//die();
$consumer_key = $account_details['consumer_key'];
$consumer_secret = $account_details['consumer_secret'];
$callback_url = admin_url('admin-post.php?action=asap_tumblr_callback_authorize&account_id=' . $account_id);
$asap_session_state = md5(uniqid(rand(), TRUE));
setcookie("asap_tumblr_session_state", $asap_session_state, "0", "/");
$tumblr_obj = new Tumblr_ASAP_API_Client($consumer_key, $consumer_secret);
//$this->print_array($tumblr_obj);
$check = $tumblr_obj->authorize($callback_url);
if($check==null){
    //echo "test";
    $_SESSION['asap_message'] = __('It seems that you have already authorized or tried to authorized just few times before so please wait or use another browser to authorize.Please double check the credentials too in both app and here.',ASAP_TD);
    $_SESSION['asap_message_type'] = 'warning';
    $redirect_url = admin_url('admin.php?page=asap&account_id='.$account_id);
    wp_redirect($redirect_url);
    exit();
}

