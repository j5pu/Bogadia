<?php
require_once('../../../wp-load.php');
global $wpdb;
$results = $wpdb->insert(
    'wp_bogashare',
    array(
        'post_id' => $_POST["post_id"],
        'user_fb_id' => $_POST["user_fb_id"],
        'comment' => $_POST["comment"],
        'date' => date("Y-m-d H:i:s"),
    ),
    array(
        '%d',
        '%d',
        '%s',
        '%s'
    )
);