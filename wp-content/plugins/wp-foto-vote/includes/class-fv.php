<?php

defined('ABSPATH') or die("No script kiddies please!");

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @since      2.2.073
 *
 * @package    FV
 * @subpackage FV/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2.2.073
 * @package    FV
 * @subpackage FV/includes
 * @author     Maxim K <wp-vote@hotmail.com>
 */
class FV
{
    /**
     * The current version of the plugin.
     *
     * @since    2.2.073
     * @access   public
     * @var      const VERSION The current version of the plugin.
     */
    const VERSION = '2.2.123';

    const NAME = 'fv';
    const PREFIX = 'fv_';
    const SLUG = 'wp-foto-vote';
    public static $DEBUG_MODE;

    const ADDONS_OPT_NAME = 'fv_addons_settings';

    public static $ADDONS_URL;
    public static $ADDONS_ROOT;

    public static $ASSETS_URL;
    public static $THEMES_ROOT;
    public static $THEMES_ROOT_URL;
    public static $ADMIN_ROOT;
    public static $ADMIN_URL;
    public static $ADMIN_PARTIALS_ROOT;
    public static $INCLUDES_ROOT;

    public static $PUBLIC_ROOT;

    public static $ADDONS;

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    2.2.073
     * @access   protected
     * @var      FV_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    2.2.073
     * @access   protected
     * @var      string $FV The string used to uniquely identify this plugin.
     */
    protected $NAME;


