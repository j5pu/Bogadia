<?php
defined('ABSPATH') or die('No script kiddies please!');
/**
 * Plugin Name: AccessPress Social Auto Post
 * Plugin URI: https://accesspressthemes.com/wordpress-plugins/accesspress-social-auto-post/
 * Description: A plugin to publish your wordpress posts to various social networks
 * Version: 1.0.5
 * Author: AccessPress Themes
 * Author URI: http://accesspressthemes.com
 * Text Domain: asap
 * Domain Path: /languages/
 * 
 */
/**
 * Declaration of necessary constants
 * */
if (!defined('ASAP_CSS_DIR')) {
    define('ASAP_CSS_DIR', plugin_dir_url(__FILE__) . '/css');
}
if (!defined('ASAP_IMG_DIR')) {
    define('ASAP_IMG_DIR', plugin_dir_url(__FILE__) . '/images');
}
if (!defined('ASAP_JS_DIR')) {
    define('ASAP_JS_DIR', plugin_dir_url(__FILE__) . '/js');
}
if (!defined('ASAP_VERSION')) {
    define('ASAP_VERSION', '1.0.5');
}
if (!defined('ASAP_TD')) {
    define('ASAP_TD', 'asap');
}
if (!defined('ASAP_PLUGIN_FILE')) {
    define('ASAP_PLUGIN_FILE', __FILE__);
}

if (!defined('ASAP_API_VERSION')) {
    define('ASAP_API_VERSION', 'v2.0');
}

if (!defined('ASAP_api')) {
    define('ASAP_api', 'https://api.facebook.com/' . ASAP_API_VERSION . '/');
}
if (!defined('ASAP_api_video')) {
    define('ASAP_api_video', 'https://api-video.facebook.com/' . ASAP_API_VERSION . '/');
}

if (!defined('ASAP_api_read')) {
    define('ASAP_api_read', 'https://api-read.facebook.com/' . ASAP_API_VERSION . '/');
}

if (!defined('ASAP_graph')) {
    define('ASAP_graph', 'https://graph.facebook.com/' . ASAP_API_VERSION . '/');
}

if (!defined('ASAP_graph_video')) {
    define('ASAP_graph_video', 'https://graph-video.facebook.com/' . ASAP_API_VERSION . '/');
}
if (!defined('ASAP_www')) {
    define('ASAP_www', 'https://www.facebook.com/' . ASAP_API_VERSION . '/');
}

