<?php
/**
 * Plugin Name: Shopper Approved Reviews
 * Plugin URI:  - https://wordpress.org/plugins/shopper-approved-reviews/
 * Description: Display Shopper approved reviews in your website, by using simple shortcode.
 * Version:     1.3
 * Author:      Chintesh Prajapati
 * Author URI:  https://profiles.wordpress.org/chinteshprajapati/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: shopperapproved
 * Domain Path: - shopperapproved
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Define constant variables
 * @since 1.0
 */
define( 'SPA_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
define( 'SPA_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

define( 'SPA','https://www.shopperapproved.com' );
define( 'SPA_API', 'https://www.shopperapproved.com/api' );

$options = get_option('spa_setting_options');

define( 'SPA_SITE_ID', $options['sap_site_id'] );
define( 'SPA_TOKEN', $options['sap_token'] );

/**
 * Include Class files
 * @since 1.0
 */

require_once SPA_PLUGIN_PATH . 'admin/SPA_Admin.php';
require_once SPA_PLUGIN_PATH . 'public/SPA_Public.php';

/**
 * Shopper Approved Activation Hook registration
 * @since 1.2
 */

function sap_activate() {
    require_once SPA_PLUGIN_PATH . 'includes/SPA_Activator.php';
    new SPA_Activator();
}

register_activation_hook( __FILE__, 'sap_activate'); //activation hook

/**
 * Shopper Approved Deactivation Hook registration
 * @since 1.2
 */

function sap_deactivate() {
    require_once SPA_PLUGIN_PATH . 'includes/SPA_Deactivator.php';
    new SPA_Deactivator();
}

register_deactivation_hook( __FILE__, 'sap_deactivate'); //deactivation hook

/**
 * Plugin activation redirection.
 *
 * @param string $plugin check plugin name.
 * @since 1.3
 */
function spa_activation_redirect( $plugin )
{
    
    if ( plugin_basename( __FILE__ ) === $plugin ) {
        wp_safe_redirect( add_query_arg( array(
            'page' => 'spa-page-content',
        ), esc_url( admin_url( 'admin.php' ) ) ) );
        exit;
    }

}

add_action( 'activated_plugin', 'spa_activation_redirect' );

new SPA_Admin(); // Admin Actions
new SPA_Public(); // Public Actions


