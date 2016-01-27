<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?>
<?php while( mpp_have_media() ): mpp_the_media(); ?>

	<div class="<?php mpp_media_class( 'mpp-u-6-24');?>">
						
		<?php do_action( 'mpp_before_media_item' ); ?>
		
		<div class="mpp-item-meta mpp-media-meta mpp-media-meta-top">
				<?php do_action( 'mpp_media_meta_top' );?>
		</div>
		
		<div class='mpp-item-entry mpp-media-entry mpp-audio-entry'>
			<a href="<?php mpp_media_permalink() ;?>" <?php mpp_media_html_attributes( array( 'class' => "mpp-item-thumbnail mpp-media-thumbnail mpp-audio-thumbnail" ) ); ?>>
				<img src="<?php mpp_media_src('thumbnail') ;?>" alt="<?php mpp_media_title();?> "/>
			</a>
		</div>
		
		<div class="mpp-item-content mpp-audio-content mpp-audio-player">
			<?php mpp_media_content() ;?>
		</div>
		
		<a href="<?php mpp_media_permalink() ;?>" class="mpp-item-title mpp-media-title mpp-audio-title"><?php mpp_media_title() ;?></a>
		
		<div class="mpp-item-actions mpp-media-actions mpp-audio-actions">
			<?php mpp_media_action_links();?>
		</div>
		
		<div class="mpp-type-icon"><?php do_action( 'mpp_type_icon', mpp_get_media_type(), mpp_get_media() );?></div>
		
		<div class="mpp-item-meta mpp-media-meta mpp-media-meta-bottom">
				<?php do_action( 'mpp_media_meta' );?>
		</div>
		
		<?php do_action( 'mpp_after_media_item' ); ?>
	</div> 

<?php endwhile; ?>