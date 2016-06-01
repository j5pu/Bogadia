<?php
/*
Plugin Name: Bogacontest
Description: Concurso de modelos
*/
function bogacontest_install() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE wp_bogacontest_contestant (
        id int UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id varchar(45) NULL,
        contest_id varchar(45) NULL,
        date datetime NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY id (id ASC)
	) $charset_collate;

	CREATE TABLE wp_bogacontest_img (
        id int UNSIGNED NOT NULL AUTO_INCREMENT,
        contestant_id int UNSIGNED NULL,
        contest_id varchar(45) NULL,
        date datetime NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY id (id ASC)
	) $charset_collate;

	CREATE TABLE wp_bogacontest_votes(
        id int UNSIGNED NOT NULL AUTO_INCREMENT,
        contestant_id bigint(20) UNSIGNED NULL,
        votes_id bigint(20) NULL,
        date datetime NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY id (id ASC)
	) $charset_collate;

	CREATE TABLE wp_bogacontest(
        id int UNSIGNED NOT NULL AUTO_INCREMENT,
        slug varchar(100) NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY id (id ASC)
	) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

function flushRules(){
    // Remember to flush_rules() when adding rules
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}

function wp_insertMyRewriteRules($rules)
{
    // Adding a new rule
    $newrules = array();
    $newrules['concursos/([^/]+)/(.+)'] = 'index.php?pagename=bogacontestant&contest=$matches[1]&contestant=$matches[2]';
    $newrules['concursos/([^/]+)'] = 'index.php?pagename=bogacontest&contest=$matches[1]';
    $finalrules = $newrules + $rules;
    return $finalrules;
}

function wp_insertMyRewriteQueryVars($vars)
{
    // Adding the var so that WP recognizes it
    array_push($vars, 'contest');
    array_push($vars, 'contestant');
    return $vars;
}

function bogacontest_assets(){
    wp_register_script('bogacontest', '/wp-content/plugins/boga-contest/assets/js/bogacontest.js', array('jquery'));
    wp_enqueue_script('bogacontest');
    wp_register_style('bogacontest', '/wp-content/plugins/boga-contest/assets/css/bogacontest.css');
    wp_enqueue_style('bogacontest');
    wp_enqueue_media();
}

include 'class/contestant.php';
$bogacontestant = new contestant();
$bogacontest = new contest();

register_activation_hook( __FILE__, 'bogacontest_install' );
add_action('wp_enqueue_scripts', 'bogacontest_assets');
add_filter('rewrite_rules_array','wp_insertMyRewriteRules');
add_filter('query_vars','wp_insertMyRewriteQueryVars');
add_filter('init','flushRules');
remove_filter('template_redirect', 'redirect_canonical'); // stop redirecting
add_shortcode( 'bogacontestant', array($bogacontestant, 'print_contestant_data') );
add_shortcode( 'bogacontest', array($bogacontest, 'print_contest_data') );

