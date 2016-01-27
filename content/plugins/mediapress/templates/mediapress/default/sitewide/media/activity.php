<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
/**
 * Single Media Activity list
 *
 * @package mediapress
 */
//is buddypress active and activity enabled? If not, no need to load this page
if( !  function_exists( 'bp_is_active' ) || ! bp_is_active( 'activity' ) ) {
	return ;
}
//is commenting enabled?
if( ! mpp_get_option( 'enable_media_comment' )  ) {
	return;
}

?>

<?php do_action( 'mpp_before_activity_loop' ); ?>

<div class="mpp-activity mpp-media-activity " id="mpp-media-activity-list">
	
	<?php if( is_user_logged_in() && mpp_media_user_can_comment( mpp_get_current_media_id() ) ) :?>
		
		<?php mpp_locate_template( array( 'buddypress/activity/post-form.php' ), true ) ;?>

	<?php endif;?>
	
	<?php if ( mpp_media_has_activity( array( 'media_id' => mpp_get_media_id() ) ) ) : ?>

		<?php /* Show pagination if JS is not enabled, since the "Load More" link will do nothing */ ?>
		<noscript>
			<div class="pagination">
				<div class="pag-count"><?php bp_activity_pagination_count(); ?></div>
				<div class="pagination-links"><?php bp_activity_pagination_links(); ?></div>
			</div>
		</noscript>

		<?php if ( empty( $_POST['page'] ) ) : ?>

			<ul id="mpp-activity-stream" class="mpp-activity-list item-list">

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

		<form action="" name="mpp-activity-loop-form" id="mpp-activity-loop-form" method="post">

			<?php wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ); ?>

		</form>

	<?php endif; ?>
</div><!-- /#mpp-media-activity-list -->
