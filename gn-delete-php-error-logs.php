<?php
/**
 * GN Delete PHP Error Logs
 *
 * @package       GNDELETEPH
 * @author        George Nicolaou
 * @license       gplv2
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   GN Delete PHP Error Logs
 * Plugin URI:    https://www.georgenicolaou.me/plugins/gn-delete-php-error-logs
 * Description:   Automatically deletes PHP error logs once daily.
 * Version:       1.0.0
 * Author:        George Nicolaou
 * Author URI:    https://www.georgenicolaou.me/
 * Text Domain:   gn-delete-php-error-logs
 * Domain Path:   /languages
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with GN Delete PHP Error Logs. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Schedule the event on plugin activation
register_activation_hook( __FILE__, 'gn_webdev_cy_delete_php_error_logs_schedule_event' );

function gn_webdev_cy_delete_php_error_logs_schedule_event() {
    if ( ! wp_next_scheduled( 'gn_webdev_cy_delete_php_error_logs_event' ) ) {
        wp_schedule_event( time(), 'daily', 'gn_webdev_cy_delete_php_error_logs_event' );
    }
}

// Hook the event
add_action( 'gn_webdev_cy_delete_php_error_logs_event', 'gn_webdev_cy_delete_php_error_logs_callback' );

function gn_webdev_cy_delete_php_error_logs_callback() {
    // Directories to search for PHP error logs
    $directories = array(
        ABSPATH, // WordPress root directory
        WP_CONTENT_DIR, // WordPress content directory
        WP_PLUGIN_DIR, // WordPress plugins directory
    );

    // Check if WP_CLI is defined before attempting to use it
    if ( defined( 'WP_CLI' ) && WP_CLI ) {
        $directories[] = WP_CLI::get_runner()->get_project_config_path(); // WP-CLI project config directory
    }

    // Check if there are any PHP error logs
    foreach ( $directories as $directory ) {
        // Check if the directory exists
        if ( is_dir( $directory ) ) {
            // Search for php_errorlog file
            $php_errorlog_path = $directory . '/php_errorlog';
            if ( is_file( $php_errorlog_path ) ) {
                // Delete php_errorlog file
                unlink( $php_errorlog_path );
            }
        }
    }
}

// Add a menu for the plugin
add_action( 'admin_menu', 'gn_webdev_cy_delete_php_error_logs_menu' );

function gn_webdev_cy_delete_php_error_logs_menu() {
    add_menu_page(
        'Delete PHP Error Logs by GN_WEBDEV_CY',
        'Delete PHP Error Logs',
        'manage_options',
        'gn_webdev_cy_delete_php_error_logs_menu',
        'gn_webdev_cy_delete_php_error_logs_page',
        'dashicons-trash'
    );
}

// Callback function to display the plugin page
function gn_webdev_cy_delete_php_error_logs_page() {
    if ( isset( $_POST['gn_webdev_cy_delete_php_error_logs'] ) ) {
        // Trigger the deletion of PHP error logs
        gn_webdev_cy_delete_php_error_logs_callback();
        echo '<div class="updated"><p>PHP error logs deleted successfully.</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>Delete PHP Error Logs by GN_WEBDEV_CY</h1>
        <form method="post" action="">
            <p>This will delete PHP error logs from the specified directories.</p>
            <p><input type="submit" name="gn_webdev_cy_delete_php_error_logs" class="button button-primary" value="Delete PHP Error Logs"></p>
        </form>
    </div>
    <?php
}

// Clear scheduled event on plugin deactivation
register_deactivation_hook( __FILE__, 'gn_webdev_cy_delete_php_error_logs_clear_schedule' );

function gn_webdev_cy_delete_php_error_logs_clear_schedule() {
    wp_clear_scheduled_hook( 'gn_webdev_cy_delete_php_error_logs_event' );
}