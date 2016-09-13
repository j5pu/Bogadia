<?php

global $wpdb;
$code = $_GET['code'];
$account_id = $_GET['account_id'];
$table_name = $wpdb->prefix . "asap_accounts";
$account_row = $wpdb->get_row("SELECT * FROM $table_name WHERE account_id = $account_id", 'ARRAY_A');
$account_details = unserialize($account_row['account_details']);
$app_id = $account_details['application_id'];
$app_secret = $account_details['application_secret'];
$redirect_url = admin_url('admin-post.php?action=asap_callback_authorize&account_id=' . $account_id);
$api_version = 'v2.0';
$param_url = urlencode($redirect_url);
$token_url = "https://graph.facebook.com/" . $api_version . "/oauth/access_token?"
        . "client_id=" . $app_id . "&redirect_uri=" . $param_url
        . "&client_secret=" . $app_secret . "&code=" . $code;

$params = null;
$access_token = "";
$response = wp_remote_get($token_url);
$body = wp_remote_retrieve_body($response);
if ($body != '') {
    parse_str($body, $params);
    if (isset($params['access_token'])) {
        $access_token = $params['access_token'];
        $offset = 0;
        $limit = 100;
        $data = array();
        $fbid = $account_details['facebook_user_id'];
        $pp = wp_remote_get("https://graph.facebook.com/" . $api_version . "/me/accounts?access_token=$access_token&limit=$limit&offset=$offset");

        $body = json_decode($pp['body']);
        $pages = $body->data;
        //$this->print_array($pages);die();
        if (empty($pages)) {
            $pages = array();
        } else {
            $new_pages = array();
            foreach ($pages as $page) {
                $new_pages[$page->id] = $page;
            }
            $pages = $new_pages;
        }
        $account_extra_details = array();
        $account_extra_details['authorize_status'] = 1;
        $account_extra_details['pages'] = $pages;
        $account_extra_details['access_token'] = $access_token;
        $wpdb->update(
                $table_name, array('account_extra_details' => serialize($account_extra_details)), array('account_id' => $account_id), array('%s'), array('%d')
        );
        $_SESSION['asap_message'] = __('Account authorized successfully.Now you can go for posting.', ASAP_TD);
        $redirect_url = admin_url() . 'admin.php?page=asap&account_id=' . $account_id;
        // die($redirect_url);
        wp_redirect($redirect_url);
        //header('Location:'.  admin_url());
        exit();
    } else {
        $this->print_array($body);
    }
} else {
    $this->print_array($body);
}


