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

	public static $settings_key = 'WCSSC_options';
	private static $settings = array();

	/**
	 * Add Settings link to plugin's entry on the Plugins page
	 * @static
	 * @param  array  $links
	 * @param  string $file
	 * @return array
	 * @since  1.0
	 */
	public static function add_settings_link( $links, $file ) {
		static $this_plugin;
		if ( ! $this_plugin ) {
			$this_plugin = WCSSC_BASENAME;
		}

		if ( $file === $this_plugin ) {
			$settings_link = '<a href="' . admin_url( 'options-general.php?page=widget-css-classes-settings' ) . '">' . esc_attr__( 'Settings', 'widget-css-classes' ) . '</a>';
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
		echo $plugin_data['Title'] . ' | ' . esc_attr__( 'Version', 'widget-css-classes' ) . ' ' . esc_html( $plugin_data['Version'] ) . ' | ' . $plugin_data['Author'] .
			' | <a href="http://codebrainmedia.com">CodeBrain Media</a> | <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=cindy@cleverness.org">' . esc_attr__( 'Donate', 'widget-css-classes' ) . '</a>
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
		if ( (string) WCSSC_DB_VERSION !== (string) $installed_version ) {
			// update options
			self::update( $installed_version );
			update_option( 'WCSSC_db_version', WCSSC_DB_VERSION );
		}

	}

	/**
	 * Install or Upgrade Options
	 * @static
	 * @param $version
	 * @since 1.0
	 */
	private static function update( $version ) {

		if ( empty( $version ) ) {

			// add default options
			self::update_settings( array() );
			add_option( 'WCSSC_db_version', WCSSC_DB_VERSION );

		} else {

			if ( version_compare( $version, '1.2', '<' ) ) {
				$options = get_option( self::$settings_key );
				$options['show_number']   = 1;
				$options['show_location'] = 1;
				$options['show_evenodd']  = 1;
				update_option( self::$settings_key, $options );
			}

			if ( version_compare( $version, '1.3', '<' ) ) {
				$options = get_option( self::$settings_key );
				// Hide option is now 0 instead of 3
				if ( isset( $options['type'] ) && 3 === (int) $options['type'] ) {
					$options['type'] = 0;
				}
				// dropdown settings are renamed to defined_classes
				if ( ! isset( $options['dropdown'] ) ) {
					$options['dropdown'] = '';
				}
				$options['defined_classes'] = $options['dropdown'];
				unset( $options['dropdown'] );
				update_option( self::$settings_key, $options );
			}

		} // End if().

		self::$settings = get_option( self::$settings_key );
	}

	/**
	 * Get plugin settings.
	 *
	 * @static
	 * @param  string|int  $key
	 * @return mixed
	 * @since  1.4.1
	 */
	public static function get_settings( $key = null ) {
		if ( null !== $key ) {
			if ( isset( self::$settings[ $key ] ) ) {
				return self::$settings[ $key ];
			}
			return null;
		}
		return self::$settings;
	}

	/**
	 * Set plugin settings. All setting changes should run through this function.
	 *
	 * @static
	 * @param  mixed       $settings
	 * @param  string|int  $key
	 * @return bool
	 * @since  1.4.1
	 */
	public static function set_settings( $settings, $key = null ) {

		if ( null !== $key ) {
			if ( ! is_int( $key ) && ! is_string( $key ) ) {
				return false;
			}
			self::$settings = (array) self::$settings;
			self::$settings[ $key ] = $settings;
			$settings = self::$settings;
		}
		elseif ( ! is_array( $settings ) ) {
			return false;
		}

		/**
		 * Modify the plugin settings.
		 * @since  1.4.1
		 * @param  array|false
		 * @return array
		 */
		$settings = apply_filters( 'widget_css_classes_set_settings', $settings );

		// Make sure all keys are there and remove invalid keys.
		$settings = shortcode_atts( array(
			'fix_widget_params' => 0,
			'show_id'           => 0,
			'type'              => 1,
			'defined_classes'   => '',
			'show_number'       => 1,
			'show_location'     => 1,
			'show_evenodd'      => 1,
		), $settings );

		self::$settings = $settings;
		return true;
	}

	/**
	 * Update plugin settings. Also sets the current settings.
	 * @static
	 * @param  mixed       $settings
	 * @param  string|int  $key
	 * @return bool
	 * @since  1.4.1
	 */
	public static function update_settings( $settings, $key = null ) {
		self::set_settings( $settings, $key );
		return update_option( self::$settings_key, self::get_settings() );
	}

}
