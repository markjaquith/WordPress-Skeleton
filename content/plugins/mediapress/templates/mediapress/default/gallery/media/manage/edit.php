<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?>
<?php if( mpp_user_can_edit_media( mpp_get_current_media_id() ) ) :?>

<?php 
	$media = mpp_get_current_media();

?>

<form method="post" action="" id="mpp-media-edit-form" class="mpp-form mpp-form-stacked mpp-media-edit-form">
	
	<div class="mpp-g">
		<?php do_action( 'mpp_before_edit_media_form_fields', $media->id ); ?>
		
		<div class="mpp-u-1-2 mpp-media-thumbnail mpp-cover-wrapper ">
			<?php do_action( 'mpp_before_edit_media_thumbnail_field', $media->id ); ?>
			<div class="mpp-media-thumbnail-edit mpp-cover-image"  id="mpp-cover-<?php echo $media->id ;?>">
				<img src="<?php	mpp_media_src( 'thumbnail' );?>" class='mpp-image mpp-cover-image ' />
				<input type="hidden" class="mpp-gallery-id" value="<?php echo mpp_get_current_gallery_id(); ?>" />
				<input type="hidden" class="mpp-parent-id" value="<?php echo $media->id; ?>" />
				<input type="hidden" class="mpp-parent-type" value="media" />
			</div>
			<?php if( $media->type !='photo' ) :?>
				<div id="change-gallery-cover">
					<a href="#" id="mpp-cover-upload"><?php _e( 'Upload New Cover', 'mediapress' ) ;?></a>
					<?php if( mpp_media_has_cover_image( $media ) ) :?>
						<a href="<?php mpp_media_cover_delete_url( $media );?>"><?php _e( 'Delete Cover', 'mediapress' );?> </a>
					<?php endif;?>
				</div>
			<?php endif; ?>
			<?php do_action( 'mpp_after_edit_media_thumbnail_field', $media->id ); ?>
		</div>
		<div class="mpp-u-1-2 mpp-media-status">
			<label form="mpp-media-status"><?php _e( 'Status', 'mediapress' );?></label>
			<?php mpp_status_dd(  array('name' => 'mpp-media-status', 'id'=>'mpp-media-status', 'selected' => $media->status, 'component' => $media->component ) );?>
		</div>
		<div class="mpp-u-1-1 mpp-media-title">
			<label form="mpp-media-title"> <?php _e( 'Title:', 'mediapress' ) ;?></label>
			<input type='text' class='mpp-input-1' placeholder="<?php _ex( 'Media title (Required)', 'Placeholder for media edit form title', 'mediapress' ) ;?>" name='mpp-media-title' value="<?php echo esc_attr( $media->title );?>"/>
			
		</div>

		<div class="mpp-u-1 mpp-media-description">
			<label form="mpp-media-description"><?php _e('Description', 'mediapress' );?></label>
			<textarea name='mpp-media-description' rows="5" class='mpp-input-1'><?php echo esc_textarea( $media->description) ;?></textarea>
		</div>
		<?php do_action( 'mpp_after_edit_media_form_fields' ); ?>
		<input type='hidden' name="mpp-action" value='edit-media' />
		<input type="hidden" name='mpp-media-id' value="<?php echo mpp_get_current_media_id();?> " />
		<?php wp_nonce_field( 'mpp-edit-media', 'mpp-nonce' );?>
		
		<div class="mpp-u-1 mpp-clearfix mpp-submit-button">
			<button type="submit"  class='mpp-button-primary mpp-button-secondary mpp-align-right'> <?php _e( 'Save', 'mediapress' ) ;?></button>
		</div>
		
		
	</div><!-- end of .mpp-g -->	
	
</form>


<?php else: ?>
<div class='mpp-notice mpp-unauthorized-access'>
	<p><?php _e( 'Unauthorized access!', 'mediapress' ) ;?></p>	
</div>	
<?php endif; ?>
