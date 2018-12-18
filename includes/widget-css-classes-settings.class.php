<?php
/**
 * Widget CSS Classes Plugin Settings
 *
 * Settings
 * @author C.M. Kendrick <cindy@cleverness.org>
 * @package widget-css-classes
 * @version 1.5.3
 */

/**
 * Settings class, based on class from Theme.fm (see link)
 * @package widget-css-classes
 * @subpackage includes
 * @link http://theme.fm/2011/10/how-to-create-tabs-with-the-settings-api-in-wordpress-2590/
 */
class WCSSC_Settings {

	/**
	 * @var string
	 */
	protected $general_key = '';

	/**
	 * @var string
	 */
	protected $plugin_key = 'widget-css-classes-settings';

	/**
	 * @var array
	 */
	protected $plugin_tabs = array();

	/**
	 * @var array
	 */
	protected $general_settings = array();

	/**
	 * @var string
	 */
	protected $current_tab = '';

	public function __construct() {
		add_action( 'admin_init', array( $this, 'load_settings' ) );
		add_action( 'admin_init', array( $this, 'register_general_settings' ) );
		add_action( 'admin_init', array( $this, 'register_importexport_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menus' ) );
	}

	public function load_settings() {
		$this->general_settings = WCSSC_Lib::get_settings();
		$this->general_key      = WCSSC_Lib::$settings_key;
	}

	public function section_general_desc() {
	}

	public function register_general_settings() {
		$this->plugin_tabs[ $this->general_key ] = esc_attr__( 'Widget CSS Classes Settings', WCSSC_Lib::DOMAIN );
		// @codingStandardsIgnoreLine >> yeah yeah, I know...
		$this->current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_key;

		register_setting( $this->general_key, $this->general_key, array( $this, 'validate_input' ) );
		add_settings_section( 'section_general', esc_attr__( 'Widget CSS Classes Settings', WCSSC_Lib::DOMAIN ), array( $this, 'section_general_desc' ), $this->general_key );
		add_settings_field(
			'show_number',
			esc_attr__( 'Add Widget Number Classes', WCSSC_Lib::DOMAIN ),
			array( $this, 'show_yes_no_option' ),
			$this->general_key,
			'section_general',
			array(
				'key' => 'show_number',
			)
		);
		add_settings_field(
			'show_location',
			esc_attr__( 'Add First/Last Classes', WCSSC_Lib::DOMAIN ),
			array( $this, 'show_yes_no_option' ),
			$this->general_key,
			'section_general',
			array(
				'key' => 'show_location',
			)
		);
		add_settings_field(
			'show_evenodd',
			esc_attr__( 'Add Even/Odd Classes', WCSSC_Lib::DOMAIN ),
			array( $this, 'show_yes_no_option' ),
			$this->general_key,
			'section_general',
			array(
				'key' => 'show_evenodd',
			)
		);
		add_settings_field(
			'show_id',
			esc_attr__( 'Show Additional Field for ID', WCSSC_Lib::DOMAIN ),
			array( $this, 'show_yes_no_option' ),
			$this->general_key,
			'section_general',
			array(
				'key' => 'show_id',
			)
		);
		add_settings_field(
			'type',
			esc_attr__( 'Class Field Type', WCSSC_Lib::DOMAIN ),
			array( $this, 'type_option' ),
			$this->general_key,
			'section_general'
		);
		add_settings_field(
			'defined_classes',
			esc_attr__( 'Predefined Classes', WCSSC_Lib::DOMAIN ),
			array( $this, 'defined_classes_option' ),
			$this->general_key,
			'section_general'
		);
		add_settings_field(
			'fix_widget_params',
			esc_attr__( 'Fix widget parameters', WCSSC_Lib::DOMAIN ),
			array( $this, 'show_yes_no_option' ),
			$this->general_key,
			'section_general',
			array(
				'key'  => 'fix_widget_params',
				'desc' => esc_html__( 'Wrap widget in a <div> element if the parameters are invalid.', WCSSC_Lib::DOMAIN ),
			)
		);
		add_settings_field(
			'filter_unique',
			esc_attr__( 'Remove duplicate classes', WCSSC_Lib::DOMAIN ),
			array( $this, 'show_yes_no_option' ),
			$this->general_key,
			'section_general',
			array(
				'key'  => 'filter_unique',
				'desc' => esc_html__( 'Plugins that run after this plugin could still add duplicates.', WCSSC_Lib::DOMAIN ),
			)
		);
		add_settings_field(
			'translate_classes',
			esc_attr__( 'Translate classes', WCSSC_Lib::DOMAIN ),
			array( $this, 'show_yes_no_option' ),
			$this->general_key,
			'section_general',
			array(
				'key'  => 'translate_classes',
				'desc' => esc_html__( 'Translate classes like `widget-first` and `widget-even`.', WCSSC_Lib::DOMAIN )
					// Translators: %s stands for a link to translate.wordpress.org.
					. ' ' . sprintf( esc_html__( 'Translations are taken from %s', WCSSC_Lib::DOMAIN ), '<a href="https://translate.wordpress.org/projects/wp-plugins/widget-css-classes" target="_blank">translate.wordpress.org</a>' ),
			)
		);
		do_action( 'widget_css_classes_settings' );
	}

