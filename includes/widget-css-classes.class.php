<?php
/**
 * Widget CSS Classes Plugin Loader
 *
 * Loader
 * @author C.M. Kendrick <cindy@cleverness.org>
 * @package widget-css-classes
 * @version 1.5.0
 */

/**
 * Main Class
 * @since 1.0
 */
class WCSSC {

	/**
	 * Counter variable for the number of widgets per sidebar ID.
	 * @static
	 * @since  1.5.0
	 * @var    array
	 */
	public static $widget_counter = array();

	/**
	 * Default capabilities to display the WCC form in widgets.
	 * @static
	 * @since  1.5.0
	 * @var    array
	 */
	private static $caps = array(
		'ids'     => 'edit_theme_options',
		'classes' => 'edit_theme_options',
		'defined' => 'edit_theme_options',
	);

	public static function init() {
		static $done;
		if ( $done ) return;

		/**
		 * Change the capability to access the CSS ID field.
		 *
		 * @since  1.5.0
		 * @param  string
		 * @return string
		 */
		self::$caps['ids'] = apply_filters( 'widget_css_classes_id_input_capability', self::$caps['ids'] );

		/**
		 * Change the capability to access the CSS Classes field.
		 *
		 * @since  1.5.0
		 * @param  string
		 * @return string
		 */
		self::$caps['classes'] = apply_filters( 'widget_css_classes_class_input_capability', self::$caps['classes'] );

		/**
		 * Change the capability to access the predefined CSS Classes select field.
		 * NOTE: If the user cannot access the predefined classes the regular input field is disabled as well.
		 *
		 * @since  1.5.0
		 * @param  string
		 * @param  string
		 * @return string
		 */
		self::$caps['defined'] = apply_filters( 'widget_css_classes_class_select_capability', self::$caps['defined'], self::$caps['classes'] );

		$done = true;
	}

	/**
	 * Adds form fields to Widget
	 * @static
	 * @param  WP_Widget $widget
	 * @param  mixed     $return
	 * @param  array     $instance
	 * @return array
	 * @since  1.0
	 */
	public static function extend_widget_form( $widget, $return, $instance ) {
		self::init();
		$instance = wp_parse_args( $instance, array(
			'ids' => '',
			'classes' => '',
			'classes-defined' => array(),
		) );

		$access_id = current_user_can( self::$caps['ids'] );
		$access_class = current_user_can( self::$caps['classes'] );
		$access_predefined = current_user_can( self::$caps['defined'] );
		if ( ! $access_predefined ) {
			$access_class = false;
		}

		$fields = '';

			// show id field.
		if ( WCSSC_Lib::get_settings( 'show_id' ) ) {
			if ( $access_id ) {
				$fields .= self::do_id_field( $widget, $instance );
			} else {
				$fields .= self::do_hidden( $widget->get_field_name( 'ids' ), $instance['ids'] );
			}
		}

		switch ( WCSSC_Lib::get_settings( 'type' ) ) {
			case 1:
				// show classes text field only.
				if ( $access_class ) {
					$fields .= self::do_class_field( $widget, $instance );
				} else {
					$fields .= self::do_hidden( $widget->get_field_name( 'classes' ), $instance['classes'] );
				}
			break;
			case 2:
				// show classes predefined only.
				if ( $access_predefined ) {
					$fields .= self::do_predefined_field( $widget, $instance, null );
				} else {
					$fields .= self::do_hidden( $widget->get_field_name( 'classes' ), $instance['classes'] );
				}
			break;
			case 3:
				// show both.
				if ( $access_predefined ) {
					$fields .= self::do_predefined_field( $widget, $instance, $access_class );
				} else {
					$fields .= self::do_hidden( $widget->get_field_name( 'classes' ), $instance['classes'] );
				}
			break;
		}

		if ( $fields ) {
			//$fields .= "<div class='wcssc' style='border: 1px solid #ddd; padding: 5px; background: #fafafa; margin: 1em 0; line-height: 1.5;'>\n";
			$fields = '<div class="wcssc" style="clear: both; margin: 1em 0;">' . $fields . '</div>';
		}

		/**
		 * Add extra fields to the widget form.
		 *
		 * @param  string  $fields    Current HTML.
		 * @param  array   $instance  The widget instance.
		 * @return string
		 */
		do_action( 'widget_css_classes_form', $fields, $instance );

		echo $fields;
		return $return;
	}

