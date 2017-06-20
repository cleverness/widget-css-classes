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
 * Main Class
 * @since 1.0
 */
class WCSSC {

	/**
	 * Default capability to display the WCC form in widgets.
	 * @static
	 * @since  1.4.1
	 * @var    string
	 */
	private static $default_cap = 'edit_theme_options';

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
		$instance = wp_parse_args( $instance, array(
			'ids' => '',
			'classes' => '',
			'classes-defined' => array(),
		) );

		/**
		 * Change the capability to access the CSS ID field.
		 *
		 * @since  1.4.1
		 * @param  string
		 * @return string
		 */
		$access_id = current_user_can( apply_filters( 'widget_css_classes_id_input_capability', self::$default_cap ) );

		/**
		 * Change the capability to access the CSS Classes field.
		 *
		 * @since  1.4.1
		 * @param  string
		 * @return string
		 */
		$access_class = current_user_can( apply_filters( 'widget_css_classes_class_input_capability', self::$default_cap ) );

		/**
		 * Change the capability to access the predefined CSS Classes select field.
		 * NOTE: If the user cannot access the predefined classes the regular input field is disabled as well.
		 *
		 * @since  1.4.1
		 * @param  string
		 * @return string
		 */
		$access_predefined = current_user_can( apply_filters( 'widget_css_classes_class_select_capability', self::$default_cap, $access_class ) );
		if ( ! $access_predefined ) {
			$access_class = false;
		}

		$fields = '';

		if ( 1 === (int) WCSSC_Lib::get_settings( 'show_id' ) || WCSSC_Lib::get_settings( 'type' ) > 0 ) {
			//$fields .= "<div class='wcssc' style='border: 1px solid #ddd; padding: 5px; background: #fafafa; margin: 1em 0; line-height: 1.5;'>\n";
			$fields .= "<div class='wcssc' style='clear: both; margin: 1em 0;'>\n";

			// show id field
			if ( 1 === (int) WCSSC_Lib::get_settings( 'show_id' ) ) {
				if ( $access_id ) {
					$field = self::do_id_field( $widget, $instance );
				} else {
					$field = self::do_hidden( $widget->get_field_name( 'ids' ), $instance['ids'] );
				}
				$fields .= "\t" . $field . "\n";
			}

			// show classes text field only
			if ( 1 === (int) WCSSC_Lib::get_settings( 'type' ) ) {
				if ( $access_class ) {
					$field = self::do_class_field( $widget, $instance );
				} else {
					$field = self::do_hidden( $widget->get_field_name( 'classes' ), $instance['classes'] );
				}
				$fields .= "\t" . $field . "\n";
			}

			// show classes predefined only
			if ( 2 === (int) WCSSC_Lib::get_settings( 'type' ) ) {
				if ( $access_predefined ) {
					$field = self::do_predefined_field( $widget, $instance, null );
				} else {
					$field = self::do_hidden( $widget->get_field_name( 'classes' ), $instance['classes'] );
				}
				$fields .= "\t" . $field . "\n";
			}

			// show both
			if ( 3 === (int) WCSSC_Lib::get_settings( 'type' ) ) {
				$field = '';
				if ( $access_predefined ) {
					$field .= self::do_predefined_field( $widget, $instance, $access_class );
				} else {
					$field .= self::do_hidden( $widget->get_field_name( 'classes' ), $instance['classes'] );
				}
				$fields .= "\t" . $field . "\n";
			}

			$fields .= "</div>\n";

		} // End if().

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
	 * @since  1.4.1
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
		$label = apply_filters( 'widget_css_classes_id', esc_html__( 'CSS ID', 'widget-css-classes' ) );

		$field .= self::do_label( $label, $id );
		$field .= "<input type='text' name='{$name}' id={$id}' value='{$instance['ids']}' class='widefat' />";

