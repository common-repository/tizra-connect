<?php
/**
 * Tizra Connect Cron
 *
 * @class Tizra_Connect_Cron
 * @author Slocum Studio
 * @version 1.0.0
 * @since 1.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Tizra_Connect_Cron' ) ) {
	final class Tizra_Connect_Cron {
		/**
		 * @var string
		 *
		 * @since 1.0.0
		 */
		public $version = '1.0.0';

		/**
		 * @var Tizra_Connect_Cron, Instance of the class
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
			// Hooks
			add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) ); // Cron Schedules
			register_activation_hook( Tizra_Connect_Plugin::plugin_file(), array( $this, 'activate_tizra_connect_plugin' ) ); // Activation Hook
			register_deactivation_hook( Tizra_Connect_Plugin::plugin_file(), array( $this, 'deactivate_tizra_connect_plugin' ) ); // Deactivation Hook
			add_action( 'tizra_connect_refresh_api_data', array( $this, 'tizra_connect_refresh_api_data' ) ); // WP Cron - Tizra Connect - Refresh API Data
		}

		/**
		 * Include required core files used in admin and on the front-end.
		 *
		 * @since 1.0.0
		 */
		public function includes() {
			// TODO
		}

		/**
		 * This function adjusts the cron schedules.
		 *
		 * @since 1.0.0
		 */
		public function cron_schedules( $schedules ) {
			// If the quarter hour schedule doesn't exist (WordPress default schedules do not use spaces in names)
			if ( ! isset( $schedules['quarterhour'] ) )
				// Add the quarter hour schedule
				$schedules['quarterhour'] = array(
					'display' => __( 'Quarter Hour', 'tizra-connect' ),
					'interval' => 900
				);

			return $schedules;
		}

		/**
		 * This function runs on plugin activation.
		 *
		 * @since 1.0.0
		 */
		public function activate_tizra_connect_plugin() {
			// Schedule a WP cron event that starts 15 minutes from now and runs every quarter hour
			if ( ! wp_next_scheduled ( 'tizra_connect_refresh_api_data' ) )
				wp_schedule_event( ( time() + 900 ), 'quarterhour', 'tizra_connect_refresh_api_data' );
		}

		/**
		 * This function runs on plugin deactivation.
		 *
		 * @since 1.0.0
		 */
		public function deactivate_tizra_connect_plugin() {
			// Remove the WP cron event
			wp_clear_scheduled_hook( 'tizra_connect_refresh_api_data' );
		}

		/**
		 * This function runs when our WP Cron event runs every quarter hour.
		 *
		 * @since 1.0.0
		 */
		public function tizra_connect_refresh_api_data() {
			// Grab the Tizra Connect Data instance
			$tizra_connect_data = Tizra_Connect_Data();

			// Update the transient data
			$tizra_connect_data->update_transient_data();
		}
	}

	/**
	 * Create an instance of the Tizra_Connect_Cron class.
	 *
	 * @since 1.0.0
	 */
	function Tizra_Connect_Cron() {
		return Tizra_Connect_Cron::instance();
	}

	Tizra_Connect_Cron();
}