    /**
     * The unique identifier of this plugin.
     *
     * @since    2.2.073
     * @access   protected
     * @var      string $FV The string used to uniquely identify this plugin.
     */
    protected $file;


    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the Dashboard and
     * the public-facing side of the site.
     *
     * @since    2.2.073
     */
    public function __construct($file, $plugin_dir)
    {

        $this->file = $file;
        //$this->NAME = 'wsds';

        self::$ADDONS_URL = plugins_url(self::SLUG . "/addons/");
        self::$ADDONS_ROOT = $plugin_dir . "/addons/";

        self::$ASSETS_URL = plugins_url(self::SLUG . "/assets/");
        self::$THEMES_ROOT = $plugin_dir . "/templates/";
        self::$THEMES_ROOT_URL = plugins_url(self::SLUG . "/templates/");
        self::$ADMIN_URL = plugins_url(self::SLUG . "/admin/");
        self::$ADMIN_ROOT = $plugin_dir . "/admin/";
        self::$ADMIN_PARTIALS_ROOT = $plugin_dir . "/admin/partials/";
        self::$INCLUDES_ROOT = $plugin_dir . "/includes/";

        self::$PUBLIC_ROOT = $plugin_dir . "/public/";

        $is_admin = is_admin();
        $this->load_dependencies($is_admin);

        // Init DEBUG Levels
        FvDebug::init_lvl();

        $this->load_plugin_textdomain();
        $this->define_admin_hooks($is_admin);
        $this->define_public_hooks($is_admin);
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    2.2.073
     * @access   private
     */
    private function load_dependencies($is_admin)
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once('class-fv-loader.php');
        include_once('class-fv-autoloader.php');

        /**
         * The classes for logging and debug
         */
        require_once('libs/class-fv-logger.php');
        require_once('libs/class-fv-debug.php');

        /**
         * Tables lists
         */

        //defined('SHORTINIT') &&
        if (!SHORTINIT) {
            // Widgets
            require_once('widget-list/class-widget.php');
            require_once('widget-gallery/class-widget.php');
        }

        /**
         * Functions and other
         */
        require_once self::$INCLUDES_ROOT . 'class-fv-functions.php';
        include_once self::$INCLUDES_ROOT . 'fv-translations-helper.php';
        //require_once self::$INCLUDES_ROOT . 'class-fv-contest.php';
        //require_once self::$INCLUDES_ROOT . 'class-fv-lightbox-evolution.php';
        //require_once self::$INCLUDES_ROOT . 'class-fv-image-lightbox.php';
        include_once self::$INCLUDES_ROOT . 'libs/class_empty_unit.php';
        require_once self::$INCLUDES_ROOT . 'notice/class-admin-notice-helper.php';
        //require_once self::$INCLUDES_ROOT . 'class-fv-theme-base.php';
        require_once self::$INCLUDES_ROOT . 'class-fv-addon-base.php';
        //require_once self::$INCLUDES_ROOT . 'class-fv-form-helper.php';
        require_once self::$ADDONS_ROOT . 'fv-addons-loader.php';

        /**
         * The class responsible for working with db
         */
        require self::$INCLUDES_ROOT . 'db/class-query.php';
        require_once self::$INCLUDES_ROOT . 'class-fv-db.php';

        /**
         * The class responsible for defining all actions that occur in the Dashboard.
         */
        require_once self::$ADMIN_ROOT . 'class-fv-admin.php';
        //require_once self::$ADMIN_ROOT . 'class-fv-admin-pages.php';
        if ($is_admin) {
            //require_once self::$ADMIN_ROOT . 'class-fv-admin-ajax.php';
            //require_once self::$ADMIN_ROOT . 'class-fv-admin-export.php';
            require_once self::$ADMIN_ROOT . 'fv-admin-helper.php';
        }

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        //require_once self::$PUBLIC_ROOT . '/class-fv-public.php';
        //require_once self::$PUBLIC_ROOT . '/class-fv-public-ajax.php';
        //require_once self::$PUBLIC_ROOT . '/class-fv-public-vote.php';

        if ($is_admin && !SHORTINIT) {
            // Updates
            require_once self::$INCLUDES_ROOT . 'plugin-updates/plugin-update-checker.php';

            /**
             * Redux options framework
             */
            //(!defined('SHORTINIT') ||
            if ( (!defined('DOING_AJAX') || !DOING_AJAX) && FvFunctions::ss('disable-addons-support', false) == false) {
                require_once self::$ADMIN_ROOT . 'options-framework/options-framework.php';
            }

        }


        //require_once FV::$INCLUDES_ROOT . 'redux/admin-init.php';


        $this->loader = new FV_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the FV_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    2.2.073
     * @access   private
     */
    private function load_plugin_textdomain()
    {
        //$plugin_i18n = new FV_i18n();
        //$plugin_i18n->set_domain($this->get_NAME());
        //$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

        load_plugin_textdomain(
            FV::NAME, false, FV::SLUG . '/languages/'
        );
    }

    /**
     * Register all of the hooks related to the dashboard functionality
     * of the plugin.
     *
     * @since    2.2.073
     * @access   private
     */
    private function define_admin_hooks($is_admin)
    {
        $plugin_admin = new FV_Admin($this->get_NAME(), $this->get_version());
        //$plugin_admin_pages = new FV_Admin_Pages($this->get_NAME());

        //$this->loader->add_action('init', $this, 'wp_init' );
        $this->loader->add_action('admin_init', $plugin_admin, 'process_admin_actions');
        $this->loader->add_action('admin_init', $plugin_admin, 'register_fv_settings');

        if ($is_admin) {
            $this->loader->add_action('admin_menu', $plugin_admin, 'admin_pages');

            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

            $this->loader->add_action('wp_ajax_fv_clear_contest_stats', 'FV_Contest', 'clear_contest_stats');
            $this->loader->add_action('wp_ajax_fv_reset_contest_votes', 'FV_Contest', 'reset_contest_votes');

            $this->loader->add_action('wp_ajax_fv_form_contestant', 'FV_Contest', 'form_contestant');
            $this->loader->add_action('wp_ajax_fv_save_contestant', 'FV_Contest', 'save_contestant');
            $this->loader->add_action('wp_ajax_fv_approve_constestant', 'FV_Contest', 'approve_constestant');
            $this->loader->add_action('wp_ajax_fv_delete_constestant', 'FV_Contest', 'delete_constestant');

            $this->loader->add_action('wp_ajax_fv_rotate_image', 'FV_Admin_Ajax', 'rotate_image');
            $this->loader->add_action('wp_ajax_fv_form_contestants', 'FV_Admin_Ajax', 'form_contestants');

            $this->loader->add_action('wp_ajax_fv_export', 'FV_Admin_Export', 'run');

            $this->loader->add_action('wp_ajax_fv_save_form_structure', 'Fv_Form_Helper', 'AJAX_save_form_structure');
            $this->loader->add_action('wp_ajax_fv_reset_form_structure', 'Fv_Form_Helper', 'AJAX_reset_form_structure');

            $this->loader->add_action('activated_plugin', 'FV_Functions', 'check_activation_error', 10, 2);

            // Add settings link to plugins page
            $this->loader->add_filter('plugin_action_links_' . $this->file, 'FV_Admin', 'add_settings_link');

            // Upadting
            $hook = "in_plugin_update_message-" . $this->file;
            //FvLogger::addLog( $hook );
            add_action($hook, 'fv_add_update_message', 10, 2);
        }

        //add_action('wp_ajax_fv_form_contestant', array('FV_Contest', 'form_contestant') );
        //add_action('wp_ajax_fv_save_contestant', array('FV_Contest', 'save_contestant') );
        //add_action('wp_ajax_fv_approve_constestant', array('FV_Contest', 'approve_constestant') );

        $this->loader->add_action('plugins_loaded', __CLASS__, 'install', 1);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    2.2.073
     * @access   private
     */
    private function define_public_hooks($is_admin)
    {
        $this->loader->add_action('wp_ajax_vote', 'FV_Public_Vote', 'vote');
        $this->loader->add_action('wp_ajax_nopriv_vote', 'FV_Public_Vote', 'vote');

        $this->loader->add_action('wp_ajax_fv_is_subscribed', 'FV_Public_Vote', 'is_subscribed');
        $this->loader->add_action('wp_ajax_nopriv_fv_is_subscribed', 'FV_Public_Vote', 'is_subscribed');

        $this->loader->add_action('wp_ajax_fv_soc_login', 'FV_Public_Vote', 'soc_login');
        $this->loader->add_action('wp_ajax_nopriv_fv_soc_login', 'FV_Public_Vote', 'soc_login');

        $this->loader->add_action('wp_ajax_fv_upload', 'FV_Public_Ajax', 'upload_photo');
        $this->loader->add_action('wp_ajax_nopriv_fv_upload', 'FV_Public_Ajax', 'upload_photo');

        $this->loader->add_action('wp_ajax_fv_ajax_get_votes', 'FV_Public_Ajax', 'ajax_get_votes_counts');
        $this->loader->add_action('wp_ajax_nopriv_fv_ajax_get_votes', 'FV_Public_Ajax', 'ajax_get_votes_counts');

        $this->loader->add_action('wp_ajax_fv_ajax_go_to_page', 'FV_Public_Ajax', 'ajax_go_to_page');
        $this->loader->add_action('wp_ajax_nopriv_fv_ajax_go_to_page', 'FV_Public_Ajax', 'ajax_go_to_page');

        // add action for lightbox
        $this->loader->add_action('fv_load_lightbox_evolution', 'FV_Lightbox_Evolution', 'assets');
        $this->loader->add_filter('fv_lightbox_list_array', 'FV_Lightbox_Evolution', 'initListThemes');

        $this->loader->add_action('fv_load_lightbox_imageLightbox', 'Fv_Image_Lightbox', 'assets');
        $this->loader->add_filter('fv_lightbox_list_array', 'Fv_Image_Lightbox', 'initListThemes');


        if (!$is_admin && !SHORTINIT) {
            $plugin_public = new FV_Public($this->get_NAME(), $this->get_version());

            $this->loader->add_shortcode("foto_vote", $plugin_public, "shortcode");
            $this->loader->add_shortcode("fv", $plugin_public, "shortcode");
            $this->loader->add_shortcode("fv_upload_form", $plugin_public, "shortcode_upload_form");
            $this->loader->add_shortcode("fv_contests_list", $plugin_public, "shortcode_show_contests_list");
            $this->loader->add_shortcode("fv_countdown", $plugin_public, "shortcode_countdown");
            $this->loader->add_shortcode("fv_leaders", $plugin_public, "shortcode_leaders");

            $this->loader->add_action('send_headers', $plugin_public, 'set_upload_cookie');

            add_filter('wp_footer', 'fv_custom_css');

            // if selected load FB SDK in head loads it with wp_enqueue_scripts
            // else it's no urgent, and loads it if we really needed it
            if (get_option('fv-fb-assets-position', 'footer') == 'head') {
                $this->loader->add_action('wp_head', $plugin_public, 'fb_assets_and_init', 1);
            } else {
                $this->loader->add_action('wp_footer', $plugin_public, 'fb_assets_and_init');
                //$this->loader->add_action( 'fv_after_contest_item', $plugin_public, 'fb_assets_and_init' );
            }
        }
        global $contest_id;
        add_action('admin_bar_menu', 'fv_add_toolbar_items', 100);

        if (isset($_GET["contest_id"])) {
            if (
                strpos($_SERVER["HTTP_USER_AGENT"], "facebookexternalhit/") !== false ||
                strpos($_SERVER["HTTP_USER_AGENT"], "Facebot") !== false ||
                strpos($_SERVER["HTTP_USER_AGENT"], "visionutils") !== false
            ) {
                # Remove WordPress' canonical links
                remove_action('wp_head', 'rel_canonical');
                add_filter('wpseo_opengraph_url', '__return_null');
                add_filter('wpseo_canonical', '__return_null');

            }
        }

        fv_default_addons_load();
    }


    // Check db version on plugin loads
    public static function install()
    {
        if ( SHORTINIT && (defined('DOING_AJAX') && DOING_AJAX == TRUE) ) {
            return;
        }

        $current_db_version = get_option('fv_db_version');
        if ($current_db_version !== FV_DB_VERSION) {

            // add / upgrade tables
            ModelContest::query()->install();
            ModelCompetitors::query()->install();
            ModelVotes::query()->install();
            // if translations already exists
            if ( !fv_add_public_translation_messages() ) {
                fv_update_exists_public_translation_messages();
            }

            // set db version
            update_option("fv_db_version", FV_DB_VERSION);

            // IF Key not exists
            $key_arr = get_option('fotov-update-key', false);
            if (!$key_arr) {
                $defaults = array('key' => FV_UPDATE_KEY, 'valid' => 1, 'expiration' => FV_UPDATE_KEY_EXPIRATION);
                add_option("fotov-update-key", $defaults, false, 'no');
            }

            //delete_option('fotov-translation');
            // add translation strings, if they not exists
            load_plugin_textdomain('fv', false, FV::SLUG . '/languages/');

        }

    }

    // Runs when wp inited
    function wp_init()
    {
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    2.2.073
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     2.2.073
     * @return    string    The name of the plugin.
     */
    public function get_NAME()
    {
        return self::NAME;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     2.2.073
     * @return    FV_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     2.2.073
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return FV::VERSION;
    }
}
