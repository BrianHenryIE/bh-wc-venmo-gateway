<?php
/**
 * The plugin page output of the plugin.
 * Adds a "Settings" link
 * Adds an "Orders" link when Filter WooCommerce Orders by Payment Method plugin is installed.
 *
 * @link
 * @since      1.1.0
 */

namespace BrianHenryIE\WC_Venmo_Gateway\Admin;

use BrianHenryIE\WC_Venmo_Gateway\WooCommerce\Venmo_Gateway;
use WC_Payment_Gateway;
use WC_Payment_Gateways;

/**
 *
 */
class Plugins_Page {

	/**
	 * Adds 'Settings' link to the configuration under WooCommerce's payment gateway settings page.
	 *
	 * @hooked plugin_action_links_{plugin basename}
	 * @see \WP_Plugins_List_Table::display_rows()
	 *
	 * @param string[] $links_array The links that will be shown below the plugin name on plugins.php (usually "Deactivate").
	 *
	 * @return string[]
	 */
	public function add_settings_action_link( array $links_array ): array {

		$payment_gateways = WC_Payment_Gateways::instance()->payment_gateways();
		$venmo_gateways   = array();
		foreach ( $payment_gateways as $gateway ) {
			if ( $gateway instanceof Venmo_Gateway ) {
				$venmo_gateways[] = $gateway;
			}
		}

		if ( 1 === count( $venmo_gateways ) ) {
			// If there is only one Venmo gateway instance, link directly to it.
			$section = '&section=' . $venmo_gateways[0]->id;
		} else {
			// If there is more than one, link to the WooCommerce / Settings / Payments page filtered to the class type.
			$section = '&class=bh-wc-venmo-gateway';
		}

		$setting_link   = admin_url( "admin.php?page=wc-settings&tab=checkout{$section}" );
		$plugin_links   = array();
		$plugin_links[] = '<a href="' . $setting_link . '">' . __( 'Settings', 'bh-wc-venmo-gateway' ) . '</a>';

		return array_merge( $plugin_links, $links_array );
	}

	/**
	 * Adds 'Orders' link if Filter WooCommerce Orders by Payment Method plugin is installed.
	 *
	 * @hooked plugin_action_links_{plugin basename}
	 * @see \WP_Plugins_List_Table::display_rows()
	 *
	 * @param string[] $links_array The links that will be shown below the plugin name on plugins.php (usually "Deactivate").
	 *
	 * @return string[]
	 */
	public function add_orders_action_link( array $links_array ): array {

		$plugin_links = array();

		/**
		 * Add an "Orders" link to a filtered list of orders if the Filter WooCommerce Orders by Payment Method plugin is installed.
		 *
		 * @see https://www.skyverge.com/blog/filtering-woocommerce-orders/
		 */
		if ( is_plugin_active( 'wc-filter-orders-by-payment/filter-wc-orders-by-gateway.php' ) && class_exists( WC_Payment_Gateway::class ) ) {

			$params = array(
				'post_type'                  => 'shop_order',
				'_shop_order_payment_method' => 'venmo',
			);

			$orders_link    = add_query_arg( $params, admin_url( 'edit.php' ) );
			$plugin_links[] = '<a href="' . $orders_link . '">' . __( 'Orders', 'bh-wc-venmo-gateway' ) . '</a>';
		}

		return array_merge( $plugin_links, $links_array );
	}

}