	/**
	 * Get the HTML for the ID input field.
	 * @static
	 * @since  1.5.0
	 * @param  WP_Widget $widget
	 * @param  array     $instance
	 * @return string
	 */
	private static function do_id_field( $widget, $instance ) {
		$field = '';
		$id = $widget->get_field_id( 'ids' );
		$name = $widget->get_field_name( 'ids' );
		/**
		 * Change the label for the CSS ID form field.
		 *
		 * @param  string
		 * @return string
		 */
		$label = apply_filters( 'widget_css_classes_id', esc_html__( 'CSS ID', WCSSC_Lib::DOMAIN ) );

		$field .= self::do_label( $label, $id );
		$field .= "<input type='text' name='{$name}' id='{$id}' value='{$instance['ids']}' class='widefat' />";

		$field = '<p>' . $field . '</p>';
		return $field;
	}

	/**
	 * Get the HTML for the class input field.
	 * @static
	 * @since  1.5.0
	 * @param  WP_Widget $widget
	 * @param  array     $instance
	 * @return string
	 */
	private static function do_class_field( $widget, $instance ) {
		$field = '';
		$id = $widget->get_field_id( 'classes' );
		$name = $widget->get_field_name( 'classes' );

		/**
		 * Change the label for the CSS Classes form field.
		 *
		 * @param  string
		 * @return string
		 */
		$label = apply_filters( 'widget_css_classes_class', esc_html__( 'CSS Classes', WCSSC_Lib::DOMAIN ) );
		$field .= self::do_label( $label, $id );

		$field .= "<input type='text' name='{$name}' id='{$id}' value='{$instance['classes']}' class='widefat' />";

		$field = '<p>' . $field . '</p>';
		return $field;
	}

	/**
	 * Get the HTML for the class input field.
	 * @static
	 * @since  1.5.0
	 * @param  WP_Widget $widget
	 * @param  array     $instance
	 * @param  bool      $do_class_field Will echo a class input field if not null. Pass false for a hidden field.
	 * @return string
	 */
	private static function do_predefined_field( $widget, $instance, $do_class_field = null ) {

		$field = '';
		$id = $widget->get_field_id( 'classes-defined' );
		$name = $widget->get_field_name( 'classes-defined' );

		/**
		 * @see WCSSC::do_class_field()
		 */
		$label = apply_filters( 'widget_css_classes_class', esc_html__( 'CSS Classes', WCSSC_Lib::DOMAIN ) );

		// Merge input classes with predefined classes.
		$predefined_classes = WCSSC_Lib::get_settings( 'defined_classes' );

		// Do we have existing classes and is the user allowed to select defined classes?
		if ( ! empty( $instance['classes'] ) ) {
			$text_classes = explode( ' ', $instance['classes'] );
			foreach ( $text_classes as $key => $value ) {
				if ( in_array( $value, $predefined_classes, true ) ) {
					if ( ! in_array( $value, $instance['classes-defined'], true ) ) {
						$instance['classes-defined'][] = $value;
					}
					unset( $text_classes[ $key ] );
				}
			}
			$instance['classes'] = implode( ' ', $text_classes );
		}

		$style = array(
			'padding'    => 'padding: 5px;',
			'max-height' => 'max-height: 70px;',
			'overflow'   => 'overflow: hidden;',
			'overflow-y' => 'overflow-y: auto;',
			'border'     => 'border: 1px solid #ddd;',
			'box-shadow' => 'box-shadow: 0 1px 2px rgba(0, 0, 0, 0.07) inset;',
			'color'      => 'color: #32373c;',
		    'margin-top' => 'margin-top: 1px;',
		);

		if ( null !== $do_class_field ) {
			if ( $do_class_field ) {
				$field .= self::do_class_field( $widget, $instance );
				$style['margin-top'] = 'margin-top: -10px;';
			} else {
				$field .= self::do_hidden( $widget->get_field_name( 'classes' ), $instance['classes'] );
				$field .= self::do_label( $label, $id );
			}
		} else {
			$field .= self::do_label( $label, $id );
		}

		$style = implode( ' ', $style );
		$field .= "<ul id='{$id}' style='{$style}'>";
		foreach ( $predefined_classes as $preset ) {
			$preset_checked = '';
			if ( in_array( $preset, $instance['classes-defined'], true ) ) {
				$preset_checked = ' checked="checked"';
			}
			$option_id = $id . '-' . esc_attr( $preset );
			$option = "<label for='{$option_id}'>";
			$option .= "<input id='{$option_id}' name='{$name}[]' type='checkbox' value='{$preset}' {$preset_checked} />";
			$option .= ' ' . $preset . '</label>';
			$field .= "<li>{$option}</li>";
		}
		$field .= '</ul>';
		return $field;
	}

