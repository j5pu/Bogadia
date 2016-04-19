<?php
/*
Plugin Name: Boga Ads
Description: Añade los anuncios de Doubleclick For Publishers
*/
function insert_dfp_desktop_ads() {
    if (!wp_is_mobile()){
        include 'includes/dfp_desktop_ads.php';
    }
}
add_action('kleo_before_main', 'insert_dfp_desktop_ads');

function insert_interstitial() {
    if (wp_is_mobile()){
        include 'includes/insterstitial.php';
    }
}
add_action('kleo_header', 'insert_interstitial');

function boga_ads_assets(){
    wp_register_script( 'boga_dfp_define_slots', '/wp-content/plugins/boga-ads/assets/js/dfp_define_slots.js', array('jquery'));
    wp_enqueue_script( 'boga_dfp_define_slots' );

    if (wp_is_mobile()){
        wp_register_script( 'dfp_interstitial_ad', '/wp-content/plugins/boga-ads/assets/js/dfp_interstitial_ad.js', array('jquery'));
        wp_enqueue_script( 'dfp_interstitial_ad' );
        wp_register_style( 'dfp_interstitial_ad', '/wp-content/plugins/boga-ads/assets/css/dfp_interstitial_ad.css');
        wp_enqueue_style( 'dfp_interstitial_ad' );
    }else{
        wp_register_style( 'boga_dfp_ads', '/wp-content/plugins/boga-ads/assets/css/dfp_ads.css');
        wp_enqueue_style( 'boga_dfp_ads' );
    }
}
add_action('wp_enqueue_scripts', 'boga_ads_assets');

function wpse_ad_content($content)
{
    if (!is_single() || !wp_is_mobile()) return $content;
    $paragraphAfter = 1; //Enter number of paragraphs to display ad after.
    $content = explode("</p>", $content);
    $new_content = '';
    for ($i = 0; $i < count($content); $i++) {
        if ($i == $paragraphAfter) {
            $new_content.= '<div class="fb-ad" data-placementid="948549275229283_975265575890986" data-format="300x250" data-testmode="false"></div>';
        }

        $new_content.= $content[$i] . "</p>";
    }
    return $new_content;
}
add_filter('the_content', 'wpse_ad_content');

add_filter( 'script_loader_tag', function ( $tag, $handle ) {

    if ( 'boga_dfp_define_slots' !== $handle )
        return $tag;

    return str_replace( ' src', ' data-pagespeed-no-defer src', $tag );
}, 10, 2 );

add_filter( 'script_loader_tag', function ( $tag, $handle ) {

    if ( 'dfp_interstitial_ad' !== $handle )
        return $tag;

    return str_replace( ' src', ' data-pagespeed-no-defer src', $tag );
}, 10, 2 );
?>