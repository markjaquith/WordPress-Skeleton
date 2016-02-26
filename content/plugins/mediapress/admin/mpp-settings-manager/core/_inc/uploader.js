/**
 * Credit
 * Adapted from Matt's code
 * @link  http://www.webmaster-source.com/2013/02/06/using-the-wordpress-3-5-media-uploader-in-your-plugin-or-theme/
 * Modified to be used with OptionsBuddy
 */
jQuery(document).ready(function($){
 
 //hide actions
 $('.delete-settings-image').hide();
 $('.settings-image-action-visible').show();
 $("img[src='']").hide();
 //on remove
 $('.delete-settings-image').click(function(){
    $this= $(this);
    $this.siblings('img').hide().attr('src','');
    $this.parent().next('.hidden-image-url').val('');
    $this.hide();
     return false;
 });
 
 
 
    var custom_uploader;
 
 
    $('.settings-upload-image-button').click(function(e) {
 
        e.preventDefault();
        var $this =$(this);
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
 
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: $this.data('uploader-title'),
            button: {
                text: $this.data('btn-title')
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
           // console.log(attachment);
            
            $('#'+get_id($this.data('id'))).val(attachment.url);
           
           //now update image
           $this.siblings('.settings-image-placeholder').find('img').show().attr('src',attachment.url);
           $this.siblings('.settings-image-placeholder').find('.delete-settings-image').show();
            
        });
 
        //Open the uploader dialog
        custom_uploader.open();
 
    });
 //escape id for js use
 function get_id( $id ){
     //escape and return teh id
     $id = $id.replace('[','\\[');
     $id = $id.replace(']','\\]');
     return $id;
 }
});