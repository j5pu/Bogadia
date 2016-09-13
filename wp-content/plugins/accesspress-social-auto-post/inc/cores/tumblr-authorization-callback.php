<?php

global $wpdb;
//$this->print_array($_POST);
$account_id = $_GET['account_id'];
$table_name = $wpdb->prefix . "asap_accounts";
$account_row = $wpdb->get_row("SELECT * FROM $table_name WHERE account_id = $account_id", 'ARRAY_A');
$account_details = unserialize($account_row['account_details']);
$account_extra_details = unserialize($account_row['account_extra_details']);
$consumer_key = $account_details['consumer_key'];
$consumer_secret = $account_details['consumer_secret'];
$tumblr_obj = new Tumblr_ASAP_API_Client($consumer_key, $consumer_secret);
$tokens = $tumblr_obj->completeAuthorization($callback_url);
if(isset($tokens['authorized']) && $tokens['authorized']==1){
    
    $access_token = $tokens['value'];
    $access_token_secret = $tokens['secret'];
//    echo "<pre>";
//    print_r($tokens);
//    echo "</pre>";die();
    $account_extra_details['authorize_status'] = 1;
    $account_extra_details['access_token'] = $access_token;
    $account_extra_details['access_token_secret'] = $access_token_secret;
    $_SESSION['asap_message'] = __('Account authorized successfully.Now you can go for posting.', ASAP_TD);
    $wpdb->update(
            $table_name, array('account_extra_details' => serialize($account_extra_details)), array('account_id' => $account_id), array('%s'), array('%d')
    );
}
else{
    $_SESSION['asap_message'] = __('Authorization Error.Please check your credentials properly both in app and here.', ASAP_TD);
}
$redirect_url = admin_url() . 'admin.php?page=asap&account_id=' . $account_id;
// die($redirect_url);
wp_redirect($redirect_url);
//header('Location:'.  admin_url());
exit();
