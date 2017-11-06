<?php
/**
* Widget CSS Classes Plugin Library
*
* Method library
* @author C.M. Kendrick <cindy@cleverness.org>
* @package widget-css-classes
* @version 1.5.2
*/

/**
* Library class
* @package widget-css-classes
* @subpackage includes
*/
class WCSSC_Lib {

	const DOMAIN = 'widget-css-classes';

	/**
	 * The plugin settings option key.
	 * @var   string
	 * @since 1.5.0
	 */
	public static $settings_key = 'WCSSC_options';

	/**
	 * The current plugin settings.
	 * @see   WCSSC_Lib::set_settings()
	 * @var   array
	 * @since 1.5.0
	 */
	private static $settings = array();

	/**
	 * The default plugin settings.
	 * @see   WCSSC_Lib::get_default_settings()
	 * @var   array
	 * @since 1.5.0
	 */
	private static $default_settings = null;

	/**
	 * Add Settings link to plugin's entry on the Plugins page.
	 *
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
			$settings_link = '<a href="' . admin_url( 'options-general.php?page=widget-css-classes-settings' ) . '">' . esc_attr__( 'Settings', self::DOMAIN ) . '</a>';
			array_unshift( $links, $settings_link );
		}

		return $links;
	}

	/**
	 * Add plugin info to admin footer.
	 *
	 * @static
	 * @since 1.0
	 */
	public static function admin_footer() {
		$plugin_data = get_plugin_data( WCSSC_FILE );
		echo $plugin_data['Title'] // @codingStandardsIgnoreLine >> no valid esc function.
			 . ' | ' . esc_attr__( 'Version', WCSSC_Lib::DOMAIN ) . ' ' . esc_html( $plugin_data['Version'] )
			 . ' | ' . $plugin_data['Author'] // @codingStandardsIgnoreLine >> no valid esc function.
			 . ' | <a href="http://codebrainmedia.com">CodeBrain Media</a>'
			 . ' | <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=cindy@cleverness.org">' . esc_attr__( 'Donate', self::DOMAIN ) . '</a>
		<br />';
	}

	/**
	 * Run install function to see if upgrade is needed.
	 *
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
	 * Install or Upgrade Options.
	 *
	 * @static
	 * @param $version
	 * @since 1.0
	 */
	private static function update( $version ) {

		$settings = get_option( self::$settings_key );

		if ( empty( $settings ) ) {
			// add default options
			self::update_settings( array() );
			add_option( 'WCSSC_db_version', WCSSC_DB_VERSION );
			return;
		}

		if ( version_compare( $version, '1.2', '<' ) ) {
			$settings['show_number']   = true;
			$settings['show_location'] = true;
			$settings['show_evenodd']  = true;
		}

		if ( version_compare( $version, '1.3', '<' ) ) {
			// Hide option is now 0 instead of 3
			if ( isset( $settings['type'] ) && 3 === (int) $settings['type'] ) {
				$settings['type'] = 0;
			}
			// dropdown settings are renamed to defined_classes
			if ( ! isset( $settings['dropdown'] ) ) {
				$settings['dropdown'] = array();
			}
			$settings['defined_classes'] = $settings['dropdown'];
			unset( $settings['dropdown'] );
		}

		if ( version_compare( $version, '1.5.2', '<' ) ) {
			// set on true because this was default in previous versions.
			if ( ! isset( $settings['translate_classes'] ) ) {
				$settings['translate_classes'] = true;
			}
		}

		self::update_settings( $settings );
	}

	/**
	 * Get plugin settings.
	 *
	 * @static
	 * @param  string|int  $key
	 * @return mixed
	 * @since  1.5.0
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
	 * @since  1.5.0
	 */
	public static function set_settings( $settings, $key = null ) {

		if ( null !== $key ) {
			// This plugin only has string type array keys.
			if ( ! is_string( $key ) ) {
				return false;
			}
			self::$settings = (array) self::$settings;
			self::$settings[ $key ] = $settings;
			$settings = self::$settings;
		}
		elseif ( ! is_array( $settings ) ) {
			return false;
		}

		// Pre-validate to make sure the user get's the correct formatted data according to the docs.
		$settings = self::validate_settings( $settings, false );

		/**
		 * Modify the plugin settings. Overwrites the DB values.
		 * IMPORTANT: Do not change the variable types of the values! Type casting will be overruled.
		 *
		 * @since  1.5.0
		 * @param  array $settings {
		 *     @type bool         $fix_widget_params
		 *     @type bool         $show_id
		 *     @type int          $type
		 *     @type array|string $defined_classes  Array of predefined classes.
		 *                                          Optionally allows a delimiter separated string.
		 *                                          See WCSSC_Lib::parse_defined_classes().
		 *     @type bool         $show_number
		 *     @type bool         $show_location
		 *     @type bool         $show_evenodd
		 * }
		 * @return array
		 */
		$settings = apply_filters( 'widget_css_classes_set_settings', $settings );

		// Full settings validation.
		$settings = self::validate_settings( $settings, true );

		self::$settings = $settings;
		return true;
	}

