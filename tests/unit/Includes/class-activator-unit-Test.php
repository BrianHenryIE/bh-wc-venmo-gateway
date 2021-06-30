<?php
/**
 * Activation time should be recorded in an option when the plugin is deactivated.
 *
 * This is later used to display a "please configure" notice for a week.
 */

namespace BrianHenryIE\WC_Venmo_Gateway\Includes;

/**
 *
 * @covers \BrianHenryIE\WC_Venmo_Gateway\Includes\Activator
 *
 * Class Activator_Unit_Test
 * @package BrianHenryIE\WC_Venmo_Gateway\Includes
 */
class Activator_Unit_Test extends \Codeception\Test\Unit {

	protected function _before() {
		parent::_before();

		\WP_Mock::setUp();
	}

	public function _after() {
		parent::_after();

		\WP_Mock::tearDown();
	}

	/**
	 *
	 */
	public function test_update_option_is_called() {
		\WP_Mock::userFunction(
			'update_option',
			array(
				'args'   => array(
					'bh-wc-venmo-gateway-last-activated-time',
					\WP_Mock\Functions::type( 'int' )
				),
				'times' => 1,
			)
		);

		Activator::activate();

	}
}
