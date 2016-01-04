jQuery(document).ready(function($) {

	var nf_columns = {
		init: function() {
			//this.move_row_actions();
			// Remove our "ID" checkbox.
			$( '#id-hide' ).parent().remove();
			var that = this;
			$( document ).on( 'click', '.hide-column-tog', that.save_hidden_columns );
		},
		save_hidden_columns: function() {
			// Send our hidden columns to our backend for saving.
			var hidden = columns.hidden();
			$.post(
				ajaxurl,
				{ 
					form_id: nf_sub.form_id,
			 		hidden: hidden,
			 		action:'nf_hide_columns'
			 	}
			);
			// Move our row-actions
			//nf_columns.move_row_actions();
		},
		move_row_actions: function() {
			// Move our row-actions class to our first column.
			$( "#the-list tr" ).each( function( e ) {
				var first_column = $( this ).find( 'td:visible' ).eq(0);
				if ( typeof $( first_column ).html() == 'undefined' ) {
					first_column = $( this ).find( 'td:first' );
				}
				$( this ).find( 'td div.row-actions' ).detach().appendTo( first_column );
			});
		}
	}

	nf_columns.init();

	$( '.datepicker' ).datepicker( nf_sub.datepicker_args );

	$( document ).on( 'change', '.nf-form-jump', function( e ) {
		$( '#posts-filter' ).submit();
	});

	$( document ).on( 'submit', function( e ) {
		$( '.spinner' ).show();
		if ( $( 'select[name="action"]' ).val() == 'export' || $( 'select[name="action2"]' ).val() == 'export' ) {
			setTimeout(function(){ // Delay for Chrome
				$( 'input:checkbox' ).attr( 'checked', false );
				$( '.spinner' ).hide();
				$( 'select[name="action"]' ).val( '-1' );
				$( 'select[name="action2"]' ).val( '-1' );
		    }, 2000);
		}
	});

	$( '.screen-options' ).prepend( $( '#nf-subs-screen-options' ).html() );
	$( '#nf-subs-screen-options' ).remove();
	
});
