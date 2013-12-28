<?php global $ci, $ci_defaults, $load_defaults, $content_width; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	
	if( !function_exists('ci_cpt_with_featured_image') ):
	function ci_cpt_with_featured_image()
	{
		return apply_filters('ci_featured_image_post_types', array('post', 'page'));
	}
	endif;

	/*	This is how to add/remove support for custom featured image size, into custom post types. 
		This function and hook will typically go into a panel tab file,
		right before the load_panel_snippet('featured_image_single'); call, in the $load_defaults===TRUE section.
		
		add_filter('ci_featured_image_post_types', 'ci_add_featured_img_cpt');
		// Add support for the applicable custom post types
		if( !function_exists('ci_add_featured_img_cpt') ):
		function ci_add_featured_img_cpt($post_types)
		{
			$post_types[] = 'post_type';
			return $post_types;		
		}
		endif;
	*/


	if( !function_exists('ci_the_post_thumbnail')):
	function ci_the_post_thumbnail($attr = '' ) {
		$post_type = get_post_type();
		if(ci_setting('featured_single_'.$post_type.'_show')=='enabled')
		{
			if(isset($attr['class']))
				$attr['class'] = $attr['class'].' '.ci_setting('featured_single_align').' ';
			else
				$attr['class'] = ci_setting('featured_single_align');
			
			the_post_thumbnail('ci_featured_single', $attr);
		}
	}
	endif;

	//
	// Checks if the featured image of current post should be displayed.
	// Usable only within the loop.
	//
	if( !function_exists('ci_is_featured_enabled') ):
	function ci_is_featured_enabled()
	{
		$post_type = get_post_type();
		if(ci_setting('featured_single_'.$post_type.'_show')=='enabled')
			return true;

		return false;
	}
	endif;

	//
	// Checks if the current post has a featured image assigned, and if it should be displayed.
	// Usable only within the loop.
	//
	if( !function_exists('ci_has_image_to_show') ):
	function ci_has_image_to_show()
	{
		if(ci_is_featured_enabled() and has_post_thumbnail())
			return true;

		return false;
	}
	endif;
	

	$img_cpt = ci_cpt_with_featured_image();
	foreach($img_cpt as $post_type)
	{
		$ci_defaults['featured_single_'.$post_type.'_show'] = 'enabled';
	}

	$ci_defaults['featured_single_width']	= intval($content_width);
	$ci_defaults['featured_single_height']	= intval($content_width/2);
	$ci_defaults['featured_single_align']	= 'alignnone';

	add_image_size( 'ci_featured_single', intval(ci_setting('featured_single_width')), intval(ci_setting('featured_single_height')), true);

?>
<?php else: ?>

	<fieldset class="set">
		<p class="guide">
			<?php 
				echo sprintf(__('Control whether you want the featured image of each post to be displayed when viewing that post\'s page. The featured image can be shown/hidden on each individual post type, with common dimensions. You can define its width and height <em>(defaults to the content width, currently: %d pixels)</em>, and whether you want it aligned on the left, right or middle of the page.', 'ci_theme'), $content_width); 
				echo " "; _e('Note that if you change the width and/or the height of the featured images, you will need to regenerate all your thumbnails using an appropriate plugin, such as the <a href="http://wordpress.org/extend/plugins/regenerate-thumbnails/" target="_blank">Regenerate Thumbnails</a> plugin, otherwise your images may appear distorted.', 'ci_theme'); 
			?>
		</p>
		<?php
			$thumb_types = ci_cpt_with_featured_image();
			foreach($thumb_types as $post_type)
			{
				$obj = get_post_type_object($post_type);
				ci_panel_checkbox('featured_single_'.$post_type.'_show', 'enabled', sprintf(__('Show featured images on <em>%s</em>', 'ci_theme'), $obj->labels->name));
			}
		?>
		<fieldset class="mt10">
			<?php ci_panel_input('featured_single_width', __('Featured image Width', 'ci_theme')); ?>
			<?php ci_panel_input('featured_single_height', __('Featured image Height', 'ci_theme')); ?>
			<?php 
				$align_options = array(
					'alignnone' => __('None', 'ci_theme'),
					'alignleft' => __('Left', 'ci_theme'),
					'aligncenter' => __('Center', 'ci_theme'),
					'alignright' => __('Right', 'ci_theme')
				);
				ci_panel_dropdown('featured_single_align', $align_options, __('Featured image alignment', 'ci_theme')); 
			?>
		</fieldset>
	</fieldset>
			
<?php endif; ?>
