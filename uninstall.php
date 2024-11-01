<?php
/**
 * Tizra Connect Uninstall
 *
 * @author Slocum Studio
 * @version 1.0.0
 * @since 1.0.0
 */

// Bail if not actually uninstalling
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit;

/**
 * Includes
 */
include_once 'tizra-connect.php'; // Tizra Connect


/**
 * Uninstall
 */

// Fetch Conductor options
$tizra_connect_options = Tizra_Connect_Options::get_options();

// Grab the Tizra Connect Data instance
$tizra_connect_data = Tizra_Connect_Data();

// Grab the transient names
$transient_names = $tizra_connect_data->get_transient_names();

// If we have transient names
if ( ! empty( $transient_names ) )
	// Loop through the transient names
	foreach ( $transient_names as $transient_name_id => $transient_name ) {
		// Switch based on transient name ID
		switch ( $transient_name_id ) {
			// Collections
			case 'collections':
				// Delete the the Tizra collections in a transient
				$tizra_connect_data->delete_transient( 'collections' );
			break;

			// Default
			default:
				// Replace the transient prefix in the transient name ID
				$transient_name_id = str_replace( $tizra_connect_data->transient_prefix, '', $transient_name_id );

				// Explode the transient name ID
				list( $transient_type, $transient_component_id ) = explode( '_', $transient_name_id );

				// Switch based on transient type
				switch ( $transient_type ) {
					// Collection
					case 'collection':
						// If we have publications stored in transient data for this collection ID
						if ( ( $transient_publications = $tizra_connect_data->get_transient( $transient_type . '_' . $transient_component_id ) ) ) {
							// Grab the maximum number of posts from the current transient data
							$current_max_num_pages = $transient_publications['max_num_pages'];

							// If we have more than one page in the original transient data
							if ( $current_max_num_pages > 1 )
								// Loop through pages
								for ( $i = 2; $i <= $current_max_num_pages; $i++ ) {
									// Generate the transient name for this page
									$transient_name = $transient_type . '_' . $transient_component_id . '_' . $i;

									// If this transient name doesn't exist
									if ( ! $tizra_connect_data->get_transient_name( $transient_name ) )
										// Add this transient name
										$tizra_connect_data->add_transient_name( $transient_name );

									// Delete this transient
									$tizra_connect_data->delete_transient( $transient_name );
								}
						}


						// Delete this transient
						$tizra_connect_data->delete_transient( $transient_type . '_' . $transient_component_id );
					break;
				}
			break;
		}
	}

// Delete the Tizra Connect option
delete_option( Tizra_Connect_Options::$option_name );