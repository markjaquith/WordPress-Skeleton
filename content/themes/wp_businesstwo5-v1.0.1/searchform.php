<form action="<?php echo esc_url(home_url('/')); ?>" method="get" role="search" class="searchform">
	<div>
		<label for="s" class="screen-reader-text"><?php _e('Search for:', 'ci_theme'); ?></label>
		<input type="text" id="s" name="s" value="">
		<input type="submit" value="<?php _e('Search', 'ci_theme'); ?>" class="searchsubmit">
	</div>
</form>
