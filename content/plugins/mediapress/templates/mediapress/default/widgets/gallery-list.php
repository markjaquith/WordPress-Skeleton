<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * List all galleries for the current widget
 * 
 */

$query = mpp_widget_get_gallery_data( 'query' );
?>

<?php if( $query->have_galleries() ): ?>
<div class="mpp-container mpp-widget-container mpp-gallery-widget-container">
	<div class='mpp-g mpp-item-list mpp-galleries-list'>

		<?php while( $query->have_galleries() ): $query->the_gallery(); ?>

			<div class="<?php mpp_gallery_class(  'mpp-u-1-1' );?>">
				
				<?php do_action( 'mpp_before_gallery_widget_entry' ); ?>
				
				<div class="mpp-item-meta mpp-gallery-meta mpp-gallery-widget-item-meta mpp-gallery-meta-top mpp-gallery-widget-item-meta-top">
						<?php do_action( 'mpp_gallery_widget_item_meta_top' );?>
				</div>
				
				<div class="mpp-item-entry mpp-gallery-entry">
					
					<a href="<?php mpp_gallery_permalink() ;?>" <?php mpp_gallery_html_attributes( array( 'class' => 'mpp-item-thumbnail mpp-gallery-cover', 'data-mpp-context'=> 'widget' ) ); ?> >
						<img src="<?php mpp_gallery_cover_src( 'thumbnail' ) ;?>" alt ="<?php echo esc_attr( mpp_get_gallery_title() );?>" />
					</a>
					
				</div>	

				<a href="<?php mpp_gallery_permalink() ;?>" <?php mpp_gallery_html_attributes( array( 'class' => 'mpp-item-title mpp-gallery-title', 'data-mpp-context'=> 'widget' ) ); ?>>
					<?php mpp_gallery_title() ;?>
				</a>
				
				<div class="mpp-item-actions mpp-gallery-actions">
					<?php mpp_gallery_action_links();?>
				</div>
				
				<div class="mpp-type-icon"><?php do_action( 'mpp_type_icon', mpp_get_gallery_type(), mpp_get_gallery() );?></div>
				
				<div class="mpp-item-meta mpp-gallery-meta mpp-gallery-widget-item-meta mpp-gallery-meta-bottom mpp-gallery-widget-item-meta-bottom">
						<?php do_action( 'mpp_gallery_widget_item_meta' );?>
				</div>	
				
				<?php do_action( 'mpp_after_gallery_widget_entry' ); ?>
				
			</div>

		<?php endwhile; ?>
		
		<?php mpp_reset_gallery_data(); ?>
		
	</div>
</div>	
<?php else:?>
	<div class="mpp-notice mpp-no-gallery-notice">
		<p> <?php _ex( 'There are no galleries available!', 'No Gallery Message', 'mediapress' ); ?> 
	</div>
<?php endif;?>
