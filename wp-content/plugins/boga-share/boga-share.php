<?php
/*
Plugin Name: BogaShare
Description: Muestra el cajon de compartir a traves de api para el concurso de share
*/

function show_bogashare_dialog() {
    if(is_single()) {
        include 'includes/insterstitial.php';
    }
}
/*function show_bogashare_mobile_button(){
    if(wp_is_mobile()){
        if(is_single() && !is_single(35229)) {
            include 'includes/mobile_bottom_share_button.php';
            echo $share_buttons;
        }
    }
}*/
add_action('wp_head', 'show_bogashare_dialog');
add_action('wp_footer', 'show_bogashare_mobile_button');

function bogashare_assets(){
    if(is_single()) {
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


add_filter('the_content', 'mte_add_incontent_ad');
function mte_add_incontent_ad($content)
{
    $src = '/wp-content/plugins/boga-share/assets/img/';
    if(is_single()  && !is_single(32358)){
        if (wp_is_mobile()){
            $src .= 'banner-prueba-'. mt_rand(11,23) .'-min.png';
        }else{
            $src .= 'landscape-'. mt_rand(1,2) .'.png';
        }

        $content_block = explode('<p>',$content);
        if(!empty($content_block[3]))
        {	$content_block[3] .= '<img id="bogashare_banner" class="img-responsive" src="'. $src .'">';
        }
        for($i=1;$i<count($content_block);$i++)
        {	$content_block[$i] = '<p>'.$content_block[$i];
        }
        $content = implode('',$content_block);
    }
    return $content;
}