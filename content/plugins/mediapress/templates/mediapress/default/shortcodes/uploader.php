<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?>
<div class="mpp-upload-shortcode">
	<!-- append uploaded media here -->
	<div id="mpp-uploaded-media-list-shortcode" class="mpp-uploading-media-list">
		<ul> 

		</ul>
	</div>
	<?php do_action( 'mpp_after_shortcode_upload_medialist' );?>		
	<!-- drop files here for uploading -->
	<?php mpp_upload_dropzone( $context ); ?>
	<?php do_action( 'mpp_after_shortcode_upload_dropzone' );?>
	<!-- show any feedback here -->
	<div id="mpp-upload-feedback-shortcode" class="mpp-feedback">
		<ul> </ul>
	</div>
	<?php do_action( 'mpp_after_shortcode_upload_feedback' );?>
	<input type='hidden' name='mpp-context' id='mpp-context' value="<?php echo $context;?>" />
	<?php if ( $type ):?>
		<input type='hidden' name='mpp-uploading-media-type' class='mpp-uploading-media-type' value="<?php echo $type ;?>" />
	<?php endif;?>
	<?php if( $gallery_id ):?>
	<input type='hidden' name='mpp-shortcode-upload-gallery-id' id='mpp-shortcode-upload-gallery-id' value="<?php echo $gallery_id ;?>" />
	
	<?php else :?>
	<?php 
	
		mpp_list_galleries_dropdown( array(
			'name'				=> 'mpp-shortcode-upload-gallery-id',
			'id'				=> 'mpp-shortcode-upload-gallery-id',
			'selected'			=> $gallery_id,
			'type'				=> $type,
			'status'			=> $status,
			'component'			=> $component,
			'component_id'		=> $component_id,
			'posts_per_page'	=> -1,
			'label_empty'		=> $label_empty
		) );
	?>
	<?php endif;?>
</div>