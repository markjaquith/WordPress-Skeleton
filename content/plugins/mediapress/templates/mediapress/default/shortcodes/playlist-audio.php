<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * mediapress/shortcodes/media-loop-audio-playlist.php
 * Shortcode Audio Playlist
 */
$query = mpp_shortcode_get_media_data('query' );

if( $query->have_media() ):
$ids = $query->get_ids();	
	?>
	<div class="mpp-item-playlist mpp-u-1-1 mpp-item-playlist-audio mpp-item-playlist-audio-shortcode">
		<?php	do_action( 'mpp_before_widget_playlist', $ids ) ;?>
		
		<?php

			echo wp_playlist_shortcode( array( 'ids' => $ids));

		?>
		<?php	do_action( 'mpp_after_widget_playlist', $ids ) ;?>
		
	</div>

<?php endif;?> 
<?php mpp_reset_media_data(); ?>