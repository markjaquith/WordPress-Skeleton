<?php
/**
 * List all items as unordered list
 * 
 */
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?>
<?php 
	$gallery = mpp_get_current_gallery();
	$type = $gallery->type;
?>
<ul class="mpp-u mpp-item-list mpp-list-item-<?php echo $type;?>">
	
<?php while( mpp_have_media() ): mpp_the_media(); ?>
	
	<li class="mpp-list-item-entry mpp-list-item-entry-<?php echo $type;?>">
		
		<?php do_action( 'mpp_before_media_item' ); ?>
		
		<a href="<?php mpp_media_permalink() ;?>" class="mpp-item-title mpp-media-title"><?php mpp_media_title() ;?></a>
		
		<div class="mpp-item-actions mpp-media-actions">
			<?php mpp_media_action_links();?>
		</div>
				
		<?php do_action( 'mpp_after_media_item' ); ?>
		
	</li>

<?php endwhile; ?>
	
</ul>