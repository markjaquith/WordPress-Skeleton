<div id="sidebar" class="four columns">
	<?php
		if ( is_page_template('template-services-listing.php') or get_post_type() == 'service' ) {
			dynamic_sidebar('services-sidebar');
		} elseif ( is_page() ) {
			dynamic_sidebar('page-sidebar');
		} else {
			dynamic_sidebar('blog-sidebar');
		}
	?>
</div> <!-- #sidebar -->
