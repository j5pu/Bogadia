<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    FV
 * @subpackage FV/includes
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    FV
 * @subpackage FV/admin
 * @author     Maxim K <wp-vote@hotmail.com>
 */
class FV_Admin
{

        /**
         * The ID of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string $name The ID of this plugin.
         */
        private $name;

        /**
         * The id of settings page
         *
         * @since    1.0.0
         * @access   private
         * @var      string $menu_page_ids The WP id's of pages
         */
        private $menu_page_ids;

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         * @var      string $name The name of this plugin.
         * @var      string $version The version of this plugin.
         */
        public function __construct($name, $version)
        {
                $this->name = $name;
        }


        /**
         * Process some admin actions (clear log and etc)
         *
         * After redirect on the same page
         * IN ORDER TO if you reboot page action don't not perform again (like clear log)
         *
         * @since    2.2.73
         */
        public function process_admin_actions()
        {
            if (isset($_REQUEST['action'])) {
                $current_page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
                if ('clear' == $_REQUEST['action'] && $current_page == 'fv-debug') {
                    // Очищаем лог
                    FvLogger::clearLog();
                    wp_add_notice( __('Log cleared.', 'fv'), "success" );
                    //do_action("shutdown");
                    wp_safe_redirect( admin_url("admin.php?page={$current_page}&clear=true") );
                    die();
                } elseif ($current_page == 'fv-settings') {
                    if ( 'clear' == $_REQUEST['action'] ) {
                        // Очищаем лог
                        $my_db = new FV_DB;
                        $my_db->clearAllData();
                        wp_add_notice( __('Tables cleared.', 'fv'), "success" );
                        //do_action("shutdown");
                        wp_safe_redirect( admin_url("admin.php?page={$current_page}&clear=true") );
                    } elseif ( 'refresh_key' == $_REQUEST['action'] ) {
                        $key_data = get_option('fotov-update-key', false);
                        if ( is_array($key_data) && !empty($key_data['key']) ) {
                            $r = wp_remote_fopen (UPDATE_SERVER_URL . '?action=get_key_info&slug=wp-foto-vote&license_key=' . $key_data['key']);
                            $new_key_data = @(array)json_decode($r);
                            //FvLogger::addLog('fv_update_key_before_save result', $key_data);
                            if (is_array($new_key_data) && isset($new_key_data['key']) && isset($new_key_data['expiration']) && isset($new_key_data['valid']) ) {
                                //FvLogger::addLog('fv_update_key_before_save Go Save');
                                wp_add_notice( __('Key data refreshed!', 'fv'), "success" );
                                update_option('fotov-update-key', $new_key_data);
                            } else {
                                wp_add_notice( __('Can`t refresh key data, some error!', 'fv'), "warning" );
                                FvLogger::addLog('fv_update_key_before_save (error) : data is not correct!', $new_key_data);
                                return '';
                            }
                        } else {
                            wp_add_notice( __('Can`t refresh key data, seems it empty!', 'fv'), "warning" );
                        }
                        //var_dump($new_key_data);
                        //die();
                        wp_safe_redirect( admin_url("admin.php?page={$current_page}#additional") );
                    }
                    //die();
                } elseif ('clear' == $_REQUEST['action'] && $current_page == 'fv-translation') {
                    fv_reset_public_translation();
                    wp_safe_redirect( admin_url("admin.php?page={$current_page}") );
                    die();
                }
            }

            // Cache notices
            if ( defined('WP_CACHE') && WP_CACHE == true && !defined('DOING_AJAX') ) {
                if ( !FvFunctions::ss('cache-support') && !isset($_COOKIE['fv-hide-cache-notice']) ) {
                    wp_add_notice(
                        '<strong>WP Foto Vote :: You are enabled cache and this may causes some probmels with voting.</strong>
                        <a href="http://docs.wp-vote.net/#cache-support" target="_blank">More info</a><br/>
                        At first enable chache support in Photo contest -> Settings (this will disable some Wordpress security features and increase cheating).

                        <button type="button" id="fv-hide-cache-notice" class="button hide-notice">Hide this message into 90 days or re-login</button>'
                        , 'danger'
                    );
                }
            }

        }
        /**
         * Register the admin pages
         *
         * @since    1.0.0
         */
        public function admin_pages()
        {
            $admin_pages = new FV_Admin_Pages($this->name);
            global $submenu;
            $my_db = new FV_DB;
            $on_moderation_count = $my_db->getCompItemsOnModerationCount();
            if ($on_moderation_count > 0) {
                $on_moderation_count_text = '<span class="on_moderation_count"><span>' . $on_moderation_count . '</span></span>';
            } else {
                $on_moderation_count_text = '';
            }
            //create new top-level menu
            $this->menu_page_ids['home'] = add_menu_page(__('Photo contests', 'fv'), __('Photo contests', 'fv') . $on_moderation_count_text, get_option('fv-needed-capability', 'edit_pages'), FV::NAME, array($admin_pages, 'page_home'), plugins_url('../assets/img/like.png', __FILE__));
            $this->menu_page_ids['moderation'] = add_submenu_page('fv', __('Moderation', 'fv'), __('Moderation', 'fv') . $on_moderation_count_text, get_option('fv-needed-capability', 'edit_pages'), FV::NAME . '-moderation', array($admin_pages, 'page_moderation') );
            $this->menu_page_ids['settings'] = add_submenu_page('fv', __('Settings', 'fv'), __('Settings', 'fv'), get_option('fv-needed-capability', 'edit_pages'), FV::NAME . '-settings', array($admin_pages, 'page_settings') );
            $this->menu_page_ids['formbuilder'] = add_submenu_page('fv', __('Form builder', 'fv'), __('Form builder', 'fv'), get_option('fv-needed-capability', 'edit_pages'), FV::NAME . '-formbuilder', array($admin_pages, 'page_form_builder') );
            $this->menu_page_ids['translation'] = add_submenu_page('fv', __('Translation', 'fv'), __('Translation', 'fv'), get_option('fv-needed-capability', 'edit_pages'), FV::NAME . '-translation', array($admin_pages, 'page_translation') );
            $this->menu_page_ids['votes_log'] = add_submenu_page('fv', __('Votes log', 'fv'), __('Votes log', 'fv'), get_option('fv-needed-capability', 'edit_pages'), FV::NAME . '-vote-log', array($admin_pages, 'page_votes_log') );
            $this->menu_page_ids['analytic'] = add_submenu_page('fv', __('Votes analytic', 'fv'), __('Votes analytic', 'fv'), get_option('fv-needed-capability', 'edit_pages'), FV::NAME . '-vote-analytic', array($admin_pages, 'page_analytic') );
            $this->menu_page_ids['subscribers'] = add_submenu_page('fv', __('Subscribers list', 'fv'), __('Subscribers list', 'fv'), get_option('fv-needed-capability', 'edit_pages'), FV::NAME . '-subscribers-list', array($admin_pages, 'page_subscribers_list') );
            $this->menu_page_ids['debug'] = add_submenu_page('fv', __('Debug', 'fv'), __('Debug', 'fv'), get_option('fv-needed-capability', 'edit_pages'), FV::NAME . '-debug', array($admin_pages, 'page_debug') );


            //var_dump( get_redux_instance(FV::ADDONS_OPT_NAME) );
            //$this->menu_page_ids['addons'] = add_submenu_page('fv', __('Addons', 'fv'), __('Addons', 'fv'), get_option('fv-needed-capability', 'edit_pages'), FV::NAME . '-addons', array(ReduxFrameworkInstances::get_instance(FV::ADDONS_OPT_NAME), 'generate_panel') );


            //add_submenu_page('fv', __('Customizer', 'fv'), __('Customizer', 'fv'), 'edit_posts', 'fv-customizer', array('FV_Theme_Customizer', 'render_page') );

            //$submenu['wsds'][0][0] = __('Payments', 'wsds');
        }