	public function show_yes_no_option( $args ) {
		if ( ! $args['key'] ) return;
		$key = esc_attr( $args['key'] );
		?>
		<label><input type="radio" name="<?php echo esc_attr( $this->general_key . '[' . $key . ']' ); ?>" value="1" <?php checked( $this->general_settings[ $key ], true ); ?> /> <?php esc_attr_e( 'Yes', WCSSC_Lib::DOMAIN ); ?></label> &nbsp;
		<label><input type="radio" name="<?php echo esc_attr( $this->general_key . '[' . $key . ']' ); ?>" value="0" <?php checked( $this->general_settings[ $key ], false ); ?> /> <?php esc_attr_e( 'No', WCSSC_Lib::DOMAIN ); ?></label>
		<?php
		if ( ! empty( $args['desc'] ) ) {
			echo WCSSC::do_description( $args['desc'] ); // @codingStandardsIgnoreLine >> no valid esc function.
		}
	}

	public function type_option() {
		?>
		<label><input type="radio" class="wcssc_type" name="<?php echo esc_attr( $this->general_key ) . '[type]'; ?>" value="1" <?php checked( $this->general_settings['type'], 1 ); ?> /> <?php esc_attr_e( 'Text', WCSSC_Lib::DOMAIN ); ?></label> &nbsp;
		<label><input type="radio" class="wcssc_type" name="<?php echo esc_attr( $this->general_key ) . '[type]'; ?>" value="2" <?php checked( $this->general_settings['type'], 2 ); ?> /> <?php esc_attr_e( 'Predefined', WCSSC_Lib::DOMAIN ); ?></label> &nbsp;
		<label><input type="radio" class="wcssc_type" name="<?php echo esc_attr( $this->general_key ) . '[type]'; ?>" value="3" <?php checked( $this->general_settings['type'], 3 ); ?> /> <?php esc_attr_e( 'Both', WCSSC_Lib::DOMAIN ); ?></label> &nbsp;
		<label><input type="radio" class="wcssc_type" name="<?php echo esc_attr( $this->general_key ) . '[type]'; ?>" value="0" <?php checked( $this->general_settings['type'], 0 ); ?> /> <?php esc_attr_e( 'None', WCSSC_Lib::DOMAIN ); ?></label>
		<?php
	}

