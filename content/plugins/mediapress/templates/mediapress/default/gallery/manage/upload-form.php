<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?>

<!-- append uploaded media here -->
<div id="mpp-uploaded-media-list-gallery" class="mpp-uploading-media-list">
	<ul> 

	</ul>
</div>
<?php do_action( 'mpp_after_gallery_upload_medialist' );?>		
<!-- drop files here for uploading -->
<?php mpp_upload_dropzone( 'gallery' ); ?>
<?php do_action( 'mpp_after_gallery_upload_dropzone' );?>
<!-- show any feedback here -->
<div id="mpp-upload-feedback-gallery" class="mpp-feedback">
	<ul> </ul>
</div>
<?php do_action( 'mpp_after_gallery_upload_feedback' );?>
<input type='hidden' name='mpp-context' id='mpp-context' value='gallery' />
<input type='hidden' name='mpp-upload-gallery-id' id='mpp-upload-gallery-id' value="<?php echo mpp_get_current_gallery_id() ;?>" />