jQuery(document).ready( function($) {
	eventorganiser_open_pointer(0);
	function eventorganiser_open_pointer(i){
		pointer = eventorganiserPointer.pointers[i];
		options = jQuery.extend( pointer.options, {
					close: function() {
					jQuery.post( ajaxurl, {
						pointer: pointer.pointer_id,
						action: 'dismiss-wp-pointer'
						});
					},
				});

		jQuery(pointer.target).pointer( options ).pointer('open');
	}
});
