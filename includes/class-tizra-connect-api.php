<?php
/**
 * Tizra Connect API - Functionality for the Tizra API.
 *
 * @class Tizra_Connect_API
 * @author Slocum Studio
 * @version 1.0.0
 * @since 1.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Tizra_Connect_API' ) ) {
	final class Tizra_Connect_API {
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
		public $url = '';

		/**
		 * @var string
		 *
		 * @since 1.0.0
		 */
		public $api_url_endpoint = 'api/';

		/**
		 * @var int
		 *
		 * @since 1.0.0
		 */
		public $limit = 100;

		/**
		 * @var Tizra_Connect_API, Instance of the class
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
			// Grab Tizra Connect Options
			$tizra_connect_options = Tizra_Connect_Options::get_options();

			// Setup the URL
			$this->url = $tizra_connect_options['account']['url'];
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
		 * This function verifies a URL to ensure it is a valid Tizra instance.
		 *
		 * @since 1.0.0
		 */
		public function verify_url( $url = '' ) {
			// Set the URL to the value stored in the Tizra Connect options if we don't have a URL
			$url = ( $url === '' ) ? $this->url : $url;

			// Flag to determine if the URL is verified
			$is_url_verified = false;

			// Make a request to the Tizra API
			$request = $this->get( '', array(), $url );

			// If we have request data and the "tizra-api-version" data is set
			if ( $request && ! is_wp_error( $request ) && ! empty( $request ) && isset( $request['tizra-api-version'] ) )
				// Set the URL is verified flag
				$is_url_verified = true;

			$is_url_verified = apply_filters( 'tizra_connect_api_verify_url', $is_url_verified, $request, $url, $this );

			return $is_url_verified;
		}

		/**
		 * This function gets Tizra Collections.
		 *
		 * @since 1.0.0
		 */
		public function get_collections( $args = array(), $url = '' ) {
			// Set the URL to the value stored in the Tizra Connect options if we don't have a URL
			$url = ( $url === '' ) ? $this->url : $url;

			$args = apply_filters( 'tizra_connect_api_get_collections_args', $args, $url, $this );

			// Make a request to the Tizra API
			$request = $this->get( 'query/collections', $args, $url );

			// Collections data
			$collections = array();

			// If we have request data and we have results (collections)
			if ( $request && ! is_wp_error( $request ) && ! empty( $request ) && isset( $request['result'] ) && ! empty( $request['result'] ) )
				// Loop through the request results
				foreach ( $request['result'] as $result )
					// Add this result to the collections data
					$collections[sanitize_text_field( $result['tizra-id'] )] = $result['parsed-props'];

			$collections = apply_filters( 'tizra_connect_api_get_collections', $collections, $request, $args, $url, $this );

			return $collections;
		}

		/**
		 * This function counts Tizra publications.
		 *
		 * @since 1.0.0
		 */
		public function count_publications( $args = array(), $url = '' ) {
			// Set the URL to the value stored in the Tizra Connect options if we don't have a URL
			$url = ( $url === '' ) ? $this->url : $url;

			$args = apply_filters( 'tizra_connect_api_count_publications_args', $args, $url, $this );

			// Make a request to the Tizra API
			$request = $this->post( 'search-count', $args, $url );

			// Publications count
			$publications_count = array();

			// If we have request data and we have results (publications)
			if ( $request && ! is_wp_error( $request ) && ! empty( $request ) )
				// Sanitize the publications count
				$publications_count = ( int ) $request;

			$publications_count = apply_filters( 'tizra_connect_api_count_publications', $publications_count, $request, $args, $url, $this );

			return $publications_count;
		}

		/**
		 * This function gets Tizra publications.
		 *
		 * @since 1.0.0
		 */
		public function get_publications( $args = array(), $url = '' ) {
			// Set the URL to the value stored in the Tizra Connect options if we don't have a URL
			$url = ( $url === '' ) ? $this->url : $url;

			// Count the publications for the current request (before we add our default arguments)
			$publications_count = $this->count_publications( $args, $url );

			$args = apply_filters( 'tizra_connect_api_get_publications_args', wp_parse_args( $args, array(
				'fields' => array(
					'tizra-id',
					'parsed-props'
				),
				'props' => array(
					'Authors',
					'CoverImage',
					'Tizra-customUrl',
					'Title'
				),
				'sort-prop' => 'Title',
				'limit' => $this->limit,
				'start' => 0
			) ), $url, $this );

			// Make a request to the Tizra API
			$request = $this->get( 'search', $args, $url );

			// Publications data
			$publications = array(
				'count' => $publications_count,
				'limit' => $args['limit'],
				'publications' => array(),
				'start' => $args['start'],
				'max_num_pages' => ( ceil( $publications_count / $args['limit'] ) )
			);

			// If we have request data and we have results (publications)
			if ( $request && ! is_wp_error( $request ) && ! empty( $request ) && isset( $request['result'] ) && ! empty( $request['result'] ) )
				// Loop through the request results
				foreach ( $request['result'] as $result )
					// Add this result to the publications data
					$publications['publications'][sanitize_text_field( $result['tizra-id'] )] = array_map( array( $this, 'array_map_sanitize_parsed_prop' ), $result['parsed-props'] );

 			$publications = apply_filters( 'tizra_connect_api_get_publications', $publications, $request, $args, $url, $this );

			return $publications;
		}


		/********************
		 * Helper Functions *
		 ********************/

		/**
		 * This function makes a request.
		 *
		 * @since 1.0.0
		 */
		public function request( $api_endpoint = '', $type = 'GET', $args = array(), $url = '' ) {
			// Set the URL to the value stored in the Tizra Connect options if we don't have a URL
			$url = ( $url === '' ) ? $this->url : $url;

			// Ensure the URL has a trailing slash
			$url = trailingslashit( $url );

			// Ensure the API endpoint doesn't have a trailing slash
			$api_endpoint = ( $api_endpoint !== '' ) ? untrailingslashit( $api_endpoint ) : $api_endpoint;

			$api_endpoint = apply_filters( 'tizra_connect_api_request_api_endpoint', $api_endpoint, $type, $args, $url, $this );

			$type = apply_filters( 'tizra_connect_api_request_type', $type, $api_endpoint, $args, $url, $this );

			$args = apply_filters( 'tizra_connect_api_request_args', $args, $api_endpoint, $type, $url, $this );

			$url = apply_filters( 'tizra_connect_api_request_url', $url, $api_endpoint, $type, $args, $this );

			// Escape the URL after adding query arguments
			$url = esc_url_raw( add_query_arg( $args, $url . $this->api_url_endpoint . $api_endpoint ) );

			/*
			 * The Tizra API accepts (requires) multiple parameters in URLs. We're removing
			 * WordPress' array parameter declarations in the URL and ensuring the "singular"
			 * parameter key is used for multiple parameters with the same name.
			 *
			 * e.g. ?props%5B0%5D=Authors&props%5B1%5D=CoverImage&props%5B2%5D=Tizra-customUrl&props%5B3%5D=Title
			 * changes to ?props=Authors&props=CoverImage&props=Tizra-customUrl&props=Title
			 */
			$url = preg_replace( '/(%5B\d+?%5D)/', '', $url );

			// Ensure type is uppercase
			$type = strtoupper( $type );

			// Switch based on type
			switch ( $type ) {
				// POST
				case 'POST':
					// Make the GET request
					$response = wp_remote_post( $url, array(
						'httpversion' => '1.1',
						'sslverify' => false,
						'timeout' => 15
					) );

					// If we have an error
					if ( is_wp_error( $response ) )
						$response_body = $response;
					// Otherwise we don't have an error
					else
						$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
				break;

				// Default (GET)
				default:
					// Make the GET request
					$response = wp_remote_get( $url, array(
						'httpversion' => '1.1',
						'sslverify' => false,
						'timeout' => 15
					) );

					// If we have an error
					if ( is_wp_error( $response ) )
						$response_body = $response;
					// Otherwise we don't have an error
					else
						$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
				break;
			}

			$response_body = apply_filters( 'tizra_connect_api_request', $response_body, $response, $api_endpoint, $type, $args, $url, $this );

			return $response_body;
		}

		/**
		 * This function makes a GET request to the Tizra API.
		 *
		 * @since 1.0.0
		 */
		public function get( $api_endpoint = '', $args = array(), $url = '' ) {
			$api_endpoint = apply_filters( 'tizra_connect_api_get_api_endpoint', $api_endpoint, $args, $url, $this );

			$args = apply_filters( 'tizra_connect_api_get_args', $args, $api_endpoint, $url, $this );

			$url = apply_filters( 'tizra_connect_api_get_url', $url, $api_endpoint, $args, $this );

			// Make a request to the Tizra API
			$response = apply_filters( 'tizra_connect_api_get_response', $this->request( $api_endpoint, 'GET', $args, $url ), $api_endpoint, $args, $this );

			return $response;
		}

		/**
		 * This function makes a POST request to the Tizra API.
		 *
		 * @since 1.0.0
		 */
		public function post( $api_endpoint = '', $args = array(), $url = '' ) {
			$api_endpoint = apply_filters( 'tizra_connect_api_post_api_endpoint', $api_endpoint, $args, $url, $this );

			$args = apply_filters( 'tizra_connect_api_post_args', $args, $api_endpoint, $url, $this );

			$url = apply_filters( 'tizra_connect_api_post_url', $url, $api_endpoint, $args, $this );

			// Make a request to the Tizra API
			$response = apply_filters( 'tizra_connect_api_post_response', $this->request( $api_endpoint, 'POST', $args, $url ), $api_endpoint, $args, $this );

			return $response;
		}


		/**********************
		 * Internal Functions *
		 **********************/

		/**
		 * This function sanitizes parsed properties from the Tizra API. It is meant to be used as a callback
		 * in array_map().
		 *
		 * @since 1.0.0
		 */
		public function array_map_sanitize_parsed_prop( $prop ) {
			return is_array( $prop ) ? array_map( array( $this, 'array_map_sanitize_parsed_prop' ), $prop ) : sanitize_text_field( $prop );
		}
	}

	/**
	 * Create an instance of the Tizra_Connect_API class.
	 *
	 * @since 1.0.0
	 */
	function Tizra_Connect_API() {
		return Tizra_Connect_API::instance();
	}

	Tizra_Connect_API();
}
