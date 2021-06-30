<?php
/**
 * Instructions shown on the Thank You page immediately after the order is created.
 */

namespace BrianHenryIE\WC_Venmo_Gateway\WooCommerce;

use WC_Order;
use WC_Payment_Gateways;

class Thank_You {

	/**
	 * Prints the HTML on the Thank You (post checkout) page.
	 *
	 * This hook is above the order details table.
	 *
	 * @hooked woocommerce_thankyou_order_received_text
	 *
	 * @param string $thank_you_text "Your order has been received".
	 * @param mixed  $order_id
	 * @return string
	 */
	public function print_instructions( $thank_you_text, $order_id ) {

		$order = wc_get_order( $order_id );

		if ( ! ( $order instanceof WC_Order ) ) {
			return $thank_you_text;
		}

		$payment_gateways = WC_Payment_Gateways::instance()->payment_gateways();

		if ( ! isset( $payment_gateways[ $order->get_payment_method() ] ) ) {
			return $thank_you_text;
		}

		$payment_gateway_instance = $payment_gateways[ $order->get_payment_method() ];

		if ( ! ( $payment_gateway_instance instanceof Venmo_Gateway ) ) {
			return $thank_you_text;
		}

		$venmo_username = $payment_gateway_instance->get_option( 'venmo_username' );

		// Your order has been received.

		$instructions = "<p>Please send payment of {$order->get_formatted_order_total()} via Venmo to <a href=\"https://venmo.com/{$venmo_username}\">@{$venmo_username}</a></p>";

		$instructions .= "<p>* Enter the order number – <b>{$order->get_id()}</b> – and nothing else in the order note.</p>";
		$instructions .= "<p>* Pay the precise amount – <b>{$order->get_formatted_order_total()}</b> – so the payment can be automatically matched to the order.";

		$instructions .= "<p><a href=\"https://venmo.com/{$venmo_username}\">Open Venmo</a></p>";

		// TODO: QR code.

		// Remove the last </p> because it is already contained in the HTML this string will be printed in.
		$instructions = preg_replace( '/<\/p>>$/', '', $instructions );

		return $thank_you_text . '</p>' . wptexturize( $instructions );

	}
}
