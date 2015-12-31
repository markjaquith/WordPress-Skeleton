/**
 * JS that handles our fields in the admin.
 * Uses backbone data models to send only modified field data to the db.
 */

// Model to hold our field settings.
var nfField = Backbone.Model.extend( {
	toggleMetabox: function() {
		/**
		 * Open and close a field metabox.
		 * When a metabox is closed:
		 * 	- update the field collection with any values that might have changed.
		 *  - remove the HTML
		 * When a metabox is opened:
		 *  - send an ajax request to grab the HTML
		 *  - populate the field settings HTML
		 */

		 var field_id = this.id;

		// Get our current metabox state.
		var current_metabox_state = this.get( 'metabox_state' );
		if ( current_metabox_state == 1 ) { // If our current state is 1, then we are closing the metabox.
			var new_metabox_state = 0;
		} else { // If our current state is 0, then we are opening the metabox.
			var new_metabox_state = 1;
		}

		// Perform specific tasks based upon the state of the metabox.
		if ( new_metabox_state == 1 ) { // If we have opened the metabox.
			// Fetch our HTML.
			this.updateHTML();
		} else { // If we have closed the metabox.
			// Update our model data.
			this.updateData();
			// Remove any tinyMCE editors
			jQuery( '#ninja_forms_field_' + field_id + '_inside' ).find( 'div.rte' ).each( function() {
				if ( 'undefined' != typeof tinymce ) {
					try {
						var editor_id = jQuery( this ).find( 'textarea.wp-editor-area' ).prop( 'id' );
						tinymce.remove( '#' + editor_id );	
					} catch (e ) {

					}
									
				}

			} );

			jQuery( '#ninja_forms_field_' + field_id + '_inside' ).slideUp('fast', function( e ) {
				// Remove the HTML contents of this metabox.
				jQuery( '#ninja_forms_field_' + field_id + '_inside' ).empty();

				// Add our no-padding class
				jQuery( '#ninja_forms_field_' + field_id + '_inside' ).addClass( 'no-padding' );
			});
		}

		// Save the state of the metabox in our data model.
		this.set( 'metabox_state', new_metabox_state );
	},
	updateHTML: function() {
		var field_id = this.id;
		jQuery( '#ninja_forms_metabox_field_' + field_id ).find( '.spinner' ).show();
		jQuery( '#ninja_forms_metabox_field_' + field_id ).find( '.spinner' ).css( 'visibility', 'visible' );
		this.updateData();
		var data = JSON.stringify( this.toJSON() );
		var that = this;
		jQuery.post( ajaxurl, { field_id: field_id, data: data, action:'nf_output_field_settings_html', nf_ajax_nonce:ninja_forms_settings.nf_ajax_nonce }, function( response ) {
			jQuery( '#ninja_forms_metabox_field_' + field_id ).find( '.spinner' ).hide();
			// Remove our no-padding class.
			jQuery( '#ninja_forms_field_' + field_id + '_inside' ).removeClass( 'no-padding' );	
			jQuery( '#ninja_forms_field_' + field_id + '_inside' ).append( response );
			if ( typeof nf_ajax_rte_editors !== 'undefined' && 'undefined' !== typeof tinyMCE ) {
				for (var x = nf_ajax_rte_editors.length - 1; x >= 0; x--) {
					try {
						var editor_id = nf_ajax_rte_editors[x];
						tinyMCE.init( tinyMCEPreInit.mceInit[ editor_id ] );
						try { quicktags( tinyMCEPreInit.qtInit[ editor_id ] ); } catch(e){ console.log( 'error' ); }
					} catch (e) {

					}
				};
			}

			that.removeEmptySettings();

			jQuery( '#ninja_forms_field_' + field_id + '_inside' ).slideDown( 'fast' );

			// Re-run makeSortable for new HTML
			nfFields.listOptionsSortable();

			jQuery( '.nf-field-settings .title' ).disableSelection();
		} );
	},
	updateData: function() {
		var field_id = this.id;
		if ( 'undefined' != typeof tinyMCE ) {
			try {
				tinyMCE.triggerSave();
			} catch (e) {

			}
		}
		
		var data = jQuery('[name^=ninja_forms_field_' + field_id + ']');
		var field_data = jQuery(data).serializeFullArray();

		if ( typeof field_data['ninja_forms_field_' + field_id] != 'undefined' ) {
			var field = field_data['ninja_forms_field_' + field_id];
			
			for( var prop in field ) {
			    if ( field.hasOwnProperty( prop ) ) {
			        nfFields.get( field_id ).set( prop, field[ prop ] );
			    }
			}
		}
		nfForm.set( 'saved', false );
	},
	remove: function() {
		var field_id = this.id;
		var answer = confirm( nf_admin.remove_field );
		if ( answer ) {
			var form_id = ninja_forms_settings.form_id
			jQuery.post(ajaxurl, { form_id: form_id, field_id: field_id, action: 'ninja_forms_remove_field', nf_ajax_nonce:ninja_forms_settings.nf_ajax_nonce }, function( response ) {
				jQuery( '#ninja_forms_field_' + field_id).remove();
				jQuery( document ).trigger( 'removeField', [ field_id ] );
			});
		}
	},
	removeEmptySettings: function() {
		var field_id = this.id;
		jQuery( '#ninja_forms_field_' + field_id + '_inside' ).find( '.nf-field-settings .inside' ).each( function() {
			var html = jQuery.trim( jQuery( this ).html() );
			if ( html == '' ) {
				jQuery( this ).parent().remove();
			}
		} );
	}

} );
// Collection to hold our fields.
var nfFields = Backbone.Collection.extend({
	model: nfField,
	setup: function() {
		// Loop through our field JSON that is already on the page and populate our collection with it.
		if( 'undefined' !== typeof nf_admin.fields ) {
			_.each( nf_admin.fields, function( field ) {
				nfFields.add( { id: field.id, metabox_state: field.metabox_state } );
			} );
		}
	},
	updateData: function() {
		// Loop through our fields collection and update any field lis that are open
		_.each( this.models, function( field ) {
			if ( field.get( 'metabox_state' ) == 1 ) {
				field.updateData();
			}
		} );
	},
	newField: function( button ) {
		var limit = jQuery( button ).data( 'limit' );
		var type = jQuery( button ).data( 'type' );
		var form_id = ninja_forms_settings.form_id

		if ( limit != '' ){
			var current_count = jQuery( '.' + type + '-li' ).length;
		}else{
			var current_count = '';
		}

		if ( typeof jQuery( button ).data( 'field' ) == 'undefined' ) {
			var field_id = '';
			var action = 'ninja_forms_new_field';
		} else if ( jQuery( button ).data( 'type' ) == 'fav' ) {
			var field_id = jQuery( button ).data( 'field' );
			var action = 'ninja_forms_insert_fav';
		} else {
			var field_id = jQuery( button ).data( 'field' );
			var action = 'ninja_forms_insert_def';
		}

		if ( ( limit != '' && current_count < limit ) || limit == '' || current_count == '' || current_count == 0 ) {
			jQuery.post( ajaxurl, { type: type, field_id: field_id, form_id: form_id, action: action, nf_ajax_nonce:ninja_forms_settings.nf_ajax_nonce }, this.newFieldResponse );
		} else {
			jQuery( button ).addClass( 'disabled' );
		}
		nfForm.set( 'saved', false );
	},
	newFieldResponse: function( response ) {
		// Fire our custom jQuery addField event.
		jQuery( document ).trigger('addField', [ response ]);
	},
	addFieldDefault: function( response ) {
		jQuery( '#ninja_forms_field_list' ).append( response.new_html ).show( 'slow' );

		if ( response.new_type == 'List' ) {
			this.listOptionsSortable();
		}
		if ( typeof nf_ajax_rte_editors !== 'undefined' && 'undefined' !== typeof tinyMCE ) {
			for (var x = nf_ajax_rte_editors.length - 1; x >= 0; x--) {
				try {
					var editor_id = nf_ajax_rte_editors[x];
					tinyMCE.init( tinyMCEPreInit.mceInit[ editor_id ] );
					try { quicktags( tinyMCEPreInit.qtInit[ editor_id ] ); } catch(e){}
				} catch (e) {

				}

			};
		}

		// Add our field to our backbone data model.
		this.add( { id: response.new_id, metabox_state: 1 } );

		nfFields.get( response.new_id ).removeEmptySettings();
	},
	listOptionsSortable: function ( response ) {
		//Make List Options sortable
		jQuery(".ninja-forms-field-list-options").sortable({
			helper: 'clone',
			handle: '.ninja-forms-drag',
			items: 'div',
			placeholder: 'ui-state-highlight',
			update: function( event, ui ) {
				var order = jQuery( this ).sortable( 'toArray' );
				var x = 0;
				_.each( order, function( id ) {
					var field_id = jQuery( '#' + id ).data( 'field' );

					var label_name = 'ninja_forms_field_' + field_id + '[list][options][' + x + '][label]';
					var value_name = 'ninja_forms_field_' + field_id + '[list][options][' + x + '][value]';
					var calc_name = 'ninja_forms_field_' + field_id + '[list][options][' + x + '][calc]';
					var selected_name = 'ninja_forms_field_' + field_id + '[list][options][' + x + '][selected]';
					
					jQuery( '#' + id ).find( '.ninja-forms-field-list-option-label' ).attr( 'name', label_name );
					jQuery( '#' + id ).find( '.ninja-forms-field-list-option-value' ).attr( 'name', value_name );
					jQuery( '#' + id ).find( '.ninja-forms-field-list-option-calc' ).attr( 'name', calc_name );
					jQuery( '#' + id ).find( '.ninja-forms-field-list-option-selected' ).attr( 'name', selected_name );

					x++;
				} );
			}
		});
	}
});