	/**
	 * Update plugin settings. Also sets the current settings.
	 *
	 * @static
	 * @param  mixed       $settings
	 * @param  string|int  $key
	 * @return bool
	 * @since  1.5.0
	 */
	public static function update_settings( $settings, $key = null ) {
		self::set_settings( $settings, $key );
		return update_option( self::$settings_key, self::get_settings() );
	}

	/**
	 * Validate plugin settings.
	 *
	 * @static
	 * @param  array  $settings
	 * @param  bool   $parse
	 * @return array
	 * @since  1.5.0
	 */
	private static function validate_settings( $settings, $parse = true ) {

		$defaults = self::get_default_settings();

		// Make sure all keys are there and remove invalid keys.
		$settings = shortcode_atts( $defaults, $settings );

		if ( $parse ) {
			// Parse all settings.
			foreach ( $settings as $key => $value ) {

				if ( 'defined_classes' === $key ) {
					// Parse defined_classes to array.
					$settings['defined_classes'] = self::parse_defined_classes( $value );
					continue;
				}

				if ( is_string( $value ) ) {
					$settings[ $key ] = strip_tags( stripslashes( $value ) );
				}

				// Validate var types.
				settype( $settings[ $key ], gettype( $defaults[ $key ] ) );
			}
		} else {
			// Only apply typecasting to defined classes.
			$settings['defined_classes'] = (array) $settings['defined_classes'];
		}

		return $settings;
	}

	/**
	 * Parse defined_classes to array.
	 *
	 * @static
	 * @param  array|string  $classes
	 * @return array
	 * @since  1.5.0
	 */
	private static function parse_defined_classes( $classes ) {
		$replace = array( ';', ' ', '|' );

		// Parse defined_classes to array.
		if ( ! is_array( $classes ) ) {
			// Convert to comma separated list.
			$classes = str_replace( $replace, ',', (string) $classes );
			// Convert to array and remove empty and duplicate values.
			return array_unique( array_filter( explode( ',', $classes ) ) );
		}

		$new_classes = array();
		// Parse each value the same way.
		foreach ( $classes as $key => $class ) {
			$class = self::parse_defined_classes( $class );
			$new_classes = array_merge( $new_classes, $class );
		}

		$new_classes = array_unique( array_filter( $new_classes ) );
		$new_classes = array_map( 'stripslashes', $new_classes );
		$new_classes = array_map( 'strip_tags', $new_classes );

		return $new_classes;
	}

	/**
	 * Get the default settings for this plugin.
	 *
	 * @static
	 * @return array
	 * @since  1.5.0
	 */
	public static function get_default_settings() {

		// Only run filters once.
		if ( self::$default_settings ) {
			return self::$default_settings;
		}

		self::$default_settings = array(
			'show_id'           => false,
			'type'              => 1,
			'defined_classes'   => array(),
			'show_number'       => true,
			'show_location'     => true,
			'show_evenodd'      => true,
			'fix_widget_params' => false,
			'filter_unique'     => false,
			'translate_classes' => false,
		);

		// Prevent passing by reference.
		$default_settings = self::$default_settings;

		/**
		 * Modify the plugin default settings. Doesn't change the DB values.
		 * IMPORTANT: Do not change the variable types of the values! Type casting will be overruled.
		 *
		 * @since  1.5.0
		 * @param  array  $default_settings  See `widget_css_classes_set_settings` filter for parameters.
		 * @return array
		 */
		$default_settings = apply_filters( 'widget_css_classes_default_settings', $default_settings );

		// Validate default settings against the original defaults.
		self::$default_settings = self::validate_settings( $default_settings, true );

		return self::$default_settings;
	}

}
