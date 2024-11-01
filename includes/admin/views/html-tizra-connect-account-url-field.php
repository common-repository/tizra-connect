<?php
/**
 * Tizra Connect Account URL Field HTML
 *
 * @version 1.0.0
 * @since 1.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

// Grab Tizra Connect Options
$tizra_connect_options = Tizra_Connect_Options::get_options();
?>

<div class="input tizra-connect-input tizra-connect-account-input tizra-connect-account-url-input">
	<input type="text" id="tizra_connect_account_url" name="<?php echo esc_attr( Tizra_Connect_Options::$option_name ); ?>[account][url]" class="regular-text <?php echo ( $tizra_connect_options['account']['status'] === 'valid' ) ? 'valid tizra-connect-valid' : ( ( $tizra_connect_options['account']['status'] === 'invalid' ) ? 'invalid tizra-connect-invalid' : false ); ?>" value="<?php echo esc_attr( $tizra_connect_options['account']['url'] ); ?>" autocomplete="off" />
	<br />
	<span class="description"><?php _e( 'Enter the URL of your Tizra website.', 'tizra' ); ?></span>

	<?php
		// If we have a valid account status
		if ( $tizra_connect_options['account']['status'] === 'valid' ) :
	?>
			<br />
			<br />
			<span class="tizra-account-status valid tizra-connect-valid">
				<span class="dashicons dashicons-yes"></span>
				<?php _e( 'Your Tizra connection is active.', 'tizra-connect' ); ?>
			</span>
			<br />
			<br />
			<?php submit_button( __( 'Refresh Tizra API Data', 'tizra-connect' ), 'secondary', 'tizra-connect-refresh-api-data', false ); ?>
			<br />
			<span class="description">
				<?php _e( 'Use this button to manually refresh the Tizra API data.', 'tizra-connect' ); ?>
				<br />
				<br />
				<?php _e( '<strong>Note:</strong> The Tizra API data is automatically refreshed every <strong>15 minutes</strong>. Use this button if you are not seeing the newest data set.', 'tizra-connect' ); ?>
			</span>
	<?php
		// Otherwise if the account status is invalid
		elseif ( $tizra_connect_options['account']['status'] === 'invalid' ) :
	?>
			<br />
			<br />
			<span class="tizra-account-status invalid tizra-connect-invalid">
				<span class="dashicons dashicons-no"></span>
				<?php _e( 'Your Tizra connection is not active. Please enter a Tizra URL and try again.', 'tizra-connect' ); ?>
			</span>
	<?php
		endif;
	?>
</div>
