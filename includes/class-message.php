<?php
/**
 * Front-end appearence of data from CPT.
 *
 * @package    WelcomeWP
 * @author     Taras Dashkevych
 * @since      1.0.0
 */
class WelcomeWP_Message {
	/**
	 * ID of the message.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $id;

	/**
	 * Global options (set via Settings).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $options_global;

	/**
	 * Local options (set via Greeter).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $options_local;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id             = 0;
		$this->options_global = array();
		$this->options_local  = array();
	}

	/**
	 * Check if there is a need of a message.
	 *
	 * @since 1.0.0
	 */
	private function is_message() {
		if ( $this->id ) {
			return true;
		}

		return false;
	}

	/**
	 * Load front-end styles and scripts for active message on a page.
	 *
	 * @since 1.0.0
	 */
	private function load_assets() {
		if ( ! $this->is_message() ) {
			return;
		}

		// Use WordPress API to load scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style' ) );
	}

	/**
	 * Load front-end styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_style() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Load base styles of a message.
		wp_enqueue_style(
			'welcomewp-base',
			WELCOMEWP_PLUGIN_URL . "assets/css/public/base{$suffix}.css",
			array(),
			'1.0.4'
		);

		// Load custom styles of a message.
		wp_add_inline_style(
			'welcomewp-base',
			$this->get_inline_styles()
		);

		// Load JavaScript functionality of a message.
		wp_enqueue_script(
			'welcomewp-script',
			WELCOMEWP_PLUGIN_URL . "assets/js/public/base{$suffix}.js",
			array(),
			'1.0.4',
			true
		);
	}

	/**
	 * Return front-end inline styles based on local options.
	 *
	 * @since 1.0.0
	 */
	public function get_inline_styles() {
		$styles = '';

		// Set a background color if needed.
		if ( isset( $this->options_local['color']['background'] ) && '' !== $this->options_local['color']['background'] ) {
			$styles .= sprintf(
				'--wwp--color--background: %s;',
				sanitize_hex_color( $this->options_local['color']['background'] )
			);
		}

		// Set a text color if needed.
		if ( isset( $this->options_local['color']['text'] ) && '' !== $this->options_local['color']['text'] ) {
			$styles .= sprintf(
				'--wwp--color--text: %s;',
				sanitize_hex_color( $this->options_local['color']['text'] )
			);
		}

		// Set a link color if needed.
		if ( isset( $this->options_local['color']['link'] ) && '' !== $this->options_local['color']['link'] ) {
			$styles .= sprintf(
				'--wwp--color--link: %s;',
				sanitize_hex_color( $this->options_local['color']['link'] )
			);
		}

		if ( '' !== $styles ) {
			$styles = sprintf(
				'.welcomewp-message { %s }',
				esc_attr( $styles )
			);
		}

		return $styles;
	}

	/**
	 * Display a message.
	 *
	 * @since 1.0.0
	 */
	public function display() {
		if ( ! $this->is_message() ) {
			return;
		}

		// Output HTML markup of a message.
		$this->output();
	}

	/**
	 * Output message markup.
	 *
	 * @since 1.0.0
	 */
	private function output() {
		// HTML markup of a message.
		$message = $this->get_html_markup();

		// Make sure there is a markup.
		if ( '' === $message ) {
			return;
		}

		echo $message;
	}

	/**
	 * Return markup of a message.
	 *
	 * @since 1.0.0
	 */
	public function get_html_markup() {
		$message         = '';
		$message_header  = $this->get_header();
		$message_content = $this->get_content();
		$message_footer  = $this->get_footer();

		if ( '' !== $message_content ) {
			if ( '' === $message_header ) {
				$message = sprintf(
					'<aside id="welcomewp-message" class="%1$s" %2$s><div class="welcomewp-message__inner">%3$s%4$s</div></aside>',
					join( ' ', $this->get_css_class() ),
					join( ' ', $this->get_attributes() ),
					$message_content,
					$message_footer
				);
			} else {
				$message = sprintf(
					'<aside id="welcomewp-message" class="%1$s" %2$s><details class="welcomewp-message__inner">%3$s%4$s%5$s</details></aside>',
					join( ' ', $this->get_css_class() ),
					join( ' ', $this->get_attributes() ),
					$message_header,
					$message_content,
					$message_footer
				);
			}
		}

		return $message;
	}

