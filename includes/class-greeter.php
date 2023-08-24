<?php
/**
 * All the plugin's goodness and basics.
 *
 * @package    WelcomeWP
 * @author     Taras Dashkevych
 * @since      1.0.0
 */

class WelcomeWP_Greeter {
	/**
	 * Slug of current custom post type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $slug = 'greeter';

	/**
	 * Options action name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $options_action_name = 'welcomewp_greeter_options';

	/**
	 * Options nonce name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $options_nonce_name = 'welcomewp_greeter_options_nonce';

	/**
	 * Option name for display option, set via metabox.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $option_name_display = 'welcomewp_greeter_display';

	/**
	 * Option name for position option, set via metabox.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $option_name_position = 'welcomewp_greeter_position';

	/**
	 * Option name for colors option, set via metabox.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $option_name_colors = 'welcomewp_greeter_colors';

	/**
	 * ID of currenty active CPT.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $active_id = 0;

	/**
	 * Front end message.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	private $message;

	/**
	 * Dashboard settings page.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	private $dashboard;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $dashboard ) {
		$this->dashboard = $dashboard;

		if ( ! post_type_exists( $this->slug ) ) {
			add_action( 'init', array( $this, 'register_cpt' ), 1 );
		}

		// Load scripts needed for admin panel.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		// Enable metaboxes.
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		// Save post.
		add_action( 'save_post', array( $this, 'save_post' ) );

		// Create a message.
		add_action( 'template_redirect', array( $this, 'create_message' ) );
		// Display a message.
		add_action( 'wp_footer', array( $this, 'display_message' ) );

		// Display onboarding hints if no posts have been created yet.
		if ( ! $this->has_posts() ) {
			add_filter( 'welcomewp-pointerplus_list', array( $this, 'init_onboarding' ) );
		}
	}

	/**
	 * Create a message to display a Greeter message.
	 *
	 * @since 1.0.0
	 */
	public function create_message() {
		$this->message = new WelcomeWP_Message();

		$this->set_active_id( $this->message->get_current_display() );
		$this->message->configure( $this->get_options() );
	}

	/**
	 * Display a message to display a Greeter message.
	 *
	 * @since 1.0.0
	 */
	public function display_message() {
		$this->message->display();
	}

	/**
	 * Set ID of a Greeter, based on a Greeter display option.
	 *
	 * @param string $display View (page) on which to display a Greeter.
	 * @since 1.0.0
	 */
	private function set_active_id( $display ) {
		$id = null;

		if ( 'all' !== $display ) {
			$id = $this->get_id( $display );
		}

		// Make sure if there is a Greeter for set display.
		if ( ! $id ) {
			// Otherwise, locate a Greeter with global (all pages) display.
			$id = $this->get_id( 'all' );
		}

		// Make sure if there is a Greeter for all pages.
		if ( ! $id ) {
			return;
		}

		// Set active id if needed.
		$this->active_id = $id;
	}

	/**
	 * Check availability of published CPT posts.
	 *
	 * @since 1.0.1
	 */
	public function has_posts() {
		$has_posts = false;
		$loop      = new WP_Query(
			array(
				'post_type'      => $this->slug,
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'fields'         => 'ids',
			)
		);

		$has_posts = $loop->have_posts();

		wp_reset_postdata();

		return $has_posts;
	}

