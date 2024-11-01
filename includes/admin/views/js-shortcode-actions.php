<?php
/**
 * Shortcode Actions UnderscoreJS Template
 */
?>

<script type="text/template" id="tmpl-tizra-connect-shortcode-actions">
	<?php do_action( 'tizra_connect_shortcode_actions_inner_before' ); ?>

	<button class="button button-primary tizra-connect-button tizra-connect-shortcode-action-button" data-type="{{ data.button_type }}" <?php disabled( true ); ?>>{{{ data.label }}}</button>

	<span class="loading tizra-connect-loading spinner tizra-connect-spinner"></span>

	<?php do_action( 'tizra_connect_shortcode_actions_inner_after' ); ?>
</script>