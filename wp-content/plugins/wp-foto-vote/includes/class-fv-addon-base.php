<?php

defined('ABSPATH') or die("No script kiddies please!");
/**
 * Uses for create addons functionality
 *
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <wp-vote@hotmail.com>
 */
abstract class FvAddonBase {

    /**
     * Addon slug (like UAR)
     *
     * @since 2.2.083
     *
     * @var string
     */
    public $slug;

    /**
     * Addon slug for using Translation functions like `_e()` or `__()`
     *
     * @since 2.2.083
     *
     * @var string
     */
    public $mu_slug;

    /**
     * Addon name (like UploadAgreeRules)
     *
     * @since 2.2.083
     *
     * @var string
     */
    public $name;


    /**
     * All addon settings
     *
     * @since 2.2.083
     *
     * @var object
     */
    protected static $addonsSettings;

    /**
     * Constructor. Loads the class.
     * And performs the necessary actions
     * - add_filter to add Addon settings section
     * - add_filter to add addon into main plugin Addon array
     *
     * @since 2.2.083
     */
    protected function __construct($name, $slug, $api_ver = 'api_v1') {
        $is_admin = is_admin();

        if ( $is_admin && $api_ver == 'api_v1' ) {
            if ( function_exists('get_class') ) {
                $addon_name = get_class($this);
            } else {
                $addon_name = $name;
            }
            wp_add_notice('Waring! Addon "' . $addon_name . '" used old API, that now is not supported. Please contact to support for update it! [http://wp-vote.net/contact-us/]', 'warning');
            return false;
        }
        $this->name = $name;
        $this->slug = $slug;
        $this->mu_slug =  'fv_' . $slug;
        //** At first init settings for Redux
        //** I we do this later, they are not shows in admin
        //add_filter( 'redux/options/' . FV::ADDONS_OPT_NAME . '/sections', array($this, 'section_settings') );

        // && (!defined('DOING_AJAX') && DOING_AJAX == TRUE)
        if ( $is_admin && !SHORTINIT) {
            add_filter( 'fv/addons/settings', array($this, 'section_settings'), 10, 1 );
            add_action( 'admin_init', array($this, 'admin_init') );
        }

        $this->init();

        //** Register addon and after main plugin will known about it
        //add_filter( 'fv/addons/list', array($this, 'register_addon') );

        //** At second add hook, for read AddonsSettings, when Redux are activated
        //** I we do this early, we  has been take empty options
        //add_action( 'redux/loaded', array($this, 'init') );

    }

    /**
     * Performs all the necessary actions
     *
     * @since 2.2.083
     */
    public function init() {
        //global $fv_addons_settings;
        //$this->addonsSettings = get_option(FV::ADDONS_OPT_NAME, array());

        //add_action( 'wp_enqueue_scripts', array($this, 'public_assets_styles') );
        //add_action( 'wp_enqueue_scripts', array($this, 'public_assets_scripts') );
    }

    /**
     * Performs all the necessary Admin actions
     *
     * @since 2.2.083
     */
    public function admin_init() {
        // There you can load plugin textdomain as example
    }

    /**
     * Dynamically add Addon settings section
     *
     * @since 2.2.083
     */
    abstract public function section_settings($sections);

    /**
     * Dynamically register addon (add addon Instance into Addons array)
     *
     * @since 2.2.083
     */
    public function register_addon($addons) {
        return array_merge( $addons, array($this->name, $this) );
    }

    /**
     * Get addon Setting
     * @since 2.2.106
     *
     * @param string $key
     * @param mixed $default    IF sets False, then check into Empty and return FALSE if empty or not ISSET
     *
     * @return mixed
     */

    public function _get_opt($key, $default = '') {
        // Add Addon unique slug
        $key = $this->slug . '_' . $key;

        if ( empty(self::$addonsSettings) ) {
            //global $fv_addons_settings;
            self::$addonsSettings = get_option(FV::ADDONS_OPT_NAME, array());
        }
        if ( !isset(self::$addonsSettings[$key]) ) {
            return $default;
        }
        /*
            if ( $default == false && empty(self::$addonsSettings[$key]) ) {
                return false;
            }
            return self::$addonsSettings[$key];
        }
        */
        return self::$addonsSettings[$key];

    }

    public function p_get_opt($key, $default = '') {
        return $this->_get_opt($key, $default);
    }

    /**
     * Helper function to get the class object. If instance is already set, return it.
     * Else create the object and return it.
     *
     * @since 2.2.083
     *
     * @return object $instance Return the class instance
     */
    //abstract public static function get_instance();

}