	public function defined_classes_option() {
		wp_enqueue_script( 'jquery-ui-sortable' );
		$presets = $this->general_settings['defined_classes'];
		?>
		<div class="wcssc_sortable">
		<?php
		if ( count( $presets ) > 1 ) {
			foreach ( $presets as $key => $preset ) {
			?>
				<p class="wcssc_defined_classes">
					<a class="wcssc_sort" href="#"><span class="dashicons dashicons-sort"></span></a>
					<input type="text" name="<?php echo esc_attr( $this->general_key ) . '[defined_classes][' . esc_attr( $key ) . ']'; ?>" value="<?php echo esc_attr( $preset ); ?>" />
					<a class="wcssc_remove" href="#"><span class="dashicons dashicons-dismiss"></span></a>
				</p>
			<?php
			}
			?>
			<p class="wcssc_defined_classes wcssc_sort_fixed">
				<a class="wcssc_sort" href="#"><span class="dashicons dashicons-sort"></span></a>
				<input type="text" name="<?php echo esc_attr( $this->general_key ) . '[defined_classes][]'; ?>" value="" />
				<a href="#" class="wcssc_copy" rel=".wcssc_defined_classes"><span class="dashicons dashicons-plus-alt"></span></a>
				<a class="wcssc_remove" href="#"><span class="dashicons dashicons-dismiss"></span></a>
			</p>
		<?php
		} else {
			$value = ( ! empty( $this->general_settings['defined_classes'][0] ) ) ? $this->general_settings['defined_classes'][0] : '';
			?>
			<p class="wcssc_defined_classes wcssc_sort_fixed">
				<a class="wcssc_sort" href="#"><span class="dashicons dashicons-sort"></span></a>
				<input type="text" name="<?php echo esc_attr( $this->general_key ) . '[defined_classes][]'; ?>" value="<?php echo esc_attr( $value ); ?>" />
				<a href="#" class="wcssc_copy" rel=".wcssc_defined_classes"><span class="dashicons dashicons-plus-alt"></span></a>
				<a class="wcssc_remove" href="#"><span class="dashicons dashicons-dismiss"></span></a>
			</p>
		<?php
		}
		?>
		</div>
		<?php
	}

	/**
	 * @todo Move to separate class or split in different methods.
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function register_importexport_settings() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// @codingStandardsIgnoreLine
		$get = $_GET; $post = $_POST;

		$this->plugin_tabs['importexport'] = esc_attr__( 'Import/Export', WCSSC_Lib::DOMAIN );

		$wcssc_message_class = '';
		$wcssc_message       = '';
		if ( isset( $get['wcssc_message'] ) ) {
			switch ( $get['wcssc_message'] ) {
				case 1:
					$wcssc_message_class = 'updated';
					$wcssc_message       = esc_attr__( 'Settings Imported', WCSSC_Lib::DOMAIN );
					break;
				case 2:
					$wcssc_message_class = 'error';
					$wcssc_message       = esc_attr__( 'Invalid Settings File', WCSSC_Lib::DOMAIN );
					break;
				case 3:
					$wcssc_message_class = 'error';
					$wcssc_message       = esc_attr__( 'No Settings File Selected', WCSSC_Lib::DOMAIN );
					break;
			}
		}

		if ( ! empty( $wcssc_message ) ) {
			echo '<div class=" ' . esc_attr( $wcssc_message_class ) . ' "><p>' . esc_html( $wcssc_message ) . '</p></div>';
		}

		// export settings
		if ( isset( $get['widget-css-classes-settings-export'] ) ) {
			header( 'Content-Disposition: attachment; filename=widget-css-classes-settings.txt' );
			header( 'Content-Type: text/plain; charset=utf-8' );
			$general = get_option( 'WCSSC_options' );

			echo "[START=WCSSC SETTINGS]\n";
			foreach ( $general as $id => $text ) {
				// @codingStandardsIgnoreLine >> wp_json_encode is WP 4.1+
				echo "$id\t" . json_encode( $text ) . "\n";
			}
			echo '[STOP=WCSSC SETTINGS]';
			exit;
		}

		// import settings
		if ( isset( $post['widget-css-classes-settings-import'] ) ) {
			$wcssc_message = 3;
			if ( $_FILES['widget-css-classes-settings-import-file']['tmp_name'] ) {
				$wcssc_message = 2;
				$import        = explode(
					"\n",
					// @codingStandardsIgnoreLine >> yeah yeah, I know...
					file_get_contents( $_FILES['widget-css-classes-settings-import-file']['tmp_name'] )
				);
				if ( array_shift( $import ) === '[START=WCSSC SETTINGS]' && array_pop( $import ) === '[STOP=WCSSC SETTINGS]' ) {
					$options = WCSSC_Lib::get_settings();
					foreach ( $import as $import_option ) {
						list( $key, $value ) = explode( "\t", $import_option );

						$options[ $key ] = json_decode( sanitize_text_field( $value ) );
						if ( $options['dropdown'] ) { // Update for 1.3.0
							$options['defined_classes'] = $options['dropdown'];
							unset( $options['dropdown'] );
						}
					}
					WCSSC_Lib::update_settings( $options );
					$wcssc_message = 1;
				}
			}

			wp_safe_redirect( admin_url( '/options-general.php?page=widget-css-classes-settings&tab=importexport&wcssc_message=' . esc_attr( $wcssc_message ) ) );
			exit;
		}
	}

	/**
	 * @param  array $input
	 * @return array
	 */
	public function validate_input( $input ) {
		WCSSC_Lib::set_settings( $input );
		return WCSSC_Lib::get_settings();
	}

