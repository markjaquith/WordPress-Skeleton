<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
/**
 * Media/Gallery Activity Post Form
 *
 */

?>

<form action="<?php bp_activity_post_form_action(); ?>" method="post" id="mpp-whats-new-form" class="mpp-activity-post-form clearfix" name="mpp-whats-new-form" role="complementary">

	<?php do_action( 'mpp_before_activity_post_form' ); ?>

	<div id="mpp-whats-new-avatar">
		<a href="<?php echo bp_loggedin_user_domain(); ?>">
			<?php bp_loggedin_user_avatar( 'width=' . bp_core_avatar_thumb_width() . '&height=' . bp_core_avatar_thumb_height() ); ?>
		</a>
	</div>

	<p class="activity-greeting">
		<?php	printf( __( "Want to say Something, %s?", 'mediapress' ), bp_get_user_firstname( bp_get_loggedin_user_fullname() ) );
	?></p>

	<div id="mpp-whats-new-content">
		
		<div id="mpp-whats-new-textarea">
			<textarea name="mpp-whats-new" id="mpp-whats-new" cols="50" rows="3"><?php if ( isset( $_GET['r'] ) ) : ?>@<?php echo esc_textarea( $_GET['r'] ); ?> <?php endif; ?></textarea>
		</div>

		<div id="mpp-whats-new-options">
			<div id="mpp-whats-new-submit">
				<input type="submit" name="mpp-aw-whats-new-submit" id="mpp-aw-whats-new-submit" value="<?php esc_attr_e( 'Post', 'mediapress' ); ?>" />
			</div>


			<?php if ( bp_is_active('groups') &&  bp_is_group() ) : ?>

				<input type="hidden" id="mpp-whats-new-post-object" name="whats-new-post-object" value="groups" />
				<input type="hidden" id="mpp-whats-new-post-in" name="whats-new-post-in" value="<?php bp_group_id( groups_get_current_group() ); ?>" />

			<?php endif; ?>
				<?php if( mpp_is_single_gallery() && !mpp_is_single_media()  ):?>
					<input type="hidden" name='mpp-item-id' id="mpp-item-id" value="<?php echo mpp_get_current_gallery_id();?>" />
					<input type="hidden" name='mpp-activity-type' id="mpp-activity-type" value="gallery" />
				<?php else:?>
						
					<input type="hidden" name='mpp-item-id' id="mpp-item-id" value="<?php echo mpp_get_current_media_id();?>" />
					<input type="hidden" name='mpp-activity-type' id="mpp-activity-type" value="media" />
				<?php endif; ?>
					
			<?php do_action( 'bp_activity_post_form_options' ); ?>

		</div><!-- #whats-new-options -->
	</div><!-- #whats-new-content -->

	<?php wp_nonce_field( 'post_update', '_wpnonce_post_update' ); ?>
	<?php do_action( 'mpp_after_activity_post_form' ); ?>

</form><!-- #whats-new-form -->
