<?php
/**
 * Functionality of dashboard functionality of the plugin.
 *
 * @package    WelcomeWP
 * @author     Taras Dashkevych
 * @since      1.0.0
 */
class WelcomeWP_Dashboard {
	/**
	 * Slug of the main settings page.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $slug = 'welcomewp-plugin';

	/**
	 * Option name used for message.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $message_option_name = 'welcomewp_message_global_options';

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_filter( 'welcomewp-pointerplus_list', array( $this, 'init_onboarding' ) );
	}

	/**
	 * Set up onboarding process.
	 *
	 * @since 1.0.1
	 */
	public function init_onboarding( $pointers ) {
		return array_merge(
			$pointers,
			array(
				$this->slug . '_settings' => array(
					'selector'   => '#menu-posts-greeter',
					'title'      => esc_html__( 'WelcomeWP', 'welcomewp' ),
					'text'       => esc_html__( 'The plugin is active. Use `Greeters` to create a website message.', 'welcomewp' ),
					'width'      => 250,
					'icon_class' => 'dashicons-nametag',
					'pages'      => array( 'plugins.php' ),
				),
			)
		);
	}

	/**
	 * Load styles and scripts needed for the plugin's settings page
	 * in WordPress dashboard.
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueue_scripts() {
		$suffix         = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$current_screen = get_current_screen();

		if ( ! strpos( $current_screen->base, $this->slug ) ) {
			return;
		}

		wp_enqueue_style(
			'welcomewp-admin-settings',
			WELCOMEWP_PLUGIN_URL . "assets/css/admin/settings{$suffix}.css",
			array(),
			'1.0.1'
		);
		// Load JavaScript functionality of a message.
		wp_enqueue_script(
			'welcomewp-admin-settings',
			WELCOMEWP_PLUGIN_URL . "assets/js/admin/settings{$suffix}.js",
			array(),
			'1.0.1',
			true
		);
	}

	/**
	 * Add plugin's settings page in WordPress dashboard.
	 *
	 * @since 1.0.0
	 */
	public function admin_menu() {
		add_options_page(
			esc_html__( 'WelcomeWP Settings', 'welcomewp' ),
			esc_html__( 'WelcomeWP', 'welcomewp' ),
			'manage_options',
			$this->slug,
			array(
				$this,
				'settings_page',
			)
		);
	}

	/**
	 * Register settings
	 * for the plugin in WordPress dashboard.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		$message_section_id      = 'welcomewp_settings__message';
		$message_cookie_field_id = 'welcomewp_settings__message_cookie';

		// Register setting for a message.
		register_setting(
			$this->message_option_name,
			$this->message_option_name,
			array( $this, 'validate_message_options' )
		);

		add_settings_section(
			$message_section_id,
			esc_html__( 'Greeter', 'welcomewp' ),
			array( $this, 'settings_message_section_description' ),
			$this->slug
		);

		add_settings_field(
			$message_cookie_field_id,
			esc_html__( 'Cookie expires in:', 'welcomewp' ),
			array( $this, 'settings_field_message_cookie_expiration' ),
			$this->slug,
			$message_section_id
		);
	}

	/**
	 * HTML output for options available
	 * for setting cookie expiration in message.
	 *
	 * @since 1.0.0
	 */
	public function settings_field_message_cookie_expiration() {
		$options         = $this->get_message_global_options();
		$cookie_options  = array();
		$cookie_defaults = $this->get_message_global_defaults();

		// Check if value for cookie has been ever set.
		if ( isset( $options['cookie']['value'] ) ) {
			$cookie_options['value'] = $options['cookie']['value'];
		}

		// Check if range for cookie has been ever set.
		if ( isset( $options['cookie']['range'] ) ) {
			$cookie_options['range'] = $options['cookie']['range'];
		}

		$args              = wp_parse_args( $cookie_options, $cookie_defaults );
		$cookie_value_name = $this->message_option_name . '[cookie][value]';
		$cookie_range_name = $this->message_option_name . '[cookie][range]';
		?>

		<div class="wwp-cookie-expiration wwp-form-inline-control">
			<p>
				<input class="small-text" type="number" name="<?php echo esc_attr( $cookie_value_name ); ?>" value="<?php echo esc_attr( $args['value'] ); ?>" min="1" max="60">
			</p>

			<fieldset class="wwp-form-inline-control">
				<label for="cookie-range-minutes">
					<input id="cookie-range-minutes" type="radio" name="<?php echo esc_attr( $cookie_range_name ); ?>" value="minutes" <?php checked( 'minutes', $args['range'], true ); ?>>
					<span><?php esc_html_e( 'Minutes', 'welcomewp' ); ?></span>
				</label>

				<label for="cookie-range-hours">
					<input id="cookie-range-hours" type="radio" name="<?php echo esc_attr( $cookie_range_name ); ?>" value="hours" <?php checked( 'hours', $args['range'], true ); ?>>
					<span><?php esc_html_e( 'Hours', 'welcomewp' ); ?></span>
				</label>

				<label for="cookie-range-days">
					<input id="cookie-range-days" type="radio" name="<?php echo esc_attr( $cookie_range_name ); ?>" value="days" <?php checked( 'days', $args['range'], true ); ?>>
					<span><?php esc_html_e( 'Days', 'welcomewp' ); ?></span>
				</label>

				<label for="cookie-range-months">
					<input id="cookie-range-months" type="radio" name="<?php echo esc_attr( $cookie_range_name ); ?>" value="months" <?php checked( 'months', $args['range'], true ); ?>>
					<span><?php esc_html_e( 'Months', 'welcomewp' ); ?></span>
				</label>
			</fieldset>
		</div>

		<details class="wwp-description">
			<summary><?php esc_html_e( 'What does it mean?', 'welcomewp' ); ?></summary>
			<p><?php esc_html_e( 'After closing a message, the browser uses a saved piece of data (cookie) to prevent displaying the message again for a set period of time, configured via this option.', 'welcomewp' ); ?></p>
		</details>

		<br />

		<div>
			<p class="wwp-clear-cache">
				<button id="wwp-clear-cache-button" class="button button-secondary" type="button"><?php esc_html_e( 'Clear cookie', 'welcomewp' ); ?></button>

				<span id="wwp-clear-cache-404" class="wwp-form-notice" aria-hidden="true"><?php esc_html_e( 'Nothing found', 'welcomewp' ); ?></span>
				<span id="wwp-clear-cache-updated" class="wwp-form-notice" aria-hidden="true"><?php esc_html_e( 'Done', 'welcomewp' ); ?></span>
				<span id="wwp-clear-cache-error" class="wwp-form-notice" aria-hidden="true"><?php esc_html_e( 'Error', 'welcomewp' ); ?></span>
			</p>
			<p class="wwp-description"><?php esc_html_e( 'This option clears message cookie only for you, and only on a browser you are currently using.', 'welcomewp' ); ?></p>
		</div>

		<?php
	}

