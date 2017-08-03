<?php
/**
 * Widget CSS Classes plugin uninstall
 *
 * Uninstall
 * @author Jory Hogeveen <info@keraweb.nl>
 * @package widget-css-classes
 * @version 1.5.1
 * @todo Uninstall for multi-networks
 */

//if uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die();
}

wcssc_uninstall();

if ( is_multisite() ) {
	global $wp_version;
	if ( version_compare( $wp_version, '4.5.999', '<' ) ) {
		// @codingStandardsIgnoreLine - Sadly does not work for large networks -> return false
		$blogs = wp_get_sites();
	} else {
		$blogs = get_sites();
	}
	if ( ! empty( $blogs ) ) {
		foreach ( $blogs as $blog ) {
			$blog = (array) $blog;
			wcssc_uninstall( intval( $blog['blog_id'] ) );
		}
		wcssc_uninstall( 'site' );
	}
}

function wcssc_uninstall( $blog_id = false ) {

	// Delete all options
	$option_keys = array( 'WCSSC_options', 'WCSSC_db_version' );
	if ( $blog_id ) {
		if ( 'site' === $blog_id ) {
			foreach ( $option_keys as $option_key ) {
				delete_site_option( $option_key );
			}
		} else {
			foreach ( $option_keys as $option_key ) {
				delete_blog_option( $blog_id, $option_key );
			}
		}
	} else {
		foreach ( $option_keys as $option_key ) {
			delete_option( $option_key );
		}
	}

}
