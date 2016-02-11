/**
 * Remove the default WP role dropdown from the DOM.
 */
jQuery( document ).ready( function( $ ) {
	$( 'select[name="role"]' ).closest( 'tr' ).remove();
} );