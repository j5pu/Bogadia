<?php
//die('hello');
global $wpdb;
$table_name = $wpdb->prefix . 'asap_accounts';
$accounts = $wpdb->get_results("select * from $table_name where account_status=1");
//$this->print_array($accounts);
//die();
if (count($accounts) > 0) {
    foreach ($accounts as $account) {
        $account_type = $account->account_type;
        switch ($account_type) {
            case 'facebook':
                include('auto-post/facebook-post.php');
                break;
            case 'twitter':
                include('auto-post/twitter-post.php');
                break;
            case 'tumblr':
                include('auto-post/tumblr-post.php');
                break;
            case 'linkedin':
                include('auto-post/linkedin-post.php');
                break;
            default:
                break;
        }
    }
}
//$this->print_array($accounts);

//mail('regan@access-keys.com','test',$message); 