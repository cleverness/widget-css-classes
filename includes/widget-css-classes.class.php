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
	 * Adds form fields to Widget
	 * @static
	 * @param $widget
	 * @param $return
	 * @param $instance
	 * @return array
	 * @since 1.0
	 */
	public static function extend_widget_form( $widget, $return, $instance ) {
		if ( !isset( $instance['classes'] ) ) $instance['classes'] = null;
		if ( !isset( $instance['classes-defined'] ) ) $instance['classes-defined'] = array();

		$fields = '';

		if ( WCSSC_Loader::$settings['show_id'] == 1 || WCSSC_Loader::$settings['type'] > 0 ) {
			//$fields .= "<div class='wcssc' style='border: 1px solid #ddd; padding: 5px; background: #fafafa; margin: 1em 0; line-height: 1.5;'>\n";
			//$fields .= "<div class='wcssc'>\n";

			// show id field
			if ( WCSSC_Loader::$settings['show_id'] == 1 ) {
				if ( !isset( $instance['ids'] ) ) $instance['ids'] = null;
				$fields .= "\t<p><label for='widget-{$widget->id_base}-{$widget->number}-ids'>".apply_filters( 'widget_css_classes_id', esc_html__( 'CSS ID', 'widget-css-classes' ) ).":</label>
				<input type='text' name='widget-{$widget->id_base}[{$widget->number}][ids]' id='widget-{$widget->id_base}-{$widget->number}-ids' value='{$instance['ids']}' class='widefat' /></p>\n";
			}

			// show text field only
			if ( WCSSC_Loader::$settings['type'] == 1 ) {
				$fields .= "\t<p><label for='widget-{$widget->id_base}-{$widget->number}-classes'>".apply_filters( 'widget_css_classes_class', esc_html__( 'CSS Classes', 'widget-css-classes' ) ).":</label>
				<input type='text' name='widget-{$widget->id_base}[{$widget->number}][classes]' id='widget-{$widget->id_base}-{$widget->number}-classes' value='{$instance['classes']}' class='widefat' /></p>\n";
			}

			// show predefined
			if ( WCSSC_Loader::$settings['type'] == 2 || WCSSC_Loader::$settings['type'] == 3 ) {

				// Merge input classes with predefined classes
				$predefined_classes = explode( ';', WCSSC_Loader::$settings['defined_classes'] );
				if ( isset( $instance['classes'] ) ) {
					$text_classes = explode( ' ', $instance['classes'] );
					foreach ( $text_classes as $key => $value ) {
						if ( in_array( $value, $predefined_classes ) ) {
							if ( ! in_array( $value, $instance['classes-defined'] ) ) {
								$instance['classes-defined'][] = $value;
							}
							unset( $text_classes[ $key ] );
						}
					}
					$instance['classes'] = implode( ' ', $text_classes );
				}

				$fields .= "\t<p><label for='widget-{$widget->id_base}-{$widget->number}-classes'>".apply_filters( 'widget_css_classes_class', esc_html__( 'CSS Classes', 'widget-css-classes' ) ).":</label>\n";
				if ( WCSSC_Loader::$settings['type'] == 3 ) {
					// show text field
					$fields .= "\t<input type='text' name='widget-{$widget->id_base}[{$widget->number}][classes]' id='widget-{$widget->id_base}-{$widget->number}-classes' value='{$instance['classes']}' class='widefat' style='margin-bottom: .5em;' />\n";
				}
				$fields .= "\t<ul id='widget-{$widget->id_base}-{$widget->number}-classes-defined' style='padding: 5px; max-height: 70px; overflow: hidden; overflow-y: auto; margin: -10px 0 0 0; border: 1px solid #ddd; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.07) inset; color: #32373c;'>\n";
				foreach ( $predefined_classes as $preset ) {
					if ( $preset != '' ) {
						$preset_checked = '';
						if ( in_array( $preset, $instance['classes-defined'] ) ) {
							$preset_checked = 'checked="checked"';
						}
						$id = 'widget-'.$widget->id_base.'-'.$widget->number.'-classes-defined-'.$preset;
						$fields .= "\t<li><input id='{$id}' name='widget-{$widget->id_base}[{$widget->number}][classes-defined][]' type='checkbox' value='".$preset."' ".$preset_checked."> <label for='{$id}'>".$preset."</label></li>\n";
					}
				}
				$fields .= "\t</ul></p>\n";
			}

			//$fields .= "</div>\n";
		}

		do_action( 'widget_css_classes_form', $fields, $instance );

		echo $fields;
		return $instance;
	}

	/**
	 * Updates the Widget with the classes
	 * @static
	 * @param $instance
	 * @param $new_instance
	 * @return array
	 * @since 1.0
	 */
	public static function update_widget( $instance, $new_instance ) {
		$instance['classes'] = $new_instance['classes'];
		$instance['classes-defined'] = $new_instance['classes-defined'];
		if (WCSSC_Loader::$settings['show_id'] == 1) {
			$instance['ids']     = $new_instance['ids'];
		}
		if ( is_array( $instance['classes-defined'] ) ) {
			// Merge predefined classes with input classes
			$text_classes = explode( ' ', $instance['classes'] );
			foreach ( $instance['classes-defined'] as $key => $value ) {
				if ( ! in_array( $value, $text_classes ) ) {
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
	 * @static
	 * @param $params
	 * @return mixed
	 * @since 1.0
	 */
	public static function add_widget_classes( $params ) {

		global $wp_registered_widgets, $widget_number;

		$arr_registered_widgets = wp_get_sidebars_widgets(); // Get an array of ALL registered widgets
		$this_id                = $params[0]['id']; // Get the id for the current sidebar we're processing
		$widget_id              = $params[0]['widget_id'];
		$widget_obj             = $wp_registered_widgets[$widget_id];
		$widget_num             = $widget_obj['params'][0]['number'];
		$widget_css_classes     = ( get_option( 'WCSSC_options' ) ? get_option( 'WCSSC_options' ) : array() );
		$widget_opt             = null;

		// If Widget Logic plugin is enabled, use it's callback
		if ( in_array( 'widget-logic/widget_logic.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$widget_logic_options = get_option( 'widget_logic' );
			if ( isset( $widget_logic_options['widget_logic-options-filter'] ) && 'checked' == $widget_logic_options['widget_logic-options-filter'] ) {
				$widget_opt = get_option( $widget_obj['callback_wl_redirect'][0]->option_name );
			} else {
				$widget_opt = get_option( $widget_obj['callback'][0]->option_name );
			}

		// If Widget Context plugin is enabled, use it's callback
		} elseif ( in_array( 'widget-context/widget-context.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$callback = isset($widget_obj['callback_original_wc']) ? $widget_obj['callback_original_wc'] : null;
			$callback = !$callback && isset($widget_obj['callback']) ? $widget_obj['callback'] : null;

			if ($callback && is_array($widget_obj['callback'])) {
				$widget_opt = get_option( $callback[0]->option_name );
			}
		}

		// Default callback
		else {
			// Check if WP Page Widget is in use
			global $post;
			$id = ( isset( $post->ID ) ? get_the_ID() : NULL );
			if ( isset( $id ) && get_post_meta( $id, '_customize_sidebars' ) ) {
				$custom_sidebarcheck = get_post_meta( $id, '_customize_sidebars' );
			}
			if ( isset( $custom_sidebarcheck[0] ) && ( $custom_sidebarcheck[0] == 'yes' ) ) {
				$widget_opt = get_option( 'widget_'.$id.'_'.substr($widget_obj['callback'][0]->option_name, 7) );
			} elseif ( isset( $widget_obj['callback'][0]->option_name ) ) {
				$widget_opt = get_option( $widget_obj['callback'][0]->option_name );
			}
		}


		// Add classes
		if ( isset( $widget_opt[$widget_num]['classes'] ) && !empty( $widget_opt[$widget_num]['classes'] ) ) {

			if ( $widget_css_classes['type'] == 1 || $widget_css_classes['type'] == 3 ) {
				// Add all classes
				$params[0]['before_widget'] = preg_replace( '/class="/', "class=\"{$widget_opt[$widget_num]['classes']} ", $params[0]['before_widget'], 1 );
			} else if ( $widget_css_classes['type'] == 2 ) {
				// Only add predefined classes
				$predefined_classes = explode( ';', $widget_css_classes['defined_classes'] );
				$classes = explode( ' ', $widget_opt[$widget_num]['classes'] );
				foreach ( $classes as $key => $value ) {
					if ( in_array( $value, $predefined_classes ) ) {
						$value = esc_attr( $value );
						$params[0]['before_widget'] = preg_replace( '/class="/', "class=\"{$value} ", $params[0]['before_widget'], 1 );
					}
				}
			}
		}

		// Add id
		if ( $widget_css_classes['show_id'] == 1 ) {
			if ( isset( $widget_opt[$widget_num]['ids'] ) && !empty( $widget_opt[$widget_num]['ids'] ) )
				$params[0]['before_widget'] = preg_replace( '/id="[^"]*/', "id=\"{$widget_opt[$widget_num]['ids']}", $params[0]['before_widget'], 1 );
		}

		// Add first, last, even, and odd classes
		if ( $widget_css_classes['show_number'] == 1 || $widget_css_classes['show_location'] == 1 || $widget_css_classes['show_evenodd'] == 1 ) {
			if ( !$widget_number ) {
				$widget_number = array();
			}

			if ( !isset( $arr_registered_widgets[$this_id] ) || !is_array( $arr_registered_widgets[$this_id] ) ) {
				return $params;
			}

			if ( isset( $widget_number[$this_id] ) ) {
				$widget_number[$this_id]++;
			} else {
				$widget_number[$this_id] = 1;
			}

			$class = 'class="';

			if ( $widget_css_classes['show_number'] == 1 ) {
				$class .= apply_filters( 'widget_css_classes_number', esc_attr__( 'widget-', 'widget-css-classes' ) ).$widget_number[$this_id].' ';
			}

			if ( $widget_css_classes['show_location'] == 1 ) {
				$widget_first = apply_filters( 'widget_css_classes_first', esc_attr__( 'widget-first', 'widget-css-classes' ) );
				$widget_last = apply_filters( 'widget_css_classes_last', esc_attr__( 'widget-last', 'widget-css-classes' ) );
				if ( $widget_number[$this_id] == 1 ) {
					$class .= $widget_first.' ';
				}
				if ( $widget_number[$this_id] == count( $arr_registered_widgets[$this_id] ) ) {
					$class .= $widget_last.' ';
				}
			}

			if ( $widget_css_classes['show_evenodd'] == 1 ) {
				$widget_even = apply_filters( 'widget_css_classes_even', esc_attr__( 'widget-even', 'widget-css-classes' ) );
				$widget_odd  = apply_filters( 'widget_css_classes_odd', esc_attr__( 'widget-odd', 'widget-css-classes' ) );
				$class .= ( ( $widget_number[$this_id] % 2 ) ? $widget_odd.' ' : $widget_even.' ' );
			}

			$params[0]['before_widget'] = str_replace( 'class="', $class, $params[0]['before_widget'] );

		}

		do_action( 'widget_css_classes_add_classes', $params, $widget_id, $widget_number, $widget_opt, $widget_obj );

		return $params;
	}

}
