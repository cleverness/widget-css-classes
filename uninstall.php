<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

if ( current_user_can( 'delete_plugins' ) ) {

	// delete options
	delete_option( 'WCSSC_options' );
	delete_option( 'WCSSC_db_version' );

}