	/**
	 * Get the HTML for a hidden field.
	 * @static
	 * @since  1.5.0
	 * @param  string $name
	 * @param  string $value
	 * @return string
	 */
	public static function do_hidden( $name, $value ) {
		return '<input type="hidden" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" />';
	}

	/**
	 * Get the HTML for a field label. Label gets appended with a colon (:).
	 * @static
	 * @since  1.5.0
	 * @param  string $label
	 * @param  string $for
	 * @return string
	 */
	public static function do_label( $label, $for ) {
		return '<label for="' . esc_attr( $for ) . '">' . $label . ':</label>';
	}

	/**
	 * Get the HTML for a field description paragraph.
	 * @static
	 * @since  1.5.0
	 * @param  string $text
	 * @return string
	 */
	public static function do_description( $text ) {
		return '<p class="description">' . $text . '</p>';
	}

	/**
	 * Updates the Widget with the classes
	 * @static
	 * @param  $instance
	 * @param  $new_instance
	 * @return array
	 * @since  1.0
	 */
	public static function update_widget( $instance, $new_instance ) {
		self::init();
		$new_instance = wp_parse_args( $new_instance, array(
			'classes' => null,
			'classes-defined' => array(),
		) );

		if ( current_user_can( self::$caps['ids'] ) && WCSSC_Lib::get_settings( 'show_id' ) ) {
			$instance['ids'] = sanitize_text_field( $new_instance['ids'] );
		}

		$access_class = current_user_can( self::$caps['classes'] );
		$access_predefined = current_user_can( self::$caps['defined'] );
		if ( ! $access_predefined ) {
			$access_class = false;
		}

		if ( ( $access_class || $access_predefined ) && WCSSC_Lib::get_settings( 'type' ) ) {

			// Get the new predefined classes.
			$new_classes = (array) $new_instance['classes-defined'];

			// Merge predefined classes with input classes. Overwrite existing.
			if ( $access_class ) {
				$new_classes = array_merge( explode( ' ', (string) $new_instance['classes'] ), $new_classes );
			}
			// User can only set predefined classes, use the original and append the new classes with validation.
			else {
				// Get the available predefined classes.
				$defined_classes = WCSSC_Lib::get_settings( 'defined_classes' );
				// Remove values that don't exist as predefined.
				$new_classes = array_intersect( $new_classes, $defined_classes );
				// Get the classes existing in the original instance, removing the ones that are predefined.
				$cur_classes = array_diff( explode( ' ', (string) $instance['classes'] ), $defined_classes );
				// Merge with the new predefined selection.
				$new_classes = array_merge( $cur_classes, $new_classes );
			}

			// Remove empty and duplicate values and overwrite the original instance.
			$new_classes = array_filter( array_unique( $new_classes ) );
			$instance['classes'] = sanitize_text_field( implode( ' ', $new_classes ) );
		}

		// Do not store predefined array in widget, no need
		unset( $instance['classes-defined'] );

		do_action( 'widget_css_classes_update', $instance, $new_instance );

		return $instance;
	}

