<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
/*** 
 * 
 * Attachment in single media comment
 * This is a fallback template for new media types
 * 
 */
$activity_id = bp_get_activity_id();

$mppq = new MPP_Cached_Media_Query( array( 'in' => (array) mpp_activity_get_media_id( $activity_id ) ) );

if( $mppq->have_media() ):?>
	<div class="mpp-container mpp-media-list mpp-activity-media-list">

		<?php while( $mppq->have_media() ): $mppq->the_media(); ?>

			<a href="<?php mpp_media_permalink();?>" ><img src="<?php mpp_media_src( 'thumbnail' );?>" class='mpp-attached-media-item' data-mpp-activity-id="<?php echo $activity_id;?>" /></a>

		<?php endwhile; ?>
	</div>
<?php endif; ?>
<?php mpp_reset_media_data();?>