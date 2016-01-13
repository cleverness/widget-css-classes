<?php
/**
* Widget CSS Classes Plugin Library
*
* Method library
* @author C.M. Kendrick <cindy@cleverness.org>
* @package widget-css-classes
* @version 1.3.0
*/

/**
* Library class
* @package widget-css-classes
* @subpackage includes
*/
class WCSSC_Lib {

	/**
	 * Add Settings link to plugin's entry on the Plugins page
	 * @static
	 * @param $links
	 * @param $file
	 * @return array
	 * @since 1.0
	 */
	public static function add_settings_link( $links, $file ) {
		static $this_plugin;
		if ( !$this_plugin ) $this_plugin = WCSSC_BASENAME;

		if ( $file == $this_plugin ) {
			$settings_link = '<a href="'.admin_url( 'options-general.php?page=widget-css-classes-settings' ).'">'.esc_attr__( 'Settings', 'widget-css-classes' ).'</a>';
			array_unshift( $links, $settings_link );
		}

		return $links;
	}

	/**
	 * Add plugin info to admin footer
	 * @static
	 * @since 1.0
	 */
	public static function admin_footer() {
		$plugin_data = get_plugin_data( WCSSC_FILE );
		echo $plugin_data['Title'].' | '.esc_attr__( 'Version', 'widget-css-classes' ).' '.esc_html( $plugin_data['Version'] ).' | '.$plugin_data['Author'].
			' | <a href="http://codebrainmedia.com">CodeBrain Media</a> | <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=cindy@cleverness.org">'.esc_attr__( 'Donate', 'widget-css-classes' ).'</a>
		<br />';
	}

	/**
	 * Run install function to see if upgrade is needed
	 * @static
	 * @since 1.0
	 */
	public static function install_plugin() {

		// get database version from options table
		if ( get_option( 'WCSSC_db_version' ) ) {
			$installed_version = get_option( 'WCSSC_db_version' );
		} else {
			$installed_version = 0;
		}

		// check if the db version is the same as the db version constant
		if ( $installed_version != WCSSC_DB_VERSION ) {
			// update options
			self::set_options( $installed_version );
			update_option( 'WCSSC_db_version', WCSSC_DB_VERSION );
		}

	}

	/**
	 * Install or Upgrade Options
	 * @static
	 * @param $version
	 * @since 1.0
	 */
	public static function set_options( $version ) {

		if ( $version == 0 ) {
			// add default options
			$options = array(
				'show_id'		=> 0,
				'type'			=> 1,
				'defined_classes'	=> '',
				'show_number'		=> 1,
				'show_location'		=> 1,
				'show_evenodd'		=> 1,
			);

			add_option( 'WCSSC_options', $options );
			add_option( 'WCSSC_db_version', WCSSC_DB_VERSION );

		} else {

			if ( $version < 1.2 ) {
				$general_options         = get_option( 'WCSSC_options' );
				$general_options['show_number'] = 1;
				$general_options['show_location'] = 1;
				$general_options['show_evenodd'] = 1;
				update_option( 'WCSSC_options', $general_options );
			}
			if ( $version < 1.3 ) {
				$general_options         = get_option( 'WCSSC_options' );
				// Hide option is now 0 instead of 3
				if ( isset( $general_options['type'] ) && $general_options['type'] == 3 ) {
					$general_options['type'] = 0;
				}
				// dropdown settings are renamed to defined_classes
				if ( ! isset( $general_options['dropdown'] ) ) {
					$general_options['dropdown'] = '';
				}
				$general_options['defined_classes'] = $general_options['dropdown'];
				unset( $general_options['dropdown'] );
				update_option( 'WCSSC_options', $general_options );
			}
		}
	}

}