        /**
         * Register the stylesheets for the Dashboard.
         *
         * @since    1.0.0
         */
        public function enqueue_styles()
        {
            //wp_enqueue_style($this->name . '_pure_grid', 'http://yui.yahooapis.com/pure/0.5.0/grids-responsive-min.css', array(), $this->version, 'all');
            //wp_enqueue_style($this->name . '_ionicons', 'http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css', array(), $this->version, 'all');

            // Стили и js для ToolTip
            wp_enqueue_style('dashicons');
            wp_enqueue_style($this->name . '_admin_css', FV::$ADMIN_URL . 'css/fv_admin.css', false, FV::VERSION, 'all');
            //wp_enqueue_style($this->name . '_admin_notices', FV::$ADMIN_URL . 'css/notice.css', false, FV::VERSION, 'all');
        }

        /**
         * Register the JavaScript for the dashboard.
         *
         * @since    1.0.0
         */
        public function enqueue_scripts()
        {
            wp_enqueue_script('fv_lib_js', fv_min_url(FV::$ASSETS_URL . 'js/fv_lib.js'), array('jquery'), FV::VERSION);
            wp_enqueue_script('fv_admin_js', FV::$ADMIN_URL . 'js/fv_admin.js', array('jquery'), FV::VERSION);


            // прописываем переменные
            $fv_data = array(
                'wp_lang' => get_bloginfo('language'),
                'ajax_url' => admin_url('admin-ajax.php'),
            );
            wp_localize_script('fv_admin_js', 'fv', $fv_data);

            $fv_lang['form_votes_tootip'] = __('You can change, but if the number of votes in the database would be another <br/> (someone vote in the editing page), <br/> changes will not be saved.', 'fv');
            $fv_lang['from_comment_tootip'] = __('The site is not displayed when loading user photos, <br/> to specify contact information and notes to photos.<br/> If specified as `email;text`, user can take<br/> notification, when photo has been approved.', 'fv');
            $fv_lang['form_img_min_tootip'] = __('Image size 150*150 px (if you specify more, with a large number of contestants <br/> page will be long load). <br /> Tip: The first set thumbnail - if its size is 150*150 <br/> plugin will automatically insert the full image in the `image` field', 'fv');
            $fv_lang['clear_stats_alert'] = __('Similarly remove all ip addresses, voted in this contest?', 'fv');
            $fv_lang['clear_stats_cleared'] = __('Cleared', 'fv');

            $fv_lang['reset_votes_alert'] = __('This will reset all pohtos votes to 0 (this will not remove votes from log)!', 'fv');
            $fv_lang['reset_votes_ready'] = __('Votes reseted!', 'fv');

            $fv_lang['delete_confirmation'] = __('Are you sure? This will delete contestant and may photo from hosting (if you enabled this in settings)!', 'fv');
            $fv_lang['contestant_and_photo_deleted'] = __('Contestant (and may be photo) deleted!', 'fv');
            $fv_lang['contestant_approved'] = __('Contestant approved!', 'fv');
            $fv_lang['saved'] = __('Saved!', 'fv');
            $fv_lang['rotate_confirm'] = __('Are you sure to rotate image and thumbnails?', 'fv');
            $fv_lang['rotate_successful'] = __('Rotating successful ends!', 'fv');
            $fv_lang['rotate_error'] = __('Rotating: some problem!', 'fv');
            $fv_lang['rotate_start'] = __('Rotating to *A* degrees start!', 'fv');

            $fv_lang['form_img'] = __('Full photo', 'fv');
            $fv_lang['form_pohto_status'] = array( __('Published', 'fv'), __('On modearation', 'fv'), __('Draft', 'fv') );

            wp_localize_script('fv_admin_js', 'fv_lang', $fv_lang);

            add_action('admin_print_styles-' . $this->menu_page_ids['translation'], array($this, 'assets_page_translation'));
            add_action('admin_print_styles-' . $this->menu_page_ids['settings'], array($this, 'assets_page_settings'));
            add_action('admin_print_styles-' . $this->menu_page_ids['moderation'], array($this, 'assets_page_moderation'));
            add_action('admin_print_styles-' . $this->menu_page_ids['votes_log'], array($this, 'assets_lib_growl'));
            add_action('admin_print_styles-' . $this->menu_page_ids['subscribers'], array($this, 'assets_lib_growl'));

        }


