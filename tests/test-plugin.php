<?php
/**
 * Widget CSS Classes - Unit tests
 */

class WCSSC_UnitTest extends WP_UnitTestCase {

	/**
	 * Check that activation doesn't break.
	 */
	function test_activated() {

		$this->assertTrue( is_plugin_active( TEST_WCSSC_PLUGIN_PATH ) );
	}
}
