jQuery(document).ready(function($){

	var media_frame;
        var inputElement;

	jQuery( 'body' ).on('click', '.upload-button', function( event ){

		event.preventDefault();
                
                inputElement = jQuery( this ).prev();
                
                console.log( inputElement );
                
		if ( media_frame ) {
			media_frame.open();
			return;
		}

		media_frame = wp.media.frames.media_frame = wp.media({
                    title: 'Upload an Image, Or Select One From the Library',
                    frame: 'select',
                    multiple: false,
                    button: { text: 'Select Image' },
                    library: { type: 'image' }
		});
		
		media_frame.on( 'select', function() {
			attachment = media_frame.state().get('selection').first().toJSON();
			console.log( attachment.url );
			inputElement.val( attachment.url );
		});

		media_frame.open();
	});
	
	jQuery('body').on('click', '.jsjr-pci-toggle', function( event ){
		jQuery( this ).toggleClass('down');
		jQuery( this ).next().slideToggle();
	});
	
	jQuery('body').on( 'mouseover', '.jsjr-pci-question', function( event ){
		if ( jQuery( this ).tooltip() == null ) {
			jQuery( this ).tooltip();
		}
		jQuery( this ).tooltip( 'open');
	});
	
});