<?php
/*
Plugin Name: BogaShare
Description: Muestra el cajon de compartir a traves de api para el concurso de share
*/

/*function show_bogashare_dialog() {
    if(is_single() && !is_single(35229)) {
        include 'includes/insterstitial.php';
    }
}*/
function show_bogashare_mobile_button(){
    if(wp_is_mobile()){
        if(is_single() && !is_single(35229)) {
            include 'includes/mobile_bottom_share_button.php';
        }
    }
}
/*add_action('wp_head', 'show_bogashare_dialog');*/
add_action('wp_footer', 'show_bogashare_mobile_button');

function bogashare_assets(){
    if(is_single() && !is_single(35229)) {
        wp_register_script('bogashare', '/wp-content/plugins/boga-share/assets/js/bogashare.js', array('jquery'));
        wp_enqueue_script('bogashare');
        wp_register_style('bogashare_style', '/wp-content/plugins/boga-share/assets/css/bogashare.css');
        wp_enqueue_style('bogashare_style');
    }
}
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


/*add_filter( 'the_content', array($this, 'inside_content_share_banner'));*/
function inside_content_share_banner( $content ) {
    function prefix_insert_after_paragraph( $insertion, $paragraph_id, $content ) {
        $closing_p = '</p>';
        $paragraphs = explode( $closing_p, $content );
        foreach ($paragraphs as $index => $paragraph) {

            if ( trim( $paragraph ) ) {
                $paragraphs[$index] .= $closing_p;
            }

            if ( $paragraph_id == $index + 1 ) {
                $paragraphs[$index] .= $insertion;
            }
        }

        return implode( '', $paragraphs );
    }

    if ( is_single() && ! is_admin()  && !in_category('Streetstyle') && !has_term( 'BogadiaTV', 'post_tag', $this->retrieved_post[0] )) {

        $ad_code = '<div style="max-width: 100%; float: left; margin:0px 20px 20px 0px;"><h5 style="margin:0px auto; font-size: 14px; text-align: center;"><strong>ARTÍCULOS DE INTERÉS</strong></h5><hr style="margin: 0px 0px 5px 0px;" />'.$post_re.'</div>';

        return prefix_insert_after_paragraph( $ad_code, 2, $content );
    }
    return $content;
}