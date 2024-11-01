<?php
/**
 * Tizra Connect Admin Views (controller)
 *
 * @class Tizra_Connect_Admin_Views
 * @author Slocum Studio
 * @version 1.0.0
 * @since 1.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Tizra_Connect_Admin_Views' ) ) {
	final class Tizra_Connect_Admin_Views {
		/**
		 * @var string
		 *
		 * @since 1.0.0
		 */
		public $version = '1.0.0';

		/**
		 * @var array
		 *
		 * @since 1.0.0
		 */
		public static $options = false;
		/**
		 * @var Tizra_Connect_Admin_Views, Instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $_instance;

		/**
		 * Function used to create instance of class.
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
		}


		/********************
		 * Helper Functions *
		 ********************/

		// TODO: Filters here to allow for templates to be over-ridden

		/**
		 * This function renders the shortcode insert content.
		 *
		 * @since 1.0.0
		 */
		public static function shortcode_insert_content() {
			require 'views/html-shortcode-insert-content.php';
		}


		/**
		 * This function renders the shortcode actions content.
		 *
		 * @since 1.0.0
		 */
		public static function shortcode_actions() {
			require 'views/html-shortcode-actions.php';
		}

		/**
		 * This function renders the shortcode insert content UnderscoreJS template.
		 *
		 * @since 1.0.0
		 */
		public static function js_shortcode_insert_content() {
			require_once 'views/js-shortcode-insert-content.php';
		}

		/**
		 * This function renders the shortcode actions UnderscoreJS template.
		 *
		 * @since 1.0.0
		 */
		public static function js_shortcode_actions() {
			require_once 'views/js-shortcode-actions.php';
		}
	}

	/**
	 * Create an instance of the Tizra_Connect_Admin_Views class.
	 *
	 * @since 1.0.0
	 */
	function Tizra_Connect_Admin_Views() {
		return Tizra_Connect_Admin_Views::instance();
	}

	Tizra_Connect_Admin_Views();
}