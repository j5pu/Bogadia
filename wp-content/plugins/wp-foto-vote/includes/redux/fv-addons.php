<?php

/**
  ReduxFramework Sample Config File
  For full documentation, please visit: https://docs.reduxframework.com
 * */

if (!class_exists('Redux_Framework_fv_addons')) {

    class Redux_Framework_fv_addons {

        public $args        = array();
        public $sections    = array();
        public $theme;
        public $ReduxFramework;

        public function __construct() {

            if (!class_exists('ReduxFramework')) {
                return;
            }

            // This is needed. Bah WordPress bugs.  ;)
            if ( true == Redux_Helpers::isTheme( __FILE__ ) ) {
                $this->initSettings();
            } else {
                add_action('plugins_loaded', array($this, 'initSettings'), 10);
            }

        }

        public function initSettings() {

            // Just for demo purposes. Not needed per say.
            $this->theme = wp_get_theme();

            // Set the default arguments
            $this->setArguments();

            // Set a few help tabs so you can see how it's done
            //$this->setHelpTabs();

            // Create the sections and fields
            $this->setSections();

            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }

            // If Redux is running as a plugin, this will remove the demo notice and links

            add_action( 'redux/loaded', array( $this, 'remove_demo' ) );

            add_action( "redux/page/".FV::ADDONS_OPT_NAME."/enqueue", array( $this, 'addAds' ) );


            // Function to test the compiler hook and demo CSS output.
            // Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
            //add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 2);
            
            // Change the arguments after they've been declared, but before the panel is created
            //add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );
            
            // Change the default value of a field after it's been set, but before it's been useds
            //add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );
            
            // Dynamically add a section. Can be also used to modify sections/fields
            //add_filter('redux/options/' . $this->args['opt_name'] . '/sections', array($this, 'dynamic_section'));

            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);

        }


        // Remove the demo link and the notice of integrated demo from the redux-framework plugin
        function remove_demo() {

            // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
            if (class_exists('ReduxFrameworkPlugin')) {
                remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::instance(), 'plugin_metalinks'), null, 2);

                // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
                remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));
            }
        }

        public function setSections() {

            $description = __('This is home pages for addons menu.
                Addons allows simply extends plugin features, wihtout code changes
                <br/>After install new addon, here will be added menu with his settings.
                <br/>Addons installation like plugin - go to Plugin => Add new => Upload.
                <br/><br/>Addons settings based on powerful Redux Framework - https://github.com/ReduxFramework/Redux-Framework', 'fv');

            $description .= '<iframe src="http://wp-vote.net/ads/addons_list.html" style="min-height: 520px; width: 100%; height: 100%;"></iframe>';

            // ACTUAL DECLARATION OF SECTIONS
            $this->sections[] = array(
                'title'     => __('Home', 'fv'),
                'desc'      => $description,
                'icon'      => 'el-icon-home',
                // 'submenu' => false, // Setting submenu to false on a given section will hide it from the WordPress sidebar menu!
                'fields'    => array()
            );
/*
            if (file_exists(dirname(__FILE__) . '/README.html')) {
                $this->sections['theme_docs'] = array(
                    'icon'      => 'el-icon-list-alt',
                    'title'     => __('Documentation', 'fv'),
                    'fields'    => array(
                        array(
                            'id'        => '17',
                            'type'      => 'raw',
                            'markdown'  => true,
                            'content'   => file_get_contents(dirname(__FILE__) . '/README.html')
                        ),
                    ),
                );
            }
*/
        }

        public function setHelpTabs() {

            // Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-1',
                'title'     => __('Theme Information 1', 'fv'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'fv')
            );

            // Set the help sidebar
            $this->args['help_sidebar'] = __('<p>This is the sidebar content, HTML is allowed.</p>', 'fv');
        }

        /**

          All the possible arguments for Redux.
          For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments

         * */
        public function setArguments() {

            $this->args = array(
                'opt_name' => FV::ADDONS_OPT_NAME,
                'display_name' => 'FV Addons',
                'page_slug' => 'fv-addons',
                'page_title' => 'FV Addons settings',
                'update_notice' => false,
                'intro_text' => '<p>Photo Contest addons settings.</p>',
                'footer_text' => '<p></p>',
                'menu_type' => 'submenu',
                'menu_title' => 'Addons',
                'allow_sub_menu' => true,
                'page_parent' => 'fv',
		        'customizer'  => false,
                //'page_parent_post_type' => 'your_post_type',
                'default_mark' => '*',

                'dev_mode'      => false,                    // Show the time the page took to load, etc
                'admin_bar'     => false,

                'hints' => 
                array(
                    'icon' => 'el-icon-question-sign',
                    'icon_position' => 'right',
                    'icon_size' => 'normal',
                    'tip_style' =>
                        array(
                            'color' => 'light',
                        ),
                    'tip_position' =>
                        array(
                            'my' => 'top left',
                            'at' => 'bottom right',
                        ),
                    'tip_effect' =>
                        array(
                            'show' =>
                                array(
                                    'duration' => '500',
                                    'event' => 'mouseover',
                                ),
                            'hide' =>
                                array(
                                    'duration' => '500',
                                    'event' => 'mouseleave unfocus',
                                ),
                        ),
                ),
                'output' => false,
                'output_tag' => false,
                'compiler' => false,
                'page_icon' => 'icon-themes',
                'page_permissions' => get_option('fv-needed-capability', 'edit_pages'),
                //'page_permissions' => 'manage_options', *TODO
                'save_defaults' => true,
                'show_import_export' => false,
                'transient_time' => '3600',
                'network_sites' => true,
              );

            // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
            $this->args['share_icons'][] = array(
                'url'   => 'https://twitter.com/hashtag/wp_foto_vote?f=realtime&src=hash',
                'title' => 'News on Twitter',
                'icon'  => 'el-icon-twitter'
            );

        }

        public function addAds() {
            wp_enqueue_script('fv_admin_addons', FV::$ADMIN_URL . 'js/fv_addons.js', array('jquery'), '1.0');
        }

    }
    
    global $reduxConfigFvAddons;
    $reduxConfigFvAddons = new Redux_Framework_fv_addons();

}
