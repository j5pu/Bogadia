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
        user_id bigint(45) NULL,
        contest_id varchar(45) NULL,
        date datetime NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY id (id ASC)
	) $charset_collate;

	CREATE TABLE wp_bogacontest_img (
        id int UNSIGNED NOT NULL AUTO_INCREMENT,
        contestant_id int UNSIGNED NULL,
        main int NULL,
        path varchar(500) NULL,
        date datetime NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY id (id ASC)
	) $charset_collate;

	CREATE TABLE wp_bogacontest_votes(
        id int UNSIGNED NOT NULL AUTO_INCREMENT,
        contestant_id bigint(20) UNSIGNED NULL,
        voter_id bigint(20) NULL,
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
    wp_register_script('typewatch', '/wp-content/plugins/boga-contest/assets/js/typewatch.js', array('jquery'));
    wp_enqueue_script('bogacontest');
    wp_register_script('bogacontest', '/wp-content/plugins/boga-contest/assets/js/bogacontest.js', array('jquery', 'typewatch'));
    wp_enqueue_script('bogacontest');
    wp_register_style('bogacontest', '/wp-content/plugins/boga-contest/assets/css/bogacontest.css');
    wp_enqueue_style('bogacontest');
    wp_enqueue_media();
}

function bogacontest_ajax_login(){
    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;
    $user_signon = wp_signon($info, is_ssl() ? true : false);
    if ( is_wp_error($user_signon) ){
        echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.')));
    } else {
        echo json_encode(array('loggedin'=>true, 'message'=>__('Login successful, redirecting...'), 'user_id'=>$user_signon->ID));
    }
    die();
}

function bogacontest_ajax_register(){

    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-register-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_nicename'] = $info['nickname'] = $info['display_name'] = $info['first_name'] = $info['user_login'] = sanitize_user($_POST['username']) ;
    $info['user_pass'] = sanitize_text_field($_POST['password']);
    $info['user_email'] = sanitize_email( $_POST['email']);

    // Register the user
    $user_register = wp_insert_user( $info );
    if ( is_wp_error($user_register) ){
        $error  = $user_register->get_error_codes()	;

        if(in_array('empty_user_login', $error))
            echo json_encode(array('loggedin'=>false, 'message'=>__($user_register->get_error_message('empty_user_login'))));
        elseif(in_array('existing_user_login',$error))
            echo json_encode(array('loggedin'=>false, 'message'=>__('This username is already registered.')));
        elseif(in_array('existing_user_email',$error))
            echo json_encode(array('loggedin'=>false, 'message'=>__('This email address is already registered.')));
    } else {
        auth_user_login($info['nickname'], $info['user_pass'], 'Registration');
    }

    die();
}

include 'class/contestant.php';
$bogacontestant = new contestant();
$bogacontest = new contest();

register_activation_hook( __FILE__, 'bogacontest_install' );
add_filter('rewrite_rules_array','wp_insertMyRewriteRules');
add_filter('query_vars','wp_insertMyRewriteQueryVars');
add_filter('init','flushRules');
remove_filter('template_redirect', 'redirect_canonical'); // stop redirecting
add_shortcode( 'bogacontestant', array($bogacontestant, 'print_contestant_data') );
add_shortcode( 'bogacontest', array($bogacontest, 'print_contest_data') );
add_action('wp_enqueue_scripts', 'bogacontest_assets');
add_action( 'wp_ajax_nopriv_bogacontest_ajax_login', 'bogacontest_ajax_login' );
add_action( 'wp_ajax_nopriv_bogacontest_ajax_register', 'bogacontest_ajax_register' );