        /**
         * Load edit_contest JS & CSS
         * @return void
         */
        public static function assets_page_edit_contest()
        {
            self::assets_lib_wp_media();
            self::assets_lib_datetimepicker();
            self::assets_lib_tooltip();
            self::assets_lib_datatable();
            self::assets_lib_boostrap();
            self::assets_lib_growl();


            wp_enqueue_script('fv_contest_js', FV::$ADMIN_URL . 'js/fv_contest.js', array('jquery'), FV::VERSION);
            wp_enqueue_style(FV::PREFIX . 'icommon', FV::$ASSETS_URL . 'icommon/fv_fonts.css', false, FV::VERSION, 'all');
        }

        /**
         * Load translation JS & CSS
         * @return void
         */
        public function assets_page_translation()
        {
            self::assets_lib_tabs();
            self::assets_lib_typoicons();
        }

        /**
         * Load settings JS & CSS
         * @return void
         */
        public function assets_page_settings()
        {
            //wp_register_script('wsds-admin-seetings-js', plugin_dir_url(__FILE__) . 'js/wsds-settings.js', array('farbtastic', 'jquery'), '1.0.0');
            //wp_enqueue_script('wsds-admin-seetings-js');
            self::assets_lib_tabs();
            self::assets_lib_tooltip();
            self::assets_lib_typoicons();
            self::assets_lib_codemirror();
            self::assets_lib_growl();
            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_style( 'wp-color-picker' );

            wp_enqueue_script('fv_settings_js', FV::$ADMIN_URL . 'js/fv_settings.js', array('jquery'), FV::VERSION, true);
        }

