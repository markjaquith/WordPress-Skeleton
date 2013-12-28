<?php
//
// Menu Item Post Type related functions.
//
add_action('init', 'ci_create_cpt_product');
add_action('admin_init', 'ci_add_cpt_product_meta');
add_action('save_post', 'ci_update_cpt_product_meta');

if( !function_exists('ci_create_cpt_product') ):
function ci_create_cpt_product() {
	$labels = array(
		'name' => _x('Products', 'post type general name', 'ci_theme'),
		'singular_name' => _x('Product', 'post type singular name', 'ci_theme'),
		'add_new' => __('New Product', 'ci_theme'),
		'add_new_item' => __('Add New Product', 'ci_theme'),
		'edit_item' => __('Edit Product', 'ci_theme'),
		'new_item' => __('New Product', 'ci_theme'),
		'view_item' => __('View Product', 'ci_theme'),
		'search_items' => __('Search Products', 'ci_theme'),
		'not_found' =>  __('No Products found', 'ci_theme'),
		'not_found_in_trash' => __('No Products found in the trash', 'ci_theme'),
		'parent_item_colon' => __('Parent Product:', 'ci_theme')
	);

	$args = array(
		'labels' => $labels,
		'singular_label' => __('Product', 'ci_theme'),
		'public' => true,
		'show_ui' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'has_archive' => false,
		'rewrite' => true,
		'menu_position' => 5,
		'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments')
	);

	register_post_type( 'product' , $args );
}
endif;

if( !function_exists('ci_add_cpt_product_meta') ):
function ci_add_cpt_product_meta(){
	add_meta_box("ci_cpt_product_meta", __('Product Details', 'ci_theme'), "ci_add_cpt_product_meta_box", "product", "normal", "high");
}
endif;

if( !function_exists('ci_update_cpt_product_meta') ):
function ci_update_cpt_product_meta($post_id){
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
	if (isset($_POST['post_view']) and $_POST['post_view']=='list') return;

	if (isset($_POST['post_type']) && $_POST['post_type'] == "product")
	{
		update_post_meta($post_id, "ci_cpt_on_homepage_slider", (isset($_POST["ci_cpt_on_homepage_slider"]) ? $_POST["ci_cpt_on_homepage_slider"] : '') );
		update_post_meta($post_id, "ci_cpt_product_internal_slider", (isset($_POST["ci_cpt_product_internal_slider"]) ? $_POST["ci_cpt_product_internal_slider"] : '') );
		update_post_meta($post_id, "ci_cpt_product_url", (isset($_POST["ci_cpt_product_url"]) ? $_POST["ci_cpt_product_url"] : '') );
		update_post_meta($post_id, "ci_cpt_project_info_fields", (isset($_POST["ci_cpt_project_info_fields"]) ? $_POST["ci_cpt_project_info_fields"] : '') );
	}
}
endif;

if( !function_exists('ci_add_cpt_product_meta_box') ):
function ci_add_cpt_product_meta_box(){
	global $post;
	$internal_slider = get_post_meta($post->ID, 'ci_cpt_product_internal_slider', true);
	$slider = get_post_meta($post->ID, 'ci_cpt_on_homepage_slider', true);
	$subtitle = get_post_meta($post->ID, 'ci_cpt_product_url', true);
	?>

	<p><?php _e('You should upload images of your product to create a gallery or a slideshow. You should also upload and/or select a Featured Image, so that it will be used as the cover of the product.', 'ci_theme'); ?></p>
	<input id="ci_cpt_product_upload" type="button" class="button ci-upload" value="<?php _e('Upload', 'ci_theme'); ?>" />
	<p><input type="checkbox" id="ci_cpt_on_homepage_slider" name="ci_cpt_on_homepage_slider" value="slider" <?php checked($slider, 'slider'); ?> /> <label for="ci_cpt_on_homepage_slider"><?php _e('Show this product item on the homepage slider.', 'ci_theme'); ?></label></p>
	
	<p><input type="checkbox" id="ci_cpt_product_internal_slider" name="ci_cpt_product_internal_slider" value="disabled" <?php checked($internal_slider, 'disabled'); ?> /> <label for="ci_cpt_product_internal_slider"><?php _e('Disable the internal product slider (displayed when this product is viewed).', 'ci_theme'); ?></label></p>
	
	<p><?php _e('You can provide a link for this product (for example, a PDF you have uploaded for it or a website). Do not forget to include the http://.', 'ci_theme'); ?></p>
	<input id="ci_cpt_product_url" name="ci_cpt_product_url" type="text" value="<?php echo esc_attr($subtitle); ?>" class="code widefat" />
	
	<p><?php _e('You can create a Product Information table bellow. For headings fill in the left input boxes, and for their descriptions fill in the right input boxes. You can drag and drop each line to rearrange them as you see fit.' , 'ci_theme'); ?></p>
	<fieldset id="project_info_fields">
		<label><?php _e('Product Information', 'ci_theme'); ?></label>
		<a href="#" id="pi-add-field"><?php _e('Add Field', 'ci_theme'); ?></a>
		<div class="inside">
			<?php
			$fields = get_post_meta($post->ID, 'ci_cpt_project_info_fields', true);
			if (!empty($fields))
			{
				for( $i = 0; $i < count($fields); $i+=2 )
				{
					echo '<p class="pi-field"><input type="text" name="ci_cpt_project_info_fields[]" value="'. $fields[$i] .'" /><input type="text" name="ci_cpt_project_info_fields[]" value="'. $fields[$i+1] .'" /> <a href="#" class="pi-remove">' . __('Remove me', 'ci_theme') . '</a></p>';
				}
			}
			?>
		</div>
	</fieldset>
	<?php

}
endif;

//
// Product post type custom admin list
//
add_filter("manage_edit-product_columns", "ci_cpt_product_edit_columns");
add_action("manage_posts_custom_column",  "ci_cpt_product_custom_columns");

if( !function_exists('ci_cpt_product_edit_columns') ):
function ci_cpt_product_edit_columns($columns){

	$new_columns = array(
		"cb" => $columns['cb'],
		"title" => __('Product Name', 'ci_theme'),
		"taxonomy-product-category" => __('Product Categories', 'ci_theme'),
		"slider" => __("Has Slider", 'ci_theme'),
		"home_slider" => __("Homepage Slider", 'ci_theme'),
		"date" => $columns['date']
	);

	return $new_columns;
}
endif;

if( !function_exists('ci_cpt_product_custom_columns') ):
function ci_cpt_product_custom_columns($column){
	global $post, $wp_version;

	if(get_post_type()!='product') return;

	switch ($column)
	{
		case "slider":
			if (get_post_meta($post->ID, 'ci_cpt_product_internal_slider', true) != 'disabled')
				echo "&radic;";
			break;
		case "home_slider":
			if (get_post_meta($post->ID, 'ci_cpt_on_homepage_slider', true) == 'slider')
				echo "&radic;";
			break;
		case "taxonomy-product-category":
			if(version_compare($wp_version, '3.5', '<'))
			{
				$terms = wp_get_post_terms($post->ID, 'product-category');
				$list='';
				foreach($terms as $term)
				{
					$list .= $term->name.'<br />';
				}
				$list = substr($list, 0, -6);
				echo $list;
			}
			break;
	}
}
endif;

?>
