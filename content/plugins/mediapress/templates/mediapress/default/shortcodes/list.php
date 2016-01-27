<?php 
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 
 * Item list in shortcode list view
 * You can override it in yourtheme/mediapress/default/shortcodes/list.php
 * 
 */
$query = mpp_shortcode_get_media_data( 'query' );
?>

<?php if ( $query->have_media() ) : ?>

<ul class="mpp-item-list mpp-list-item-shortcode">
	
<?php while( $query->have_media() ): $query->the_media(); ?>
	
	<li class="mpp-list-item-entry mpp-list-item-entry-<?php mpp_media_type();?>">
	
		<?php do_action( 'mpp_before_media_shortcode_item' ); ?>
		
		<a href="<?php mpp_media_permalink() ;?>" class="mpp-item-title mpp-media-title"><?php mpp_media_title() ;?></a>
		
		<?php do_action( 'mpp_after_media_shortcode_item' ); ?>
	
	</li>

<?php endwhile; ?>
	
</ul>
<?php mpp_reset_media_data();?>
<?php endif;?>