        /**
         * Load settings JS & CSS
         * @return void
         */
        public function assets_page_form_builder()
        {
            //wp_register_script('wsds-admin-seetings-js', plugin_dir_url(__FILE__) . 'js/wsds-settings.js', array('farbtastic', 'jquery'), '1.0.0');
            //wp_enqueue_script('wsds-admin-seetings-js');
            self::assets_lib_typoicons();
            wp_enqueue_style(FV::PREFIX . 'icommon', FV::$ASSETS_URL . 'icommon/fv_fonts.css', false, FV::VERSION, 'all');
            wp_enqueue_script('fv_formbuilder_vendor', FV::$ADMIN_URL . 'libs/formBuilder/vendor.js', array('jquery'), FV::VERSION, true);
            wp_enqueue_script('fv_formbuilder', FV::$ADMIN_URL . 'libs/formBuilder/formbuilder.js', array('jquery'), FV::VERSION, true);
            wp_enqueue_style(FV::PREFIX . 'formbuilder', FV::$ADMIN_URL . 'libs/formBuilder/formbuilder.css', false, FV::VERSION, 'all');
            self::assets_lib_growl();
        }

        /**
         * Load moderation JS & CSS
         * @return void
         */
        public function assets_page_moderation()
        {
            self::assets_lib_datatable();
            self::assets_lib_growl();
            wp_enqueue_script('fv_contest_js', FV::$ADMIN_URL . 'js/fv_contest.js', array('jquery'), FV::VERSION);
        }

        /**
         * Load typoicons JS & CSS
         * @return void
         */
        public static function assets_lib_typoicons()
        {
            wp_enqueue_style(FV::PREFIX . '_typicons', FV::$ASSETS_URL . 'typoicons/typicons.min.css', false, FV::VERSION, 'all');
        }

        /**
         * Load Tabs JS & CSS
         * @return void
         */
        public static function assets_lib_tabs()
        {
            wp_enqueue_style(FV::PREFIX . '_tabs_css', FV::$ADMIN_URL . 'css/fv_tab.css', false, FV::VERSION, 'all');
            wp_enqueue_script(FV::PREFIX . '_tabs_js', FV::$ADMIN_URL . 'js/fv_tabs.js', array('jquery'), FV::VERSION);
        }

        /**
         * Load tooltip JS & CSS
         * @return void
         */
        public static function assets_lib_tooltip()
        {
            wp_enqueue_script('fv_admin_tooltip', FV::$ADMIN_URL . 'js/fv_tooltip.js', array('jquery'), '1.0');
        }

        /**
         * Load datatable JS & CSS
         * @return void
         */
        public static function assets_lib_datatable()
        {
            wp_enqueue_style('fv_admin_datatable', '//cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css', false, '1.0', 'all');
            wp_enqueue_script('fv_admin_datatable', '//cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js', array('jquery'), '1.0');
        }

    /**
         * Load DateTimepicker JS & CSS
         * @return void
         */
        public static function assets_lib_datetimepicker()
        {
            // Jquery Datetimepicker library
            wp_enqueue_style( FV::PREFIX. 'datetimepicker', FV::$ADMIN_URL . 'libs/datetimepicker/jquery.datetimepicker.css', false, FV::VERSION, 'all' );
            wp_enqueue_script( FV::PREFIX. 'datetimepicker', FV::$ADMIN_URL .'libs/datetimepicker/jquery.datetimepicker.min.js', array('jquery'), FV::VERSION );
        }

