jQuery(function(){
    jQuery('#the-list').on('click', '.editinline', function(){

        if (jQuery('.inline-edit-author').length && jQuery('.inline-edit-author-new').length) { 
            jQuery('.inline-edit-author').hide(); 
            jQuery('.inline-edit-author').attr('class', 'inline-edit-author-old'); 
            jQuery('select[name=post_author]').attr('name', 'post_author-old');
            jQuery('.inline-edit-author-new').attr('class', 'inline-edit-author');
            jQuery('select[name=post_author-new]').attr('name', 'post_author');
        } 

        inlineEditPost.revert();

        var post_id = jQuery(this).closest('tr').attr('id');

        post_id = post_id.replace("post-", "");

        var wcv_inline_data = jQuery('#vendor_' + post_id),
            wc_inline_data = jQuery('#woocommerce_inline_' + post_id );

        var vendor = wcv_inline_data.find("#_vendor").text();

        jQuery('select[name="post_author"] option[value="' + vendor + '"]', '.inline-edit-row').attr('selected', 'selected');

        var product_type = wc_inline_data.find('.product_type').val();

        if (product_type=='simple' || product_type=='external') {
            jQuery('.vendor', '.inline-edit-row').show();
        } else {
            jQuery('.vendor', '.inline-edit-row').hide();
        }

    });
});