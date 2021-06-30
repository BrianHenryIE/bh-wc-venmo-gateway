<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * frontend-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    BrianHenryIE\WC_Venmo_Gateway
 * @subpackage BrianHenryIE\WC_Venmo_Gateway/includes
 */

namespace BrianHenryIE\WC_Venmo_Gateway\Includes;

use BrianHenryIE\WC_Venmo_Gateway\Admin\Plugins_Page;
use BrianHenryIE\WC_Venmo_Gateway\API\API_Interface;
use BrianHenryIE\WC_Venmo_Gateway\API\Settings_Interface;
use BrianHenryIE\WC_Venmo_Gateway\Admin\Admin;
use Psr\Log\LoggerInterface;
use BrianHenryIE\WC_Venmo_Gateway\WooCommerce\Email;
use BrianHenryIE\WC_Venmo_Gateway\WooCommerce\Order;
use BrianHenryIE\WC_Venmo_Gateway\WooCommerce\Payment_Gateways;
use BrianHenryIE\WC_Venmo_Gateway\WooCommerce\Thank_You;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * frontend-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    BrianHenryIE\WC_Venmo_Gateway
 * @subpackage BrianHenryIE\WC_Venmo_Gateway/includes
 * @author     Brian Henry <BrianHenryIE@gmail.com>
 */
class BH_WC_Venmo_Gateway {

	/**
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * @var Settings_Interface
	 */
	protected $settings;

	/**
	 * @var API_Interface
	 */
	protected $api;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the frontend-facing side of the site.
	 *
	 * @param API_Interface      $api
	 * @param Settings_Interface $settings
	 * @param LoggerInterface    $logger
	 * @since    1.0.0
	 */
	public function __construct( API_Interface $api, Settings_Interface $settings, LoggerInterface $logger ) {

		$this->logger   = $logger;
		$this->settings = $settings;
		$this->api      = $api;

		$this->set_locale();
		$this->define_admin_hooks();

		$this->define_woocommerce_hooks();
		$this->define_cron_hooks();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	protected function set_locale(): void {

		$plugin_i18n = new I18n();

		add_action( 'plugins_loaded', array( $plugin_i18n, 'load_plugin_textdomain' ) );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	protected function define_admin_hooks(): void {

		$admin = new Admin();
		add_action( 'plugins_loaded', array( $admin, 'init_notices' ) );
		add_action( 'admin_init', array( $admin, 'add_setup_notice' ) );

		$plugins_page    = new Plugins_Page();
		$plugin_basename = "{$this->settings->get_plugin_basename()}";
		add_filter( "plugin_action_links_{$plugin_basename}", array( $plugins_page, 'add_settings_action_link' ) );
		add_filter( "plugin_action_links_{$plugin_basename}", array( $plugins_page, 'add_orders_action_link' ) );
	}

	/**
	 * Register the payment gateway and customise the UI.
	 */
	protected function define_woocommerce_hooks(): void {

		$payment_gateways = new Payment_Gateways();
		// Register the payment gateway with WooCommerce.
		add_filter( 'woocommerce_payment_gateways', array( $payment_gateways, 'add_to_woocommerce' ) );
		// In admin UI, show the username associated with the gateway.
		add_filter( 'woocommerce_gateway_method_title', array( $payment_gateways, 'format_admin_gateway_name' ), 10, 2 );

		add_filter( 'woocommerce_order_get_payment_method_title', array( $payment_gateways, 'format_method_title' ), 10, 2 );

		add_filter( 'woocommerce_payment_gateways', array( $payment_gateways, 'filter_to_only_venmo_gateways' ), 100 );

		$admin_order_page = new Order( $this->settings, $this->logger );
		// On admin order screen, show the Venmo username in place of the billing address.
		add_filter( 'woocommerce_order_get_formatted_billing_address', array( $admin_order_page, 'admin_view_billing_address' ), 10, 3 );
		add_action( 'woocommerce_order_status_changed', array( $admin_order_page, 'schedule_email_check' ), 10, 3 );

		$thank_you = new Thank_You();
		// Display payment instructions on thank you page.
		add_filter( 'woocommerce_thankyou_order_received_text', array( $thank_you, 'print_instructions' ), 10, 2 );

		$email = new Email();
		// Add payment link and instructions to the customer emails.
		add_action( 'woocommerce_email_before_order_table', array( $email, 'email_instructions' ), 10, 2 );

	}

	/**
	 * Register the cron job.
	 */
	protected function define_cron_hooks(): void {

		$cron = new Cron( $this->api, $this->settings, $this->logger );
		// Make sure the cron job is enabled/disabled as appropriate.
		add_action( 'plugins_loaded', array( $cron, 'add_cron_jon' ) );

		// Hook the function that the cron job will run.
		add_action( Cron::CHECK_FOR_PAYMENT_EMAILS_CRON_HOOK, array( $cron, 'check_for_payment_emails' ) );

	}
}
