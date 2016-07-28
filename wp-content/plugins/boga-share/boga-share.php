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


/*function show_bogashare_contestants(){
    global $wpdb;
    $results = $wpdb->get_results( "SELECT user_fb_id FROM wp_bogashare", OBJECT );
    $contestans_fb_ids = array();
    foreach($results as $result){
        array_push($contestans_fb_ids, $result->user_fb_id);
    }
    $contestans_fb_ids = array_unique($contestans_fb_ids);
    $results = $wpdb->get_results( "SELECT user_id,meta_value,display_name FROM wp_usermeta INNER JOIN wp_users ON wp_usermeta.user_id = wp_users.ID WHERE meta_key = '_fbid' AND meta_value IN (" . implode(',', array_map('intval', $contestans_fb_ids)) . ")", OBJECT );
    echo '<ul class="media-list">';
    foreach($results as $contestant){
        echo '<li class="media col-xs-6 col-sm-4 portada_posts"><div class="media"><a href="https://www.facebook.com/'. $contestant->meta_value . '"><img class="media-object img-responsive" src="https://graph.facebook.com/'. $contestant->meta_value . '/picture?type=large"></a></div><div class="media-body"><h4 class="media-heading">'. $contestant->display_name . '</h4></div></li>';
    }
    echo '</ul>';
}
add_shortcode('bogashare_contestants', 'show_bogashare_contestants');*/
