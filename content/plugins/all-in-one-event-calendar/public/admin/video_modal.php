<div class="ai1ec-modal ai1ec-fade timely" id="ai1ec-video-modal"
	style="width: 600px; margin-left: -300px; margin-top: -200px;">
	<div class="ai1ec-modal-dialog">
		<div class="ai1ec-modal-content">
			<div class="ai1ec-modal-header">
				<button class="ai1ec-close" data-dismiss="ai1ec-modal">×</button>
				<h1><small><?php echo esc_html( $title ); ?></small></h1>
			</div>
			<div id="ai1ec-video"></div>
			<?php if ( isset( $footer ) ): ?>
				<div class="ai1ec-modal-footer">
					<?php echo $footer; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<script type="text/javascript">
/* <![CDATA[ */
	// TODO: Localize this variable using requirejs-suitable technique rather than
	// this way.
	var ai1ecVideo = {
		youtubeId: '<?php echo $youtube_id; ?>'
	};
/* ]]> */
</script>