var nfForm = Backbone.Model.extend( {
	defaults: {
		'id' 	 	: ninja_forms_settings.form_id,
		'status' 	: nf_admin.form_status,
		'title'	 	: nf_admin.form_title,
		'saved'		: true
	},
	setup: function() {
		this.changeMenu();
	},
	changeMenu: function() {
		
		if ( 'new' == this.get( 'status' ) ) { // If we're working with a new form, highlight the "Add New" menu item.
			jQuery( '.wp-submenu li' ).removeClass( 'current' );
			jQuery( 'a[href="admin.php?page=ninja-forms&tab=builder&form_id=new"]' ).parent().addClass( 'current' );
		} else {
			var html = '<li class="current"><a href="#">' + nf_admin.edit_form_text + '</a></li>';
			if ( jQuery( 'li a:contains("' + nf_admin.edit_form_text + '")' ).length == 0 ) {
				jQuery( '.wp-submenu li' ).removeClass( 'current' );
				jQuery( 'a[href="admin.php?page=ninja-forms&tab=builder&form_id=new"]' ).parent().after( html );
			}
		}
	},
	save: function() {
		jQuery( '.nf-save-admin-fields' ).hide();
		jQuery( '.nf-save-spinner' ).show();
		jQuery( '.nf-save-spinner' ).css( 'visibility', 'visible' );

		// If our form is new, then prompt for a title before we save
		if ( 'new' == this.get( 'status' ) ) {
			if ( jQuery( '._submit-li' ).length > 0 ) {
				jQuery( '#nf-insert-submit-div' ).hide();
				this.set( 'show_insert_submit', false );
			} else {
				jQuery( '#nf-insert-submit-div' ).show();
				this.set( 'show_insert_submit', true );
			}
			// Open our save form modal.
			jQuery( '#nf-save-title' ).nfAdminModal( 'open' );
			jQuery( '#modal-contents-wrapper' ).find( '#nf-form-title' ).focus();
			return false;			
		}

		nfFields.updateData();

		var field_data = JSON.stringify( nfFields.toJSON() );
		var field_order = {};
		var current_order = jQuery( '#ninja_forms_field_list' ).sortable( 'toArray' );
	
		for ( var i = 0; i < current_order.length; i++ ) {
			field_order[i] = current_order[i];
		};
		field_order = JSON.stringify( field_order );
		var form_id = ninja_forms_settings.form_id;

		jQuery( document ).data( 'field_order', field_order );
		jQuery( document ).data( 'field_data', field_data );

		jQuery( document ).triggerHandler( 'nfAdminSaveFields' );
		
		var field_order = jQuery( document ).data( 'field_order' );
		var data = jQuery( document ).data( 'field_data' );

		jQuery.post( ajaxurl, { form_title: this.get( 'title' ), form_id: form_id, field_data: field_data, field_order: field_order, action: 'nf_admin_save_builder', nf_ajax_nonce:ninja_forms_settings.nf_ajax_nonce }, function( response ) {
			jQuery( '.nf-save-spinner' ).hide();
			jQuery( '.nf-save-admin-fields' ).show();
			var html = '<div id="message" class="updated below-h2" style="display:none;margin-top: 20px;"><p>' + nf_admin.saved_text + '</p></div>';
			jQuery( '.nav-tab-wrapper:last' ).after( html );
			jQuery( '#message' ).fadeIn();
			if ( jQuery( '#nf-display-form-title' ).html() == '' ) {
				jQuery( '#nf-display-form-title' ).html( nfForm.get( 'title' ) );
			}
			nfForm.set( 'saved', true );
			nfForm.set( 'status', '' );
			nfForm.changeMenu();
		} );
	},
	saveTitle: function() {
		var title = jQuery( '#modal-contents-wrapper' ).find( '#nf-form-title' ).val();
		var show_insert_submit = this.get( 'show_insert_submit' );
		var insert_submit = jQuery( '#modal-contents-wrapper' ).find( '#nf-insert-submit' ).prop( 'checked' );
		this.set( 'title', title );
		this.set( 'status', '' );

		// Insert our submit button if we checked the box.
		if ( show_insert_submit && insert_submit ) {
			var that = this;
			// Add our custom addField behaviour
			jQuery( document ).on( 'addField.insertSubmit', function( e, response ) {
				jQuery( '#ninja_forms_field_' + response.new_id + '_toggle' ).click();
				jQuery( '#nf-save-title' ).nfAdminModal( 'close' );
				that.save();
				jQuery( document ).off( 'addField.insertSubmit' );
			} );
			jQuery( '#_submit' ).click();
		} else {
			jQuery( '#nf-save-title' ).nfAdminModal( 'close' );
			this.save();	
		}
	}
} );

