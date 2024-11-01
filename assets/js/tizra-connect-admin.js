/**
 * Tizra Connect Admin
 */

var tizra_connect = tizra_connect || {};

( function ( wp, $ ) {
	"use strict";

	var	Tizra_Connect_Shortcode_Model,
		Tizra_Connect_Shortcode_Collection,
		Tizra_Connect_Shortcode_View,
		Tizra_Connect_Shortcode_Actions_View,
		Tizra_Connect_Shortcode_Insert_View;

	// Defaults
	if ( ! tizra_connect.hasOwnProperty( 'Backbone' ) ) {
		tizra_connect.Backbone = {
			Views: {},
			Models: {},
			Collections: {},
			instances: {
				models: {
					shortcode: []
				},
				collections: {
					shortcode: []
				},
				views: {
					shortcode: []
				}
			}
		};
	}

	if ( ! tizra_connect.hasOwnProperty( 'fn' ) ) {
		tizra_connect.fn = {
			/**
			 * Shortcode
			 */
			shortcode: {
				/**
				 * This function initializes the shortcode Backbone components.
				 */
				init: function() {
					/*
					 * Backbone Collections
					 */

					// Create a new instance of the Tizra Connect Shortcode Backbone Collection
					Tizra_Connect_Shortcode_Collection = new tizra_connect.Backbone.Collections.Shortcode();
					tizra_connect.Backbone.instances.collections.shortcode.push( Tizra_Connect_Shortcode_Collection );


					/*
					 * Backbone Models
					 */

					// Create a new instance of the Tizra Connect Shortcode Backbone Model
					Tizra_Connect_Shortcode_Model = new tizra_connect.Backbone.Models.Shortcode();
					Tizra_Connect_Shortcode_Collection.add( Tizra_Connect_Shortcode_Model );
					tizra_connect.Backbone.instances.models.shortcode.push( Tizra_Connect_Shortcode_Model );


					/*
					 * Backbone Views
					 */

					// Create a new instance of the Tizra Connect Shortcode Backbone View
					Tizra_Connect_Shortcode_View = new tizra_connect.Backbone.Views.Shortcode( {
						model: Tizra_Connect_Shortcode_Model,
						collections: tizra_connect.collections,
						type: 'shortcode'
					} );
					tizra_connect.Backbone.instances.views.shortcode = Tizra_Connect_Shortcode_View;


					// Create a new instance of the Tizra Connect Shortcode Actions Backbone View
					Tizra_Connect_Shortcode_Actions_View = new tizra_connect.Backbone.Views.Shortcode_Actions( {
						type: 'shortcode-actions',
						button_type: 'insert',
						label: tizra_connect.l10n.shortcode.insert
					} );
					tizra_connect.Backbone.instances.views.shortcode_actions = Tizra_Connect_Shortcode_Actions_View;

					// Attach the Tizra Connect Shortcode Actions Backbone View to the Tizra Connect Shortcode Backbone View
					Tizra_Connect_Shortcode_View.views.set( Tizra_Connect_Shortcode_Actions_View.el_selector, Tizra_Connect_Shortcode_Actions_View, {
						// No DOM modifications
						silent: true
					} );


					// Create a new instance of the Tizra Connect Shortcode Insert Backbone View
					Tizra_Connect_Shortcode_Insert_View = new tizra_connect.Backbone.Views.Shortcode_Insert( {
						collections: tizra_connect.collections,
						max_num_columns: parseInt( tizra_connect.max_num_columns, 10 ),
						type: 'shortcode-insert'
					} );
					tizra_connect.Backbone.instances.views.shortcode_insert = Tizra_Connect_Shortcode_Insert_View;

					// Attach the Tizra Connect Shortcode Insert Backbone View to the Tizra Connect Shortcode Backbone View
					Tizra_Connect_Shortcode_View.views.set( Tizra_Connect_Shortcode_Insert_View.el_selector, Tizra_Connect_Shortcode_Insert_View, {
						// No DOM modifications
						silent: true
					} );


					// Render the Tizra Connect Shortcode Backbone View
					Tizra_Connect_Shortcode_View.render();
				},
				/**
				 * This function resets the shortcode Backbone components.
				 */
				reset: function() {
					// Reset the Tizra Connect Shortcode Backbone View
					Tizra_Connect_Shortcode_View.reset();

					// Reset the Tizra Connect Shortcode Insert Backbone View
					Tizra_Connect_Shortcode_Insert_View.reset();
				}
			}
		};
	}


	/**************
	 * Shortcodes *
	 **************/

	/**
	 * Tizra Connect Shortcode Backbone Model
	 */
	tizra_connect.Backbone.Models.Shortcode = Backbone.Model.extend( {
		defaults: {
			insert_collection: '',
			insert_title: '',
			insert_columns: ''
		},
		id_prefix: 'tizra-connect-',
		initialize: function() {
			// Bind "this" to all functions
			_.bindAll(
				this,
				'setID',
				'removeFromInstances'
			);

			// Set the ID
			this.setID();

			// Remove the model from the instances
			this.listenTo( this, 'remove', this.removeFromInstances );

			// Stop listening to events on the model when it's removed
			this.listenTo( this, 'remove', this.stopListening );
		},
		/**
		 * This function sets the ID value of this model based on the number of existing models of this model's type.
		 */
		setID: function() {
			// Set the ID
			this.set( 'id', this.id_prefix + 'shortcode' );
		},
		/**
		 * This function removes the model from instances.
		 */
		removeFromInstances: function() {
			// TODO
		},
		/**
		 * This function returns a shortcode based on model parameters.
		 */
		getShortcode: function( attributes ) {
			// Defaults
			attributes = attributes || {
				collection: this.get( 'insert_collection' )
			};

			var shortcode = '';

			// Bail if we don't have at least an id
			if ( ! attributes.collection ) {
				return shortcode;
			}

			/*
			 * Build the shortcode string.
			 */
			shortcode += '[' + tizra_connect.shortcode;

			// Loop through the attributes
			_.each( attributes, function ( value, attribute ) {
				// If we have an attribute value
				if ( value ) {
					// Append the attribute to the shortcode
					// TODO: UnderscoreJS template?
					shortcode += ' ' + attribute + '="' + value + '"';
				}
			} );

			shortcode += ']';

			return shortcode;
		}
	} );

	/**
	 * Tizra Connect Shortcode Backbone Collection
	 */
	tizra_connect.Backbone.Collections.Shortcode = Backbone.Collection.extend( {} );

	/**
	 * Tizra Connect Shortcode Backbone View
	 */
	tizra_connect.Backbone.Views.Shortcode = wp.Backbone.View.extend( {
		el: '#tizra-connect-shortcode-wrapper',
		// Events
		events: {
			'click .tizra-connect-shortcode-action-button': 'shortcodeActionButton',
			'change #tizra-connect-shortcode-insert-collection': 'setInsertCollection',
			'keyup #tizra-connect-shortcode-insert-title': 'setInsertTitle',
			'change #tizra-connect-shortcode-insert-title': 'setInsertTitle',
			'change #tizra-connect-shortcode-insert-columns': 'setInsertColumns'
		},
		/**
		 * This function runs on initialization of the view.
		 */
		initialize: function( options ) {
			// Bind "this" to all functions
			_.bindAll(
				this,
				'setInsertCollection'
			);
		},
		/**
		 * This function resets the fields within this view.
		 */
		reset: function() {
			// Reset the insert title value
			this.$el.find( '#tizra-connect-shortcode-insert-title' ).val( '' ).change();

			// Reset the insert columns value
			this.$el.find( '#tizra-connect-shortcode-insert-columns' ).val( '2' ).change();
		},
		/**
		 * This function toggles the action button if required fields are not entered.
		 */
		maybeToggleActionButton: function( type ) {
			var $button = Tizra_Connect_Shortcode_Actions_View.$el.find( '.tizra-connect-shortcode-action-button' ),
				value;

			// Defaults
			type = type || $button.data( 'type' );

			// Switch based on type
			switch ( type ) {
				// Insert
				case 'insert':
					// Grab the insert collection value
					value = this.model.get( 'insert_collection' );

					// If the insert collection isn't set and the button is enabled
					if ( ! value && ! $button.prop( 'disabled' ) ) {
						// Disable the button
						Tizra_Connect_Shortcode_Actions_View.disableActionButton();
					}
					// Otherwise if we have an insert collection and the button is disabled
					else if ( value && $button.prop( 'disabled' ) ) {
						// Enable the button
						Tizra_Connect_Shortcode_Actions_View.enableActionButton();
					}
				break;
			}
		},
		/**
		 * This function performs the shortcode action button action.
		 */
		shortcodeActionButton: function ( event ) {
			var $this = $( event.currentTarget ),
				type = $this.data( 'type' ),
				shortcode;

			// Prevent default
			event.preventDefault();

			// Add the active CSS classes to the spinner
			Tizra_Connect_Shortcode_Actions_View.$el.find( '.tizra-connect-loading' ).addClass( 'is-active tizra-connect-spinner-is-active' );

			// Switch based on type
			switch ( type ) {
				// Insert
				case 'insert':
					// Grab the shortcode
					shortcode = this.model.getShortcode( {
						collection: this.model.get( 'insert_collection' ),
						title: this.model.get( 'insert_title' ),
						columns: this.model.get( 'insert_columns' )
					} );

					// If we have a shortcode
					if ( shortcode ) {
						// Set the shortcode to the editor
						send_to_editor( shortcode );
					}
					// Otherwise close the thickbox
					else {
						tb_remove();
					}

					// Remove the active CSS classes from the spinner
					Tizra_Connect_Shortcode_Actions_View.$el.find( '.tizra-connect-loading' ).removeClass( 'is-active tizra-connect-spinner-is-active' );
				break;
			}
		},
		/**
		 * This function sets the insert collection on the model.
		 */
		setInsertCollection: function( event ) {
			var $this = $( event.currentTarget ),
				value = $this.val();

			// Set the insert collection on the model
			this.model.set( 'insert_collection', value );

			// Maybe toggle action button
			this.maybeToggleActionButton();
		},
		/**
		 * This function sets the insert title on the model.
		 */
		setInsertTitle: function( event ) {
			var $this = $( event.currentTarget ),
				value = $this.val();

			// Set the insert title on the model
			this.model.set( 'insert_title', value );
		},
		/**
		 * This function sets the insert columns on the model.
		 */
		setInsertColumns: function( event ) {
			var $this = $( event.currentTarget ),
				value = $this.val();

			// Set the insert columns on the model
			this.model.set( 'insert_columns', value );
		}
	} );

	/**
	 * Tizra Connect Shortcode Actions Backbone View
	 */
	tizra_connect.Backbone.Views.Shortcode_Actions = wp.Backbone.View.extend( {
		id: 'tizra-connect-shortcode-actions-inner',
		el_selector: '#tizra-connect-shortcode-actions',
		template: wp.template( 'tizra-connect-shortcode-actions' ),
		/**
		 * This function runs on initialization of the view.
		 */
		initialize: function( options ) {
			// TODO
		},
		/**
		 * This function disables the action button.
		 */
		disableActionButton: function() {
			var $button = this.$el.find( '.tizra-connect-shortcode-action-button' );

			// Disable the button
			$button.prop( 'disabled', true );
		},
		/**
		 * This function enables the action button.
		 */
		enableActionButton: function() {
			var $button = this.$el.find( '.tizra-connect-shortcode-action-button' );

			// Enable the button
			$button.prop( 'disabled', false );
		}
	} );

	/**
	 * Tizra Connect Shortcode Insert Backbone View
	 */
	tizra_connect.Backbone.Views.Shortcode_Insert = wp.Backbone.View.extend( {
		id: 'tizra-connect-shortcode-insert-inner',
		el_selector: '#tizra-connect-shortcode-insert',
		select2_selector: '.tizra-connect-select2',
		template: wp.template( 'tizra-connect-shortcode-insert' ),
		/**
		 * This function runs on initialization of the view.
		 */
		initialize: function( options ) {
			// Bind "this" to all functions
			_.bindAll(
				this,
				'render'
			);
		},
		/**
		 * This function renders the view.
		 */
		render: function() {
			// Call (apply) the default wp.Backbone.View render function
			wp.Backbone.View.prototype.render.apply( this, arguments );

			// Initialize Select2
			this.initializeSelect2();

			return this;
		},
		/**
		 * This function resets the fields within this view
		 */
		reset: function() {
			// Reset the insert title value
			this.$el.find( '#tizra-connect-shortcode-insert-title' ).val( '' ).change();

			// Destroy Select2
			this.destroySelect2();
		},
		/**
		 * This function initializes Select2.
		 */
		initializeSelect2: function() {
			var self = this;

			// Initialize Select2 (new thread)
			setTimeout( function() {
				self.$el.find( self.select2_selector ).tizra_connect_select2( {
					dropdownParent: $( '#TB_ajaxContent' )
				} );
			}, 1 );
		},
		/**
		 * This function destroys all Select2 instances.
		 */
		destroySelect2: function() {
			var $select2 = this.$el.find( this.select2_selector );

			// Loop through possible Select2 instances
			$select2.each( function() {
				var $this = $( this ),
					Select2 = $this.data( 'select2' );

				// If we have a Select2 instance
				if ( Select2 ) {
					// Destroy Select2
					$select2.tizra_connect_select2( 'destroy' );
				}
			} );
		}
	} );


	/**
	 * Document Ready
	 */
	$( function() {
		var $body = $( 'body' ),
			bodyMutationObserver,
			$document = $( document ),
			$add_shortcode = $( '.tizra-connect-add-shortcode' );


		/**
		 * Shortcodes
		 */

		// Add shortcode
		$add_shortcode.on( 'click.tizra-connect', function( event ) {
			// Add our custom CSS class to the body element
			$body.addClass( 'tizra-connect-shortcode-ui-visible' );

			// If we don't already have the Backbone views setup
			if ( ! Tizra_Connect_Shortcode_View ) {
				// Initialize the shortcode Backbone components
				tizra_connect.fn.shortcode.init();
			}
			// Otherwise if we have the Backbone views setup
			else {
				// Reset the shortcode Backbone components
				tizra_connect.fn.shortcode.reset();

				// Re-render the shortcode insert Backbone view
				Tizra_Connect_Shortcode_Insert_View.render();
			}

			// Show the Thickbox
			tb_show( tizra_connect.l10n.shortcode.title, '#TB_inline?inlineId=tizra-connect-shortcode-wrapper-container&width=753&height=480', false );
		} );

		// Add an event listener to the thickbox remove event
		$body.on( 'thickbox:removed', function() {
			// If the body element contains our custom CSS class
			if ( $body.hasClass( 'tizra-connect-shortcode-ui-visible' ) ) {
				// Reset the shortcode Backbone components
				tizra_connect.fn.shortcode.reset();

				// Remove our custom CSS class to the body element (delay 1ms; new thread)
				setTimeout( function() {
					$body.removeClass( 'tizra-connect-shortcode-ui-visible' );
				}, 1 );
			}
		} );

		// Add an event listener to the Select2 search fields keydown event
		$document.on( 'keydown.tizra-connect', '.select2-search__field, .select2-search--inline', function( event ) {
			// Bail if the shortcode UI is not visible
			if ( ! $body.hasClass( 'tizra-connect-shortcode-ui-visible' ) ) {
				return;
			}

			// If the escape key was pressed
			if ( event.which === 27 ) {
				// Stop propagation
				event.stopImmediatePropagation();
			}
		} );


		/*
		 * Create a MutationObserver to listen for when the thickbox nodes
		 * are added to the body and add an event listener to the close button
		 * since we can't listen for this element dynamically as it is added and removed
		 * each time thickbox is opened and closed.
		 *
		 * This ensures that our event listener is added before thickbox's.
		 */
		bodyMutationObserver = new MutationObserver( function( mutations ) {
			// Bail if the shortcode UI is not visible
			if ( ! $body.hasClass( 'tizra-connect-shortcode-ui-visible' ) ) {
				return;
			}

			// Loop through mutations
			_.each( mutations, function( mutation ) {
				// If we have added nodes
				if ( mutation.addedNodes && mutation.addedNodes.length ) {
					// Loop through added nodes
					_.each( mutation.addedNodes, function ( node ) {
						// If this is the thickbox window
						if ( node.id === 'TB_window' ) {
							var $tb_close_els = $( '#TB_overlay, #TB_closeWindowButton' );

							// Ensure our event is at the front of the queue
							$tb_close_els.each( function() {
								var $this = $( this ),
									events = $._data( this, 'events' ),
									the_index = -1,
									the_event;

								// If we have click event data
								if ( events && events.click && events.click.length ) {
									// Loop through the click events
									_.each( events.click, function ( event, index ) {
										// If the namespace of this event matches ours
										if ( event.namespace === 'tizra-connect' ) {
											the_index = index;
										}
									} );

									// If we have an index for our event
									if ( the_index !== -1 ) {
										// Remove our event
										the_event = events.click.splice( the_index, 1 );

										// If we have an event
										if ( the_event.length ) {
											// Splice our event at the beginning of the array
											events.click.splice( 0, 0, the_event[0] );
										}
									}

									// Save the updated events data on the elements
									$._data( this, 'events', events );
								}
							} );
						}
					} );
				}
			} );
		});

		// Observe
		bodyMutationObserver.observe( $body[0], {
			childList: true // Listen for elements added or removed to the body element
		} );
	} );
}( wp, jQuery ) );