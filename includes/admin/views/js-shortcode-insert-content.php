<?php
/**
 * Shortcode Insert Content UnderscoreJS Template
 */
?>

<script type="text/template" id="tmpl-tizra-connect-shortcode-insert">
	<?php do_action( 'tizra_connect_shortcode_insert_inner_before' ); ?>

		<#
			// If we have collections
			if ( data.collections && ! _.isEmpty( data.collections ) ) {
				/*
				 * Collections
				 */
		#>
				<?php do_action( 'tizra_connect_shortcode_insert_inner_shortcode_before' ); ?>

				<p class="tizra-connect-shortcode-setting">
					<label for="tizra-connect-shortcode-insert-collection" class="tizra-connect-label tizra-connect-shortcode-insert-label tizra-connect-shortcode-insert-collection-label"><strong><?php _e( 'Select a Tizra Collection', 'tizra-connect' ); ?></strong></label>
					<br />
					<select id="tizra-connect-shortcode-insert-collection" class="tizra-connect-select tizra-connect-select2 tizra-connect-shortcode-insert-select tizra-connect-shortcode-insert-collection-select" name="tizra_connect_shortcode_insert_collection">
						<option value="">{{{ tizra_connect.l10n.shortcode.select }}}</option>
						<#
							// Loop through the collections
							_.each( data.collections, function ( collection, collection_id ) {
						#>
								<option value="{{ collection_id }}">{{{ collection.Name }}}</option>
						<#
							} );
						#>
					</select>
				</p>

				<?php do_action( 'tizra_connect_shortcode_insert_inner_shortcode_after' ); ?>


		<#
				/*
				 * Title
				 */
		#>
				<?php do_action( 'tizra_connect_shortcode_insert_inner_title_before' ); ?>

				<p class="tizra-connect-shortcode-setting">
					<label for="tizra-connect-shortcode-insert-title" class="tizra-connect-label tizra-connect-shortcode-insert-label tizra-connect-shortcode-insert-title-label"><strong><?php _e( 'Add a title (optional)', 'tizra-connect' ); ?></strong></label>
					<br />
					<input type="text" id="tizra-connect-shortcode-insert-title" class="regular-text tizra-connect-input tizra-connect-shortcode-insert-input tizra-connect-shortcode-insert-title-input" name="tizra_connect_shortcode_insert_title" value="" />
				</p>

				<?php do_action( 'tizra_connect_shortcode_insert_inner_title_after' ); ?>


		<#
				/*
				 * Number of Columns
				 */
		#>
				<?php do_action( 'tizra_connect_shortcode_insert_inner_columns_before' ); ?>

				<p class="tizra-connect-shortcode-setting">
					<label for="tizra-connect-shortcode-insert-columns" class="tizra-connect-label tizra-connect-shortcode-insert-label tizra-connect-shortcode-insert-columns-label"><strong><?php _e( 'Select Number of Columns (defaults to 2)', 'tizra-connect' ); ?></strong></label>
					<br />
					<select id="tizra-connect-shortcode-insert-columns" class="tizra-connect-select tizra-connect-select2 tizra-connect-shortcode-insert-select tizra-connect-shortcode-insert-columns-select" name="tizra_connect_shortcode_insert_columns">
						<option value="">{{{ tizra_connect.l10n.shortcode.select_columns }}}</option>
						<#
							// Loop through the maximum number of columns
							for( i = 1; i <= data.max_num_columns; i++ ) {
						#>
								<option value="{{ i }}" <# if ( i === 2 ) { #> selected="selected" <# } #>>{{{ ( i === 1 ) ? i + ' ' + tizra_connect.l10n.shortcode.column : i + ' ' + tizra_connect.l10n.shortcode.columns }}}</option>
						<#
							}
						#>
					</select>
				</p>

				<?php do_action( 'tizra_connect_shortcode_insert_inner_columns_after' ); ?>
		<#
			}
			// Otherwise there are no collections to insert
			else {
		#>
			<?php do_action( 'tizra_connect_shortcode_insert_inner_no_collections_before' ); ?>

			<p class="tizra-connect-shortcode-notice">{{{ tizra_connect.l10n.shortcode.none }}}</p>

			<?php do_action( 'tizra_connect_shortcode_insert_inner_no_collections_before' ); ?>
		<#
			}
		#>

	<?php do_action( 'tizra_connect_shortcode_insert_inner_after' ); ?>
</script>