	public function add_admin_menus() {
		add_options_page( esc_attr__( 'Widget CSS Classes', WCSSC_Lib::DOMAIN ), esc_attr__( 'Widget CSS Classes', WCSSC_Lib::DOMAIN ), 'manage_options', 'widget-css-classes-settings', array( $this, 'plugin_options_page' ) );
	}

	/*
	 * Plugin Options page rendering goes here, checks
	 * for active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 */
	public function plugin_options_page() {
		$tab = $this->current_tab;
		?>
	<div class="wrap">
		<?php $this->plugin_options_tabs(); ?>
		<form method="post" action="options.php" enctype="multipart/form-data">
			<?php settings_fields( $tab ); ?>
			<?php do_settings_sections( $tab ); ?>
			<?php if ( 'importexport' === $tab ) $this->importexport_fields(); ?>
			<?php if ( 'importexport' !== $tab ) submit_button(); ?>
		</form>
	</div>
	<?php
		add_action( 'in_admin_footer', array( 'WCSSC_Lib', 'admin_footer' ) );
	}

	/*
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one. Provides the heading for the
	 * plugin_options_page method.
	 */
	public function plugin_options_tabs() {

		echo '<h1 class="nav-tab-wrapper">';
		foreach ( $this->plugin_tabs as $tab_key => $tab_caption ) {
			$active = $this->current_tab === $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . esc_attr( $active ) . '" href="?page=' . esc_attr( $this->plugin_key ) . '&amp;tab=' . esc_attr( $tab_key ) . '">' . esc_html( $tab_caption ) . '</a>';
		}
		echo '</h1>';
	}

	public function importexport_fields() {
	?>
		<h3><?php esc_html_e( 'Import/Export Settings', WCSSC_Lib::DOMAIN ); ?></h3>

		<p><a class="submit button" href="?widget-css-classes-settings-export"><?php esc_attr_e( 'Export Settings', WCSSC_Lib::DOMAIN ); ?></a></p>

		<p>
			<input type="hidden" name="widget-css-classes-settings-import" id="widget-css-classes-settings-import" value="true" />
			<?php submit_button( esc_attr__( 'Import Settings', WCSSC_Lib::DOMAIN ), 'button', 'widget-css-classes-settings-submit', false ); ?>
			<input type="file" name="widget-css-classes-settings-import-file" id="widget-css-classes-settings-import-file" />
		</p>
	<?php
	}
}
