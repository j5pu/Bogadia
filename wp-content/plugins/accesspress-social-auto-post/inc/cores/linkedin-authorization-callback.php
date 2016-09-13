<?php

global $wpdb;
//$this->print_array($_POST);
$account_id = $_GET['account_id'];
$table_name = $wpdb->prefix . "asap_accounts";
$account_row = $wpdb->get_row("SELECT * FROM $table_name WHERE account_id = $account_id", 'ARRAY_A');
$account_details = unserialize($account_row['account_details']);
$account_extra_details = unserialize($account_row['account_extra_details']);

$api_key = $account_details['client_id'];
$api_secret = $account_details['client_secret'];
$li_oauth = new nsx_LinkedIn($api_key, $api_secret);
$li_oauth->request_token = new nsx_trOAuthConsumer($account_extra_details['liOAuthToken'], $account_extra_details['liOAuthTokenSecret'], 1);
$li_oauth->oauth_verifier = $_REQUEST['oauth_verifier'];
$li_oauth->getAccessToken($_REQUEST['oauth_verifier']);
if ($li_oauth->access_token->key != '') {
    
    $account_extra_details = array();
    $account_extra_details['liOAuthVerifier'] = $_REQUEST['oauth_verifier'];
    $account_extra_details['liAccessToken'] = $li_oauth->access_token->key;
    $account_extra_details['liAccessTokenSecret'] = $li_oauth->access_token->secret;
    $account_extra_details['authorize_status'] = 1;
    $_SESSION['asap_message'] = __('Account authorized successfully.Now you can go for posting.', ASAP_TD);
    $wpdb->update(
                $table_name, array('account_extra_details' => serialize($account_extra_details)), array('account_id' => $account_id), array('%s'), array('%d')
        );
} else {
    $account_extra_details = array();
    $account_extra_details['authorize_status'] = 0;
    $url = admin_url('admin-post.php?action=asap_linkedin_callback_authorize&account_id=' . $account_id);
    $_SESSION['asap_message'] = __('Authorization failed!! Please check Authorized Redirect URL and Accept Redirect URL details kept in the application settings and try again.URL should be ', ASAP_TD) . $url;
}
$redirect_url = admin_url() . 'admin.php?page=asap&account_id=' . $account_id;
// die($redirect_url);
wp_redirect($redirect_url);
//header('Location:'.  admin_url());
exit();
