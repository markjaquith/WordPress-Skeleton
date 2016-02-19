<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * List all galleries for the current component
 * 
 */
?>

<?php if ( mpp_have_galleries() ): ?>
	<div class='mpp-g mpp-item-list mpp-galleries-list'>

		<?php while( mpp_have_galleries() ): mpp_the_gallery(); ?>

			<div class="<?php mpp_gallery_class(  mpp_get_gallery_grid_column_class() );?>" id="mpp-gallery-<?php mpp_gallery_id();?>">
				
				<?php do_action( 'mpp_before_gallery_entry' );?>
				
				<div class="mpp-item-meta mpp-gallery-meta mpp-gallery-meta-top">
						<?php do_action( 'mpp_gallery_meta_top' );?>
				</div>
				
				<div class="mpp-item-entry mpp-gallery-entry">
					<a href="<?php mpp_gallery_permalink() ;?>" <?php mpp_gallery_html_attributes( array( 'class' => 'mpp-item-thumbnail mpp-gallery-cover' ) ); ?>>
						<img src="<?php mpp_gallery_cover_src( 'thumbnail' ) ;?>" alt ="<?php echo esc_attr( mpp_get_gallery_title() );?>" />
					</a>
				</div>	
								
				<?php do_action( 'mpp_before_gallery_title' ); ?>
				
				<a href="<?php mpp_gallery_permalink() ;?>" class="mpp-gallery-title"><?php mpp_gallery_title() ;?></a>
				
				<?php do_action( 'mpp_before_gallery_actions' ); ?>	
				
				<div class="mpp-item-actions mpp-gallery-actions">
					<?php mpp_gallery_action_links();?>
				</div>
				
				<?php do_action( 'mpp_before_gallery_type_icon' ); ?>
				
				<div class="mpp-type-icon"><?php do_action( 'mpp_type_icon', mpp_get_gallery_type(), mpp_get_gallery() );?></div>
				
				<div class="mpp-item-meta mpp-gallery-meta mpp-gallery-meta-bottom">
						<?php do_action( 'mpp_gallery_meta' );?>
				</div>
				
				<?php do_action( 'mpp_after_gallery_entry' ); ?>
			</div>

		<?php endwhile; ?>
		<?php mpp_gallery_pagination(); ?>
	</div>
	<?php mpp_reset_gallery_data();?> 
<?php else:?>
	<div class="mpp-notice mpp-no-gallery-notice">
		<p> <?php _ex( 'There are no galleries available!', 'No Gallery Message', 'mediapress' ); ?> 
	</div>
<?php endif;?>
