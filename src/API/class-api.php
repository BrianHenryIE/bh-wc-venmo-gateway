<?php

namespace BrianHenryIE\WC_Venmo_Gateway\API;

use BrianHenryIE\WC_Venmo_Gateway\WC_IMAP_Reconcile\IMAP_Reconcile;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class API implements API_Interface {

	use LoggerAwareTrait;

	/**
	 * The plugin settings.
	 *
	 * @var Settings_Interface
	 */
	protected Settings_Interface $settings;

	/**
	 * Instance of IMAP_Reconcile library to fetch emails and match with unpaid orders.
	 *
	 * @var IMAP_Reconcile
	 */
	protected IMAP_Reconcile $imap;

	/**
	 * @param IMAP_Reconcile     $imap
	 * @param Settings_Interface $settings
	 * @param LoggerInterface    $logger
	 */
	public function __construct( IMAP_Reconcile $imap, Settings_Interface $settings, LoggerInterface $logger ) {
		$this->logger   = $logger;
		$this->settings = $settings;
		$this->imap     = $imap;
	}

	/**
	 * Fetches emails from email servers for Venmo Gateways, parses them for payment data, reconciles with
	 * unpaid orders.
	 *
	 * @param ?int $since Unix time to check for emails since.
	 */
	public function check_for_payment_emails( $since = null ): void {

		$this->imap->check_for_payment_emails( $since );
	}
}
