<?php
/**
 * Tizra Connect
 *
 * @class Tizra_Connect
 * @author Slocum Studio
 * @version 1.0.0
 * @since 1.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'Tizra_Connect' ) ) {
	final class Tizra_Connect {
		/**
		 * @var string
		 *
		 * @since 1.0.0
		 */
		public static $version = '1.0.0';
		/**
		 * @var string
		 *
		 * @since 1.0.0
		 */
		public $shortcode = 'tizra_connect';

		/**
		 * @var Tizra_Connect, Instance of the class
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
			// Hooks
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 0 ); // Admin Enqueue Scripts (Very Early)
			add_action( 'media_buttons', array( $this, 'media_buttons' ) ); // Media Buttons
			add_action( 'admin_footer', array( $this, 'admin_footer' ) ); // Admin Footer
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) ); // WordPress Enqueue Scripts

			// Shortcodes
			add_shortcode( 'tizra_connect', array( $this, 'tizra_connect' ) ); // Tizra Connect Shortcode - [tizra_connect]
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
		 * This function enqueues scripts and styles in the admin.
		 *
		 * @since 1.0.0
		 */
		public function admin_enqueue_scripts( $hook ) {
			global $post;

			// Bail if we're not on a page that supports Tizra Connect
			if ( ! in_array( $hook, array( 'post.php', 'post-new.php', 'page.php', 'page-new.php' ) ) )
				return;

			// Grab the post ID
			$post_id = get_post_field( 'ID', $post );

			// Grab the Tizra Connect Data instance
			$tizra_connect_data = Tizra_Connect_Data();

			// Grab the Tizra collections data
			$collections = $tizra_connect_data->get_collections();

			// Thickbox
			add_thickbox();

			// Select2 Stylesheet
			wp_enqueue_style( 'tizra-connect-select2', Tizra_Connect_Plugin::plugin_url() . '/assets/css/select2/select2.min.css', false, Tizra_Connect::$version );

			/*
			 * Select2 Script
			 * License: MIT
			 * Copyright: Kevin Brown (https://github.com/kevin-brown), Igor Vaynberg (https://github.com/ivaynberg), and contributors (https://github.com/select2/select2/graphs/contributors)
			 *
			 * Due to potential conflicts that arise when multiple Select2 versions are enqueued on a page, we have
			 * to enqueue this script in the <head> element to ensure we can capture the correct jQuery Select2 function.
			 * This is also why we are hooking into admin_enqueue_scripts with a priority of 0.
			 */
			wp_enqueue_script( 'tizra-connect-select2', Tizra_Connect_Plugin::plugin_url() . '/assets/js/select2/select2.min.js', array( 'jquery' ), Tizra_Connect::$version );
			wp_add_inline_script( 'tizra-connect-select2', '( function ( $ ) { $.fn.tizra_connect_select2 = $.fn.select2; }( jQuery ) );' );

			// Tizra Connect Admin Stylesheet
			wp_enqueue_style( 'tizra-connect-admin', Tizra_Connect_Plugin::plugin_url() . '/assets/css/tizra-connect-admin.css', false, Tizra_Connect::$version );

			// Tizra Connect Admin Script
			wp_enqueue_script( 'tizra-connect-admin', Tizra_Connect_Plugin::plugin_url() . '/assets/js/tizra-connect-admin.js', array( 'wp-util', 'jquery-ui-core', 'underscore', 'wp-backbone', 'thickbox', 'tizra-connect-select2' ), Tizra_Connect::$version, true );
			wp_localize_script( 'tizra-connect-admin', 'tizra_connect', apply_filters( 'tizra_connect_admin_localize', array(
				// Collections
				'collections' => $collections,
				// ID
				'ID' => $post_id,
				// Localization
				'l10n' => array(
					'shortcode' => array(
						'add' => _x( '+', 'label for shortcode add', 'tizra-connect' ),
						'columns' => _x( 'Columns', 'label for columns', 'tizra-connect' ),
						'column' => _x( 'Column', 'label for column', 'tizra-connect' ),
						'insert' => _x( 'Insert', 'label for shortcode insert', 'tizra-connect' ),
						'none' => _x( 'We weren\'t able to find any Tizra collections. Please verify the Tizra Account URL and try again.', 'label for no collections', 'tizra-connect' ),
						'select' => _x( '&mdash; Select a Collection &mdash;', 'label for selecting a collection', 'tizra-connect' ),
						'select_columns' => _x( '&mdash; Select Number of Columns &mdash;', 'label for selecting number of columns', 'tizra-connect' ),
						'title' => _x( 'Insert a Tizra Collection', 'label for shortcode title', 'tizra-connect' )
					),
				),
				// Maximum Number of Columns
				'max_num_columns' => 6,
				// Shortcode
				'shortcode' => $this->shortcode
			), $this ) );
		}

		/**
		 * This function adds a media button to insert a Tizra shortcode into the editor.
		 *
		 * @since 1.0.0
		 */
		public function media_buttons() {
			global $pagenow;

			// Bail if we're not on the following pages
			if ( ! apply_filters( 'tizra_connect_display_media_buttons', in_array( $pagenow, array( 'post.php', 'post-new.php', 'page.php', 'page-new.php' ) ), $pagenow, $this ) )
				return;
		?>
			<a href="#" id="tizra-connect-add-shortcode" class="button tizra-connect-button tizra-connect-add-shortcode" title="<?php esc_attr_e( 'Add Tizra Collection', 'tizra-connect' ); ?>">
				<?php // TODO: Icon ?>
				<?php _e( 'Add Tizra Collection', 'tizra-connect' ); ?>
			</a>
		<?php
		}

		/**
		 * This function outputs scripts in the admin footer.
		 *
		 * @since 1.0.0
		 */
		public function admin_footer() {
			global $hook_suffix;
			// If we're on a page that supports Tizra Connect
			if ( in_array( $hook_suffix, array( 'post.php', 'post-new.php', 'page.php', 'page-new.php' ) ) ) {
				// [tizra_connect] shortcode Thickbox template
				$this->tizra_connect_shortcode_thickbox();

				// Shortcode insert UnderscoreJS Template
				Tizra_Connect_Admin_Views::js_shortcode_insert_content();

				// Shortcode actions UnderscoreJS Template
				Tizra_Connect_Admin_Views::js_shortcode_actions();
			}
		}

		/**
		 * This function enqueues scripts.
		 *
		 * @since 1.0.0
		 */
		public function wp_enqueue_scripts() {
			// Tizra Connect Stylesheet
			wp_enqueue_style( 'tizra-connect', Tizra_Connect_Plugin::plugin_url() . '/assets/css/tizra-connect.css', false, Tizra_Connect::$version );

		}


		/**************
		 * Shortcodes *
		 **************/

		/**
		 * This function renders the [tizra_connect] shortcode.
		 *
		 * @since 1.0.0
		 */
		public function tizra_connect( $attributes ) {
			// Output
			$output = '';

			// Grab the collection IDs
			$collections = ( isset( $attributes['collection'] ) ) ? explode( ',', $attributes['collection'] ) : ( ( isset( $attributes['collections'] ) ) ? explode( ',', $attributes['collections'] ) : array() );

			// Grab the titles
			$titles = ( isset( $attributes['title'] ) ) ? explode( '|', $attributes['title'] ) : ( ( isset( $attributes['titles'] ) ) ? explode( '|', $attributes['titles'] ) : array() );

			// Grab the columns
			$columns = ( isset( $attributes['columns'] ) ) ? explode( ',', $attributes['columns'] ) : ( ( isset( $attributes['columns'] ) ) ? explode( '|', $attributes['columns'] ) : array() );

			// TODO: Flag to determine if intro text should be displayed?

			// Bail if we don't have at least one collection
			if ( empty( $collections ) )
				return $output;

			// Start output buffering
			ob_start();

				// Loop through post IDs
				foreach ( $collections as $index => $collection_id ) {
					// Trim/sanitize the collection ID
					$collection_id = trim( $collection_id );

					// Grab the title for this collection
					$title = ( isset( $titles[$index] ) && ! empty( $titles[$index] ) ) ? $titles[$index] : '';

					// Grab the columns for this collection
					$columns = ( isset( $columns[$index] ) && ! empty( $columns[$index] ) ) ? $columns[$index] : 2;

					// Render this Tizra Collection
					$this->render( $collection_id, $title, $columns );
				}

			// Grab the output from the buffer
			$output .= ob_get_clean();

			return $output;
		}


		/********************
		 * Helper Functions *
		 ********************/

		/**
		 * This function renders a Tizra collection.
		 *
		 * @since 1.0.0
		 */
		public function render( $collection_id = false, $title = '', $columns = 2 ) {
			global $wp_query;

			// Grab the Tizra Connect options
			$options = Tizra_Connect_Options::get_options();

			// Bail if the URL status isn't valid
			if ( $options['account']['status'] !== 'valid' )
				return;

			// Grab the Tizra Connect Data instance
			$tizra_connect_data = Tizra_Connect_Data();

			// Grab the Tizra collections data
			$collections = $tizra_connect_data->get_collections();

			// Bail if the collection ID isn't valid
			if ( ! array_key_exists( $collection_id, $collections ) )
				return;

			// Grab the collection data
			$collection = $collections[$collection_id];

			// Transient name
			$transient_name = 'collection_' . $collection_id;

			// Get the "true" paged query variable from the main query (defaulting to 1)
			$paged = $query_args['paged'] = ( int ) get_query_var( 'paged' );

			// Use the paged query var if set
			if ( empty( $query_args['paged'] ) && isset( $wp_query->query['paged'] ) )
				$paged = $query_args['paged'] = ( int ) $wp_query->query['paged'];
			// Single post uses "page" instead of "paged"
			else if ( is_single() && ( int ) get_query_var( 'page' ) )
				$paged = $query_args['paged'] = ( int ) get_query_var( 'page' );
			// Otherwise assume page 1
			else if ( empty( $query_args['paged'] ) )
				$paged = $query_args['paged'] = 1;

			// If we're paged
			if ( $paged > 1 )
				// Append the page number to the transient name
				$transient_name .= '_' . $paged;

			// If this transient name doesn't exist
			if ( ! $tizra_connect_data->get_transient_name( $transient_name ) )
				// Add this transient name
				$tizra_connect_data->add_transient_name( $transient_name );

			// If we don't have transient data for this collection
			if ( ! ( $publications = $tizra_connect_data->get_transient( $transient_name ) ) ) {
				// Grab the Tizra Connect API instance
				$tizra_connect_api = Tizra_Connect_API();

				// Setup the arguments for the get_publications() call
				$args = array(
					'filter-collection-id' => $collection_id
				);

				// If we're paged
				if ( $paged > 1 )
					// Add the "start" argument
					$args['start'] = ( $tizra_connect_api->limit * ( $paged - 1 ) );

				// Grab the Tizra publications for this collection ID
				$publications = $tizra_connect_api->get_publications( $args );

				// If we have publications
				if ( $publications && ! empty( $publications ) && isset( $publications['publications'] ) && ! empty( $publications['publications'] ) )
					// Store the the Tizra publications in a transient
					$tizra_connect_data->set_transient( $transient_name, $publications );
			}

			// Sanitize the collection ID
			$collection_id = esc_attr( $collection_id );

			// CSS Classes
			$css_classes = array(
				'tizra-connect',
				'tizra-connect-' . $collection_id,
				'tizra-connect-wrap',
				'tizra-connect-wrap-' . $collection_id,
				'tizra-connect-collection-wrap',
				'tizra-connect-collection-wrap-' . $collection_id,
				'tizra-connect-row',
				'tizra-connect-flex',
				'tizra-connect-row-' . $columns . '-columns',
				'tizra-connect-flex-' . $columns . '-columns',
				'tizra-connect-' . $columns . '-columns'
			);

			// Sanitize CSS classes
			$css_classes = array_filter( $css_classes, 'sanitize_html_class' );

			// Title CSS Classes
			$title_css_classes = array(
				'tizra-connect-title',
				'tizra-connect-' . $collection_id . '-title',
				'tizra-connect-collection-title',
				'tizra-connect-collection' . $collection_id . '-title',
			);

			// Sanitize CSS classes
			$title_css_classes = array_filter( $title_css_classes, 'sanitize_html_class' );
		?>
			<div id="tizra-connect-<?php echo $collection_id; ?>-collection" class="<?php echo esc_attr( implode( ' ', $css_classes ) ); ?>">
				<h3 class="<?php echo esc_attr( implode( ' ', $title_css_classes ) ); ?>">
					<?php echo ( empty( $title ) ) ? $collection['Name'] : $title; ?>
				</h3>

				<?php // TODO: Intro message (Description) ?>

				<?php
					// If we have publications
					if ( isset( $publications['publications'] ) && ! empty( $publications['publications'] ) ) :
						// Column index
						$column_index = 1;

						// Loop through publications
						foreach ( $publications['publications'] as $publication_id => $publication ) :
							// Sanitize the publication ID
							$publication_id = esc_attr( $publication_id );

							// Create the publication URL
							$publication_url = esc_url( $options['account']['url'] . trailingslashit( ( ! empty( $publication['Tizra-customUrl'] ) ) ? $publication['Tizra-customUrl'] : $publication_id ) );

							// Publication CSS Classes
							$publication_css_classes = array(
								'tizra-connect-col',
								'tizra-connect-' . $publication_id . '-col',
								'tizra-connect-' . $publication_id . '-col-' . $column_index,
								'tizra-connect-' . $collection_id . '-' . $publication_id . '-col',
								'tizra-connect-' . $collection_id . '-' . $publication_id . '-col-' . $column_index,
								'tizra-connect-collection-col',
								'tizra-connect-collection-' . $publication_id . '-col-' . $column_index,
								'tizra-connect-collection-' . $collection_id . '-' . $publication_id . '-col',
								'tizra-connect-collection-' . $collection_id . '-' . $publication_id . '-col-' . $column_index,
							);

							// Sanitize CSS classes
							$publication_css_classes = array_filter( $publication_css_classes, 'sanitize_html_class' );
				?>
							<div class="<?php echo esc_attr( implode( ' ', $publication_css_classes ) ); ?>">
								<!-- Publication -->
								<article class="tizra-connect-article tizra-connect-collection-article tizra-connect-cf">
									<!-- Cover Image -->
									<div class="tizra-connect-cover-image tizra-connect-collection-cover-image tizra-connect-cf">
										<a href="<?php echo $publication_url; ?>">
											<img src="<?php echo esc_url( $options['account']['url'] . $publication_id . $publication['CoverImage'] ); ?>" class="" alt="<?php printf( __( '%1$s Cover Image', 'tizra-connect' ), $publication['Title'] ); ?>">
										</a>
									</div>
									<!-- End Cover Image -->

									<!-- Title -->
									<div class="tizra-connect-title-wrap tizra-connect-collection-title-wrap tizra-connect-cf">
										<h2 class="tizra-connect-title tizra-connect-collection-title tizra-connect-cf">
											<a href="<?php echo $publication_url; ?>">
												<?php echo $publication['Title']; ?>
											</a>
										</h2>
									</div>
									<!-- End Title-->

									<!-- Author -->
									<div class="tizra-connect-author tizra-connect-collection-author tizra-connect-authors tizra-connect-collection-authors tizra-connect-cf">
										<?php echo implode( ', ', $publication['Authors'] ); ?>
									</div>
									<!-- End Author -->
								</article>
							</div>
				<?php
							// Increase the column index
							$column_index ++;
						endforeach;

						// Output pagination links
						$this->get_pagination_links( $paged, $publications['max_num_pages'] );

					// Otherwise we don't have any publications
					else:
						// No Results CSS Classes
						$no_results_css_classes = array(
							'tizra-connect',
							'tizra-connect-' . $collection_id,
							'tizra-connect-wrap',
							'tizra-connect-wrap-' . $collection_id,
							'tizra-connect-collection-wrap',
							'tizra-connect-collection-wrap-' . $collection_id,
							'tizra-connect-no-results',
							'tizra-connect-no-results-' . $collection_id,
							'tizra-connect-' . $collection_id . '-no-results'
						);

						// Sanitize CSS classes
						$no_results_css_classes = array_filter( $no_results_css_classes, 'sanitize_html_class' );
				?>
						<div class="<?php echo esc_attr( implode( ' ', $no_results_css_classes ) ); ?>">
							<p><?php _e( 'No publications found. Please try again', 'tizra-connect' ); ?></p>
						</div>
				<?php
					endif;
				?>
			</div>
		<?php
		}

		/**
		 * This function returns or will echo pagination for the [tizra_connect] shortcode based on parameters.
		 *
		 * @since 1.0.0
		 */
		public function get_pagination_links( $paged = 1, $max_num_pages = 1, $echo = true ) {
			// Permalink structure
			$permalink_structure = get_option( 'permalink_structure' );

			$paginate_links_args = array(
				'base' => esc_url( get_pagenum_link() ) . '%_%', // %_% will be replaced with format below
				'format' => ( $permalink_structure ) ? 'page/%#%/' : '&paged=%#%', // %#% will be replaced with page number
				'current' => max( 1, $paged ),
				'total' => $max_num_pages, // Get total number of pages in current query
				'next_text' => __( 'Next &#8594;', 'tizra-connect' ),
				'prev_text' => __( '&#8592; Previous', 'tizra-connect' ),
				'type' => ( ! $echo ) ? 'array' : 'list'  // Output this as an array or unordered list
			);

			// Front page
			if ( is_front_page() )
				$paginate_links_args['format'] = ( $permalink_structure ) ? 'page/%#%/' : '/?paged=%#%';

			// Single post uses "page" instead of "paged"
			if ( is_single() ) {
				$paginate_links_args['base'] = esc_url( get_permalink() ) . '%_%';
				$paginate_links_args['format'] = ( get_option( 'permalink_structure' ) ) ? '%#%/' : '&page=%#%'; // %#% will be replaced with page number
			}

			$paginate_links_args = apply_filters( 'tizra_connect_paginate_links_args', $paginate_links_args, $paged, $max_num_pages, $echo, $this );

			$paginate_links = paginate_links( $paginate_links_args );

			if ( $echo )
				echo $paginate_links;
			else
				return $paginate_links;
		}


		/**********************
		 * Internal Functions *
		 **********************/

		/**
		 * This function renders the [tizra_connect] shortcode Thickbox template/elements.
		 *
		 * @since 1.0.0
		 */
		public function tizra_connect_shortcode_thickbox() {
		?>
			<?php // Thickbox requires an element within the wrapper ?>
			<div id="tizra-connect-shortcode-wrapper-container" class="tizra-connect-shortcode-wrapper-container">
				<div id="tizra-connect-shortcode-wrapper" class="tizra-connect-shortcode-wrapper">
					<div id="tizra-connect-shortcode-content-wrapper" class="tizra-connect-content-wrapper tizra-connect-shortcode-content-wrapper">
						<?php
							/*
							 * [tizra_connect] shortcode insert
							 */
							Tizra_Connect_Admin_Views::shortcode_insert_content();
						?>
					</div>

					<?php
						/*
						 * [tizra_connect] shortcode actions
						 */
						Tizra_Connect_Admin_Views::shortcode_actions();
					?>
				</div>
			</div>
		<?php
		}
	}

	/**
	 * Create an instance of the Tizra_Connect class.
	 *
	 * @since 1.0.0
	 */
	function Tizra_Connect() {
		return Tizra_Connect::instance();
	}

	Tizra_Connect();
}
