<?php
/**
 * Uses for extends themes functionality
 *
 * Add ability themes more beauty add custom assets
 * and set some params, like support custom leaders block, etc
 *
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <wp-vote@hotmail.com>
 */
class Fv_Theme_Base {

    /**
     * Is theme supports leaders block
     *
     * @since    2.2.081
     * @access   public
     * @var      bool
     */
    public static $customLeaders;

    /**
     * Is theme supports open photo on new page
     *
     * @since    2.2.081
     * @access   public
     * @var      bool
     */
    public static $newPage;

    public static function init($customLeaders = false, $newPage = false) {
        self::$customLeaders = (bool) $customLeaders;
        self::$newPage = (bool) $newPage;
    }

    public static function assets_item() {

    }

    public static function assets_list() {

    }

}