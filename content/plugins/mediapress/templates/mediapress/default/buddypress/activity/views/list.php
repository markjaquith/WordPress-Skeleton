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

$ids = mpp_activity_get_attached_media_ids( $activity_id );

if( $mppq->have_media() ):?>

<ul class="mpp-item-list mpp-activity-media-item-list">
<?php while( $mppq->have_media() ): $mppq->the_media(); ?>
	<li class="mpp-list-item-entry mpp-list-item-entry-<?php echo mpp_media_type();?>">
		<?php do_action( 'mpp_before_media_activity_item' ); ?>
		
		<a href="<?php mpp_media_permalink() ;?>" class="mpp-item-title mpp-media-title"><?php mpp_media_title() ;?></a>
				
		<?php do_action( 'mpp_after_media_activity_item' ); ?>
	</li>

<?php endwhile; ?>
</ul>
<?php endif; ?>
<?php mpp_reset_media_data();?>