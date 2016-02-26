(function($) {

    jQuery(document).on( 'click', '.bf_add_files a', function( event ) {

        var $el = $(this);
        // BuddyForms gallery file uploads
        var buddyforms_gallery_frame;

        var $image_gallery_ids  = $('#'+$el.data('slug'));
        var bf_files            = $('#bf_files_container_'+$el.data('slug')+' ul.bf_files');

        var attachment_ids = $image_gallery_ids.val();

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( buddyforms_gallery_frame ) {
            buddyforms_gallery_frame.open();
            return;
        }

        // Create the media frame.
        buddyforms_gallery_frame = wp.media.frames.buddyfoms_gallery = wp.media({
            // Set the title of the modal.
            title: $el.data('choose'),
            button: {
                text: $el.data('update'),
            },
            library: {
                type: $el.data('library_type'),
            },
            multiple: $el.data('multiple'),
        });

        if($el.data('type'))
            buddyforms_gallery_frame.uploader.options.uploader['params']['allowed_type'] = $el.data('allowed_type');

        // When an image is selected, run a callback.
        buddyforms_gallery_frame.on( 'select', function() {

            var selection = buddyforms_gallery_frame.state().get('selection');

            selection.map( function( attachment ) {

                attachment = attachment.toJSON();

                if ( attachment.id ) {

                    if($el.data('multiple')){
                        attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;
                    } else {
                        attachment_ids = attachment.id;
                    }

                    attachment_url = attachment.url;
                    if(attachment.type == 'image'){
                        attachment_image = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                    } else {
                        attachment_image = attachment.icon;
                    }

                    if(!$el.data('multiple'))
                        bf_files.html('');


                    bf_files.append('\
                        <li class="image" data-attachment_id="' + attachment.id + '">\
                        <div class="bf_attachment_li">\
                        <div class="bf_attachment_img">\
                        <img style="height:64px" src="' + attachment_image + '" />\
                        </div><div class="bf_attachment_meta">\
                        <p>Name:' + attachment.name + '</p>\
                        <p><a href="#" class="delete tips" data-slug="' + $el.data('slug') + '" data-tip="' + $el.data('tip') +  '">' + $el.data('text') +  '</a>\
                        <a href="' + attachment_url + '" target="_blank" class="view" data-tip="' + +  '">' + +  '</a></p>\
                        </div></div>\
                        </li>');

                }

            });

            $image_gallery_ids.val( attachment_ids );
        });

        // Finally, open the modal.
        buddyforms_gallery_frame.open();
    });

    // Remove images
    jQuery(document).on( 'click', '.bf_files a.delete', function( event ) {
        if (confirm('Delete Permanently')) {
            var $el = $(this);

            var $image_gallery_ids = $('#' + $el.data('slug'));
            var bf_files = $('#bf_files_container_' + $el.data('slug') + ' ul.bf_files');

            var attachment_ids = $image_gallery_ids.val();

            $(this).closest('li.image').remove();

            var attachment_ids = '';

            $('#bf_files_container_' + $el.data('slug') + ' ul li.image').css('cursor', 'default').each(function () {
                var attachment_id = jQuery(this).attr('data-attachment_id');
                attachment_ids = attachment_ids + attachment_id + ',';
            });

            $image_gallery_ids.val(attachment_ids);

            // remove any lingering tooltips
            $('#tiptip_holder').removeAttr('style');
            $('#tiptip_arrow').removeAttr('style');
        }
        return false;
    });

})(jQuery);