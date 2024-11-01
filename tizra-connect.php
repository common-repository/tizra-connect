<?php
/**
 * Plugin Name: Tizra Connect
 * Plugin URI: http://tizra.com/
 * Description: Connect to Tizra - The Agile, Integrated ePublishing Platform.
 * Version: 1.0.0
 * Author: Slocum Studio
 * Author URI: http://www.slocumstudio.com/
 * Requires at least: 4.4
 * Tested up to: 4.7.4
 * License: GPL2
 *
 * Text Domain: tizra-connect
 * Domain Path: /languages/
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Tizra_Connect' ) ) {
	final class Tizra_Connect_Plugin {
		/**
		 * @var string
		 *
		 * @since 1.0.0
		 */
		public static $version = '1.0.0';

		/**
		 * @var Tizra_Connect_Plugin, Instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $_instance;

		/**
		 * Function used to create instance of class.
		 *
		 * @since 1.0.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) )
				self::$_instance = new self();

			return self::$_instance;
		}


		/**
		 * This function sets up all of the actions and filters on instance. It also loads (includes)
		 * the required files and assets.
		 */
		function __construct() {
			// Load required assets
			$this->includes();

			// Hooks
			register_activation_hook( self::plugin_file(), array( $this, 'activate_tizra_connect_plugin' ) ); // Activation Hook
			register_deactivation_hook( self::plugin_file(), array( $this, 'deactivate_tizra_connect_plugin' ) ); // Deactivation Hook
		}

		/**
		 * Include required core files used in admin and on the front-end.
		 *
		 * @since 1.0.0
		 */
		private function includes() {
			// All
			include_once Tizra_Connect_Plugin::plugin_dir() . '/includes/class-tizra-connect.php'; // Tizra Connect
			include_once Tizra_Connect_Plugin::plugin_dir() . '/includes/class-tizra-connect-options.php'; // Tizra Connect Options
			include_once Tizra_Connect_Plugin::plugin_dir() . '/includes/admin/class-tizra-connect-admin.php'; // Tizra Connect Admin
			include_once Tizra_Connect_Plugin::plugin_dir() . '/includes/class-tizra-connect-data.php'; // Tizra Connect Data
			include_once Tizra_Connect_Plugin::plugin_dir() . '/includes/class-tizra-connect-cron.php'; // Tizra Connect Cron

			include_once Tizra_Connect_Plugin::plugin_dir() . '/includes/class-tizra-connect-api.php'; // Tizra Connect API

			// Admin Only
			if ( is_admin() ) { }

			// Front-End Only
			if ( ! is_admin() ) { }
		}

		/**
		 * This function runs on plugin activation.
		 *
		 * @since 1.0.0
		 */
		public function activate_tizra_connect_plugin() {
			// TODO
		}

		/**
		 * This function runs on plugin deactivation.
		 *
		 * @since 1.0.0
		 */
		public function deactivate_tizra_connect_plugin() {
			// TODO
		}


		/********************
		 * Helper Functions *
		 ********************/

		/**
		 * This function returns the plugin url for Tizra_Connect_Plugin without a trailing slash.
		 *
		 * @since 1.0.0
		 */
		public static function plugin_url() {
			return untrailingslashit( plugins_url( '', __FILE__ ) );
		}

		/**
		 * This function returns the plugin directory for Tizra_Connect_Plugin without a trailing slash.
		 *
		 * @since 1.0.0
		 */
		public static function plugin_dir() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * This function returns a reference to this Tizra_Connect_Plugin class file.
		 *
		 * @since 1.0.0
		 */
		public static function plugin_file() {
			return __FILE__;
		}

		/**
		 * This function returns a boolean result comparing against the current WordPress version.
		 *
		 * @since 1.0.0
		 */
		public static function wp_version_compare( $version, $operator = '>=' ) {
			global $wp_version;

			return version_compare( $wp_version, $version, $operator );
		}
	}

	/**
	 * Create an instance of the Tizra_Connect_Plugin class.
	 *
	 * @since 1.0.0
	 */
	function Tizra_Connect_Plugin() {
		return Tizra_Connect_Plugin::instance();
	}

	Tizra_Connect_Plugin();
}