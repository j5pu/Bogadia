<?php
require_once('../../../wp-load.php');
global $wpdb;
$wpdb->insert(
    'wp_bogashare',
    array(
        'post_id' => 999999,
        'user_id' => 123,
        'comment' => 'this is so dope',
        'date' => 123,
    ),
    array(
        '%d',
        '%d',
        '%s',
        '%d'
    )
);