jQuery.fn.nfAdminModal = function( action, options ) {

	if ( 0 == jQuery( '#nf-admin-modal-backdrop' ).length ) {
		var modalHtml = '<div id="nf-admin-modal-backdrop" style="display: none;"></div><div id="nf-admin-modal-wrap" class="wp-core-ui" style="display: none;"><div id="nf-admin-modal" tabindex="-1"><div id="admin-modal-title"><span id="nf-modal-title"></span><button type="button" id="nf-admin-modal-close" class="modal-close"><span class="screen-reader-text modal-close">Close</span></button></div><div id="modal-contents-wrapper" style="padding:20px;"><div id="nf-admin-modal-content" class="admin-modal-inside"></div><div class="submitbox" style="display:block;"></div></div></div></div>';
		jQuery( 'body' ).append( modalHtml );
	}

	if ( 'object' === typeof action ) {
		options = action;
	}

	var defaults = { 'title' : '', 'buttons' : false, 'backgroundClose': false };

	if ( 'undefined' === typeof options ) {
		options = jQuery( this ).data( 'nfAdminModal' );
		if ( 'undefined' === typeof options ) {
			// Merge our default options with the options sent
			options = jQuery.extend( defaults, options );
		}
	} else {
		// Merge our default options with the options sent
		options = jQuery.extend( defaults, options );
	}

	// Set our data with the current options
	jQuery( this ).data( 'nfAdminModal', options );

	jQuery( this ).hide();
	jQuery( '#nf-admin-modal-content' ).html( this.html() );

	jQuery( '#nf-modal-title' ).html( options.title );

	if ( options.buttons ) {
		jQuery( options.buttons ).hide();
		var buttons = jQuery( options.buttons ).html();
		jQuery( '#modal-contents-wrapper' ).find( '.submitbox' ).html( buttons );
		jQuery( '#nf-admin-modal-content' ).addClass( 'admin-modal-inside' );
		jQuery( '#modal-contents-wrapper' ).find( '.submitbox' ).show();
	} else {
		jQuery( '#nf-admin-modal-content' ).removeClass( 'admin-modal-inside' );
		jQuery( '#modal-contents-wrapper' ).find( '.submitbox' ).hide();
	}

	jQuery( '#nf-admin-modal-backdrop' ).data( 'backgroundClose', options.backgroundClose );

	if ( 'close' == action ) {
		jQuery.fn.nfAdminModal.close();
	} else if ( 'open' == action ) {
		jQuery.fn.nfAdminModal.open();
	}
};

jQuery( document ).on( 'click', '#nf-admin-modal-backdrop', function( e ) { 
	if ( jQuery( this ).data( 'backgroundClose' ) == true ) {
		jQuery.fn.nfAdminModal.close();
	}
} );

jQuery( document ).on( 'click', '.modal-close', function( e ) {
	e.preventDefault();
	jQuery.fn.nfAdminModal.close();
} );

jQuery.fn.nfAdminModal.close = function() {
	jQuery( '#nf-admin-modal-backdrop' ).hide();
	jQuery( '#nf-admin-modal-wrap' ).hide();
	jQuery( document ).triggerHandler( 'nfAdminModalClose' );
}

jQuery.fn.nfAdminModal.open = function() {
	jQuery( '#nf-admin-modal-backdrop' ).show();
	jQuery( '#nf-admin-modal-wrap' ).show();
	jQuery( document ).triggerHandler( 'nfAdminModalOpen' );
}