        /**
         * Load boostrap CSS
         * @return void
         */
        public static function assets_lib_boostrap()
        {
            wp_enqueue_style( FV::PREFIX. 'bootstrap', FV::$ADMIN_URL .'css/vendor/bootstrap.css' , false, FV::VERSION, 'all' );
            //wp_enqueue_style( FV::PREFIX. 'bootstrap-theme', FV::$ADMIN_URL .'css/vendor/bootstrap-theme.css' , false, '1.0', 'all' );
            wp_enqueue_script( FV::PREFIX. 'bootstrap', FV::$ADMIN_URL . 'js/vendor/bootstrap.min.js' , array('jquery'), FV::VERSION );
        }

    /**
         * Load settings JS & CSS
         * @return void
         */
        public static function assets_lib_wp_media()
        {
            /*
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
            wp_enqueue_style('thickbox');
            */
            wp_enqueue_media();
        }

        /**
         * Load jVectormap JS & CSS
         * @return void
         */
        public static function assets_lib_jvectormap()
        {
            wp_enqueue_script('fv_admin_jvectormap',FV::$ADMIN_URL .'libs/jquery-jvectormap/jquery-jvectormap-2.0.1.min.js', array('jquery'), '1.0');
            wp_enqueue_script('fv_admin_jvectormap-world', FV::$ADMIN_URL .'libs/jquery-jvectormap/jquery-jvectormap-world-mill-en.js', array('jquery'), '1.0');
            wp_enqueue_style('fv_admin_jvectormap_css', FV::$ADMIN_URL .'libs/jquery-jvectormap/jquery-jvectormap-2.0.1.css', false, '1.0', 'all');
        }

        /**
         * Load jVectormap JS & CSS
         * @return void
         */
        public static function assets_lib_amstockchart()
        {

            wp_enqueue_script('fv_admin_amstockchart_main', FV::$ADMIN_URL . 'libs/amstockchart/amcharts.js', array('jquery'), FV::VERSION);
            //wp_enqueue_script('fv_admin_amstockchart_amstock', FV::$ASSETS_URL . 'vendor/amstockchart/amstock.js', array('fv_admin_amstockchart_main'), '1.0');
            wp_enqueue_script('fv_admin_amstockchart_serial', FV::$ADMIN_URL . 'libs/amstockchart/serial.js', array('fv_admin_amstockchart_main'), FV::VERSION);
            wp_enqueue_style('fv_admin_amstockchart_css', FV::$ADMIN_URL . 'libs/amstockchart/style.css', false, FV::VERSION, 'all');
        }

    /**
         * Load growl JS & CSS
         * @return void
         */
        public static function assets_lib_growl()
        {
            wp_enqueue_style('fv_admin_growl', FV::$ADMIN_URL . 'css/vendor/jquery.growl.css', false, '1.0', 'all');
            wp_enqueue_script('fv_admin_growl', FV::$ADMIN_URL . 'js/vendor/jquery.growl.js', array('jquery'), '1.0');
        }

        /**
         * Load codemirror JS & CSS
         * @return void
         */
        public static function assets_lib_codemirror($text_mode = false)
        {
            wp_enqueue_script('fv_admin_codemirror', FV::$ADMIN_URL . '/libs/codemirror/codemirror.js', array('jquery'), '1.0');
            wp_enqueue_style('fv_admin_codemirror', FV::$ADMIN_URL . '/libs/codemirror/codemirror.css', false, '1.0', 'all');
            if (!$text_mode) {
                wp_enqueue_script('fv_admin_codemirror-mode-css', FV::$ADMIN_URL . '/libs/codemirror/css.js', array('jquery'), '1.0');
                wp_enqueue_script('fv_admin_codemirror-hint', FV::$ADMIN_URL . '/libs/codemirror/show-hint.js', array('jquery'), '1.0');
                wp_enqueue_script('fv_admin_codemirror-hint-css', FV::$ADMIN_URL . '/libs/codemirror/css-hint.js', array('jquery'), '1.0');
                wp_enqueue_style('fv_admin_codemirror-hint', FV::$ADMIN_URL .'/libs/codemirror/show-hint.css', false, '1.0', 'all');
            } else {
                wp_enqueue_script('fv_admin_codemirror-textile', FV::$ADMIN_URL . '/libs/codemirror/textile/textile.js', array('jquery'), '1.0');

            }
        }

