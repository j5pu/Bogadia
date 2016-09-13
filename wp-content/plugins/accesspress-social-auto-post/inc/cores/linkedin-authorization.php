<?php

global $wpdb;
//$this->print_array($_POST);
$account_id = $_POST['account_id'];
$table_name = $wpdb->prefix . "asap_accounts";
$account_row = $wpdb->get_row("SELECT * FROM $table_name WHERE account_id = $account_id", 'ARRAY_A');
$account_details = unserialize($account_row['account_details']);
//$this->print_array($account_details);die();
$client_id = $account_details['client_id'];
$app_secret = $account_details['client_secret'];
$callback_url = admin_url('admin-post.php?action=asap_linkedin_callback_authorize&account_id=' . $account_id);

$asap_session_state = md5(uniqid(rand(), TRUE));
setcookie("asap_linkedin_session_state", $asap_session_state, "0", "/");


//$callback_url = 'http://192.168.1.70/social-auto-post/linkedin/index.php?auth=lia';
$li_oauth = new nsx_LinkedIn($client_id, $app_secret, $callback_url);
$request_token = $li_oauth->getRequestToken(); //echo "####"; prr($request_token); die();
if (!is_object($request_token)) {
    $_SESSION['asap_message'] = __('LinkedIn Authorization Error.Please check all your credentials properly.', ASAP_TD);
    $url = admin_url('admin.php?page=asap&account_id=' . $account_id);
    header('Location:' . $url);
    die();
}
$account_extra_details['liOAuthToken'] = $request_token->key;
$account_extra_details['liOAuthTokenSecret'] = $request_token->secret;
$wpdb->update(
        $table_name, array(
    'account_extra_details' => serialize($account_extra_details)
        ), array('account_id' => $account_id), array(
    '%s'
        ), array('%d')
);
switch ($li_oauth->http_code) {
    case 200: $url = $li_oauth->generateAuthorizeUrl();
        echo '<script type="text/javascript">window.location = "' . $url . '"</script>';
        break;
    default:
        $_SESSION['asap_message'] = __('Could not connect to LinkedIn. Refresh the page or try again later.', ASAP_TD);
        $url = admin_url('admin.php?page=asap&account_id=' . $account_id);
        header('Location:' . $url);
        die();
}die();
