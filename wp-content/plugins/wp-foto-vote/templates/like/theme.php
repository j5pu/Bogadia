<?php

wp_enqueue_script('fv_theme_like', FvFunctions::get_theme_url($theme, 'fv_theme_like.js'), array( 'jquery', 'fv_lib' ), FV::VERSION);

?>
