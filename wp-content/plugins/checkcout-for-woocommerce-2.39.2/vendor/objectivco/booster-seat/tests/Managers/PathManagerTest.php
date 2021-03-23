<?php
/**
 * Class SampleTest
 *
 * @package Woocommerce_Bulk_Codes
 */

use Objectiv\BoosterSeat\Managers\PathManager;

/**
 * Sample test case.
 *
 * @since 1.0.0
 */
class PathManagerTest extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	function test_raw_file_equals_file() {

		$pm = new PathManager(plugin_dir_path(__FILE__), plugin_dir_url(__FILE__), __FILE__);

		// Replace this with some actual testing code.
		$this->assertTrue( $pm->get_raw_file() === __FILE__ );
	}
}