	/**
	 * Return message header.
	 *
	 * @since 1.0.0
	 */
	private function get_header() {
		$message_header              = '';
		$message_header_thumbnail    = '';
		$message_header_excerpt      = '';
		$message_header_close_button = $this->get_close_button( $type = 'minimal' );

		if ( has_post_thumbnail( $this->id ) ) {
			$message_header_thumbnail = sprintf(
				'<figure class="welcomewp-message__thumb">%s</figure>',
				get_the_post_thumbnail( $this->id, array( 64, 64 ) )
			);
		}

		if ( has_excerpt( $this->id ) ) {
			$message_header_excerpt = sprintf(
				'<span class="welcomewp-message__excerpt">%s</span>',
				get_the_excerpt( $this->id )
			);
		}

		if ( '' !== $message_header_thumbnail && '' === $message_header_excerpt ) {
			return sprintf(
				'<summary class="welcomewp-message__header">%s</summary>',
				$message_header_thumbnail
			);
		}

		if ( '' !== $message_header_excerpt ) {
			return sprintf(
				'<summary class="welcomewp-message__header">%1$s%2$s%3$s</summary>',
				$message_header_thumbnail,
				$message_header_excerpt,
				$message_header_close_button
			);
		}

		return $message_header;
	}

	/**
	 * Return message content.
	 *
	 * @since 1.0.0
	 */
	private function get_content() {
		$message_main = '';

		if ( '' !== get_post_field( 'post_content', $this->id ) ) {
			$message_main = sprintf(
				'<div class="welcomewp-message__content">%s</div>',
				apply_filters( 'the_content', get_post_field( 'post_content', $this->id ) )
			);
		}

		return $message_main;
	}

	/**
	 * Return message footer.
	 *
	 * @since 1.0.0
	 */
	private function get_footer() {
		return sprintf(
			'<div class="welcomewp-message__footer">%s</div>',
			$this->get_close_button()
		);
	}

	/**
	 * Return close button of the message.
	 *
	 * @param string $type Preview of the button (full or minimal).
	 * @since 1.0.0
	 */
	private function get_close_button( $type = 'full' ) {
		$icon  = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M23.954 21.03l-9.184-9.095 9.092-9.174-2.832-2.807-9.09 9.179-9.176-9.088-2.81 2.81 9.186 9.105-9.095 9.184 2.81 2.81 9.112-9.192 9.18 9.1z"/></svg>';
		$id    = esc_attr( 'welcomewp-message__close-button-' . $type );
		$label = esc_html__( 'Close', 'welcomewp' );

		if ( 'minimal' === $type ) {
			$label = sprintf(
				'<span class="sr-only">%s</span>',
				$label
			);
		}

		return sprintf(
			'
            <button id="%1$s" class="welcomewp-message__close-button" type="button">
                %2$s %3$s
            </button>
            ',
			$id,
			$icon,
			$label
		);
	}

	/**
	 * CSS classes of a message.
	 *
	 * @since 1.0.0
	 */
	private function get_css_class() {
		// Default classes.
		$classes = array(
			'welcomewp-message',
		);

		// Add class if Greeter has a thumbnail.
		if ( has_post_thumbnail( $this->id ) ) {
			$classes[] = 'has-thumbnail';
		}

		// Add class if Greeter has an excerpt.
		if ( has_excerpt( $this->id ) ) {
			$classes[] = 'has-excerpt';
		}

		return $classes;
	}

	/**
	 * Return message specific attributes.
	 *
	 * @since 1.0.0
	 */
	private function get_attributes() {
		$attributes = array(
			'data-message-id="' . esc_attr( $this->id ) . '"',
			'data-message-position="' . esc_attr( $this->options_local['position'] ) . '"',
			'data-message-expire-time="' . esc_attr( $this->options_global['cookie']['value'] ) . '|' . esc_attr( $this->options_global['cookie']['range'] ) . '"',
		);

		return $attributes;
	}

	/**
	 * Return the current display (view) page.
	 *
	 * @since 1.0.0
	 */
	public function get_current_display() {
		$current_display = 'all';

		if ( is_singular() ) {
			return 'singular';
		}

		if ( is_home() || is_archive() ) {
			return 'archive';
		}

		return $current_display;
	}

	/**
	 * Return the current display (view) page.
	 *
	 * @param array $options message options.
	 * @since 1.0.0
	 */
	public function configure( $options ) {
		// Set message id, based on information from Greeter.
		if ( isset( $options['id'] ) ) {
			$this->id = $options['id'];
		}

		// Set global options (set via Settings) for the message.
		if ( isset( $options['global'] ) ) {
			$this->options_global = $options['global'];
		}

		// Set global options (set via Greeter) for the message.
		if ( isset( $options['local'] ) ) {
			$this->options_local = $options['local'];
		}

		// Load styles and scripts if needed.
		$this->load_assets();
	}
}
