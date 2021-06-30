<?php

namespace BrianHenryIE\WC_Venmo_Gateway\Includes;


use BrianHenryIE\WC_Venmo_Gateway\API\API_Interface;
use BrianHenryIE\WC_Venmo_Gateway\API\Settings_Interface;

use Psr\Log\NullLogger;

/**
 * @covers \BrianHenryIE\WC_Venmo_Gateway\Includes\Cron
 *
 * Class Cron_Unit_Test
 * @package BrianHenryIE\WC_Venmo_Gateway\Includes
 */
class Cron_Unit_Test extends \Codeception\Test\Unit {

	protected function _before() {
		\WP_Mock::setUp();
	}
	public function _after() {
		parent::_after();

		\WP_Mock::tearDown();
	}

    /**
     * Check when the cron's check_for_payment_emails function is called, i.e.
     * by Cron, that it calls API's check_for_payment_emails function.
     */
    public function test_check_for_payment_emails_calls_api() {

        $settings_mock = $this->makeEmpty( Settings_Interface::class,
            ['get_plugin_slug' => '', 'get_plugin_version' => 'a' ]
        );

        $api_mock = $this->makeEmpty( API_Interface::class,
            ['check_for_payment_emails' => \Codeception\Stub\Expected::once()]
        );

        $cron =  new Cron( $api_mock, $settings_mock, new NullLogger() );

        $cron->check_for_payment_emails();

    }
}
