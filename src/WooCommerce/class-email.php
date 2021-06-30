<?php

namespace BrianHenryIE\WC_Venmo_Gateway\WooCommerce;

use WC_Order;
use WC_Payment_Gateways;

class Email {

	// TODO: Don't send order received email unless the order has not been paid in 60 minutes.

	/**
	 * Adds instructions to the order confirmation emails.
	 *
	 * This runs for every order, on every email (received, complete, etc.).
	 *
	 * @hooked woocommerce_email_before_order_table
	 *
	 * @param WC_Order $order
	 * @param bool     $sent_to_admin
	 * @param bool     $plain_text
	 */
	public function email_instructions( WC_Order $order, bool $sent_to_admin, bool $plain_text = false ): void {

		$payment_gateways = WC_Payment_Gateways::instance()->payment_gateways();

		if ( ! isset( $payment_gateways[ $order->get_payment_method() ] ) ) {
			return;
		}

		$payment_gateway_instance = $payment_gateways[ $order->get_payment_method() ];

		if ( ! ( $payment_gateway_instance instanceof Venmo_Gateway ) ) {
			return;
		}

		$venmo_username = $payment_gateway_instance->get_option( 'venmo_username' );

		if ( empty( $venmo_username ) ) {
			return;
		}

		// Your order has been received.

		$instructions = "<p>Please send payment of {$order->get_formatted_order_total()} via Venmo to <a href=\"https://venmo.com/{$venmo_username}\">@{$venmo_username}</a></p>";

		$instructions .= '<p>* Do not enter any note in the payment – we don\'t read it!</p>';
		$instructions .= "<p>* Please pay the precise amount – {$order->get_formatted_order_total()} – so the payment can be automatically matched to the order.";

		$instructions .= "<p><a href=\"https://venmo.com/{$venmo_username}\">Open Venmo</a></p>";

		// TODO: QR code.

		// TODO: escape output.
		if ( $instructions && ! $sent_to_admin && $order->has_status( 'on-hold' ) ) {
			echo wptexturize( $instructions ) . PHP_EOL;
		}
	}

}
