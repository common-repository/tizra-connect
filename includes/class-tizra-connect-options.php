<?php
/**
 * Tizra Connect Options
 *
 * @class Tizra_Connect_Options
 * @author Slocum Studio
 * @version 1.0.0
 * @since 1.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Tizra_Connect_Options' ) ) {
	final class Tizra_Connect_Options {
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
		public static $option_name = 'tizra_connect';

		/**
		 * @var array
		 *
		 * @since 1.0.0
		 */
		public static $options = array();

		/**
		 * @var Tizra_Connect_Options, Instance of the class
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
			// Load Tizra Connect options
			self::$options = self::get_options();

			// Load required assets
			$this->includes();
		}

		/**
		 * Include required core files used in admin and on the front-end.
		 *
		 * @since 1.0.0
		 */
		private function includes() {
			// TODO
		}


		/**********************
		 * Internal Functions *
		 **********************/

		/**
		 * This function returns the current option values for Tizra Connect.
		 *
		 * @since 1.0.0
		 */
		public static function get_options() {
			return ( ! empty( self::$options ) ) ? self::$options : wp_parse_args( get_option( self::$option_name ), self::get_option_defaults() );
		}

		/**
		 * This function returns the default option values for Tizra Connect.
		 *
		 * @since 1.0.0
		 */
		public static function get_option_defaults() {
			$defaults = array(
				// Account
				'account' => array(
					// URL
					'url' => '',
					// Status
					'status' => ''
				)
			);

			return apply_filters( 'tizra_connect_options_defaults', $defaults );
		}
	}

	/**
	 * Create an instance of the Tizra_Connect_Options class.
	 *
	 * @since 1.0.0
	 */
	function Tizra_Connect_Options() {
		return Tizra_Connect_Options::instance();
	}

	Tizra_Connect_Options();
}
