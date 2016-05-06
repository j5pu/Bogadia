<?php
/*
Plugin Name: BogaShare
Description: Muestra el cajon de compartir a traves de api para el concurso de share
*/

function show_bogashare_dialog() {
    if(is_single() && !is_single(34201)) {
        include 'includes/insterstitial.php';
        if(wp_is_mobile()){
            include 'includes/share.php';
        }
    }
}
function bogashare_assets(){
    if(is_single() && !is_single(34201)) {
        wp_register_script('bogashare', '/wp-content/plugins/boga-share/assets/js/bogashare.js', array('jquery'));
        wp_enqueue_script('bogashare');
        wp_register_style('bogashare_style', '/wp-content/plugins/boga-share/assets/css/bogashare.css');
        wp_enqueue_style('bogashare_style');
    }
}
add_action('wp_footer', 'show_bogashare_dialog');
add_action('wp_enqueue_scripts', 'bogashare_assets');
add_filter( 'script_loader_tag', function ( $tag, $handle ) {
    if ( 'bogashare' !== $handle )
        return $tag;
    return str_replace( ' src', ' data-pagespeed-no-defer src', $tag );
}, 10, 2 );

function bogashare_install() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'bogashare';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id int UNSIGNED NOT NULL AUTO_INCREMENT,
        post_id int(9) NULL,
        user_fb_id varchar(30) NULL,
        comment varchar(1000) NULL,
        date datetime NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY id (id ASC)
	) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

register_activation_hook( __FILE__, 'bogashare_install' );
?>