        /**
         * Add settings link to plugin list table
         *
         * @param  array $links Existing links
         *
         * @return array        Modified links
         */
        public static function add_settings_link($links)
        {
                $settings_link = sprintf ('<a href="admin.php?page=fv-settings">%s</a>', __('Settings', 'fv') );
                array_push($links, $settings_link);
                return $links;
        }


        public function register_fv_settings()
        {
                //register our settings
                // Leaders
                register_setting('fotov-settings-group', 'fv');
                register_setting('fotov-settings-group', 'fotov-leaders-hide');
                register_setting('fotov-settings-group', 'fotov-leaders-count');
                register_setting('fotov-settings-group', 'fotov-leaders-type');

                register_setting('fotov-settings-group', 'fotov-block-width');

                register_setting('fotov-settings-group', 'fv-image-delete-from-hosting');
                register_setting('fotov-settings-group', 'fotov-image-size');
                register_setting('fotov-settings-group', 'fotov-image-width');
                register_setting('fotov-settings-group', 'fotov-image-height');
                register_setting('fotov-settings-group', 'fotov-image-hardcrop');

                register_setting('fotov-settings-group', 'fotov-voting-no-lightbox');
                register_setting('fotov-settings-group', 'fotov-photo-in-new-page');
                register_setting('fotov-settings-group', 'fotov-voting-noshow-social');
                register_setting('fotov-settings-group', 'fotov-voting-noshow-vk');
                register_setting('fotov-settings-group', 'fotov-voting-noshow-fb');
                register_setting('fotov-settings-group', 'fotov-voting-noshow-tw');
                register_setting('fotov-settings-group', 'fotov-voting-noshow-ok');
                register_setting('fotov-settings-group', 'fotov-voting-noshow-gp');
                register_setting('fotov-settings-group', 'fotov-voting-noshow-pi');
                register_setting('fotov-settings-group', 'fotov-voting-noshow-email');

                register_setting('fotov-settings-group', 'fotov-upload-autorize');
                register_setting('fotov-settings-group', 'fotov-upload-notify');
                register_setting('fotov-settings-group', 'fotov-upload-notify-email');
                register_setting('fotov-settings-group', 'fotov-upload-limit-email');
                register_setting('fotov-settings-group', 'fotov-upload-limit-cookie');
                register_setting('fotov-settings-group', 'fotov-upload-limit-ip');
                register_setting('fotov-settings-group', 'fotov-upload-limit-userid');
                register_setting('fotov-settings-group', 'fotov-users-notify');
                register_setting('fotov-settings-group', 'fotov-users-notify-upload');
                register_setting('fotov-settings-group', 'fotov-users-notify-from-mail');
                register_setting('fotov-settings-group', 'fotov-users-notify-from-name');

                register_setting('fotov-settings-group', 'fotov-upload-photo-resize');
                register_setting('fotov-settings-group', 'fotov-upload-photo-maxwidth');
                register_setting('fotov-settings-group', 'fotov-upload-photo-maxheight');

                register_setting('fotov-settings-group', 'fotov-upload-form-show-email');

                register_setting('fotov-settings-group', 'fotov-upload-form-show-comment');
                register_setting('fotov-settings-group', 'fotov-upload-form-show-adress');
                register_setting('fotov-settings-group', 'fotov-upload-form-show-city');
                register_setting('fotov-settings-group', 'fotov-upload-form-show-country');
                register_setting('fotov-settings-group', 'fotov-upload-form-show-age');
                register_setting('fotov-settings-group', 'fotov-upload-form-show-custom-dropdown');
                register_setting('fotov-settings-group', 'fotov-upload-form-custom-dropdown-lines');
                register_setting('fotov-settings-group', 'fotov-upload-photo-limit-size');

                register_setting('fotov-settings-group', 'fotov-update-key', 'fv_update_key_before_save');

                register_setting('fotov-settings-group', 'fotov-custom-css');

                register_setting('fotov-settings-group', 'fotov-fb-apikey');
                register_setting('fotov-settings-group', 'fv-fb-assets-position');
                register_setting('fotov-settings-group', 'fv-export-delimiter');

                register_setting('fotov-settings-group', 'fv-needed-capability');
        }


}