	/**
	 * Return ID of a Greeter, based on a Greeter display option.
	 *
	 * @param string $display View (page) on which to display a Greeter.
	 * @since 1.0.0
	 */
	private function get_id( $display ) {
		$id   = 0;
		$loop = new WP_Query(
			array(
				'post_type'      => $this->slug,
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'   => $this->option_name_display,
						'value' => $display,
					),
				),
			)
		);

		if ( ! $loop->have_posts() ) {
			return $id;
		}

		$id = $loop->posts[0];

		wp_reset_postdata();

		return $id;
	}

	/**
	 * Registers the custom post type.
	 *
	 * @since 1.0.0
	 */
	public function register_cpt() {
		$labels = array(
			'name'               => _x( 'Greeters', 'post type general name', 'welcomewp' ),
			'singular_name'      => _x( 'Greeter', 'post type singular name', 'welcomewp' ),
			'add_new'            => __( 'Add New', 'welcomewp' ),
			'add_new_item'       => __( 'Add New Greeter', 'welcomewp' ),
			'edit_item'          => __( 'Edit Greeter', 'welcomewp' ),
			'new_item'           => __( 'New Greeter', 'welcomewp' ),
			'all_items'          => __( 'All Greeters', 'welcomewp' ),
			'view_item'          => __( 'View Greeter', 'welcomewp' ),
			'search_items'       => __( 'Search Greeters', 'welcomewp' ),
			'not_found'          => __( 'No Greeters found', 'welcomewp' ),
			'not_found_in_trash' => __( 'No Greeters found in Trash', 'welcomewp' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Greeters', 'welcomewp' ),
		);

		$supports = array(
			'title',
			'thumbnail',
			'editor',
			'excerpt',
		);

		// Custom post type arguments, which can be filtered if needed.
		$args = apply_filters(
			'welcomewp_greeter_post_type_args',
			array(
				'labels'              => $labels,
				'public'              => false,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_admin_bar'   => true,
				'rewrite'             => false,
				'query_var'           => false,
				'can_export'          => false,
				'supports'            => $supports,
				'menu_icon'           => 'dashicons-nametag',
			)
		);

		// Register the post type
		register_post_type( $this->slug, $args );
	}

	/**
	 * Add a meta box for the current CPT.
	 *
	 * @since 1.0.0
	 */
	public function add_metabox() {
		add_meta_box(
			$this->options_action_name,
			esc_html__( 'WelcomeWP', 'welcomewp' ),
			array( $this, 'metabox_callback' ),
			$this->slug,
			'normal',
			'default'
		);
	}

	/**
	 * Display markup of the meta box for the current CPT.
	 *
	 * @since 1.0.0
	 */
	public function metabox_callback( $post ) {
		// Add an nonce field so we can check for it later.
		wp_nonce_field( $this->options_action_name, $this->options_nonce_name );

		// Output form fields for display option.
		$this->output_fields_option_display( $post->ID );
		// Output form fields for position option.
		$this->output_fields_option_position( $post->ID );
		// Output form fields for colors option.
		$this->output_fields_option_colors( $post->ID );
	}

	/**
	 * Save custom data (settings) during CPT update.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save_post( $post_id ) {
		// Save settings only for the plugin's CPT.
		if ( $this->slug !== get_post_type( $post_id ) ) {
			return $post_id;
		}

		// Check if our nonce is set.
		if ( ! isset( $_POST[ $this->options_nonce_name ] ) ) {
			return $post_id;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST[ $this->options_nonce_name ], $this->options_action_name ) ) {
			return $post_id;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// OPTION: Display.

		// Get new saved display option.
		$option_display_new = sanitize_key( $_POST[ $this->option_name_display ] );
		// Make sure a new saved display option is valid.
		$option_display_new = $this->validate_option_display( $option_display_new );
		// Get current saved display option.
		$option_display_current = $this->get_option_display( $post_id );

		// Check if there is already an active entry with this specific visibility option.
		if ( $this->is_available_display( $post_id, $option_display_new ) ) {
			$option_display_new = 'inactive';
		}

		// Update display option if needed.
		if ( $option_display_new && $option_display_new !== $option_display_current ) {
			update_post_meta( $post_id, $this->option_name_display, $option_display_new );
		}

		// OPTION: Position.

		// Get new saved position option.
		$option_position_new = sanitize_key( $_POST[ $this->option_name_position ] );
		// Make sure a new saved position option is valid.
		$option_position_new = $this->validate_option_position( $option_position_new );
		// Get current saved position option.
		$option_position_current = $this->get_option_position( $post_id );

		// Update position option if needed.
		if ( $option_position_new && $option_position_new !== $option_position_current ) {
			update_post_meta( $post_id, $this->option_name_position, $option_position_new );
		}

		// OPTION: Colors
		$option_colors_new = array_map( 'sanitize_hex_color', $_POST[ $this->option_name_colors ] );
		// Make sure a new saved colors option is valid.
		$option_colors_new = $this->validate_option_colors( $option_colors_new );
		// Get current saved colors option.
		$option_colors_current = $this->get_option_colors( $post_id );

		// Update colors option if needed.
		if ( is_array( $option_colors_new ) && $option_colors_new !== $option_colors_current ) {
			update_post_meta( $post_id, $this->option_name_colors, $option_colors_new );
		}
	}

	/**
	 * Load back-end styles.
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueue_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_add_inline_script(
			'wp-color-picker',
			'jQuery(document).ready(function($){$(".wwp-form__color-field").each(function(){$(this).wpColorPicker();});});'
		);

		wp_enqueue_style(
			'welcomewp-admin-cpt',
			WELCOMEWP_PLUGIN_URL . "assets/css/admin/cpt{$suffix}.css",
			array(),
			'1.0.1'
		);
	}

	/**
	 * Return all message options (global and local).
	 *
	 * @since 1.0.0
	 */
	public function get_options( $id = null ) {
		if ( ! isset( $id ) ) {
			$id = $this->active_id;
		}

		return array(
			'id'     => $id,
			'global' => $this->dashboard->get_message_global_options(),
			'local'  => array(
				'display'  => $this->get_option_display( $id ),
				'position' => $this->get_option_position( $id ),
				'color'    => $this->get_option_colors( $id ),
			),
		);
	}

	/**
	 * Check if there is already published a greeter
	 *
	 * @since 1.0.0
	 * @param int    $id    ID of the greeter.
	 * @param string $value Display option value.
	 */
	private function is_available_display( $id, $value ) {
		$loop = new WP_Query(
			array(
				'post_type'      => $this->slug,
				'posts_per_page' => 1,
				'post__not_in'   => array( $id ),
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'   => $this->option_name_display,
						'value' => $value,
					),
				),
			)
		);

		$is_available = $loop->have_posts();

		wp_reset_postdata();

		return $is_available;
	}

	/**
	 * Return defaults for display option.
	 *
	 * @since 1.0.0
	 */
	private function get_option_display_defaults() {
		return array(
			'inactive',
			'all',
			'singular',
			'archive',
		);
	}

	/**
	 * Return display option.
	 *
	 * @since 1.0.0
	 * @param int $id ID of the greeter.
	 */
	public function get_option_display( $id ) {
		// Get saved options.
		$option = get_post_meta( $id, $this->option_name_display, true );
		// Validate saved options.
		$option = $this->validate_option_display( $option );

		return $option;
	}

	/**
	 * Return a validated display option.
	 *
	 * @since 1.0.0
	 * @param string $option Option that needs validation.
	 */
	public function validate_option_display( $option ) {
		// All available choices for the option.
		$defaults = $this->get_option_display_defaults();

		// Make sure saved option exists; otherwise return default value.
		if ( ! $option ) {
			return $defaults[0];
		}

		// Make sure saved option is valid.
		if ( ! in_array( $option, $defaults ) ) {
			return $defaults[0];
		}

		return $option;
	}

	/**
	 * Ouput display option fields for metabox.
	 *
	 * @since 1.0.0
	 * @param int $id ID of the greeter.
	 */
	public function output_fields_option_display( $id ) {
		$defaults = $this->get_option_display_defaults();
		$option   = $this->get_option_display( $id );

		echo '<div class="pagebox">';
		echo '<p><strong>' . esc_html__( 'Display settings', 'welcomewp' ) . '</strong></p>';
		echo '<ul>';

		foreach ( $defaults as $default ) {
			switch ( $default ) {
				case 'inactive':
					$label = esc_html__( 'Inactive', 'welcomewp' );
					break;
				case 'all':
					$label = esc_html__( 'All pages', 'welcomewp' );
					break;
				case 'singular':
					$label = esc_html__( 'Singular views', 'welcomewp' );
					break;
				case 'archive':
					$label = esc_html__( 'Archive views', 'welcomewp' );
					break;
				default:
					$label = '';
			}

			// Avoid output any field if there is no label.
			if ( '' === $label ) {
				continue;
			}

			printf(
				'<li>
                    <input id="display-%1$s" value="%1$s" type="radio" name="%2$s" %3$s>
                    <label for="display-%1$s">%4$s</label>
                </li>',
				esc_attr( $default ),
				esc_attr( $this->option_name_display ),
				checked( $option, $default, false ),
				$label
			);
		}

		echo '</ul>';
		echo '</div><!-- .pagebox -->';
	}

	/**
	 * Return defaults for position option.
	 *
	 * @since 1.0.0
	 */
	private function get_option_position_defaults() {
		return array(
			'right-bottom',
			'left-bottom',
		);
	}

	/**
	 * Return position option.
	 *
	 * @since 1.0.0
	 * @param int $id ID of the greeter.
	 */
	public function get_option_position( $id ) {
		// Get saved options.
		$option = get_post_meta( $id, $this->option_name_position, true );
		// Validate saved options.
		$option = $this->validate_option_position( $option );

		return $option;
	}

	/**
	 * Return a validated position option.
	 *
	 * @since 1.0.0
	 * @param string $option Option that needs validation.
	 */
	public function validate_option_position( $option ) {
		// All available choices for the option.
		$defaults = $this->get_option_position_defaults();

		// Make sure saved option exists; otherwise return default value.
		if ( ! $option ) {
			return $defaults[0];
		}

		// Make sure saved option is valid.
		if ( ! in_array( $option, $defaults ) ) {
			return $defaults[0];
		}

		return $option;
	}

	/**
	 * Ouput display option fields for metabox.
	 *
	 * @since 1.0.0
	 * @param int $id ID of the greeter.
	 */
	public function output_fields_option_position( $id ) {
		$defaults = $this->get_option_position_defaults();
		$option   = $this->get_option_position( $id );

		echo '<div class="pagebox">';
		echo '<p><strong>' . esc_html__( 'Position settings', 'welcomewp' ) . '</strong></p>';
		echo '<ul>';

		foreach ( $defaults as $default ) {
			switch ( $default ) {
				case 'right-bottom':
					$label = esc_html__( 'Right', 'welcomewp' );
					break;
				case 'left-bottom':
					$label = esc_html__( 'Left', 'welcomewp' );
					break;
				default:
					$label = '';
			}

			// Avoid output any field if there is no label.
			if ( '' === $label ) {
				continue;
			}

			printf(
				'<li>
                    <input id="display-%1$s" value="%1$s" type="radio" name="%2$s" %3$s>
                    <label for="display-%1$s">%4$s</label>
                </li>',
				esc_attr( $default ),
				esc_attr( $this->option_name_position ),
				checked( $option, $default, false ),
				$label
			);
		}

		echo '</ul>';
		echo '</div><!-- .pagebox -->';
	}

	/**
	 * Return defaults for position option.
	 *
	 * @since 1.0.0
	 */
	private function get_option_colors_defaults() {
		return array(
			'background' => '',
			'text'       => '',
			'link'       => '',
		);
	}

	/**
	 * Return colors option.
	 *
	 * @since 1.0.0
	 * @param int $id ID of the greeter.
	 */
	public function get_option_colors( $id ) {
		// Get saved options.
		$option = get_post_meta( $id, $this->option_name_colors, true );
		// Validate saved options.
		$option = $this->validate_option_colors( $option );

		return $option;
	}

	/**
	 * Return a validated position option.
	 *
	 * @since 1.0.0
	 * @param string $option Option that needs validation.
	 */
	public function validate_option_colors( $option ) {
		// All available choices for the option.
		$defaults = $this->get_option_colors_defaults();

		// Make sure saved option exists; otherwise return default value.
		if ( ! $option ) {
			return $defaults;
		}

		return wp_parse_args(
			$option,
			$defaults
		);
	}

	/**
	 * Ouput colors option fields for metabox.
	 *
	 * @since 1.0.0
	 * @param int $id ID of the greeter.
	 */
	public function output_fields_option_colors( $id ) {
		$defaults = $this->get_option_colors_defaults();
		$option   = $this->get_option_colors( $id );

		echo '<div class="pagebox">';
		echo '<p><strong>' . esc_html__( 'Colors settings', 'welcomewp' ) . '</strong></p>';
		echo '<div class="wwp-form-group">';

		foreach ( $defaults as $key => $default ) {
			switch ( $key ) {
				case 'background':
					$label = esc_html__( 'Background color', 'welcomewp' );
					break;
				case 'text':
					$label = esc_html__( 'Text color', 'welcomewp' );
					break;
				case 'link':
					$label = esc_html__( 'Link color', 'welcomewp' );
					break;
				default:
					$label = '';
			}

			// Avoid output any field if there is no label.
			if ( '' === $label ) {
				continue;
			}

			printf(
				'<div class="wwp-form-group__item wwp-form-group__item--color">
                    <span class="wwp-form-group__item-label">%1$s</span>
                    <input id="%2$s-color" class="wwp-form__color-field" type="hidden" name="%3$s[%2$s]" value="%4$s" data-default-color="%5$s"/>
                </div>',
				$label,
				esc_attr( $key ),
				esc_attr( $this->option_name_colors ),
				esc_attr( $option[ $key ] ),
				esc_attr( $default )
			);
		}

		echo '</div><!-- .wwp-form-group -->';
		echo '</div><!-- .pagebox -->';
	}

	/**
	 * Set up onboarding process.
	 *
	 * @since 1.0.0
	 * @param int $id ID of the greeter.
	 */
	public function init_onboarding( $pointers ) {
		return array_merge(
			$pointers,
			array(
				$this->slug . '_new'           => array(
					'selector'   => '#menu-posts-greeter',
					'title'      => esc_html__( 'WelcomeWP', 'welcomewp' ),
					'text'       => esc_html__( 'Click `Add New` to start creating a welcome message.', 'welcomewp' ),
					'post_type'  => array( 'greeter' ),
					'icon_class' => 'dashicons-nametag',
					'width'      => 250,
				),

				$this->slug . '_title'         => array(
					'selector'   => '#titlewrap',
					'title'      => esc_html__( 'WelcomeWP', 'welcomewp' ),
					'text'       => esc_html__( 'Title serves as a name for your message. This title is not shown for website visitors. It is only for your own records.', 'welcomewp' ),
					'post_type'  => array( 'greeter' ),
					'icon_class' => 'dashicons-nametag',
					'width'      => 250,
					'edge'       => 'top',
					'align'      => 'left',
					'next'       => $this->slug . '_content',
				),

				$this->slug . '_content'       => array(
					'selector'   => '#wp-content-editor-container',
					'title'      => esc_html__( 'WelcomeWP', 'welcomewp' ),
					'text'       => esc_html__( 'This is a main content of your message. The message without main content does not appear on a website.', 'welcomewp' ),
					'post_type'  => array( 'greeter' ),
					'icon_class' => 'dashicons-nametag',
					'width'      => 250,
					'edge'       => 'top',
					'align'      => 'left',
					'next'       => $this->slug . '_excerpt',
					'show'       => 'close',
				),

				$this->slug . '_excerpt'       => array(
					'selector'   => '#postexcerpt',
					'title'      => esc_html__( 'WelcomeWP', 'welcomewp' ),
					'text'       => esc_html__( '(Optional): This is a summary of your message. Use summary to add an accordion (toggle) functionality to a message.', 'welcomewp' ),
					'post_type'  => array( 'greeter' ),
					'icon_class' => 'dashicons-nametag',
					'width'      => 250,
					'edge'       => 'bottom',
					'align'      => 'left',
					'next'       => $this->slug . '_image',
					'show'       => 'close',
				),

				$this->slug . '_image'         => array(
					'selector'   => '#postimagediv',
					'title'      => esc_html__( 'WelcomeWP', 'welcomewp' ),
					'text'       => esc_html__( '(Optional): This is a thumbnail of your message. Use thumbnail to add an accordion (toggle) functionality to a message.', 'welcomewp' ),
					'post_type'  => array( 'greeter' ),
					'icon_class' => 'dashicons-nametag',
					'width'      => 250,
					'align'      => 'middle',
					'edge'       => 'right',
					'next'       => $this->slug . '_configuration',
					'show'       => 'close',
				),

				$this->slug . '_configuration' => array(
					'selector'   => '#welcomewp_greeter_options',
					'title'      => esc_html__( 'WelcomeWP', 'welcomewp' ),
					'text'       => esc_html__( 'Additional options of a message. Use them to configure visibility of your message.', 'welcomewp' ),
					'post_type'  => array( 'greeter' ),
					'icon_class' => 'dashicons-nametag',
					'width'      => 250,
					'edge'       => 'bottom',
					'align'      => 'left',
					'show'       => 'close',
				),
			)
		);
	}
}
