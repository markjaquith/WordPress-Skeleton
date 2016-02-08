<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Shortcode Video Playlist
 */
$query = mpp_widget_get_media_data( 'query' );
if( $query->have_media() ): 
	$ids = $query->get_ids();
	
?>

<div class="mpp-u-1-1 mpp-item-playlist  mpp-item-playlist-video mpp-item-playlist-video-widget">
	<?php	do_action( 'mpp_before_widget_playlist', $ids ) ;?>
<?php
	
	echo wp_playlist_shortcode( array( 'ids' => $ids, 'type' => 'video' ));

?>
	<?php	do_action( 'mpp_after_widget_playlist', $ids ) ;?>
</div>
<?php mpp_reset_media_data(); ?>
<?php endif; ?>
