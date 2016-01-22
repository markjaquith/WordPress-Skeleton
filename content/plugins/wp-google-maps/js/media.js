jQuery(document).ready(function($){

    var tgm_media_frame_default;

    $(document.body).on('click.tgmOpenMediaManager', '#upload_default_marker_btn', function(e){
        e.preventDefault();

        if ( tgm_media_frame_default ) {
            tgm_media_frame_default.open();
            return;
        }

        tgm_media_frame_default = wp.media.frames.tgm_media_frame = wp.media({
            className: 'media-frame tgm-media-frame',
            frame: 'select',
            multiple: false,
            title: 'Default Marker Icon',
            library: {
                type: 'image'
            },
            button: {
                text:  'Use as Default Marker'
            }
        });

        tgm_media_frame_default.on('select', function(){
            var media_attachment = tgm_media_frame_default.state().get('selection').first().toJSON();
            jQuery('#upload_default_marker').val(media_attachment.url);
            jQuery("#wpgmza_mm").html("<img src=\""+media_attachment.url+"\" />");
        });
        tgm_media_frame_default.open();
    });

    var tgm_media_frame_img;
    $(document.body).on('click.tgmOpenMediaManager', '#upload_image_button', function(e){
        e.preventDefault();

        if ( tgm_media_frame_img ) {
            tgm_media_frame_img.open();
            return;
        }

        tgm_media_frame_img = wp.media.frames.tgm_media_frame = wp.media({
            className: 'media-frame tgm-media-frame',
            frame: 'select',
            editing: true,
            multiple: false,
            title: 'Upload Image',
            library: {
                type: 'image'
            },
            button: {
                text:  'Use this image'
            }
        });
        

        tgm_media_frame_img.on('select', function(){
            var media_attachment = tgm_media_frame_img.state().get('selection').first().toJSON();
            if (typeof media_attachment["sizes"]["thumbnail"] === "object" && media_attachment["sizes"]["thumbnail"]["url"].length > 0) { var wpgmza_img_thumbnail = media_attachment["sizes"]["thumbnail"]["url"]; }
            if (typeof media_attachment["sizes"]["full"] === "object" && media_attachment["sizes"]["full"]["url"].length > 0) { var wpgmza_img_full = media_attachment["sizes"]["full"]["url"]; }
            if (typeof wpgmza_img_thumbnail !== "undefined" && wpgmza_img_thumbnail.length > 0) { jQuery('#wpgmza_add_pic').val(wpgmza_img_thumbnail); }
            else { jQuery('#wpgmza_add_pic').val(wpgmza_img_full); }
        });
        tgm_media_frame_img.open();
    });


    var tgm_media_frame_custom;
    $(document.body).on('click.tgmOpenMediaManager', '#upload_custom_marker_button', function(e){
        e.preventDefault();

        if ( tgm_media_frame_custom ) {
            tgm_media_frame_custom.open();
            return;
        }

        tgm_media_frame_custom = wp.media.frames.tgm_media_frame = wp.media({
            className: 'media-frame tgm-media-frame',
            frame: 'select',
            multiple: false,
            title: 'Upload Custom Marker Icon',
            library: {
                type: 'image'
            },

            button: {
                text:  'Use as Custom Marker'
            }
        });

        tgm_media_frame_custom.on('select', function(){
            var media_attachment = tgm_media_frame_custom.state().get('selection').first().toJSON();
            jQuery('#wpgmza_add_custom_marker').val(media_attachment.url);
            jQuery("#wpgmza_cmm").html("<img src=\""+media_attachment.url+"\" />");
        });
        tgm_media_frame_custom.open();
    });
});