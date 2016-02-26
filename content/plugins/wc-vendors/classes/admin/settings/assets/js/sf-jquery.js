jQuery(document).ready(function () {

    jQuery(".sf-tips").tooltip({ animation: true, html: true, delay: { show: 300, hide: 100 } });


    //This if statement checks if the color picker widget exists within jQuery UI
    //If it does exist then we initialize the WordPress color picker on our text input field
    if (typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function') {
        jQuery('.colorpick').wpColorPicker();
    } else {
        // Color picker
        jQuery('.colorpick').each(function () {
            jQuery('.colorpickdiv', jQuery(this).parent()).farbtastic(this);
            jQuery(this).click(function () {
                if (jQuery(this).val() == "") jQuery(this).val('#');
                jQuery('.colorpickdiv', jQuery(this).parent()).show();
            });
        });
        jQuery(document).mousedown(function () {
            jQuery('.colorpickdiv').hide();
        });
    }
});