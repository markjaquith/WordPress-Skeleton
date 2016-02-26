<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?>
<?php while( mpp_have_media() ): mpp_the_media(); ?>

	<div class="mpp-u <?php mpp_media_class( mpp_get_media_grid_column_class() );?>">
		
		<?php do_action( 'mpp_before_media_item' ); ?>
		
		<div class="mpp-item-meta mpp-media-meta mpp-media-meta-top">
				<?php do_action( 'mpp_media_meta_top' );?>
		</div>
		
		<div class='mpp-item-entry mpp-media-entry mpp-photo-entry'>
			<a href="<?php mpp_media_permalink() ;?>" <?php mpp_media_html_attributes( array( 'class' => 'mpp-item-thumbnail mpp-media-thumbnail mpp-photo-thumbnail' ) ); ?>>
				<img src="<?php mpp_media_src( 'thumbnail' ) ;?>" alt="<?php echo esc_attr( mpp_get_media_title() );?> "/>
			</a>
		</div>
		
		<div class="mpp-item-actions mpp-media-actions mpp-photo-actions">
			<?php mpp_media_action_links();?>
		</div>
		
		<div class="mpp-item-meta mpp-media-meta mpp-media-meta-bottom">
				<?php do_action( 'mpp_media_meta' );?>
		</div>	
		
			<?php do_action( 'mpp_after_media_item' ); ?>
	</div>

<?php endwhile; ?>