include_once('inc/cores/extra-functions.php');
if (!class_exists('ASAP_Class')) {

    /**
     * Declaration of plugin main class
     * */
    class ASAP_Class {

        function __construct() {
            register_activation_hook(__FILE__, array($this, 'plugin_activation')); //On plugin activation tasks
            add_action('init', array($this, 'plugin_text_domain')); //loads text domain for translation ready
            add_action('admin_init', array($this, 'session_init')); //starts the session for wp-admin
            add_action('admin_menu', array($this, 'plugin_menu')); //adds plugin menu
            add_action('admin_enqueue_scripts', array($this, 'register_admin_assets')); //registers admin assests such as js and css
            add_action('admin_post_asap_form_action', array($this, 'asap_form_action')); //action to recieve the account settings
            add_action('admin_post_asap_fb_authorize_action', array($this, 'fb_authorize_action')); //action to authorize fb
            add_action('admin_post_asap_linkedin_authorize_action', array($this, 'linkedin_authorize_action')); //action to authorize linked in
            add_action('admin_post_asap_tumblr_authorize_action', array($this, 'tumblr_authorize_action')); //action to authorize tumblr
            add_action('admin_post_asap_callback_authorize', array($this, 'callback_authorize')); //callback after authorize
            add_action('admin_post_asap_linkedin_callback_authorize', array($this, 'linkedin_callback_authorize')); //linked in callback after authorize
            add_action('admin_post_asap_tumblr_callback_authorize', array($this, 'tumblr_callback_authorize')); //tumblr in callback after authorize
            add_action('admin_post_asap_delete_account', array($this, 'delete_account')); //delete account
            add_action('admin_post_asap_activate', array($this, 'activate_account')); //activate or deactivate account
            add_action('admin_init', array($this, 'auto_post_trigger')); // auto post trigger
            add_action('admin_post_asap_clear_log', array($this, 'clear_log')); // Log clear action
            add_action('add_meta_boxes', array($this, 'add_asap_meta_box')); //adds plugin's meta box
            add_action('save_post', array($this, 'save_asap_meta_value')); //saves meta value 
            add_action('admin_post_asap_delete_log', array($this, 'delete_log')); //delete log action
            add_action('future_to_publish', array($this, 'auto_post_schedule')); 
        }

        /**
         * Plugin Activation tasks
         */
        function plugin_activation() {
            include('inc/cores/activation.php');
        }

        /**
         * Loads text domain when init hook is fired
         */
        function plugin_text_domain() {
            load_plugin_textdomain(ASAP_TD, false, basename(dirname(__FILE__)) . '/languages/');
        }

        /**
         * Starts session when admin_init hook is fired
         */
        function session_init() {
            if (!session_id()) {
                session_start();
            }
        }

        /**
         * Adds Plugin Admin menu
         * */
        function plugin_menu() {
            add_menu_page(__('AccessPress Social Auto Post', ASAP_TD), __('AccessPress Social Auto Post', ASAP_TD), 'manage_options', 'asap', array($this, 'plugin_main_page'));
        }

        /**
         * Plugin Main Page
         * */
        function plugin_main_page() {
            include('inc/main-page.php');
        }

        /**
         * Registers Admin Assets
         * */
        function register_admin_assets() {
            if (isset($_GET['page']) && $_GET['page'] == 'asap') {
                wp_enqueue_style('apsp-fontawesome-css', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', APSP_VERSION);
                wp_enqueue_style('asap-css', ASAP_CSS_DIR . '/admin-style.css', array(), ASAP_VERSION);
                wp_enqueue_script('asap-js', ASAP_JS_DIR . '/admin-scripts.js', array('jquery'), ASAP_VERSION);
            }
        }

        /**
         * Returns all registered post types
         */
        function get_registered_post_types() {
            $post_types = get_post_types();
            unset($post_types['revision']);
            unset($post_types['attachment']);
            unset($post_types['nav_menu_item']);
            return $post_types;
        }

        /**
         * Saves the account to database
         */
        function asap_form_action() {
            if (!empty($_POST) && wp_verify_nonce($_POST['asap_form_nonce'], 'asap_form_action')) {
                include('inc/cores/save-account.php');
            } else {
                die('No script kiddies please!!');
            }
        }

        /**
         * Prints array in pre format
         */
        function print_array($array) {
            echo "<pre>";
            print_r($array);
            echo "</pre>";
        }

        /**
         * Action to authorize fb
         */
        function fb_authorize_action() {
            if (!empty($_POST) && wp_verify_nonce($_POST['asap_fb_authorize_nonce'], 'asap_fb_authorize_action')) {
                include('inc/cores/fb-authorization.php');
            } else {
                die('No script kiddies please');
            }
        }

        /**
         * Authorize callback
         */
        function callback_authorize() {
            if (isset($_COOKIE['asap_session_state']) && isset($_REQUEST['state']) && ($_COOKIE['asap_session_state'] === $_REQUEST['state'])) {
                include('inc/cores/fb-authorization-callback.php');
            } else {
                die('No script kiddies please!');
            }
        }

        /**
         * Delete Account
         * */
        function delete_account() {
            if (!empty($_GET) && wp_verify_nonce($_GET['_wpnonce'], 'asap_delete_nonce')) {
                $account_id = $_GET['account_id'];
                global $wpdb;
                $table_name = $wpdb->prefix . 'asap_accounts';
                $wpdb->delete($table_name, array('account_id' => $account_id), array('%d'));
                $_SESSION['asap_message'] = __('Account Deleted Successfully', ASAP_TD);
                wp_redirect(admin_url('admin.php?page=asap'));
            } else {
                die('No script kiddies please!');
            }
        }

        /**
         * Activate or Deactivate Account
         * */
        function activate_account() {
            if (!empty($_GET) && wp_verify_nonce($_GET['_wpnonce'], 'asap_activate_nonce')) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'asap_accounts';
                $account_id = $_GET['account_id'];
                $account_status = $_GET['activate'];
                $wpdb->update(
                        $table_name, array('account_status' => $account_status), array('account_id' => $account_id), array('%d'), array('%d')
                );
                $_SESSION['asap_message'] = __('Account Updated Successfully.');
                $redirect_url = admin_url('admin.php?page=asap');
                wp_redirect($redirect_url);
                exit;
            } else {
                die('No script kiddies please!');
            }
        }

        /**
         * Auto Post Trigger
         * */
        function auto_post_trigger() {
            $post_types = $this->get_registered_post_types();
            foreach ($post_types as $post_type) {
                $publish_action = 'publish_' . $post_type;
                add_action($publish_action, array($this, 'auto_post'), 10, 2);
            }
        }

        /**
         * Auto Post Action
         * */
        function auto_post($id, $post) {
            //echo $id;die();
            //$this->print_array($_POST);die();
            $auto_post = $_POST['asap_auto_post'];
            if ($auto_post == 'yes' || $auto_post == '') {
                include_once('api/facebook/facebook.php'); // facebook api library
                include_once('api/twitter/codebird.php');  // twitter api library
                include_once('api/tumblr/TumblrAPIClient.php');  // Tumblr api library
                include_once('api/linkedin/liOAuth.php');
                include('inc/cores/auto-post.php');
                $check = update_post_meta($post->ID, 'asap_auto_post', 'no');
                $_POST['asap_auto_post'] = 'no';
            }
        }
        
        function auto_post_schedule($post){
            $auto_post = get_post_meta($post->ID,'asap_auto_post',true);
            if ($auto_post == 'yes' || $auto_post == '') {
                include_once('api/facebook/facebook.php'); // facebook api library
                include_once('api/twitter/codebird.php');  // twitter api library
                include_once('api/tumblr/TumblrAPIClient.php');  // Tumblr api library
                include_once('api/linkedin/liOAuth.php');
                include('inc/cores/auto-post.php');
                $check = update_post_meta($post->ID, 'asap_auto_post', 'no');
                $_POST['asap_auto_post'] = 'no';
            }
        }

        /* make a URL small */

        function make_bitly_url($url, $login, $appkey, $format = 'xml', $version = '2.0.1') {
            //create the URL
            $bitly = 'http://api.bit.ly/shorten?version=' . $version . '&longUrl=' . urlencode($url) . '&login=' . $login . '&apiKey=' . $appkey . '&format=' . $format;

            //get the url
            //could also use cURL here
            $response = file_get_contents($bitly);
            //var_dump($response);
            //parse depending on desired format
            if (strtolower($format) == 'json') {
                $json = @json_decode($response, true);
                $result = $json['results'][$url]['shortUrl'];
            } else { //xml
                $xml = simplexml_load_string($response);
                $result = 'http://bit.ly/' . $xml->results->nodeKeyVal->hash;
            }
            $result = ($result == '') ? $url : $result;
            return $result;
        }

        /**
         * Clears Log from log table
         * */
        function clear_log() {
            if (!empty($_GET) && wp_verify_nonce($_GET['_wpnonce'], 'asap-clear-log-nonce')) {
                global $wpdb;
                $log_table_name = $wpdb->prefix . 'asap_logs';
                $wpdb->query("TRUNCATE TABLE $log_table_name");
                $_SESSION['asap_message'] = __('Logs cleared successfully.', ASAP_TD);
                wp_redirect(admin_url('admin.php?page=asap&tab=logs'));
                exit();
            } else {
                die('No script kiddies please!');
            }
        }

        /**
         * Plugin's meta box
         * */
        function add_asap_meta_box($post_type) {
            add_meta_box(
                    'asap_meta_box'
                    , __('AccessPress Social Auto Post', ASAP_TD)
                    , array($this, 'render_meta_box_content')
                    , $post_type
                    , 'side'
                    , 'high'
            );
        }

        /**
         * asap_meta_box html
         * 
         * */
        function render_meta_box_content($post) {
            // Add an nonce field so we can check for it later.
            wp_nonce_field('asap_meta_box_nonce_action', 'asap_meta_box_nonce_field');

            // Use get_post_meta to retrieve an existing value from the database.
            $auto_post = get_post_meta($post->ID, 'asap_auto_post', true);
            //var_dump($auto_post);
            $auto_post = ($auto_post == '' || $auto_post == 'yes') ? 'yes' : 'no';

            // Display the form, using the current value.
            ?>
            <label for="asap_auto_post"><?php _e('Enable Auto Post', ASAP_TD); ?></label>
            <p>
                <select name="asap_auto_post">
                    <option value="yes" <?php selected($auto_post, 'yes'); ?>><?php _e('Yes', ASAP_TD); ?></option>
                    <option value="no" <?php selected($auto_post, 'no'); ?>><?php _e('No', ASAP_TD); ?></option>
                </select>
            </p>
            <?php
        }

        /**
         * Saves meta value
         * */
        function save_asap_meta_value($post_id) {
            //$this->print_array($_POST);die('abc');
            /*
             * We need to verify this came from the our screen and with proper authorization,
             * because save_post can be triggered at other times.
             */

            // Check if our nonce is set.
            if (!isset($_POST['asap_auto_post']))
                return $post_id;

            $nonce = $_POST['asap_meta_box_nonce_field'];

            // Verify that the nonce is valid.
            if (!wp_verify_nonce($nonce, 'asap_meta_box_nonce_action'))
                return $post_id;

            // If this is an autosave, our form has not been submitted,
            //     so we don't want to do anything.
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
                return $post_id;

            // Check the user's permissions.
            if ('page' == $_POST['post_type']) {

                if (!current_user_can('edit_page', $post_id))
                    return $post_id;
            } else {

                if (!current_user_can('edit_post', $post_id))
                    return $post_id;
            }

            /* OK, its safe for us to save the data now. */

            // Sanitize the user input.
            $auto_post = sanitize_text_field($_POST['asap_auto_post']);

            // Update the meta field.
            update_post_meta($post_id, 'asap_auto_post', $auto_post);
        }

        function delete_log() {
            if (!empty($_GET) && wp_verify_nonce($_GET['_wpnonce'], 'asap_delete_nonce')) {
                $log_id = $_GET['log_id'];
                global $wpdb;
                $table_name = $wpdb->prefix . 'asap_logs';
                $wpdb->delete($table_name, array('log_id' => $log_id), array('%d'));
                $_SESSION['asap_message'] = __('Log Deleted Successfully', ASAP_TD);
                wp_redirect(admin_url('admin.php?page=asap'));
            } else {
                die('No script kiddies please!');
            }
        }

        /**
         * Returns Tumblr APP registration URL
         */
        function get_tumblr_reg_url() {
            include_once('api/tumblr/TumblrAPIClient.php');
            $tumblr_obj = new Tumblr_ASAP_API_Client;
            $params = array(
                'title' => get_bloginfo('name'),
                // Max 400 chars for Tumblr
                'description' => mb_substr(get_bloginfo('description'), 0, 400, get_bloginfo('charset')),
                'url' => home_url(),
                'admin_contact_email' => get_bloginfo('admin_email'),
                'default_callback_url' => plugins_url('/oauth-callback.php', __FILE__)
            );
            return $tumblr_obj->getAppRegistrationUrl($params);
        }

        /**
         * Action to authorize Linked In account
         */
        function linkedin_authorize_action() {
            if (!empty($_POST) && wp_verify_nonce($_POST['asap_linkedin_authorize_nonce'], 'asap_linkedin_authorize_action')) {
                include_once('api/linkedin/liOAuth.php');
                include('inc/cores/linkedin-authorization.php');
            } else {
                die('No script kiddies please!!');
            }
        }

        /**
         * Linked Callback action
         */
        function linkedin_callback_authorize() {
            if (isset($_COOKIE['asap_linkedin_session_state'])) {
                include_once('api/linkedin/liOAuth.php');
                include('inc/cores/linkedin-authorization-callback.php');
            } else {
                die('No script kiddies please!');
            }
        }

        /**
         * Tumblr Authorize Action
         */
        function tumblr_authorize_action() {
            if (!empty($_POST) && wp_verify_nonce($_POST['asap_tumblr_authorize_nonce'], 'asap_tumblr_authorize_action')) {
                include_once('api/tumblr/TumblrAPIClient.php');  // Tumblr api library
                include('inc/cores/tumblr-authorization.php');
            } else {
                die('No script kiddies please!!');
            }
        }
        /**
         * Tumblr Callback action
         */
        function tumblr_callback_authorize() {
            if (isset($_COOKIE['asap_tumblr_session_state'])) {
                include_once('api/tumblr/TumblrAPIClient.php');  // Tumblr api library
                include('inc/cores/tumblr-authorization-callback.php');
            } else {
                die('No script kiddies please!');
            }
        }

    }

    //ASAP_Class termination

    $ASAP_Obj = new ASAP_Class();
}
 
 