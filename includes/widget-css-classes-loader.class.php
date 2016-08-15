<?php
/**
 * Widget CSS Classes Plugin Loader
 *
 * Loader
 * @author C.M. Kendrick <cindy@cleverness.org>
 * @package widget-css-classes
 * @version 1.3.0
 */

/**
 * Loader class
 * @package widget-css-classes
 * @subpackage includes
 */
class WCSSC_Loader {
	public static $settings;

	/**
	 * Plugin Loader init
	 * @static
	 * @since 1.0
	 */
	public static function init() {
		self::check_for_upgrade();
		self::$settings  = ( get_option( 'WCSSC_options' ) ? get_option( 'WCSSC_options' ) : array() );
		self::include_files();
		self::call_wp_hooks();
		new WCSSC_Settings();
	}

	/**
	 * Check to see if plugin has an upgrade
	 * @static
	 * @since 1.0
	 */
	private static function check_for_upgrade() {
		global $wp_version;

		$exit_msg = esc_html__( 'Widget CSS Classes requires WordPress 3.3 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update.</a>', 'widget-css-classes' );
		if ( version_compare( $wp_version, "3.3", "<" ) ) {
			exit( $exit_msg );
		}

		widget_css_classes_activation();
	}

	/**
	 * Calls the plugin files for inclusion
	 * @static
	 * @since 1.0
	 */
	private static function include_files() {
		include_once WCSSC_PLUGIN_DIR.'includes/widget-css-classes-library.class.php';
		include_once WCSSC_PLUGIN_DIR.'includes/widget-css-classes-settings.class.php';
		include_once WCSSC_PLUGIN_DIR.'includes/widget-css-classes.class.php';
	}

	/**
	 * Adds WordPress hooks for actions and filters
	 * @static
	 * @since 1.0
	 */
	private static function call_wp_hooks() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts_styles' ) );
		add_action( 'in_widget_form', array( 'WCSSC', 'extend_widget_form' ), 10, 3 );
		add_filter( 'widget_update_callback', array( 'WCSSC', 'update_widget' ), 10, 2 );
		add_filter( 'plugin_action_links', array( 'WCSSC_Lib', 'add_settings_link' ), 10, 2 );
	}

	/**
	 * Load the plugin CSS, JS and Help tab
	 * @static
	 * @since 1.0
	 */
	public static function enqueue_scripts_styles() {
		$screen = get_current_screen();

		// if on the settings page
		if ( $screen->id == 'settings_page_widget-css-classes-settings' ) {
			wp_enqueue_style( 'widget-css-classes_css', WCSSC_PLUGIN_URL.'/css/widget-css-classes.css' );

			wp_register_script( 'widget-css-classes_js', WCSSC_PLUGIN_URL.'/js/widget-css-classes.js', array( 'jquery' ), '1.0' );
			wp_register_script( 'relcopy_js', WCSSC_PLUGIN_URL.'/js/relCopy.min.js', array( 'jquery' ), '1.0' );
			wp_enqueue_script( 'widget-css-classes_js' );
			wp_enqueue_script( 'relcopy_js' );
		}

	}

}
