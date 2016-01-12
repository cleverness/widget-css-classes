<?php
/*
Plugin Name: Widget CSS Classes
Version: 1.3.0
Description: Add custom, first, last, even, odd, and numbered classes to your widgets.
Author: C.M. Kendrick
Author URI: http://cleverness.org
Plugin URI: http://cleverness.org/plugins/widget-css-classes
Text Domain: widget-css-classes
*/

add_action( 'init', 'widget_css_classes_loader' );
add_action( 'wp_loaded', 'widget_css_classes_frontend_hook' );

/**
 * Define constants and load the plugin
 * @since 1.0
 */
function widget_css_classes_loader() {

	$languages_path = plugin_basename( dirname( __FILE__ ).'/languages' );
	load_plugin_textdomain( 'widget-css-classes', FALSE, $languages_path );

	if ( is_admin() ) {

		if ( !defined( 'WCSSC_PLUGIN_VERSION' ) ) define( 'WCSSC_PLUGIN_VERSION', '1.2.8' );
		if ( !defined( 'WCSSC_FILE' ) ) define( 'WCSSC_FILE', __FILE__ );
		if ( !defined( 'WCSSC_BASENAME' ) ) define( 'WCSSC_BASENAME', plugin_basename( __FILE__ ) );
		if ( !defined( 'WCSSC_PLUGIN_DIR' ) ) define( 'WCSSC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		if ( !defined( 'WCSSC_PLUGIN_URL' ) ) define( 'WCSSC_PLUGIN_URL', plugins_url( '', __FILE__ ) );

		include_once 'includes/widget-css-classes-loader.class.php';
		WCSSC_Loader::init();

	}
}

/**
 * Call the following hook at wp_loaded
 * It must be added after register_sidebars is called
 */
function widget_css_classes_frontend_hook() {
	if ( !is_admin() ) {
		include_once 'includes/widget-css-classes.class.php';
		add_filter( 'dynamic_sidebar_params', array( 'WCSSC', 'add_widget_classes' ) );
	}
}

/**
 * Install plugin
 */
function widget_css_classes_activation() {
	global $wp_version;

	$exit_msg = esc_html__( 'Widget CSS Classes requires WordPress 3.3 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update.</a>', 'widget-css-classes' );
	if ( version_compare( $wp_version, "3.3", "<" ) ) {
		exit( $exit_msg );
	}

	if ( !defined( 'WCSSC_DB_VERSION' ) ) define( 'WCSSC_DB_VERSION', '1.3' );
	if ( !defined( 'WCSSC_FILE' ) ) define( 'WCSSC_FILE', __FILE__ );
	include_once 'includes/widget-css-classes-library.class.php';

	if ( get_option( 'WCSSC_db_version' ) ) {
		$installed_ver = get_option( 'WCSSC_db_version' );
	} else {
		$installed_ver = 0;
	}

	// if the installed version is not the same as the current version, run the install function
	if ( WCSSC_DB_VERSION != $installed_ver ) {
		WCSSC_Lib::install_plugin();
	}
}

register_activation_hook( __FILE__, 'widget_css_classes_activation' );