		$field = '<p>' . $field . '</p>';
		return $field;
	}

	/**
	 * Get the HTML for the class input field.
	 * @static
	 * @since  1.4.1
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
		$label = apply_filters( 'widget_css_classes_class', esc_html__( 'CSS Classes', 'widget-css-classes' ) );
		$field .= self::do_label( $label, $id );

		$field .= "<input type='text' name='{$name}' id={$id}' value='{$instance['classes']}' class='widefat' />";

		$field = '<p>' . $field . '</p>';
		return $field;
	}

	/**
	 * Get the HTML for the class input field.
	 * @static
	 * @since  1.4.1
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
		$label = apply_filters( 'widget_css_classes_class', esc_html__( 'CSS Classes', 'widget-css-classes' ) );

		// Merge input classes with predefined classes.
		$predefined_classes = explode( ';', WCSSC_Lib::get_settings( 'defined_classes' ) );
		// Remove any empty values.
		$predefined_classes = array_filter( $predefined_classes );

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
		$field .= "\t<ul id='{$id}' style='{$style}'>\n";
		foreach ( $predefined_classes as $preset ) {
			$preset_checked = '';
			if ( in_array( $preset, $instance['classes-defined'], true ) ) {
				$preset_checked = ' checked="checked"';
			}
			$option_id = $id . '-' . esc_attr( $preset );
			$option = "<label for='{$option_id}'>";
			$option .= "<input id='{$option_id}' name='{$name}[]' type='checkbox' value='{$preset}' {$preset_checked} />";
			$option .= ' ' . $preset . '</label>';
			$field .= "\t<li>{$option}</li>\n";
		}
		$field .= "\t</ul>";
		return $field;
	}

	/**
	 * Get the HTML for a hidden field.
	 * @static
	 * @since  1.4.1
	 * @param  string $name
	 * @param  string $value
	 * @return string
	 */
	private static function do_hidden( $name, $value ) {
		return '<input type="hidden" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" />';
	}

	/**
	 * Get the HTML for a field label.
	 * @static
	 * @since  1.4.1
	 * @param  string $label
	 * @param  string $for
	 * @return string
	 */
	private static function do_label( $label, $for ) {
		return '<label for="' . esc_attr( $for ) . '">' . $label . ":</label>\n";
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
		$new_instance = wp_parse_args( $new_instance, array(
			'classes' => null,
			'classes-defined' => array(),
		) );

		$instance['classes'] = $new_instance['classes'];
		$instance['classes-defined'] = $new_instance['classes-defined'];
		if ( 1 === (int) WCSSC_Lib::get_settings( 'show_id' ) ) {
			$instance['ids'] = $new_instance['ids'];
		}
		if ( ! empty( $instance['classes-defined'] ) && is_array( $instance['classes-defined'] ) ) {
			// Merge predefined classes with input classes
			$text_classes = explode( ' ', $instance['classes'] );
			foreach ( $instance['classes-defined'] as $key => $value ) {
				if ( ! in_array( $value, $text_classes, true ) ) {
					$text_classes[] = $value;
				}
			}
			$instance['classes'] = implode( ' ', $text_classes );
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

		global $wp_registered_widgets, $widget_number;

		if ( ! isset( $params[0] ) ) {
			return $params;
		}

		$arr_registered_widgets = wp_get_sidebars_widgets(); // Get an array of ALL registered widgets
		$this_id                = $params[0]['id']; // Get the id for the current sidebar we're processing
		$widget_id              = $params[0]['widget_id'];
		$widget_obj             = $wp_registered_widgets[ $widget_id ];
		$widget_num             = $widget_obj['params'][0]['number'];
		$widget_opt             = self::get_widget_opt( $widget_obj );

		/**
		 * Make sure all keys are there and remove invalid keys.
		 * @see  WCSSC_Lib::set_settings()
		 */
		$settings = shortcode_atts( WCSSC_Lib::get_default_settings(), WCSSC_Lib::get_settings() );

		// If set, try to fix invalid sidebar parameters.
		if ( $settings['fix_widget_params'] ) {
			$params[0] = self::fix_widget_params( $params[0] );
		}

		// Add custom and predefined classes.
		if ( isset( $widget_opt[ $widget_num ]['classes'] ) && ! empty( $widget_opt[ $widget_num ]['classes'] ) ) {

			$classes = explode( ' ', (string) $widget_opt[ $widget_num ]['classes'] );
			$defined_classes = array_filter( explode( ';', $settings['defined_classes'] ) );

			// Order classes by predefined classes order and append the other (custom) classes.
			if ( ! empty( $defined_classes ) ) {
				// Order classes selection by predefined classes order and append the other (custom) classes.
				$classes = array_filter( array_unique( array_merge( array_intersect( $defined_classes, $classes ), $classes ) ) );
			}

			/**
			 * Modify the list of CSS classes.
			 * Can also be used for ordering etc.
			 *
			 * @since  1.4.1
			 * @param  array      $classes
			 * @param  string     $widget_id
			 * @param  int        $widget_number
			 * @param  array      $widget_opt
			 * @param  WP_Widget  $widget_obj
			 * @return array
			 */
			$classes = (array) apply_filters( 'widget_css_classes', $classes, $widget_id, $widget_number, $widget_opt, $widget_obj );

			if ( 1 === (int) $settings['type'] || 3 === (int) $settings['type'] ) {
				// Add all classes
				$classes = implode( ' ', $classes );
				$params[0]['before_widget'] = preg_replace( '/class="/', "class=\"{$classes} ", $params[0]['before_widget'], 1 );
			} elseif ( 2 === (int) $settings['type'] ) {
				// Only add predefined classes
				foreach ( $classes as $key => $value ) {
					if ( in_array( $value, $defined_classes, true ) ) {
						$value = esc_attr( $value );
						$params[0]['before_widget'] = preg_replace( '/class="/', "class=\"{$value} ", $params[0]['before_widget'], 1 );
					}
				}
			}
		} // End if().

		// Add id.
		if ( $settings['show_id'] ) {
			if ( isset( $widget_opt[ $widget_num ]['ids'] ) && ! empty( $widget_opt[ $widget_num ]['ids'] ) )
				$params[0]['before_widget'] = preg_replace( '/id="[^"]*/', "id=\"{$widget_opt[ $widget_num ]['ids']}", $params[0]['before_widget'], 1 );
		}
		// Remove empty ID attr.
		$params[0]['before_widget'] = str_replace( 'id="" ', '', $params[0]['before_widget'] );

		// Add first, last, even, and odd classes.
		if ( $settings['show_number'] || $settings['show_location'] || $settings['show_evenodd'] ) {
			if ( ! $widget_number ) {
				$widget_number = array();
			}

			if ( ! isset( $arr_registered_widgets[ $this_id ] ) || ! is_array( $arr_registered_widgets[ $this_id ] ) ) {
				return $params;
			}

			if ( isset( $widget_number[ $this_id ] ) ) {
				$widget_number[ $this_id ]++;
			} else {
				$widget_number[ $this_id ] = 1;
			}

			$class = 'class="';

			if ( $settings['show_number'] ) {
				$class .= apply_filters( 'widget_css_classes_number', esc_attr__( 'widget-', 'widget-css-classes' ) ) . $widget_number[ $this_id ] . ' ';
			}

			if ( $settings['show_location'] ) {
				$widget_first = apply_filters( 'widget_css_classes_first', esc_attr__( 'widget-first', 'widget-css-classes' ) );
				$widget_last = apply_filters( 'widget_css_classes_last', esc_attr__( 'widget-last', 'widget-css-classes' ) );
				if ( 1 === (int) $widget_number[ $this_id ] ) {
					$class .= $widget_first . ' ';
				}
				if ( count( $arr_registered_widgets[ $this_id ] ) === (int) $widget_number[ $this_id ] ) {
					$class .= $widget_last . ' ';
				}
			}

			if ( $settings['show_evenodd'] ) {
				$widget_even = apply_filters( 'widget_css_classes_even', esc_attr__( 'widget-even', 'widget-css-classes' ) );
				$widget_odd  = apply_filters( 'widget_css_classes_odd', esc_attr__( 'widget-odd', 'widget-css-classes' ) );
				$class .= ( ( $widget_number[ $this_id ] % 2 ) ? $widget_odd . ' ' : $widget_even . ' ' );
			}

			$params[0]['before_widget'] = str_replace( 'class="', $class, $params[0]['before_widget'] );

		} // End if().

		/**
		 * Modify the widget parameters, normally to add extra classes.
		 *
		 * @param  array      $params
		 * @param  string     $widget_id
		 * @param  int        $widget_number
		 * @param  array      $widget_opt
		 * @param  WP_Widget  $widget_obj
		 * @return array
		 */
		do_action( 'widget_css_classes_add_classes', $params, $widget_id, $widget_number, $widget_opt, $widget_obj );

		return $params;
	}

	/**
	 * Get the widget option value. Also handles third party plugin compatibility.
	 *
	 * // Disable complexity check because of third part plugin handling.
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 *
	 * @static
	 * @since  1.4.1
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
	 * @since  1.4.1
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
