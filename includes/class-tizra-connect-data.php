<?php
/**
 * Tizra Connect Data - Functionality for the Tizra Data.
 *
 * @class Tizra_Connect_Data
 * @author Slocum Studio
 * @version 1.0.0
 * @since 1.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Tizra_Connect_Data' ) ) {
	final class Tizra_Connect_Data {
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
		public $transient_prefix = 'tizra_connect_';

		/**
		 * @var array
		 *
		 * @since 1.0.0
		 */
		public $transients = array();

		/**
		 * @var Tizra_Connect_Data, Instance of the class
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
			// TODO: Move to 'init' action and add a filter
			// Setup the transient names
			$this->transients = array(
				'collections' => $this->transient_prefix . 'collections',
			);

			// If we have collections transient data
			if ( ( $collections = $this->get_transient( 'collections' ) ) )
				// Loop through collections
				foreach ( array_keys( $collections ) as $collection_id )
					// Add this collection to the transient names
					$this->transients['collection_' . $collection_id] = $this->transient_prefix . 'collection_' . $collection_id;
		}

		/**
		 * Include required core files used in admin and on the front-end.
		 *
		 * @since 1.0.0
		 */
		public function includes() {
			// TODO
		}


		/********************
		 * Helper Functions *
		 ********************/

		/**
		 * This function gets transient data.
		 *
		 * @since 1.0.0
		 */
		public function get_transient( $key ) {
			// TODO: Filter(s), one general, one for specific key
			return get_transient( $this->get_transient_name( $key ) );
		}

		/**
		 * This function sets transient data.
		 *
		 * @since 1.0.0
		 */
		public function set_transient( $key, $data, $expiration = 0 ) {
			// TODO: Filter(s), one general, one for specific key
			return set_transient( $this->get_transient_name( $key ), $data, $expiration );
		}

		/**
		 * This function gets all transient names.
		 *
		 * @since 1.0.0
		 */
		public function get_transient_names() {
			// TODO: Filter
			return $this->transients;
		}

		/**
		 * This function gets a transient name by $key.
		 *
		 * @since 1.0.0
		 */
		public function get_transient_name( $key ) {
			// TODO: Filter
			return ( isset( $this->transients[$key] ) ) ? $this->transients[$key] : false;
		}

		/**
		 * This function adds a transient name.
		 *
		 * @since 1.0.0
		 */
		public function add_transient_name( $key ) {
			$this->transients[$key] = $this->transient_prefix . $key;
		}

		/**
		 * This function deletes all transient data associated with the Tizra API.
		 *
		 * @since 1.0.0
		 */
		public function delete_transients() {
			// Grab the transient names
			$transient_names = $this->get_transient_names();

			// Loop through transient names
			foreach ( $transient_names as $transient_name )
				// Delete transient data
				delete_transient( $transient_name );
		}

		/**
		 * This function deletes one transient data associated with the Tizra API.
		 *
		 * @since 1.0.0
		 */
		public function delete_transient( $key ) {
			// Grab the transient names
			$transient_names = $this->get_transient_names();

			// Delete transient data
			delete_transient( $transient_names[$key] );
		}

		/**
		 * This function gets Tizra Collections from transient data.
		 *
		 * @since 1.0.0
		 */
		public function get_collections() {
			// If we have Tizra Collections in transient data
			if ( $tizra_collections = $this->get_transient( 'collections' ) ) {
				// Sort the Tizra Collections data alphabetically
				uasort( $tizra_collections, array( $this, 'uasort_tizra_collections_alphabetically' ) );
			}

			// If we don't have any Tizra Collections data
			if ( $tizra_collections === false )
				// Set the Tizra Collections to an empty array
				$tizra_collections = array();

			return $tizra_collections;
		}

		/**
		 * This function gets Tizra Collections from transient data.
		 *
		 * @since 1.0.0
		 */
		public function update_transient_data( $specific_transient_names = array(), $url = '' ) {
			// Grab the Tizra Connect API instance
			$tizra_connect_api = Tizra_Connect_API();

			// Set the URL to the value stored in the Tizra Connect options if we don't have a URL
			$url = ( $url === '' ) ? $tizra_connect_api->url : $url;

			// Flag to determine whether or not Tizra API data has been refreshed
			$tizra_api_data_refreshed = false;

			// Grab the transient names
			$transient_names = ( ! empty( $specific_transient_names ) ) ? $specific_transient_names : $this->get_transient_names();

			// If we have transient names
			if ( ! empty( $transient_names ) )
				// Loop through the transient names
				foreach ( $transient_names as $transient_name_id => $transient_name ) {
					// Switch based on transient name ID
					switch ( $transient_name_id ) {
						// Collections
						case 'collections':
							// If we have collections from the Tizra API
							if ( ( $collections = $tizra_connect_api->get_collections( array(), $url ) ) ) {
								// Store the the Tizra collections in a transient
								$this->set_transient( 'collections', $collections );

								// Set the refreshed data flag
								$tizra_api_data_refreshed = ( $tizra_api_data_refreshed === false ) ? true : $tizra_api_data_refreshed;
							}
							// Otherwise if the refreshed data flag is set
							else if ( $tizra_api_data_refreshed === true )
								// Set the refreshed data flag to "some"
								$tizra_api_data_refreshed = 'some';
						break;

						// Default
						default:
							// Replace the transient prefix in the transient name ID
							$transient_name_id = str_replace( $this->transient_prefix, '', $transient_name_id );

							// Explode the transient name ID
							list( $transient_type, $transient_component_id ) = explode( '_', $transient_name_id );

							// Switch based on transient type
							switch ( $transient_type ) {
								// Collection
								case 'collection':
									// If we have publications stored in transient data for this collection ID
									if ( ( $transient_publications = $this->get_transient( $transient_type . '_' . $transient_component_id ) ) ) {
										// Grab the maximum number of posts from the current transient data
										$current_max_num_pages = $transient_publications['max_num_pages'];

										// Grab the Tizra publications for this collection ID
										$publications = $tizra_connect_api->get_publications( array(
											'filter-collection-id' => $transient_component_id
										), $url );

										// If we have publications
										if ( $publications && ! empty( $publications ) && isset( $publications['publications'] ) && ! empty( $publications['publications'] ) ) {
											// Store the the Tizra publications in a transient
											$this->set_transient( $transient_type . '_' . $transient_component_id, $publications );

											// Set the refreshed data flag
											$tizra_api_data_refreshed = ( $tizra_api_data_refreshed === false ) ? true : $tizra_api_data_refreshed;

											// If we have more than one page in the original transient data
											if ( $current_max_num_pages > 1 ) {
												// Grab the maximum number of posts from the new transient data
												$new_max_num_pages = $publications['max_num_pages'];

												// Loop through pages
												for ( $i = 2; $i <= $current_max_num_pages; $i++ ) {
													// Generate the transient name for this page
													$transient_name = $transient_type . '_' . $transient_component_id . '_' . $i;

													// If this transient name doesn't exist
													if ( ! $this->get_transient_name( $transient_name ) )
														// Add this transient name
														$this->add_transient_name( $transient_name );

													// If this is a page that no longer exists
													if ( $i > $new_max_num_pages )
														// Delete this transient
														$this->delete_transient( $transient_name );
													// Otherwise this page exists
													else {
														// Grab the Tizra publications for this collection ID
														$page_publications = $tizra_connect_api->get_publications( array(
															'filter-collection-id' => $transient_component_id,
															'start' => (  $tizra_connect_api->limit * ( $i- 1 ) )
														), $url );

														// If we have publications
														if ( $page_publications && ! empty( $page_publications ) && isset( $page_publications['publications'] ) && ! empty( $page_publications['publications'] ) ) {
															// Store the the Tizra publications in a transient
															$this->set_transient( $transient_name, $page_publications );

															// Set the refreshed data flag
															$tizra_api_data_refreshed = ( $tizra_api_data_refreshed === false ) ? true : $tizra_api_data_refreshed;
														}
													}
												}
											}
										}
										// Otherwise if the refreshed data flag is set
										else if ( $tizra_api_data_refreshed === true )
											// Set the refreshed data flag to "some"
											$tizra_api_data_refreshed = 'some';
									}
								break;
							}
						break;
					}
				}

			return $tizra_api_data_refreshed;
		}


		/**********************
		 * Internal Functions *
		 **********************/

		/**
		 * This function is used as a uasort() callback and sorts Tizra Collections alphabetically.
		 *
		 * @since 1.0.0
		 */
		public function uasort_tizra_collections_alphabetically( $a, $b ) {
			return strcasecmp( $a['Name'], $b['Name'] );
		}
	}

	/**
	 * Create an instance of the Tizra_Connect_Data class.
	 *
	 * @since 1.0.0
	 */
	function Tizra_Connect_Data() {
		return Tizra_Connect_Data::instance();
	}

	Tizra_Connect_Data();
}
