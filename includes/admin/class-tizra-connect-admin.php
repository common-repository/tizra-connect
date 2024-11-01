<?php
/**
 * Tizra Connect Admin
 *
 * @class Tizra_Connect_Admin
 * @author Slocum Studio
 * @version 1.0.0
 * @since 1.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Tizra_Connect_Admin' ) ) {
	final class Tizra_Connect_Admin {
		/**
		 * @var string
		 *
		 * @since 1.0.0
		 */
		public $version = '1.0.0';

		/**
		 * @var string
		 *
		 * @since 1.0.0
		 */
		public static $slug = 'tizra-connect';

		/**
		 * @var Tizra_Connect_Admin, Instance of the class
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
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );// Admin Menu
			add_action( 'admin_init', array( $this, 'admin_init' ) ); // Admin Init
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) ); // Admin Enqueue Scripts
		}

		/**
		 * Include required core files used in admin and on the front-end.
		 *
		 * @since 1.0.0
		 */
		private function includes() {
			include_once 'class-tizra-connect-admin-views.php'; // Tizra Connect Admin View Controller
		}

		/**
		 * This function registers menu items on the admin screen.
		 *
		 * @since 1.0.0
		 */
		public function admin_menu() {
			// Tizra
			add_menu_page( __( 'Tizra', 'tizra' ), __( 'Tizra', 'tizra' ), 'manage_options', Tizra_Connect_Admin::$slug, array( $this, 'tizra_connect' ) );
		}

		/**
		 * This function registers settings using the WordPress Settings API.
		 *
		 * @since 1.0.0
		 */
		public function admin_init() {
			// Register Setting
			register_setting( Tizra_Connect_Options::$option_name, Tizra_Connect_Options::$option_name, array( $this, 'sanitize_option' ) );

			// Account Section
			add_settings_section( 'tizra_connect_account_section', __( 'Account', 'tizra' ), array( $this, 'tizra_connect_account_section' ), Tizra_Connect_Options::$option_name . '_account' );
			add_settings_field( 'tizra_connect_account_url_field', __( 'URL', 'tizra' ), array( $this, 'tizra_connect_account_url_field' ), Tizra_Connect_Options::$option_name . '_account', 'tizra_connect_account_section' );
		}

		/**
		 * This function enqueues scripts and styles in the admin.
		 *
		 * @since 1.0.0
		 */
		public function admin_enqueue_scripts( $hook ) {
			// If this is the Tizra Connect admin page
			if ( $hook === 'toplevel_page_' . self::$slug )
				// Tizra Connect Admin
				wp_enqueue_style( 'tizra-connect-admin', Tizra_Connect_Plugin::plugin_url() . '/assets/css/tizra-connect-admin.css', false, $this->version );
		}


		/*****************
		 * Tizra Connect *
		 *****************/

		/**
		 * This function renders the Tizra Connect admin page.
		 *
		 * @since 1.0.0
		 */
		public function tizra_connect() {
			include_once Tizra_Connect_Plugin::plugin_dir() .'/includes/admin/views/html-tizra-connect.php'; // Tizra Connect HTML
		}


		/**************************
		 * WordPress Settings API *
		 **************************/

		/*
		 * Tizra Connect Account Section
		 */

		/**
		 * This function renders the Tizra Connect Account section.
		 *
		 * @since 1.0.0
		 */
		public function tizra_connect_account_section() {
			include_once Tizra_Connect_Plugin::plugin_dir() . '/includes/admin/views/html-tizra-connect-account-section.php'; // Tizra Connect Account Section HTML
		}

		/**
		 * This function renders the Tizra Connect Account URL field.
		 *
		 * @since 1.0.0
		 */
		public function tizra_connect_account_url_field() {
			include_once Tizra_Connect_Plugin::plugin_dir() . '/includes/admin/views/html-tizra-connect-account-url-field.php'; // Tizra Connect Account URL Field HTML
		}


		/**********************
		 * Internal Functions *
		 **********************/

		/**
		 * This function sanitizes data for the Tizra Connect settings.
		 *
		 * @since 1.0.0
		 */
		public function sanitize_option( $input ) {
			// Grab the option defaults
			$option_defaults = Tizra_Connect_Options::get_option_defaults();

			// Parse arguments, replacing defaults with user input
			$input = wp_parse_args( $input, $option_defaults );

			// Grab existing options
			$options = Tizra_Connect_Options::get_options();

			// Grab the Tizra Connect API instance
			$tizra_connect_api = Tizra_Connect_API();


			// TODO: If the URL changes, delete all existing transients

			/*
			 * Account
			 */

			$input['account']['url'] = ( $input['account']['url'] ) ? esc_url( trailingslashit( $input['account']['url'] ) ) : ''; // Account URL

			// If the account URL has changed
			if ( $input['account']['url'] !== $options['account']['url'] ) {
				// Reset the account status
				$input['account']['status'] = $option_defaults['account']['status'];

				$is_url_verified = ( $input['account']['url'] && $tizra_connect_api->verify_url( $input['account']['url'] ) );

				// Set the account status
				$input['account']['status'] = ( $input['account']['url'] && $is_url_verified ) ? 'valid' : ( ( $input['account']['url'] ) ? 'invalid' : '' );

				// If the URL is verified
				if ( $is_url_verified ) {
					// Grab the Tizra Connect Data instance
					$tizra_connect_data = Tizra_Connect_Data();

					// Store the the Tizra collections in a transient
					$tizra_connect_data->set_transient( 'collections', $tizra_connect_api->get_collections( array(), $input['account']['url'] ) );
				}
			}
			// Otherwise the URL hasn't changed
			else
				// Set the account status to the current value
				$input['account']['status'] = $options['account']['status'];


			/*
			 * Tizra API - Refresh Data
			 */
			if ( isset( $_POST['tizra-connect-refresh-api-data'] ) ) {
				// Grab the Tizra Connect Data instance
				$tizra_connect_data = Tizra_Connect_Data();

				// Update the transient data
				$tizra_api_data_refreshed = $tizra_connect_data->update_transient_data( array(), $input['account']['url'] );

				// If the Tizra API data was refreshed
				if ( $tizra_api_data_refreshed === true )
					// Add the ("updated") settings error
					add_settings_error( Tizra_Connect_Options::$option_name, 'settings_updated', __( 'The Tizra API data has been refreshed.', 'tizra-connect' ), 'updated' );
				// Otherwise if some of the Tizra API data was refreshed
				else if ( $tizra_api_data_refreshed === 'some' )
					// Add the ("error") settings error
					add_settings_error( Tizra_Connect_Options::$option_name, 'settings_updated', __( 'Some of the Tizra API data could not be refreshed at this time. Please try again later.', 'tizra-connect' ) );
				// Otherwise the Tizra API data was refreshed
				else
					// Add the ("error") settings error
					add_settings_error( Tizra_Connect_Options::$option_name, 'settings_updated', __( 'The Tizra API data could not be refreshed at this time. Please try again later.', 'tizra-connect' ) );
			}

			return $input;
		}
	}

	/**
	 * Create an instance of the Tizra_Connect_Admin class.
	 *
	 * @since 1.0.0
	 */
	function Tizra_Connect_Admin() {
		return Tizra_Connect_Admin::instance();
	}

	Tizra_Connect_Admin();
}
