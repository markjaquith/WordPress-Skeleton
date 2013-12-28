jQuery(document).ready(function($) {
	// Based on https://gist.github.com/4283059
	// also based on the original CSSIgniter script

	// Store the original values.
	function ci_backup_send_attachment(){
		window.ci_orig_send_text = wp.media.view.l10n.insertIntoPost;
		window.ci_orig_send_attachment = wp.media.editor.send.attachment;
	}
	// This will be called *after* wp.media.editor.send.attachment has run.
	// Actually it will be called while it returns;
	function ci_restore_send_attachment(){
		wp.media.editor.send.attachment = window.ci_orig_send_attachment;
		wp.media.view.l10n.insertIntoPost = window.ci_orig_send_text;
		// Must not return anything other than implied void.
	}
	
	// All elements with class .ci-upload become buttons.
	// Use the '.uploaded' class in a *sibling* element to indicate the target field for the image URL.
	// Similarly, '.uploaded-id' is the target for the image ID
	// I.e. button: class="ci-upload" and input: class="uploaded"
	$('.ci-upload').click(function(event){
		event.preventDefault();

		ci_backup_send_attachment();

		var button = $(this);
		var target_url = button.siblings('.uploaded');
		var target_id = button.siblings('.uploaded-id');
		
		// Replace the "Insert into [post|page]" button text.
		// We better internationalize this string with wp_localize_script();
		wp.media.view.l10n.insertIntoPost = 'Use this file';

		// prop holds the info from "Attachment Display Settings" 
		// attachment holds info about the file, such as id, link, caption, etc
		wp.media.editor.send.attachment = function(props, attachment){
			// Assign the selected url to our target. That is, the appropriate url depending on the selected size.
			if(target_url.length > 0){
				// Handle images
				if(attachment.type == 'image') {
					if(typeof attachment.sizes != 'undefined') {
						// Generic case of resizable images
						// props.size holds the user-selected image size.
						target_url.val(attachment.sizes[props.size].url);						
					}
					else {
						// handle images without attachment.sizes set (e.g. icons)
						target_url.val(attachment.url);
					}

					/*
					// Example of specific mime subtype handler.
					else if(attachment.subtype == 'x-icon') {
						// handle .ico format (non-resizable, doesn't have attachment.sizes property
						target_url.val(attachment.url);
					}
					*/
				}
				else {
					// All other filetype cases
					target_url.val(attachment.url);
				}
			}

			// Assign the ID to our target.
			if(target_id.length > 0){
				target_id.val(attachment.id);
			}
			
			// TODO: change the themes that use #default_header_bg_hidden to use the .uploaded-id class
			// and then remove this check.
			if($('#default_header_bg_hidden').length > 0){
				$('#default_header_bg_hidden').val(attachment.id);
			}
				
			// It's important to restore original functionality.
			// Don't return anything other than implied void, otherwise it will get
			// appended in the editor. Even boolean false, gets added as string into the editor.
			return ci_restore_send_attachment();
		}
		
		var ci_editor = wp.media.editor.open(button);

		return false;
	});
});
