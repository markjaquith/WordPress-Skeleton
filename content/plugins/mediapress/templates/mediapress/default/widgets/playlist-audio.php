<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Shortcode Audio Playlist
 */
//get the Query Object we had saved earlier
$query = mpp_widget_get_media_data( 'query' );

$ids = $query->get_ids();

if( $query->have_media() ): ?>
	<div class="mpp-u-1-1 mpp-item-playlist  mpp-item-playlist-audio mpp-item-playlist-audio-widget">
		<?php	do_action( 'mpp_before_widget_playlist', $ids ) ;?>
		<?php

			echo wp_playlist_shortcode( array( 'ids' => $ids ) );

		?>
		<?php	do_action( 'mpp_after_widget_playlist', $ids ) ;?>
	</div>
<?php endif;?> 
<?php mpp_reset_media_data(); ?>