// Instantiate our fields collection.
var nfFields = new nfFields();

// Instantiate our form settings.
var nfForm = new nfForm();

(function($){

$( document ).ready( function( $ ) {

	nfFields.setup();
	nfForm.setup();

	// Open and close a field metabox.
	$( document ).on( 'click', '.nf-edit-field', function( e ) {
		e.preventDefault();

		// Get our field id.
		var field_id = jQuery( e.target ).data( 'field' );
		nfFields.get( field_id ).toggleMetabox();		

		// Get our current metabox state.
		var current_metabox_state = nfFields.get( field_id ).get( 'metabox_state' );
		if ( current_metabox_state == 1 ) {
			$( this ).addClass( 'open' );
		} else {
			$( this ).removeClass( 'open' );
		}
	});

	// Remove the saved message when the user clicks anywhere on the page.
	$( document ).on( 'click', function() {
		$( '#message' ).fadeOut( 'slow' );
	} );

	$( document ).on( 'click', '.nf-save-admin-fields', function( e ) {
		e.preventDefault();
		nfForm.save();
	} );

	$( document ).on( 'click', '#nf-save-title-submit', function( e ) {
		e.preventDefault();
		nfForm.saveTitle();
	} );

	// Add New Field
	$( document ).on( 'click', '.ninja-forms-new-field', function( e ) {
		e.preventDefault();
		nfFields.newField( e.target );
	} );

	// Remove Field
	$( document ).on( 'click', '.nf-remove-field', function( e ){
		e.preventDefault();
		var field_id = jQuery( e.target ).data( 'field' );
		nfFields.get( field_id ).remove();
	});

	// Hook into our add field response event
	$( document ).on( 'addField.default', function( e, response ) {
		nfFields.addFieldDefault( response );
	} );

	// Insert a Favorite Field
	$( document ).on( 'click', '.ninja-forms-insert-fav-field', function( e ){
		e.preventDefault();
		nfFields.newField( e.target );
	});

	// Insert a Defined Field
	$( document ).on( 'click', '.ninja-forms-insert-def-field', function( e ){
		e .preventDefault();
		nfFields.newField( e.target );
	});

	// Create our save form modal.
	$( '#nf-save-title' ).nfAdminModal( { title: 'Save Your Form', buttons: '#nf-save-title-buttons' } );

	// Remove spinners when the save title modal is closed
	$( document ).on( 'nfAdminModalClose.hideSpinners', function( e ) {
		jQuery( '.nf-save-spinner' ).hide();
		jQuery( '.nf-save-admin-fields' ).show();
	} );

	// Enable/disable our save button on the name modal
	$( document ).on( 'keyup', '#nf-form-title', function( e ) {
		var value = this.value;
		if ( this.value.length > 0 ) {
			$( '#modal-contents-wrapper' ).find( '#nf-save-title-submit' ).prop( 'disabled', false );
			$( '#modal-contents-wrapper' ).find( '#nf-save-title-submit' ).removeClass( 'button-secondary' );
			$( '#modal-contents-wrapper' ).find( '#nf-save-title-submit' ).addClass( 'button-primary' );
		} else {
			$( '#modal-contents-wrapper' ).find( '#nf-save-title-submit' ).prop( 'disabled', true );
			$( '#modal-contents-wrapper' ).find( '#nf-save-title-submit' ).removeClass( 'button-primary' );
			$( '#modal-contents-wrapper' ).find( '#nf-save-title-submit' ).addClass( 'button-secondary' );
		}

		if( e.keyCode == 13 && this.value.length > 0 ){
			nfForm.saveTitle();
		}		
	} );

	// Make the field list sortable
	$( '.ninja-forms-field-list' ).sortable({
		handle: '.menu-item-handle',
		items: 'li:not(.not-sortable)',
		connectWith: '.ninja-forms-field-list',
		//cursorAt: {left: -10, top: -1},
		start: function( e, ui ) {
			var wp_editor_count = $( ui.item ).find( '.wp-editor-wrap' ).length;
			if(wp_editor_count > 0){
				$(ui.item).find('.wp-editor-wrap').each(function(){
					try {
						var ed_id = this.id.replace( 'wp- ', '');
						ed_id = ed_id.replace( '-wrap', '' );
						tinyMCE.execCommand( 'mceRemoveControl', false, ed_id );
					} catch (e) {

					}
				});
			}
		},
		stop: function( e,ui ) {
			var wp_editor_count = $( ui.item ).find( '.wp-editor-wrap' ).length;
			if( wp_editor_count > 0 ) {
				$( ui.item ).find( '.wp-editor-wrap').each(function(){
					try {
						var ed_id = this.id.replace( 'wp-', '' );
						ed_id = ed_id.replace( '-wrap', '' );
						tinyMCE.execCommand( 'mceAddControl', true, ed_id );
					} catch (e) {

					}
				});
			}
			$( this ).sortable( 'refresh' );
			nfForm.set( 'saved', false );
		}
	});

	$( document ).on( 'dblclick', '.nf-field-settings .title', function(e) {
		$( this ).find( '.nf-field-sub-section-toggle' ).click();
	} );

	$( document ).on( 'click', '.nf-field-sub-section-toggle', function(e) {
		e.preventDefault();
		if ( $( this ).hasClass( 'dashicons-arrow-down' ) ) {
			$( this ).removeClass( 'dashicons-arrow-down' ).addClass( 'dashicons-arrow-up' );
		} else {
			$( this ).removeClass( 'dashicons-arrow-up' ).addClass( 'dashicons-arrow-down' );
		}
		$( this ).parent().next( '.inside' ).slideToggle();
	} );
	
	$( window ).bind( 'beforeunload', function() {
		if ( 'new' == nfForm.get( 'status' ) ) { // Prompt the user to give a name if they leave the builder before naming their form.
			$( '#nf-save-title' ).nfAdminModal( 'open' );
			return 'Please save your form before leaving this page.';
		} else if ( nfForm.get( 'saved' ) == false ) {
			return 'You have unsaved changes. Please save before leaving this page.';
		}
	} );

	$( document ).on( 'dblclick', '.menu-item-handle', function( e ) {
		$( this ).find( '.nf-edit-field' ).click();
	} );

});

})(jQuery);