<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?>
<?php while( mpp_have_media() ): mpp_the_media(); ?>

	<div class="<?php mpp_media_class( 'mpp-u-12-24');?>">
		
		<?php do_action( 'mpp_before_media_item' ); ?>
		
		<div class="mpp-item-meta mpp-media-meta mpp-media-meta-top">
				<?php do_action( 'mpp_media_meta_top' );?>
		</div>
		
		<?php 

			$args = array(
				'src'		=> mpp_get_media_src(),
				'loop'		=> false,
				'autoplay'	=> false,
				'poster'	=> mpp_get_media_src( 'thumbnail' ),
				'width'		=> 320,
				'height'	=> 180
			);


			//$ids = mpp_get_all_media_ids();
			//echo wp_playlist_shortcode( array( 'ids' => $ids));

		?>
		<div class='mpp-item-entry mpp-media-entry mpp-audio-entry'>
			
		</div>
		
		<div class="mpp-item-content mpp-video-content mpp-video-player">
			<?php echo wp_video_shortcode(  $args );?>
		</div>
		
		<a href="<?php mpp_media_permalink() ;?>" class="mpp-item-title mpp-media-title mpp-audio-title"><?php mpp_media_title() ;?></a>
		
		<div class="mpp-item-actions mpp-media-actions mpp-video-actions">
			<?php mpp_media_action_links();?>
		</div>
		
		<div class="mpp-type-icon"><?php do_action( 'mpp_type_icon', mpp_get_media_type(), mpp_get_media() );?></div>
		
		<div class="mpp-item-meta mpp-media-meta mpp-media-meta-bottom">
				<?php do_action( 'mpp_media_meta' );?>
		</div>
		
		<?php do_action( 'mpp_after_media_item' );?>
	</div> 

<?php endwhile; ?>