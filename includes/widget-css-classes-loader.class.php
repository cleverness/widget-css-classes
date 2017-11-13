<?php
/**
 * Widget CSS Classes Plugin Loader.
 *
 * Loader
 * @author C.M. Kendrick <cindy@cleverness.org>
 * @package widget-css-classes
 * @version 1.5.2.1
 */

/**
 * Loader class.
 * @package widget-css-classes
 * @subpackage includes
 */
class WCSSC_Loader {

	/**
	 * Plugin Loader init.
	 * @static
	 * @since 1.0
	 */
	public static function init() {
		self::check_for_upgrade();
		self::include_files();
		self::add_wp_hooks();

		// Instantiate settings (admin) class.
		new WCSSC_Settings();
	}

	/**
	 * Check to see if plugin has an upgrade.
	 * @static
	 * @since 1.0
	 */
	private static function check_for_upgrade() {
		widget_css_classes_activation();
	}

	/**
	 * Calls the plugin files for inclusion.
	 * @static
	 * @since 1.0
	 */
	private static function include_files() {
		include_once WCSSC_PLUGIN_DIR . 'includes/widget-css-classes-settings.class.php';
		include_once WCSSC_PLUGIN_DIR . 'includes/widget-css-classes.class.php';
	}

	/**
	 * Adds WordPress hooks for actions and filters.
	 * @static
	 * @since 1.0
	 */
	private static function add_wp_hooks() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts_styles' ) );
		add_action( 'in_widget_form', array( 'WCSSC', 'extend_widget_form' ), 10, 3 );
		add_filter( 'widget_update_callback', array( 'WCSSC', 'update_widget' ), 10, 2 );
		add_filter( 'plugin_action_links', array( 'WCSSC_Lib', 'add_settings_link' ), 10, 2 );
	}

	/**
	 * Load the plugin CSS, JS and Help tab.
	 * @static
	 * @since 1.0
	 */
	public static function enqueue_scripts_styles() {
		$screen = get_current_screen();

		// if on the settings page.
		if ( 'settings_page_widget-css-classes-settings' === $screen->id ) {
			wp_enqueue_style( 'widget-css-classes_css', WCSSC_PLUGIN_URL . '/css/widget-css-classes.css', array(), WCSSC_PLUGIN_VERSION );
			wp_enqueue_script( 'widget-css-classes_js', WCSSC_PLUGIN_URL . '/js/widget-css-classes.js', array( 'jquery' ), WCSSC_PLUGIN_VERSION );
		}
	}

}
