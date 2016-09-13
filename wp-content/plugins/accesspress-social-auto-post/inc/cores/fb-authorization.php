<?php

global $wpdb;
//$this->print_array($_POST);
$account_id = $_POST['account_id'];
$table_name = $wpdb->prefix . "asap_accounts";
$account_row = $wpdb->get_row("SELECT * FROM $table_name WHERE account_id = $account_id", 'ARRAY_A');
$account_details = unserialize($account_row['account_details']);
//$this->print_array($account_details);die();
$app_id = $account_details['application_id'];
$app_secret = $account_details['application_secret'];
$redirect_url = admin_url('admin-post.php?action=asap_callback_authorize&account_id=' . $account_id);
$api_version = 'v2.0';
$param_url = urlencode($redirect_url);
$asap_session_state = md5(uniqid(rand(), TRUE));
setcookie("asap_session_state", $asap_session_state, "0", "/");

$dialog_url = "https://www.facebook.com/" . $api_version . "/dialog/oauth?client_id="
        . $app_id . "&redirect_uri=" . $param_url . "&state="
        . $asap_session_state . "&scope=email,user_about_me,publish_pages,user_posts,publish_actions,manage_pages";
//die($dialog_url);

header("Location: " . $dialog_url);
