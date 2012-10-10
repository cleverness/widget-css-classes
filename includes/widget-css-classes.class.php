<?php
/**
 * Widget CSS Classes Plugin Loader
 *
 * Loader
 * @author C.M. Kendrick <cindy@cleverness.org>
 * @package widget-css-classes
 * @version 1.1
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

		$fields = "<p>\n";

		// show id field
		if ( WCSSC_Loader::$settings['show_id'] == 1 ) {
			if ( !isset( $instance['ids'] ) ) $instance['ids'] = null;
			$fields .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-ids'>".apply_filters( 'widget_css_classes_id', esc_html__( 'CSS ID', 'widget-css-classes' ) ).":</label>
			<input type='text' name='widget-{$widget->id_base}[{$widget->number}][ids]' id='widget-{$widget->id_base}-{$widget->number}-ids' value='{$instance['ids']}'/><br />\n";
		}

		// show text field
		if ( WCSSC_Loader::$settings['type'] == 1 ) {
			$fields .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-classes'>".apply_filters( 'widget_css_classes_class', esc_html__( 'CSS Class', 'widget-css-classes' ) ).":</label>
			<input type='text' name='widget-{$widget->id_base}[{$widget->number}][classes]' id='widget-{$widget->id_base}-{$widget->number}-classes' value='{$instance['classes']}'/>\n";
		}

		// show dropdown
		if ( WCSSC_Loader::$settings['type'] == 2 ) {
			$preset_values = explode( ';', WCSSC_Loader::$settings['dropdown'] );
			$fields .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-classes'>".apply_filters( 'widget_css_classes_class', esc_html__( 'CSS Class', 'widget-css-classes' ) ).":</label>\n";
			$fields .= "\t<select  name='widget-{$widget->id_base}[{$widget->number}][classes]' id='widget-{$widget->id_base}-{$widget->number}-classes'>\n";
			$fields .= "\t<option value=''>".esc_attr__( 'Select', 'widget-css-classes' )."</option>\n";
			foreach ( $preset_values as $preset ) {
				if ( $preset != '' ) {
					$fields .= "\t<option value='".$preset."' ".selected( $instance['classes'], $preset, 0 ).">".$preset."</option>\n";
				}
			}
			$fields .= "</select>\n";
		}

		$fields .= "</p>\n";

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
		$instance['ids']     = $new_instance['ids'];
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

		// if Widget Logic plugin is enabled, use it's callback
		if ( in_array( 'widget-logic/widget_logic.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$widget_logic_options = get_option( 'widget_logic' );
			if ( $widget_logic_options['widget_logic-options-filter'] == 'checked' ) {
				$widget_opt = get_option( $widget_obj['callback_wl_redirect'][0]->option_name );
			} else {
				$widget_opt = get_option( $widget_obj['callback'][0]->option_name );
			}

		// if Widget Context plugin is enabled, use it's callback
		} elseif ( in_array( 'widget-context/widget-context.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$widget_opt = get_option( $widget_obj['callback_original_wc'][0]->option_name );
		
		// Default callback
		} else {
			$widget_opt = get_option( $widget_obj['callback'][0]->option_name );
		}

		// add classes
		if ( $widget_css_classes['type'] != 3 ) {
			if ( isset( $widget_opt[$widget_num]['classes'] ) && !empty( $widget_opt[$widget_num]['classes'] ) )
				$params[0]['before_widget'] = preg_replace( '/class="/', "class=\"{$widget_opt[$widget_num]['classes']} ", $params[0]['before_widget'], 1 );
		}

		// add id
		if ( $widget_css_classes['show_id'] == 1 ) {
			if ( isset( $widget_opt[$widget_num]['ids'] ) && !empty( $widget_opt[$widget_num]['ids'] ) )
				$params[0]['before_widget'] = preg_replace( '/id="/', "id=\"{$widget_opt[$widget_num]['ids']} ", $params[0]['before_widget'], 1 );
		}

		// add first, last, even, and odd classes
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

		$class        = 'class="'.apply_filters( 'widget_css_classes_number', esc_attr__( 'widget-', 'widget-css-classes' ) ).$widget_number[$this_id].' ';
		$widget_first = apply_filters( 'widget_css_classes_first', esc_attr__( 'widget-first', 'widget-css-classes' ) );
		$widget_last  = apply_filters( 'widget_css_classes_last', esc_attr__( 'widget-last', 'widget-css-classes' ) );
		$widget_even  = apply_filters( 'widget_css_classes_even', esc_attr__( 'widget-even', 'widget-css-classes' ) );
		$widget_odd   = apply_filters( 'widget_css_classes_odd', esc_attr__( 'widget-odd', 'widget-css-classes' ) );

		if ( $widget_number[$this_id] == 1 ) {
			$class .= $widget_first.' '.$widget_odd.' ';
		} else {
			$class .= ( ( $widget_number[$this_id] % 2 ) ? $widget_odd.' ' : $widget_even.' ' );
		}

		if ( $widget_number[$this_id] == count( $arr_registered_widgets[$this_id] ) ) {
			$class .= $widget_last.' ';
		}

		$params[0]['before_widget'] = str_replace( 'class="', $class, $params[0]['before_widget'] );

		do_action( 'widget_css_classes_add_classes', $params, $widget_id, $widget_number, $widget_opt, $widget_obj );

		return $params;
	}

}