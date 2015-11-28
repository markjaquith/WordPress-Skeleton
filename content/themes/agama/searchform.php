<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="search-table">
		<label class="search-field">
			<span class="screen-reader-text"><?php echo _x( 'Search for:', 'screen-reader', 'agama' ) ?></span>
			<input type="search" class="search-field" placeholder="<?php _e( 'Search', 'agama' ) ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'Title', 'agama' ) ?>" />
		</label>
		<div class="search-button">
			<input type="submit" class="search-submit" value="&raquo;" />
		</div>
	</div>
</form>