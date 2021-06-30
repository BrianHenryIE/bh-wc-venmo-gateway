<?php


namespace BrianHenryIE\WC_Venmo_Gateway\API;

interface Settings_Interface {

	public function get_plugin_slug(): string;

	public function get_plugin_basename(): string;

	public function get_plugin_version(): string;

	/**
	 * TODO: Returns true if the user has entered all the appropriate settings and checked enable.
	 *
	 * @return bool
	 */
	public function is_imap_reconcile_enabled(): bool;

	/**
	 * @param string $gateway_id
	 *
	 * @return array[]
	 */
	public function get_form_fields( string $gateway_id ): array;


	/**
	 * Returns the ids for all instances of Venmo Gateway registered with WooCommerce.
	 *
	 * @return string[]
	 */
	public function get_payment_method_ids(): array;

}

