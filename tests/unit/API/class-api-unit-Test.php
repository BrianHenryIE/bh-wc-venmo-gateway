<?php

namespace BrianHenryIE\WC_Venmo_Gateway\API;

use BrianHenryIE\WC_Venmo_Gateway\WC_IMAP_Reconcile\IMAP_Reconcile;
use Psr\Log\NullLogger;
use Codeception\Stub\Expected;

/**
 * @covers \BrianHenryIE\WC_Venmo_Gateway\API\API
 *
 * Class API_Unit_Test
 * @package BrianHenryIE\WC_Venmo_Gateway\API
 */
class API_Unit_Test extends \Codeception\Test\Unit {

	protected function _before() {
		\WP_Mock::setUp();
	}
	public function _after() {
		parent::_after();

		\WP_Mock::tearDown();
	}

	public function test_happy_simple_api() {

		$imap = $this->make( IMAP_Reconcile::class,
		array(
			'check_for_payment_emails' => Expected::once()
		));
		$settings = $this->makeEmpty( Settings_Interface::class );
		$logger = new NullLogger();

		$sut = new API( $imap, $settings, $logger );

		$sut->check_for_payment_emails();

	}
}
