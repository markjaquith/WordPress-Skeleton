<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Bulk Edit Media template for single gallery bulk media edit page
 * action:mediapress/gallery/galleryname/manage/edit/
 * 
 */


?>
<a href="#" id = "mpp-reload-bulk-edit-tab" class="mpp-reload" title="<?php _e( 'Reload media edit panel', 'mediapress' );?>"><span class="dashicons dashicons-update"></span> <?php _e( 'Reload', 'mediapress' );?></a>
<?php
$current_gallery_id = mpp_get_current_gallery_id();


	//fetch all media in the gallery
	$items = mediapress()->the_media_query;
?>
<?php if( $items->have_media() ):?>
<div class="mpp-container" id="mpp-container">
	<div id="mpp-media-bulkedit-div" class="mpp-form mpp-form-stacked mpp-form-bulkedit-media ">
		
		<?php do_action( 'mpp_before_bulkedit_media_form' )  ; ?>
		
		<div class="mpp-g mpp-media-edit-bulk-action-row">
			<div class="mpp-u-2-24">
				<?php //allow to check/uncheck ?>
				<input type="checkbox" name="mpp-check-all" value="1" id="mpp-check-all" />
			</div>

			<div class="mpp-u-17-24">

				<select name="mpp-edit-media-bulk-action" id="mpp-edit-media-bulk-action">
					<option value=""><?php _e( 'Bulk Action', 'mediapress' );?></option>
					<option value="delete"><?php _e( 'Delete', 'mediapress' );?></option>
				</select>
				<?php do_action( 'mpp_after_media_bulkedit_actions' ); ?>
				<?php //bulk action ?>
				<button class="button button-primary mpp-button mpp-button-success mpp-button-primary mpp-bulk-action-apply-button" name="bulk-action-apply" id="bulk-action-apply"><?php _e( 'Apply', 'mediapress' ) ;?></button>

			</div>

			<div class="mpp-u-5-24">
				<button type="submit" name="mpp-edit-media-submit"  id="mpp-edit-media-submit" class="button button-primary"><?php _e( 'Update','mediapress' );?> </button>

			</div>

		</div> <!-- end of bulk action row -->
		
		<?php do_action( 'mpp_before_bulkedit_media_list' )  ; ?>
		
		<div id="mpp-editable-media-list" class="mpp-g">
	
	

			<?php while( $items->have_media() ) : $items->the_media(); ?>
	
				<?php 
					$media = mpp_get_media();
					$media_id = $media->id;
				?>
		
				<div class='mpp-edit-media' id="mpp-edit-media-<?php mpp_media_id(); ?>">

					<div class="mpp-u-2-24">
						<input type='checkbox' name="mpp-delete-media-check[<?php echo $media_id;?>]" class="mpp-delete-media-check" value='1' />
					</div>	

					<div class='mpp-u-8-24 mpp-edit-media-cover'>
						
						<?php do_action( 'mpp_before_edit_media_item_thumbnail' ); ?>
						<img src="<?php mpp_media_src('thumbnail');?>" class="mpp-image" />
						<?php do_action( 'mpp_after_edit_media_item_thumbnail' ); ?>
						
					 </div>

					<div class='mpp-u-14-24'>
							<div class="mpp-g">
								<?php do_action( 'mpp_before_edit_media_item_form_fields' ); ?>

								<?php $status_name = 'mpp-media-status[' .$media_id .']'; ?>	
								<div class="mpp-u-1-1 mpp-media-status">
									<label for="<?php echo $status_name;?>"><?php _ex( 'Status', 'Media status label on edit media page', 'mediapress' ); ?></label>
									<?php mpp_status_dd( array( 'name' => $status_name, 'id'=> $status_name, 'selected' => mpp_get_media_status(), 'component' => $media->component  ) );?>
								</div>
								
								<div class="mpp-u-1-1 mpp-media-title">
									<label for="mpp-gallery-title[<?php echo $media_id;?>]"><?php _ex( 'Title:', 'Media title label on edit media page', 'mediapress' ); ?></label>
									<input type='text' class='mpp-input-1' placeholder="<?php _ex( 'Title (Required)', 'Placeholder for media edit form title', 'mediapress' ) ;?>" name="mpp-media-title[<?php echo $media_id;?>]" value="<?php echo esc_attr(mpp_get_media_title() );?>"/>

								</div>

								<div class="mpp-u-1 mpp-media-description">
									<label for="mpp-media-description"><?php _ex( 'Description', 'Media description label on edit media page', 'mediapress' );?></label>
									<textarea name="mpp-media-description[<?php echo $media_id;?>]" rows="5" class='mpp-input-1'><?php echo esc_textarea( mpp_get_media_description() ) ;?></textarea>
								</div>

								<?php do_action( 'mpp_after_edit_media_item_form_fields' ); ?>


							</div><!-- end of .mpp-g -->	
					</div>	<!--end of edit section -->
					<hr />
				</div>	
			<?php endwhile;	?>
			
			<?php $ids = $items->get_ids(); ?>
			
			<input type='hidden' name='mpp-editing-media-ids' value="<?php echo join( ',', $ids );?>" />

		</div>
		
		<?php do_action( 'mpp_after_bulkedit_media_list' )  ; ?>
		
			
		<?php mpp_reset_media_data() ;?>
		
				
		<?php //please do not delete the 2 lines below ; ?>
		<input type='hidden' name="mpp-action" value='edit-gallery-media' />
		<?php wp_nonce_field( 'mpp-edit-gallery-media', 'mpp-nonce' ); ?>		

		<button type="submit" name="mpp-edit-media-submit"  id="mpp-edit-media-submit" class="button button-primary"><?php _e( 'Update','mediapress' );?> </button>

	</div>
</div>
<?php else: ?>

	<div class="mpp-notice mpp-empty-gallery-notice">
		<p><?php _e( 'There is no media in this gallery. Please add media to see them here!', 'mediapress' );?></p>
	</div>

<?php endif; ?>