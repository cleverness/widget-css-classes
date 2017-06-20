<?php
/**
 * Widget CSS Classes - Unit tests
 *
 * @author Jory Hogeveen <info@keraweb.nl>
 * @package widget-css-classes
 * @version 1.4.1
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

		WCSSC_Lib::update_settings( $settings );

		$compare = array(
			'fix_widget_params' => 0,
			'show_id'           => 1,
			'type'              => 3,
			'defined_classes'   => array( 'test', 'semicolon', 'space', 'comma' ),
			'show_number'       => 1,
			'show_location'     => 0,
			'show_evenodd'      => 1,
		);

		$this->assertEquals( $compare, WCSSC_Lib::get_settings() );
	}
}
