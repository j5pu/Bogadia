<?php

defined('ABSPATH') or die("No script kiddies please!");

/**
 * The contest class.
 *
 * Used from doing most operations with contest and photos - add/edit/deleted
 *
 * @since      ?
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <wp-vote@hotmail.com>
 */
class FV_Addons_Loader
{

    /**
     * Delete contest and all photos from it
     *
     */
    public static function load()
    {
        include_once 'coutdown-deafult/fv_addon__coutdown-deafult.php';
        include_once 'fv-form-simple-rounded/fv_addon__form-simple-rounded.php';
        //include_once 'fv-facebook-like/fv_addon__fb-like.php';
    }
}