	/**
	 * Validate saved message options.
	 *
	 * @since 1.0.0
	 */
	public function validate_message_options( $input ) {
		$defaults        = $this->get_message_global_defaults();
		$available_range = array(
			'minutes',
			'hours',
			'days',
			'months',
		);

		// Make sure message cookie is set.
		if ( ! isset( $input['cookie'] ) ) {
			// Otherwise, return defaults.
			return $defaults;
		}

		// Make sure cookie value is set.
		if ( ! isset( $input['cookie']['value'] ) ) {
			$input['cookie']['value'] = $defaults['cookie']['value'];
		} else {
			$input['cookie']['value'] = absint( $input['cookie']['value'] );
		}

		// Make sure we have a correct value.
		if ( $input['cookie']['value'] < 1 || $input['cookie']['value'] > 60 ) {
			$input['cookie']['value'] = $defaults['cookie']['value'];
		}

		// Make sure cookie range is set.
		if ( ! isset( $input['cookie']['range'] ) ) {
			$input['cookie']['range'] = $defaults['cookie']['range'];
		} else {
			$input['cookie']['range'] = sanitize_key( $input['cookie']['range'] );
		}

		// Make sure range is valid.
		if ( ! in_array( $input['cookie']['range'], $available_range ) ) {
			$input['cookie']['range'] = $defaults['cookie']['range'];
		}

		return $input;
	}

	/**
	 * HTML output with description
	 * for the message settings.
	 *
	 * @since 1.0.0
	 */
	function settings_message_section_description() {
		printf(
			'<p class="wwp-description">%s</p>',
			esc_html__( 'Here you can set global options for a WelcomeWP greeter, which delivers a message to site visitors.', 'welcomewp' )
		);
	}

	/**
	 * HTML output for form with plugin's settings.
	 *
	 * @since 1.0.0
	 */
	public function settings_page() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'WelcomeWP Settings', 'welcomewp' ); ?></h2>
			<form action="options.php" method="post">
				<?php
				settings_fields( $this->message_option_name );
				do_settings_sections( $this->slug );
				?>
				<input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
			</form>
		</div>
		<?php
	}

	/**
	 * Return message default global options.
	 *
	 * @since 1.0.0
	 */
	private function get_message_global_defaults() {
		$defaults = array(
			'cookie' => array(
				'value' => '5',
				'range' => 'minutes',
			),
		);

		return $defaults;
	}

	/**
	 * Return message global options.
	 *
	 * @since 1.0.0
	 */
	public function get_message_global_options() {
		$options = get_option( $this->message_option_name );

		// Make sure options are set.
		if ( ! $options ) {
			return $this->get_message_global_defaults();
		}

		return $options;
	}
}
