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

function assets(){
    wp_register_style( 'boga_dfp_ads', 'assets/css/dfp_ads.css');
    wp_enqueue_style( 'boga_dfp_ads' );
    wp_register_script( 'boga_dfp_define_slots', 'assets/js/dfp_define_slots.js', array('jquery'));
    wp_enqueue_script( 'boga_dfp_define_slots' );
}

add_action('wp_enqueue_scripts', 'assets');
?>