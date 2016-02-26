<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
/*** 
 * List Photos attched to an activity
 * 
 * Media List attached to an activity
 * 
 */
$activity_id = bp_get_activity_id();

$mppq = new MPP_Cached_Media_Query( array( 'in' => mpp_activity_get_attached_media_ids( $activity_id ) ) );

if( $mppq->have_media() ):?>
	<div class="mpp-container mpp-media-list mpp-activity-media-list mpp-activity-photo-list">

	<?php while( $mppq->have_media() ): $mppq->the_media(); ?>

		<a href="<?php mpp_media_permalink();?>" ><img src="<?php mpp_media_src( 'thumbnail' );?>" class='mpp-attached-media-item' data-mpp-activity-id="<?php echo $activity_id;?>" title="<?php echo esc_attr( mpp_get_media_title() );?>" /></a>

	<?php endwhile; ?>
	</div><!-- end of .mpp-activity-media-list -->
<?php endif; ?>
<?php mpp_reset_media_data(); ?>