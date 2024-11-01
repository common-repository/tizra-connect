<?php
/**
 * Tizra Connect HTML
 *
 * @version 1.0.0
 * @since 1.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;
?>

<div class="wrap about-wrap tizra-connect-wrap">
	<h1><?php _e( 'Tizra Connect Options', 'tizra' ); ?></h1>

	<?php do_action( 'tizra_connect_options_notifications' ); ?>

	<?php
		settings_errors( 'general' ); // General Settings Errors
		settings_errors( Tizra_Connect_Options::$option_name ); // Tizra Connect Settings Errors
	?>

	<form method="post" action="options.php" enctype="multipart/form-data" id="tizra-connect-options-form">
		<?php settings_fields( Tizra_Connect_Options::$option_name ); ?>

		<?php
			/**
			 * Tizra Connect Account Settings
			 */
			do_settings_sections( Tizra_Connect_Options::$option_name . '_account' );
		?>

		<p class="submit">
			<?php submit_button( __( 'Save Options', 'tizra' ), 'primary', 'submit', false ); ?>
		</p>
	</form>
</div>