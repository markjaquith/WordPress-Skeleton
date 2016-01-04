jQuery(document).ready(function() {
	wptuts_open_pointer(0);
	function wptuts_open_pointer(i) {
		pointer = postman_admin_pointer.pointers[i];
		options = jQuery.extend(pointer.options, {
			close : function() {
				jQuery.post(ajaxurl, {
					pointer : pointer.pointer_id,
					action : 'dismiss-wp-pointer'
				}).fail(function(response) {
					//ajaxFailed(response);
				});
			}
		});

		if (typeof (jQuery().pointer) != 'undefined') {
			jQuery(pointer.target).pointer(options).pointer('open');
		}
	}
});