<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?>
<?php $activity_id = bp_get_activity_id();

$mppq = new MPP_Cached_Media_Query( array( 'in' => mpp_activity_get_attached_media_ids( $activity_id ) ) );

if( $mppq->have_media() ):?>
	<div class="mpp-container mpp-media-list mpp-activity-media-list mpp-activity-audio-list mpp-activity-audio-player">

		<?php while( $mppq->have_media() ): $mppq->the_media(); ?>

			<div class="mpp-item-content mpp-audio-content mpp-audio-player">
				<?php mpp_media_content() ;?>
			</div>

		<?php endwhile; ?>
		<script type='text/javascript'>
			mpp_mejs_activate(<?php echo bp_get_activity_id();?>);
		</script>
	</div>
<?php endif; ?>
<?php mpp_reset_media_data(); ?>