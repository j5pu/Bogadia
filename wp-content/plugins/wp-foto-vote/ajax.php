<?php
if ( !isset($_REQUEST['action']) ) {
    die(0);
}
if ( $_REQUEST['action'] !== 'vote' && $_REQUEST['action'] !== 'fv_is_subscribed' ) {
    die(00);
}

//make sure we skip most of the loading which we might not need
//http://core.trac.wordpress.org/browser/branches/3.4/wp-settings.php#L99
define('SHORTINIT', true);

//mimic the actuall admin-ajax
define('DOING_AJAX', true);

// This include gives us all the WordPress functionality
$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
require_once( $parse_uri[0] . 'wp-load.php' );
/*
ini_set('display_errors',0);
ini_set('display_startup_errors',0);
error_reporting(0);

//============
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
*/

try {
    //Typical headers
    header('Content-Type: text/html');
    send_nosniff_header();

    //Disable caching
    header('Cache-Control: no-cache');
    header('Pragma: no-cache');

    require_once( ABSPATH . WPINC . '/l10n.php' );
    //Include only the files and function we need
    global $wp_version;
    if ( $wp_version  < 4.4 ) {

        require( ABSPATH . WPINC . '/formatting.php' );
        require( ABSPATH . WPINC . '/capabilities.php' );
        //require( ABSPATH . WPINC . '/query.php' );
        //require( ABSPATH . WPINC . '/date.php' );
        require( ABSPATH . WPINC . '/class-wp-roles.php' );
        require( ABSPATH . WPINC . '/class-wp-role.php' );
        require( ABSPATH . WPINC . '/class-wp-user.php' );

        require( ABSPATH . WPINC . '/user.php' );
        require( ABSPATH . WPINC . '/session.php' );
        require( ABSPATH . WPINC . '/meta.php' );
        require( ABSPATH . WPINC . '/general-template.php' );
        require( ABSPATH . WPINC . '/link-template.php' );
        //require( ABSPATH . WPINC . '/post.php' );
        require( ABSPATH . WPINC . '/kses.php' );
        require( ABSPATH . WPINC . '/http.php' );
        require( ABSPATH . WPINC . '/class-http.php' );

        require( ABSPATH . WPINC . '/l10n.php' );
    } elseif ( $wp_version >= 4.4 ) {
        require( ABSPATH . WPINC . '/class-wp-walker.php' );
        require( ABSPATH . WPINC . '/class-wp-ajax-response.php' );
        require( ABSPATH . WPINC . '/formatting.php' );
        require( ABSPATH . WPINC . '/capabilities.php' );
        require( ABSPATH . WPINC . '/class-wp-roles.php' );
        require( ABSPATH . WPINC . '/class-wp-role.php' );

        require( ABSPATH . WPINC . '/class-wp-user.php' );
        require( ABSPATH . WPINC . '/query.php' );
        //require( ABSPATH . WPINC . '/date.php' );
        require( ABSPATH . WPINC . '/template.php' );
        require( ABSPATH . WPINC . '/user.php' );
        require( ABSPATH . WPINC . '/class-wp-user-query.php' );
        require( ABSPATH . WPINC . '/session.php' );
        require( ABSPATH . WPINC . '/meta.php' );
        require( ABSPATH . WPINC . '/class-wp-meta-query.php' );
        require( ABSPATH . WPINC . '/general-template.php' );
        require( ABSPATH . WPINC . '/link-template.php' );
        require( ABSPATH . WPINC . '/post.php' );
        require( ABSPATH . WPINC . '/class-wp-post.php' );
        require( ABSPATH . WPINC . '/rewrite.php' );
        require( ABSPATH . WPINC . '/class-wp-rewrite.php' );
        require( ABSPATH . WPINC . '/kses.php' );
        require( ABSPATH . WPINC . '/http.php' );
        require( ABSPATH . WPINC . '/class-http.php' );
        require( ABSPATH . WPINC . '/class-wp-http-streams.php' );
        require( ABSPATH . WPINC . '/class-wp-http-curl.php' );
        require( ABSPATH . WPINC . '/class-wp-http-proxy.php' );
        require( ABSPATH . WPINC . '/class-wp-http-cookie.php' );
        require( ABSPATH . WPINC . '/class-wp-http-encoding.php' );
        require( ABSPATH . WPINC . '/class-wp-http-response.php' );

        require( ABSPATH . WPINC . '/rest-api.php' );
        require( ABSPATH . WPINC . '/rest-api/class-wp-rest-server.php' );
        require( ABSPATH . WPINC . '/rest-api/class-wp-rest-response.php' );
        require( ABSPATH . WPINC . '/rest-api/class-wp-rest-request.php' );
    }

    // Define constants
    wp_plugin_directory_constants();
    wp_cookie_constants();
    // Define and enforce our SSL constants
    wp_ssl_constants();

    wp_register_plugin_realpath( WP_PLUGIN_DIR . '/wp-foto-vote/wp-foto-vote.php' );

    global $wp_plugin_paths;
    $wp_plugin_paths = array();

    //and do your stuff
    require( 'wp-foto-vote.php' );

    require( ABSPATH . WPINC . '/pluggable.php' );
    require( ABSPATH . WPINC . '/pluggable-deprecated.php' );

    // Define constants which affect functionality if not already defined.
    wp_functionality_constants();

    // Add magic quotes and set up $_REQUEST ( $_GET + $_POST )
    wp_magic_quotes();

    if ( !FvFunctions::ss('fast-ajax', true) ) {
        die('hehe');
    }

    if ( $_REQUEST['action'] == 'vote' ) {
        do_action('wp_ajax_nopriv_vote');
    } elseif( $_REQUEST['action'] == 'fv_is_subscribed' ) {
        do_action('wp_ajax_nopriv_fv_is_subscribed');
    }

} catch (Exception $e) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
}