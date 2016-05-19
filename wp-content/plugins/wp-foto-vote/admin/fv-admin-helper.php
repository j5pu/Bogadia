<?php

function fv_admin_echo_switch_toggle($name, $value) {
    echo fv_admin_get_switch_toggle($name, $value);
}

function fv_admin_get_switch_toggle($name, $value) {
    $output = '<div class="switch switch-toggle';
    if ($value) {
        $output .= ' switch-toggle-checked';
    }
    $output .= '"><input type="hidden" name="' . $name . '" value="' . (int)$value . '">';
    $output .= '<label class="switch-toggle-label"></label></div>';
    return $output;
}