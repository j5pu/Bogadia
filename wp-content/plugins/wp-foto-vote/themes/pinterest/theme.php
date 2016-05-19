<?php

// if not Infinite pagination and AJAX action
if ( defined('DOING_AJAX') && DOING_AJAX == TRUE && FvFunctions::ss('pagination-type', 'default') == 'infinite' ) {
    return;
}

wp_enqueue_script('fv_theme_pinterest', FvFunctions::get_theme_url ( $theme, 'js/fv_theme_pinterest.js' ), array( 'jquery', 'fv_lib_js' ) , FV::VERSION);

add_action('fv_before_shows_loop',  'fv_pinterest_before_show');
add_action('fv_after_shows_loop',  'fv_pinterest_after_show');
//add_action('fv_after_contest_list',  'fv_pinterest_contest_list');

function fv_pinterest_before_show() {
        echo '<div class="column-grid" id="grid">';
}

function fv_pinterest_after_show() {
        echo '</div>
                    <div id="progress" class="waiting"><dt></dt><dd></dd></div>
                ';
}

