<?php
/**
 * Widget CSS Classes - Unit tests
 *
 * @author Jory Hogeveen <info@keraweb.nl>
 * @package widget-css-classes
 * @version 1.5.0
 */

class WCSSC_UnitTest extends WP_UnitTestCase {

	/**
	 * Check that activation doesn't break.
	 */
	function test_activated() {

		$this->assertTrue( is_plugin_active( TEST_WCSSC_PLUGIN_PATH ) );

		widget_css_classes_loader();
	}

	/**
	 * Test setting storage functionality.
	 */
	function test_settings() {

		// Test array, these values should all be parsed to the correct types and format.
		$settings = array(
			'non_existing_key'  => 'test',
			'fix_widget_params' => 0,
			'show_id'           => 1,
			'type'              => 3,
			'defined_classes'   => 'test;semicolon space,comma',
			'show_number'       => 1,
			'show_location'     => 0,
			'show_evenodd'      => 1,
		);

		// Compare array, this is what the resulting settings should be after parsing.
		$compare = array(
			'fix_widget_params' => false,
			'show_id'           => true,
			'type'              => 3,
			'defined_classes'   => array( 'test', 'semicolon', 'space', 'comma' ),
			'show_number'       => true,
			'show_location'     => false,
			'show_evenodd'      => true,
		);

		// Trigger update.
		WCSSC_Lib::update_settings( $settings );

		// Compare full array after validation.
		$this->assertEquals( $compare, WCSSC_Lib::get_settings() );

		// Test get_settings() with key parameter.
		$this->assertTrue( WCSSC_Lib::get_settings( 'show_id' ) );
		$this->assertEquals( 3, WCSSC_Lib::get_settings( 'type' ) );
		$this->assertNull( WCSSC_Lib::get_settings( 'non_existing_key' ) );

		// @todo assertNotTrue() not available in PHP 5.2 unit tests
		$this->wcssc_assertNotTrue( WCSSC_Lib::get_settings( 'type' ) ); // Should be parsed to an integer.
	}

	/**
	 * Test filter `widget_css_classes_set_settings`
	 */
	function test_filter_set_settings() {

		/**
		 * Gets triggered by WCSSC_Lib::update_settings()
		 * @see WCSSC_Lib::set_settings()
		 * @see WCSSC_UnitTest::filter_widget_css_classes_set_settings()
		 */
		add_filter( 'widget_css_classes_set_settings', array( $this, 'filter_widget_css_classes_set_settings' ) );

		// Trigger update.
		WCSSC_Lib::update_settings( WCSSC_Lib::get_settings() );

		// Test new settings changed by the filter.
		$this->assertFalse( WCSSC_Lib::get_settings( 'show_id' ) );
		$this->assertEquals( 1, WCSSC_Lib::get_settings( 'type' ) );

		// @todo assertNotTrue() not available in PHP 5.2 unit tests
		$this->wcssc_assertNotTrue( WCSSC_Lib::get_settings( 'type' ) ); // Should be parsed to an integer.
	}

///////////////////////////////////////////////
//           HELPER FUNCTIONS
///////////////////////////////////////////////

	/**
	 * Temp fix for assertNotTrue() not available in PHP 5.2 unit tests.
	 * @param  mixed  $condition
	 */
	function wcssc_assertNotTrue( $condition ) {
		if ( is_callable( array( $this, 'assertNotTrue' ) ) ) {
			$this->assertNotTrue( $condition );
			return;
		}
		// Fallback.
		$invalid = true;
		if ( true !== $condition ) {
			$invalid = false;
		}
		$this->assertFalse( $invalid );
	}

	/**
	 * Helper function for `widget_css_classes_set_settings` filter.
	 * @param  array $settings
	 * @return array
	 */
	function filter_widget_css_classes_set_settings( $settings ) {
		$settings['show_id'] = ''; // false.
		$settings['type'] = true; // 1
		return $settings;
	}
}
