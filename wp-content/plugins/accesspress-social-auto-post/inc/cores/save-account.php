<?php

//$this->print_array($_POST);
/**
 * [action] => asap_form_action
  [network_name] => facebook
  [asap_form_nonce] => e12aa310e5
  [_wp_http_referer] => /social-auto-post/wp-admin/admin.php?page=asap
  [account_title] =>
  [account_details] => Array
  (
  [application_id] =>
  [application_secret] =>
  [facebook_user_id] =>
  [message_format] =>
  [post_image] => featured_image
  [custom_image_url] =>
  )

  [facebook_add_submit] => Add Account
 */
foreach ($_POST as $key => $val) {
    $$key = $val;
}
global $wpdb;
$table_name = $wpdb->prefix . "asap_accounts";
if (isset($account_id)) {
    /**
     * Update settings section
     */
    $account_details['include_image'] = (isset($account_details['include_image']) && $account_details['include_image'] == 1) ? 1 : 0;
    $account_status = isset($auto_publish) ? 1 : 0;
    $wpdb->update(
            $table_name, array(
        'account_title' => $account_title,
        'account_type' => $network_name,
        'account_status' => $account_status,
        'account_details' => serialize($account_details)
            ), array('account_id' => $account_id), array(
        '%s',
        '%s',
        '%d',
        '%s'
            ), array('%d')
    );
    $_SESSION['asap_message'] = __('Account Updated Successfully.');
    $redirect_url = admin_url('admin.php?page=asap&account_id=' . $account_id);
    wp_redirect($redirect_url);
    exit;
} else {
    /**
     * Add Section
     */
    $account_details['include_image'] = (isset($account_details['include_image']) && $account_details['include_image'] == 1) ? 1 : 0;
    $account_extra_details = array();
    $account_extra_details['authorize_status'] = 0;
    $account_extra_details['pages'] = '';
    $account_extra_details['access_token'] = '';
    $account_status = isset($auto_publish) ? 1 : 0;


    $result = $wpdb->query($wpdb->prepare(
                    "
		INSERT INTO $table_name
		( account_title, account_status, account_type,account_details )
		VALUES ( %s, %d, %s, %s )
	", array(
                $account_title,
                $account_status,
                $network_name,
                serialize($account_details)
                    )
    ));
    $_SESSION['asap_message'] = __('Account Added Successfully.');
    $account_id = $wpdb->insert_id;
    $redirect_url = admin_url('admin.php?page=asap&account_id=' . $account_id);
    wp_redirect($redirect_url);
    exit;
}


