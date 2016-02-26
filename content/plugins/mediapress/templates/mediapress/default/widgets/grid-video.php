<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * mediapress/widgets/loop-video-
 */
$query = mpp_widget_get_media_data('query' ); ?>

<?php if( $query->have_media() ): ?>

<div class="mpp-container mpp-widget-container mpp-media-widget-container mpp-media-video-widget-container">
	<div class='mpp-g mpp-item-list mpp-media-list mpp-video-list'>

		<?php while( $query->have_media() ): $query->the_media(); ?>

		<div class="<?php mpp_media_class( 'mpp-widget-item mpp-widget-video-item '. mpp_get_grid_column_class( 1 ) );?>">
			<?php do_action( 'mpp_before_media_widget_item' ); ?>
			
			<div class="mpp-item-meta mpp-media-meta mpp-media-widget-item-meta mpp-media-meta-top mpp-media-widget-item-meta-top">
				<?php do_action( 'mpp_media_widget_item_meta_top' );?>
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
				<div class='mpp-item-entry mpp-media-entry mpp-video-entry'>

				</div>
				<div class="mpp-item-content mpp-video-content mpp-video-player">
					<?php echo wp_video_shortcode(  $args );?>
				</div>
				<a href="<?php mpp_media_permalink() ;?>" <?php mpp_media_html_attributes( array( 'class' => "mpp-item-title mpp-media-title mpp-video-title", 'data-mpp-context' => 'widget' ) ); ?>><?php mpp_media_title() ;?></a>
				<div class="mpp-item-actions mpp-media-actions mpp-audio-actions">
					<?php mpp_media_action_links();?>
				</div>
				<div class="mpp-type-icon"><?php do_action( 'mpp_type_icon', mpp_get_media_type(), mpp_get_media() );?></div>
				
				<div class="mpp-item-meta mpp-media-meta mpp-media-widget-item-meta mpp-media-meta-bottom mpp-media-widget-item-meta-bottom">
					<?php do_action( 'mpp_media_widget_item_meta' );?>
				</div>
				
				<?php do_action( 'mpp_after_media_widget_item' ); ?>
			</div> 


		<?php endwhile; ?>
	<?php mpp_reset_media_data(); ?>
	</div>
</div>	
<?php endif; ?>		