	/**
	 * Adds the classes to the widget in the front-end
	 *
	 * // Disable variable check because of global $wp_registered_widgets.
	 * @SuppressWarnings(PHPMD.LongVariables)
	 *
	 * @static
	 * @param  $params
	 * @return mixed
	 * @since  1.0
	 */
	public static function add_widget_classes( $params ) {

		global $wp_registered_widgets;

		if ( ! isset( $params[0] ) ) {
			return $params;
		}

		$arr_registered_widgets = wp_get_sidebars_widgets(); // Get an array of ALL registered widgets
		$this_id                = $params[0]['id']; // Get the id for the current sidebar we're processing
		$widget_id              = $params[0]['widget_id'];
		$widget_obj             = $wp_registered_widgets[ $widget_id ];
		$widget_num             = $widget_obj['params'][0]['number'];
		$widget_opt             = self::get_widget_opt( $widget_obj );

		// If set, try to fix invalid sidebar parameters.
		if ( WCSSC_Lib::get_settings( 'fix_widget_params' ) ) {
			$params[0] = self::fix_widget_params( $params[0] );
		}

		// Add id.
		if ( WCSSC_Lib::get_settings( 'show_id' ) && ! empty( $widget_opt[ $widget_num ]['ids'] ) ) {
			$params[0]['before_widget'] = preg_replace(
				'/id="[^"]*/',
				"id=\"{$widget_opt[ $widget_num ]['ids']}",
				$params[0]['before_widget'],
				1
			);
		}
		// Remove empty ID attr.
		$params[0]['before_widget'] = str_replace( 'id="" ', '', $params[0]['before_widget'] );

		// All classes array.
		$classes = array();

		// Add custom and predefined classes.
		if ( ! empty( $widget_opt[ $widget_num ]['classes'] ) ) {

			$custom_classes = explode( ' ', (string) $widget_opt[ $widget_num ]['classes'] );
			$defined_classes = WCSSC_Lib::get_settings( 'defined_classes' );

			// Order classes by predefined classes order and append the other (custom) classes.
			if ( ! empty( $defined_classes ) ) {
				// Order classes selection by predefined classes order and append the other (custom) classes.
				$custom_classes = array_filter( array_unique( array_merge( array_intersect( $defined_classes, $custom_classes ), $custom_classes ) ) );
			}

			/**
			 * Modify the list of custom CSS classes.
			 * Can also be used for ordering etc.
			 *
			 * @since  1.5.0
			 * @param  array      $custom_classes
			 * @param  string     $widget_id
			 * @param  int        $widget_num
			 * @param  array      $widget_opt
			 * @param  WP_Widget  $widget_obj
			 * @return array
			 */
			$custom_classes = (array) apply_filters( 'widget_css_classes_custom', $custom_classes, $widget_id, $widget_num, $widget_opt, $widget_obj );

			$type = WCSSC_Lib::get_settings( 'type' );

			if ( 1 === (int) $type || 3 === (int) $type ) {
				// Add all classes
				$classes = array_merge( $classes, $custom_classes );
			} elseif ( 2 === (int) $type ) {
				// Only add predefined classes
				foreach ( $custom_classes as $key => $value ) {
					if ( in_array( $value, $defined_classes, true ) ) {
						$classes[] = $value;
					}
				}
			}
		} // End if().

		// Add first, last, even, and odd classes.
		if ( WCSSC_Lib::get_settings( 'show_number' ) ||
		     WCSSC_Lib::get_settings( 'show_location' ) ||
		     WCSSC_Lib::get_settings( 'show_evenodd' )
		) {

			if ( ! self::$widget_counter ) {
				self::$widget_counter = array();
			}

			if ( isset( self::$widget_counter[ $this_id ] ) ) {
				self::$widget_counter[ $this_id ]++;
			} else {
				self::$widget_counter[ $this_id ] = 1;
			}

			if ( WCSSC_Lib::get_settings( 'show_number' ) ) {
				$class = apply_filters( 'widget_css_classes_number', esc_attr__( 'widget-', WCSSC_Lib::DOMAIN ) ) . self::$widget_counter[ $this_id ];
				array_unshift( $classes, $class );
			}

			if ( WCSSC_Lib::get_settings( 'show_location' ) &&
			     isset( $arr_registered_widgets[ $this_id ] ) &&
			     is_array( $arr_registered_widgets[ $this_id ] )
			) {
				$widget_first = apply_filters( 'widget_css_classes_first', esc_attr__( 'widget-first', WCSSC_Lib::DOMAIN ) );
				$widget_last = apply_filters( 'widget_css_classes_last', esc_attr__( 'widget-last', WCSSC_Lib::DOMAIN ) );
				if ( 1 === (int) self::$widget_counter[ $this_id ] ) {
					array_unshift( $classes, $widget_first );
				}
				if ( count( $arr_registered_widgets[ $this_id ] ) === (int) self::$widget_counter[ $this_id ] ) {
					array_unshift( $classes, $widget_last );
				}
			}

			if ( WCSSC_Lib::get_settings( 'show_evenodd' ) ) {
				$widget_even = apply_filters( 'widget_css_classes_even', esc_attr__( 'widget-even', WCSSC_Lib::DOMAIN ) );
				$widget_odd  = apply_filters( 'widget_css_classes_odd', esc_attr__( 'widget-odd', WCSSC_Lib::DOMAIN ) );
				$class = ( ( self::$widget_counter[ $this_id ] % 2 ) ? $widget_odd : $widget_even );
				array_unshift( $classes, $class );
			}

		} // End if().

		/**
		 * Modify the list of extra CSS classes.
		 * Can also be used for ordering etc.
		 *
		 * @since  1.5.0
		 * @param  array      $classes
		 * @param  string     $widget_id
		 * @param  int        $widget_num
		 * @param  array      $widget_opt
		 * @param  WP_Widget  $widget_obj
		 * @return array
		 */
		$classes = (array) apply_filters( 'widget_css_classes', $classes, $widget_id, $widget_num, $widget_opt, $widget_obj );

		// Only unique, non-empty values, separated by space, escaped for HTML attributes.
		$classes = esc_attr( implode( ' ', array_filter( array_unique( $classes ) ) ) );

		if ( ! empty( $classes ) ) {
			// Add the classes.
			$params[0]['before_widget'] = self::append_to_attribute(
				$params[0]['before_widget'],
				'class',
				$classes,
				(boolean) WCSSC_Lib::get_settings( 'filter_unique' )
			);
		}

		/**
		 * Modify the widget parameters, normally to add extra classes.
		 *
		 * @param  array      $params
		 * @param  string     $widget_id
		 * @param  int        $widget_num
		 * @param  array      $widget_opt
		 * @param  WP_Widget  $widget_obj
		 * @return array
		 */
		do_action( 'widget_css_classes_add_classes', $params, $widget_id, $widget_num, $widget_opt, $widget_obj );

		return $params;
	}

