jQuery(document).ready(function() {

var clicked_on_imgbtn = false;
var clicked_on_markerbtn = false;
var clicked_on_custommarkerbtn = false;

jQuery('#upload_image_button').click(function() {
 formfield = jQuery('#wpgmza_add_pic').attr('name');
 clicked_on_imgbtn = true;
 clicked_on_markerbtn = false;
 clicked_on_custommarkerbtn = false;
 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
 return false;
});


jQuery('#upload_default_marker_btn').click(function() {
 formfield = jQuery('#upload_default_marker').attr('name');
 clicked_on_imgbtn = false;
 clicked_on_markerbtn = true;
 clicked_on_custommarkerbtn = false;
 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
 return false;
});

jQuery('#upload_custom_marker_button').click(function() {
 formfield = jQuery('#wpgmza_add_custom_marker').attr('name');
 clicked_on_imgbtn = false;
 clicked_on_markerbtn = false;
 clicked_on_custommarkerbtn = true;
 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
 return false;
});


window.send_to_editor = function(html) {
 imgurl = jQuery('img',html).attr('src');
 if (clicked_on_imgbtn) { jQuery('#wpgmza_add_pic').val(imgurl); }
 if (clicked_on_markerbtn) { jQuery('#upload_default_marker').val(imgurl); jQuery("#wpgmza_mm").html("<img src=\""+imgurl+"\" />"); }
 if (clicked_on_custommarkerbtn) { jQuery('#wpgmza_add_custom_marker').val(imgurl); jQuery("#wpgmza_cmm").html("<img src=\""+imgurl+"\" />"); }
 tb_remove();
}



});