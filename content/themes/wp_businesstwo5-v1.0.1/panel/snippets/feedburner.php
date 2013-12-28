<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	
	$ci_defaults['feedburner_feed'] = '';

	// Returns the site's custom feed URL, or the default if custom doesn't exist.
	if( !function_exists('ci_rss_feed')):
	function ci_rss_feed()
	{
		if (ci_setting('feedburner_feed'))
			return ci_setting('feedburner_feed');
		else
			return get_bloginfo('rss2_url');
	}
	endif;
	
	// Register FeedBurner feed if exists, else register automatic feeds.
	if( !function_exists('ci_register_custom_feed')):
	function ci_register_custom_feed()
	{
		if (ci_setting('feedburner_feed'))
			add_action('wp_head', 'ci_feedburner_feed');
		else
			add_theme_support( 'automatic-feed-links' );
	}
	endif;
	
	if( !function_exists('ci_feedburner_feed')):
	function ci_feedburner_feed()
	{
		$s = '<link rel="alternate" type="application/rss+xml" title="'.get_bloginfo('name').' RSS Feed" href="'.ci_setting('feedburner_feed').'" />';
		echo $s;
	}
	endif;


?>
<?php else: ?>

	<fieldset class="set">
		<p class="guide"><?php _e('By adding your FeedBurner URL here, your main feed will be served by FeedBurner instead of your Wordpress site.', 'ci_theme'); ?></p>
		<?php ci_panel_textarea('feedburner_feed', __('FeedBurner Feed URL', 'ci_theme')); ?>
	</fieldset>

<?php endif; ?>