	/**
	 * Find an attribute and add the data as a HTML string.
	 *
	 * @see    WCC_Genesis_Widget_Column_Classes::append_to_attribute()
	 * @link   https://github.com/JoryHogeveen/genesis-widget-column-classes/blob/master/genesis-widget-column-classes.php
	 *
	 * @static
	 * @since  1.5.0
	 *
	 * @param  string  $str            The HTML string.
	 * @param  string  $attr           The attribute to find.
	 * @param  string  $content_extra  The content that needs to be appended.
	 * @param  bool    $unique         Do we need to filter for unique values?
	 *
	 * @return string
	 */
	public static function append_to_attribute( $str, $attr, $content_extra, $unique = false ) {

		// Check if attribute has single or double quotes.
		// @codingStandardsIgnoreLine
		if ( $start = stripos( $str, $attr . '="' ) ) {
			// Double.
			$quote = '"';

		// @codingStandardsIgnoreLine
		} elseif ( $start = stripos( $str, $attr . "='" ) ) {
			// Single.
			$quote = "'";

		} else {
			// Not found
			return $str;
		}

		// Add quote (for filtering purposes).
		$attr .= '=' . $quote;

		$content_extra = trim( $content_extra );

		if ( $unique ) {

			// Set start pointer to after the quote.
			$start += strlen( $attr );
			// Find first quote after the start pointer.
			$end = strpos( $str, $quote, $start );
			// Get the current content.
			$content = explode( ' ', substr( $str, $start, $end - $start ) );
			// Get our extra content.
			$content_extra = explode( ' ', $content_extra );
			foreach ( $content_extra as $class ) {
				if ( ! empty( $class ) && ! in_array( $class, $content, true ) ) {
					// This one can be added!
					$content[] = $class;
				}
			}
			// Remove duplicates and empty values.
			$content = array_filter( array_unique( $content ) );
			// Convert to space separated string.
			$content = implode( ' ', $content );
			// Get HTML before content.
			$before_content = substr( $str, 0, $start );
			// Get HTML after content.
			$after_content = substr( $str, $end );

			// Combine the string again.
			$str = $before_content . $content . $after_content;

		} else {
			$str = preg_replace(
				'/' . preg_quote( $attr, '/' ) . '/',
				$attr . $content_extra . ' ' ,
				$str,
				1
			);
		} // End if().

		// Return full HTML string.
		return $str;
	}

