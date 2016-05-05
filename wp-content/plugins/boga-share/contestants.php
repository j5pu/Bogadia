<?php
require_once('../../../wp-load.php');
global $wpdb;
$results = $wpdb->get_results( "SELECT user_fb_id FROM wp_bogashare", OBJECT );
$post_ids = array();
foreach($results as $result){
     array_push($post_ids, $result);
}
$results = $wpdb->get_results( "SELECT 'display_name' FROM wp_users WHERE 'ID' IN (" . implode(',', array_map('intval', $array)) . ")", OBJECT );
echo '<ul>';
foreach($results as $contestant){
    echo '<li>'. $contestant . '</li>';
}
echo '</ul>';