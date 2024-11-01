<?php
/**
 * Shortcode Insert Content
 */

?>
<div id="tizra-connect-shortcode-insert-content" class="tizra-connect-shortcode-insert-content" data-type="tizra-connect-shortcode">
	<?php do_action( 'tizra_connect_shortcode_insert_content_before' ); ?>

	<?php
		/**
		 * Insert
		 */
	?>
	<div id="tizra-connect-shortcode-insert" class="tizra-connect-shortcode-insert tizra-connect-cf">
		<?php do_action( 'tizra_connect_shortcode_insert_content_insert' ); ?>
	</div>

	<?php do_action( 'tizra_connect_shortcode_insert_content_after' ); ?>
</div>