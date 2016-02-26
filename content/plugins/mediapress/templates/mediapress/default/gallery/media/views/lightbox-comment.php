<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
$media = mpp_get_current_media();
?>
<div class="mpp-lightbox-content mpp-clearfix">
	<?php $media = mpp_get_media();?>
	<div class="mpp-lightbox-media-container">
		
		<?php do_action( 'mpp_before_lightbox_media', $media );?>
		
		<div class="mpp-item-meta mpp-media-meta mpp-lightbox-media-meta mpp-lightbox-media-meta-top">
			<?php do_action( 'mpp_lightbox_media_meta_top', $media ); ?> 
		</div>
		
		
		<a href="<?php mpp_media_permalink();?>" title="<?php echo esc_attr( mpp_get_media_title() ) ;?>">
			<img src="<?php mpp_media_src() ;?>" alt="<?php echo esc_attr( mpp_get_media_title() ) ;?>" class="mpp-lightbox-single-media"/>
		</a>
		
		<div class="mpp-item-meta mpp-media-meta mpp-lightbox-media-meta mpp-lightbox-media-meta-bottom">
			<?php do_action( 'mpp_lightbox_media_meta', $media ); ?> 
		</div>
		
		<?php do_action( 'mpp_after_lightbox_media', $media );?>	
		
	</div>
	
	<div class="mpp-lightbox-activity-container">
		<div class="mpp-lightbox-media-uploader-meta mpp-clearfix">
			<div class="mpp-lightbox-media-uploader-avatar">
				<a href="<?php echo bp_core_get_user_domain( mpp_get_media_creator_id() ); ?>">
					<?php echo bp_core_fetch_avatar ( array( 'item_id' => mpp_get_media_creator_id(), 'object'=> 'members', 'width' => bp_core_avatar_thumb_width(), 'height'=>  bp_core_avatar_thumb_height() ) ); ?>
				</a>
			</div>
			<div class="mpp-lightbox-uploader-upload-details">
				<div class="mpp-lightbox-uploader-link">
					<?php echo bp_core_get_userlink( mpp_get_media_creator_id() );?>
				</div>
			</div>
		</div><!--end of the top row -->
		<div class="mpp-item-description mpp-media-description mpp-lightbox-media-description mpp-clearfix">
			<?php			mpp_media_description();?>
		</div>
		
	<?php do_action( 'mpp_before_lightbox_media_activity', $media );?>
		<?php mpp_locate_template( array( 'gallery/media/views/lightbox/activity.php' ), true ); ?>
		
	<?php do_action( 'mpp_after_lightbox_media_activity', $media );?>
		
	</div>
	
</div>
