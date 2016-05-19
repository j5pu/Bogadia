<?php
/**
 * @wordpress-plugin
 * Plugin Name:       WP Foto Vote
 * Plugin URI:        http://wp-vote.net/
 * Description:       Simple photo contest plugin with ability to user upload photos. Includes protection from cheating by IP and cookies. User log voting. After the vote invite to share post about contest in Google+, Twitter, Facebook, OK, VKontakte.
 * Version:           2.2.123
 * Author:            Maxim Kaminsky
 * Author URI:        http://www.maxim-kaminsky.com/
 * Text Domain:       fv
 * Domain Path:       /languages
 * Plugin support EMAIL: wp-vote@hotmail.com

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

 */



// If this file is called directly, abort.
if (!class_exists('WP')) {
        die();
}

if ( $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) {
        die( 'Access denied.' );
}

define('ST_PUBLISHED', '0');
define('ST_MODERAION', '1');
define('ST_DRAFT', '2');
define('FV_RES_OP_PAGE', '15');
define('FV_CONTEST_BLOCK_WIDTH', '200');
define('UPDATE_SERVER_URL', 'http://wp-vote.net/updater/');

define('FV_DB_VERSION', '1.5.162');
define('FV_UPDATE_KEY', 'ufOluHsNayLc3E');
define('FV_UPDATE_KEY_EXPIRATION', '2016-04-01');

define("FV_LOG_FILE", dirname(__FILE__) . '/logs/log.txt');
define("FV_ROOT", dirname(__FILE__));

if (!SHORTINIT) {

    /**
     * The code that runs during plugin activation.
     */
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-fv-activator.php';

    /**
     * The code that runs during plugin deactivation.
     */
    //require_once plugin_dir_path( __FILE__ ) . 'includes/class-fv-deactivator.php';

    /** This action is documented in includes/class-wsds-activator.php */
    register_activation_hook( __FILE__, array( 'FV_Activator', 'activate' ) );

    /** This action is documented in includes/class-wsds-deactivator.php */
    //register_deactivation_hook( __FILE__, array( 'FV_Deactivator', 'deactivate' ) );

}

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-fv.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.2.073
 */

function run_FV() {

	$plugin = new FV( plugin_basename( __FILE__ ), FV_ROOT );
	$plugin->run();

    // (!defined('DOING_AJAX') || DOING_AJAX == FALSE)
    if ( !SHORTINIT && is_admin() ) {
        $FvUpdateChecker = PucFactory::buildUpdateChecker(
            UPDATE_SERVER_URL . '?action=get_metadata&slug=' . FV::SLUG, __FILE__, FV::SLUG
        );

        //Add the license key to query arguments.
        $FvUpdateChecker->addQueryArgFilter('fv_filter_update_checks');
    }

}
run_FV();