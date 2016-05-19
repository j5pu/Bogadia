<?php

defined('ABSPATH') or die("No script kiddies please!");

/**
 * Load all default addons and extensions
*/

function fv_default_addons_load()
{
    include 'coutdown-deafult/fv_addon__coutdown-deafult.php';
    include 'fv-form-simple-rounded/fv_addon__form-simple-rounded.php';
    include 'agree-rules/fv_addon__agree_rules.php';
}
