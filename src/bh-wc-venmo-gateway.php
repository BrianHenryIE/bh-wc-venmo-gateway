<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://BrianHenryIE.com
 * @since             1.0.0
 * @package           BrianHenryIE\WC_Venmo_Gateway
 *
 * @wordpress-plugin
 * Plugin Name:       Venmo Gateway
 * Plugin URI:        http://github.com/BrianHenryIE/bh-wc-venmo-gateway/
 * Description:       Accepts payments via Venmo and reconciles WooCommerce orders through email receipts.
 * Version:           2.1.5
 * Author:            BrianHenryIE
 * Author URI:        http://BrianHenryIE.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bh-wc-venmo-gateway
 * Domain Path:       /languages
 */

namespace BrianHenryIE\WC_Venmo_Gateway;

use BrianHenryIE\WC_Venmo_Gateway\API\API;
use BrianHenryIE\WC_Venmo_Gateway\API\Settings;
use BrianHenryIE\WC_Venmo_Gateway\WC_IMAP_Reconcile\IMAP_Reconcile;
use BrianHenryIE\WC_Venmo_Gateway\WP_Logger\Logger;
use BrianHenryIE\WC_Venmo_Gateway\Includes\Activator;
use BrianHenryIE\WC_Venmo_Gateway\Includes\Deactivator;
use BrianHenryIE\WC_Venmo_Gateway\Includes\BH_WC_Venmo_Gateway;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'autoload.php';

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BH_WC_VENMO_GATEWAY_VERSION', '2.1.5' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-activator.php
 */
function activate_bh_wc_venmo_gateway(): void {

	Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-deactivator.php
 */
function deactivate_bh_wc_venmo_gateway(): void {

	Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'BrianHenryIE\WC_Venmo_Gateway\activate_bh_wc_venmo_gateway' );
register_deactivation_hook( __FILE__, 'BrianHenryIE\WC_Venmo_Gateway\deactivate_bh_wc_venmo_gateway' );


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function instantiate_bh_wc_venmo_gateway(): API {

	$settings = new Settings();
	$logger   = Logger::instance( $settings );

	$imap = new IMAP_Reconcile( $settings, $logger );
	$api  = new API( $imap, $settings, $logger );

	$plugin = new BH_WC_Venmo_Gateway( $api, $settings, $logger );

	return $api;
}

/** @var API $GLOBALS['bh_wc_venmo_gateway'] */
$GLOBALS['bh_wc_venmo_gateway'] = instantiate_bh_wc_venmo_gateway();
