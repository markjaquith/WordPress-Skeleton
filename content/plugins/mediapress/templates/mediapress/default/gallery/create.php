<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
//action:mediapress/create :- Create Gallery template
?>
<div id="mpp-gallery-create-form-wrapper" class="mpp-container" >
	
	<?php if( mpp_user_can_create_gallery( mpp_get_current_component(), mpp_get_current_component_id() ) ) :?>

		<form method="post" action="" id="mpp-gallery-create-form" class="mpp-form mpp-form-stacked mpp-gallery-create-form">
			<?php
			$title = $description = $status = $type = '';

			if( ! empty( $_POST['mpp-gallery-title'] ) )
				$title = $_POST['mpp-gallery-title'];

			if( ! empty( $_POST['mpp-gallery-description'] ) )
				$description = $_POST['mpp-gallery-description'];

			if( ! empty( $_POST['mpp-gallery-status'] ) )
				$status = $_POST['mpp-gallery-status'];

			if( ! empty( $_POST['mpp-gallery-type'] ) )
				$type = $_POST['mpp-gallery-type'];


			?>
			
			<?php	do_action( 'mpp_before_create_gallery_form' ) ;?>
			
			<div class="mpp-g mpp-form-wrap">
				<div class="mpp-u-1-1 mpp-before-create-gallery-form-fields">
					
					<?php //use this hook to add anything at the top of the gallery create form  ?>
					<?php do_action( 'mpp_before_create_gallery_form_fields' ); ?>

				</div>

				<div class="mpp-u-1-2 mpp-editable-gallery-type">
					<label form="mpp-gallery-type"><?php _e( 'Type', 'mediapress' );?></label>
					<?php mpp_type_dd( array( 'selected' => $type, 'component' => mpp_get_current_component() ))?>
				</div>
				
				<div class="mpp-u-1-2 mpp-editable-gallery-status">
					<label form="mpp-gallery-status"><?php _e( 'Status', 'mediapress' );?></label>
					<?php mpp_status_dd( array( 'selected' => $status, 'component' => mpp_get_current_component() ) );?>
				</div>
				
				<div class="mpp-u-1-1 mpp-editable-gallery-title">
					<label form="mpp-gallery-title"><?php _e( 'Title:', 'mediapress' );?></label>
					<input type='text' value="<?php echo esc_attr( $title )?>" class='mpp-input-1' placeholder="<?php _ex( 'Gallery Title (Required)', 'Placeholder for gallery create form title', 'mediapress' ) ;?>" name='mpp-gallery-title' />

				</div>

				<div class="mpp-u-1 mpp-editable-gallery-description">
					<label form="mpp-gallery-description"><?php _e('Description', 'mediapress');?></label>
					<textarea name='mpp-gallery-description' rows="5" class='mpp-input-1'><?php echo esc_textarea( $description );?></textarea>
				</div>

				<div class="mpp-u-1-1 mpp-after-create-gallery-form-fields">
					
					<?php //use this hook to add any extra data here for settings or other things at the bottom of create gallery form ?>
					<?php do_action( 'mpp_after_create_gallery_form_fields' ); ?>
					
				</div>
				<?php	do_action( 'mpp_before_create_gallery_form_submit_field' ) ;?>
				<?php
					//do not delete this line, we need it to validate
					wp_nonce_field( 'mpp-create-gallery', 'mpp-nonce' );
					//also do not delete the next line <input type='hidde' name='mpp-action' value='create-gallery' >
				?>
				
				<input type='hidden' name="mpp-action" value='create-gallery' />

				<div class="mpp-u-1 mpp-clearfix mpp-submit-button">
					<button type="submit"  class='mpp-align-right mpp-button-primary mpp-create-gallery-button '> <?php _e( 'Create', 'mediapress' ) ;?></button>
				</div>


			</div><!-- end of .mpp-g -->
			
			<?php	do_action( 'mpp_after_create_gallery_form' ) ;?>
	</form>


	<?php else: ?>
		<div class='mpp-notice mpp-unauthorized-access'>
			<p><?php _e( 'Unauthorized access!', 'mediapress' ) ;?></p>	
		</div>	
	<?php endif; ?>
</div><!-- end of mpp-container -->