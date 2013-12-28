<?php 
	$title = '';
	$excerpt = '';
	if ( is_singular() ) :
		$title = get_the_title();
		$excerpt = has_excerpt() ? get_the_excerpt() : '';
	elseif ( is_category() or is_tag() or is_tax() ):
		$title = single_term_title('', false);
		$excerpt = term_description(false, get_post_type());
	elseif ( is_home() or is_front_page() ):
		$title = __('Blog Listing', 'ci_theme');
	elseif ( is_date() ):
		$title = single_month_title('', false);
	elseif ( is_search() ):
		$title = __('Search Results', 'ci_theme');
	elseif ( is_404() ):
		$title = __('Oops! 404', 'ci_theme');
	endif;
?>

<h1><?php echo $title; ?></h1>

<?php if(!empty($excerpt)): ?>
	<p><?php echo $excerpt; ?></p>
<?php endif; ?>
