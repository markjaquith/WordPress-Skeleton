<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * action:mediapress/gallery/gallery_name/ Activity comments
 * Activity Loop to show Single gallery Item Activity
 *
 **/
if( !  function_exists( 'bp_is_active' ) || ! bp_is_active( 'activity' ) ) {
	return ;
}
//if gallery comment is not enabled do not load it?
if( ! mpp_get_option( 'enable_gallery_comment' ) ) {
	return;
}

?>


<div class="mpp-activity mpp-media-activity" id="mpp-media-activity-list">
	
	<?php if( is_user_logged_in() && mpp_user_can_comment_on_gallery( mpp_get_current_gallery_id() ) ) :?>
		
		<?php mpp_locate_template( array('buddypress/activity/post-form.php'), true ) ;?>

	<?php endif;?>
	<?php do_action( 'mpp_before_activity_loop' ); ?>
	<?php if ( mpp_gallery_has_activity( array( 'gallery_id' => mpp_get_gallery_id() ) ) ) : ?>

		<?php /* Show pagination if JS is not enabled, since the "Load More" link will do nothing */ ?>
		<noscript>
			<div class="pagination">
				<div class="pag-count"><?php bp_activity_pagination_count(); ?></div>
				<div class="pagination-links"><?php bp_activity_pagination_links(); ?></div>
			</div>
		</noscript>

		<?php if ( empty( $_POST['page'] ) ) : ?>

			<ul id="mpp-activity-stream" class="mpp-activity-list clearfix item-list">

		<?php endif; ?>

		<?php while ( bp_activities() ) : bp_the_activity(); ?>

			<?php mpp_locate_template( array( 'buddypress/activity/entry.php' ), true, false ); ?>

		<?php endwhile; ?>

		<?php if ( bp_activity_has_more_items() ) : ?>

			<li class="load-more">
				<a href="#more"><?php _e( 'Load More', 'mediapress' ); ?></a>
			</li>

		<?php endif; ?>

		<?php if ( empty( $_POST['page'] ) ) : ?>

			</ul>
		<?php endif; ?>


	<?php endif; ?>

	<?php do_action( 'mpp_after_activity_loop' ); ?>

	<?php if ( empty( $_POST['page'] ) ) : ?>

		<form action="" name="activity-loop-form" id="activity-loop-form" method="post">

			<?php wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ); ?>

		</form>

	<?php endif; ?>
</div>
