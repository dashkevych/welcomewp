<?php
/**
 * Plugin Name: WelcomeWP
 * Description: A simple WordPress plugin to create welcome messages.
 * Author:      Taras Dashkevych
 * Author URI:  https://tarascodes.com
 * Version:     1.0.4
 * Text Domain: welcomewp
 * Domain Path: languages
 *
 * WelcomeWP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WelcomeWP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * GNU General Public License: <http://www.gnu.org/licenses/>.
 *
 * @package    WelcomeWP
 * @author     Taras Dashkevych
 * @since      1.0.0
 * @license    GPL-2.0+
 */

/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

if ( ! class_exists( 'WelcomeWP' ) ) :
	/**
	 * Main WelcomeWP class.
	 *
	 * @since 1.0.0
	 * @package WelcomeWP
	 */
	final class WelcomeWP {
		/**
		 * The one true WelcomeWP.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance;

		/**
		 * Plugin version for enqueueing, etc.
		 *
		 * @since 1.0.0
		 * @var sting
		 */
		public $version = '1.0.3';

		/**
		 * Main WelcomeWP Instance.
		 *
		 * Insures that only one instance of WelcomeWP exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 * @return WelcomeWP
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WelcomeWP ) ) {

				self::$instance = new WelcomeWP();
				self::$instance->constants();
				self::$instance->load_textdomain();
				self::$instance->includes();

				$pointerplus = new PointerPlus( array( 'prefix' => 'welcomewp' ) );
				$dashboard   = new WelcomeWP_Dashboard();
				$greeter     = new WelcomeWP_Greeter( $dashboard );
			}

			return self::$instance;
		}

		/**
		 * Setup plugin constants.
		 *
		 * @since 1.0.0
		 */
		private function constants() {

			// Plugin version
			if ( ! defined( 'WELCOMEWP_VERSION' ) ) {
				define( 'WELCOMEWP_VERSION', $this->version );
			}

			// Plugin Folder Path
			if ( ! defined( 'WELCOMEWP_PLUGIN_DIR' ) ) {
				define( 'WELCOMEWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'WELCOMEWP_PLUGIN_URL' ) ) {
				define( 'WELCOMEWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'WELCOMEWP_PLUGIN_FILE' ) ) {
				define( 'WELCOMEWP_PLUGIN_FILE', __FILE__ );
			}
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @since 1.0.0
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'welcomewp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Include files.
		 *
		 * @since 1.0.0
		 */
		private function includes() {
			// Global includes.
			require_once WELCOMEWP_PLUGIN_DIR . 'includes/class-pointerplus.php';
			require_once WELCOMEWP_PLUGIN_DIR . 'includes/class-dashboard.php';
			require_once WELCOMEWP_PLUGIN_DIR . 'includes/class-message.php';
			require_once WELCOMEWP_PLUGIN_DIR . 'includes/class-greeter.php';
		}
	}
endif;

/**
 * The function which returns the one WelcomeWP instance.
 *
 * @since 1.0.0
 * @return object
 */
function welcomewp() {
	return WelcomeWP::instance();
}
welcomewp();
