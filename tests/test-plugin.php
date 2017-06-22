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
			//'filter_unique'     => false, // This value should be appended.
		);

		// Compare array, this is what the resulting settings should be after parsing.
		$compare = array(
			'show_id'           => true,
			'type'              => 3,
			'defined_classes'   => array( 'test', 'semicolon', 'space', 'comma' ),
			'show_number'       => true,
			'show_location'     => false,
			'show_evenodd'      => true,
			'fix_widget_params' => false,
			'filter_unique'     => false,
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

	/**
	 * Check append_to_attribute() method
	 */
	function test_append_to_attribute() {

		$tests = array(
			array(
				'start'  => '<div class="test">',
				'data'   => 'one two three',
				'result' => '<div class="test one two three">',
			),
			array(
				'start'  => '<div class="test one two">',
				'data'   => 'one two three',
				'result' => '<div class="test one two three">',
			),
			array(
				'start'  => '<div class="test one one two">',
				'data'   => 'one two three',
				'result' => '<div class="test one two three">',
			),
			array(
				'start'  => '<div class="test one two">',
				'data'   => 'one one two three',
				'result' => '<div class="test one two three">',
			),
			// Single quotes.
			array(
				'start'  => "<div class='test'>",
				'data'   => 'one two three',
				'result' => "<div class='test one two three'>",
			),
			array(
				'start'  => "<div class='test one two'>",
				'data'   => 'one one two three',
				'result' => "<div class='test one two three'>",
			),
			// Multiple elements (only first attribute found should be modified).
			array(
				'start'  => '<div class="test one one two"><p class="test"></p>',
				'data'   => 'one two three',
				'result' => '<div class="test one two three"><p class="test"></p>',
			),
			// @todo Should this happen?
			array(
				'start'  => '<div><p class="test"></p>',
				'data'   => 'one two three',
				'result' => '<div><p class="test one two three"></p>',
			),
		);

		// Unique result tests.
		foreach ( $tests as $test ) {
			$this->assertEquals( $test['result'], WCSSC::append_to_attribute( $test['start'], 'class', $test['data'], true ) );
		}

		unset( $tests[0] );

		foreach ( $tests as $test ) {
			$this->assertNotEquals( $test['result'], WCSSC::append_to_attribute( $test['start'], 'class', $test['data'], false ) );
		}
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