	/**
	 * Get the widget option value. Also handles third party plugin compatibility.
	 *
	 * // Disable complexity check because of third part plugin handling.
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 *
	 * @static
	 * @since  1.5.0
	 * @param  array  $widget_obj
	 * @return mixed
	 */
	private static function get_widget_opt( $widget_obj ) {
		$widget_opt = null;

		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
		// If Widget Logic plugin is enabled, use it's callback
		if ( in_array( 'widget-logic/widget_logic.php', $active_plugins, true ) ) {
			$widget_logic_options = get_option( 'widget_logic' );
			if ( isset( $widget_logic_options['widget_logic-options-filter'] ) && 'checked' === $widget_logic_options['widget_logic-options-filter'] ) {
				$widget_opt = get_option( $widget_obj['callback_wl_redirect'][0]->option_name );
			} else {
				$widget_opt = get_option( $widget_obj['callback'][0]->option_name );
			}
		}
		// If Widget Context plugin is enabled, use it's callback
		elseif ( in_array( 'widget-context/widget-context.php', $active_plugins, true ) ) {
			$callback = isset( $widget_obj['callback_original_wc'] ) ? $widget_obj['callback_original_wc'] : null;
			$callback = ! $callback && isset( $widget_obj['callback'] ) ? $widget_obj['callback'] : null;

			if ( $callback && is_array( $widget_obj['callback'] ) ) {
				$widget_opt = get_option( $callback[0]->option_name );
			}
		}
		// If Widget Output filter is enabled (f.e. by WP External Links plugin), don't use it's callback but the original callback
		elseif ( isset( $widget_obj['_wo_original_callback'] ) ) {
			$widget_opt = get_option( $widget_obj['_wo_original_callback'][0]->option_name );
		}

		// Default callback
		else {
			// Check if WP Page Widget is in use
			global $post;
			$id = ( isset( $post->ID ) ? get_the_ID() : null );
			if ( isset( $id ) && get_post_meta( $id, '_customize_sidebars' ) ) {
				$custom_sidebarcheck = get_post_meta( $id, '_customize_sidebars' );
			}
			if ( isset( $custom_sidebarcheck[0] ) && ( 'yes' === $custom_sidebarcheck[0] ) ) {
				$widget_opt = get_option( 'widget_' . $id . '_' . substr( $widget_obj['callback'][0]->option_name, 7 ) );
			} elseif ( isset( $widget_obj['callback'][0]->option_name ) ) {
				// Default
				$widget_opt = get_option( $widget_obj['callback'][0]->option_name );
			}
		}

		return $widget_opt;
	}

	/**
	 * Try to fix the widget parameters if they are invalid.
	 * @static
	 * @since  1.5.0
	 * @param  array $params
	 * @return array
	 */
	private static function fix_widget_params( $params ) {
		if ( empty( $params['before_widget'] ) || ! strpos( $params['before_widget'], 'class="' ) ) {

			if ( empty( $params['before_widget'] ) ) {
				$params['before_widget'] = '';
			}
			$params['before_widget'] = '<div id="" class="">' . $params['before_widget'];

			if ( empty( $params['after_widget'] ) ) {
				$params['after_widget'] = '';
			}
			$params['after_widget'] = $params['after_widget'] . '</div>';
		}
		return $params;
	}

}
