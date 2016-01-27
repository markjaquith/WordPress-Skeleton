<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?>
<?php if( mpp_user_can_edit_gallery( mpp_get_current_gallery_id() ) ) :?>

<?php

	$gallery = mpp_get_current_gallery();
?>
<div class="mpp-container">
<div  id="mpp-gallery-edit-form" class="mpp-form mpp-form-stacked mpp-gallery-edit-form">
	

	
	<div class="mpp-g">
		
		<div class="mpp-u-1-1 mpp-clearfix">
			
				<?php do_action( 'mpp_before_edit_gallery_form_fields', $gallery->id ); ?>
			
		</div>
		
		<div class="mpp-u-1-2 mpp-gallery-type mpp-cover-wrapper">
			<div class="mpp-gallery-cover-edit-image mpp-cover-image"  id="mpp-cover-<?php echo $gallery->id ;?>">
				<img src="<?php	mpp_gallery_cover_src( 'thumbnail' );?>" class='mpp-image mpp-cover-image '/>
				<input type="hidden" class="mpp-gallery-id" value="<?php echo $gallery->id; ?>" />
				<input type="hidden" class="mpp-parent-id" value="<?php echo $gallery->id; ?>" />
				
			</div>
			<div id="change-gallery-cover">
				<a href="#" id="mpp-cover-upload" class="button button-primary button-small mpp-admin-button-primary"><?php _e( 'Upload New Cover', 'mediapress' ) ;?></a>
				<?php if( mpp_gallery_has_cover_image()) :?>
				<a id="mpp-cover-delete" href="<?php mpp_gallery_cover_delete_url();?>" title="<?php _e( 'Delete Cover', 'mediapress' );?>" class="button button-primary button-small mpp-admin-button-delete"><?php _e( 'Delete Cover', 'mediapress' );?> </a>
				<?php endif;?>
			</div>
		</div>
		
		<div class="mpp-u-1-2 mpp-gallery-status">
			<?php //do_action( 'mpp_admin_gallery_edit_');?>
		</div>
		
	

		<div class="mpp-u-1 mpp-gallery-description">
			<label form="mpp-gallery-description"><?php _e( 'Description', 'mediapress' );?></label>
			<textarea name='mpp-gallery-description' rows="5" class='mpp-input-1'><?php echo esc_textarea( $gallery->description) ;?></textarea>
		</div>
		<div class="mpp-u-1-1 mpp-clearfix">
			<?php do_action( 'mpp_after_edit_gallery_form_fields' ); ?>
		</div>	
		
		<input type='hidden' name="mpp-action" value='edit-gallery' />
		<input type="hidden" name='mpp-gallery-id' value="<?php echo mpp_get_current_gallery_id();?> " />
		
		<?php wp_nonce_field( 'mpp-edit-gallery', 'mpp-nonce' );?>
		
		<div class="mpp-u-1 mpp-clearfix mpp-submit-button">
			<button id="mpp-update-gallery-details" type="submit"  class='button button-primary mpp-button-primary mpp-button-secondary mpp-align-right'> <?php _e( 'Save', 'mediapress' ) ;?></button>
		</div>
		
		
	</div><!-- end of .mpp-g -->	
	
</div>
</div>

<?php else: ?>
<div class='mpp-notice mpp-unauthorized-access'>
	<p><?php _e( 'Unauthorized access!', 'mediapress' ) ;?></p>	
</div>	
<?php endif; ?>
