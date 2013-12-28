<?php
//
// Services Post Type related functions.
//
add_action('init', 'ci_create_cpt_service');
add_action('admin_init', 'ci_add_cpt_service_meta');
add_action('save_post', 'ci_update_cpt_service_meta');

if( !function_exists('ci_create_cpt_service') ):
function ci_create_cpt_service() {
	$labels = array(
		'name' => _x('Services', 'post type general name', 'ci_theme'),
		'singular_name' => _x('Service', 'post type singular name', 'ci_theme'),
		'add_new' => __('New Service', 'ci_theme'),
		'add_new_item' => __('Add New Service', 'ci_theme'),
		'edit_item' => __('Edit Service', 'ci_theme'),
		'new_item' => __('New Service', 'ci_theme'),
		'view_item' => __('View Service', 'ci_theme'),
		'search_items' => __('Search Services', 'ci_theme'),
		'not_found' =>  __('No Services found', 'ci_theme'),
		'not_found_in_trash' => __('No Services found in the trash', 'ci_theme'),
		'parent_item_colon' => __('Parent Service:', 'ci_theme')
	);

	$args = array(
		'labels' => $labels,
		'singular_label' => __('Service', 'ci_theme'),
		'public' => true,
		'show_ui' => true,
		'capability_type' => 'page',
		'hierarchical' => false,
		'has_archive' => false,
		'rewrite' => true,
		'menu_position' => 5,
		'supports' => array('title', 'editor', 'thumbnail', 'excerpt')
	);

	register_post_type( 'service' , $args );
}
endif;

if( !function_exists('ci_add_cpt_service_meta') ):
function ci_add_cpt_service_meta(){
	add_meta_box("ci_cpt_service_meta", __('Service Details', 'ci_theme'), "ci_add_cpt_service_meta_box", "service", "normal", "high");
}
endif;

if( !function_exists('ci_update_cpt_service_meta') ):
function ci_update_cpt_service_meta($post_id){
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
	if (isset($_POST['post_view']) and $_POST['post_view']=='list') return;

	if (isset($_POST['post_type']) && $_POST['post_type'] == "service")
	{
		update_post_meta($post_id, "ci_cpt_service_on_homepage", (isset($_POST["ci_cpt_service_on_homepage"]) ? $_POST["ci_cpt_service_on_homepage"] : '') );
		update_post_meta($post_id, "ci_cpt_secondary_featured_id", (isset($_POST["ci_cpt_secondary_featured_id"]) ? intval($_POST["ci_cpt_secondary_featured_id"]) : '') );
	}
}
endif;

if( !function_exists('ci_add_cpt_service_meta_box') ):
function ci_add_cpt_service_meta_box(){
	global $post;
	$on_homepage = get_post_meta($post->ID, 'ci_cpt_service_on_homepage', true);
	$secondary_id = get_post_meta($post->ID, 'ci_cpt_secondary_featured_id', true);
	?>
	<p><input type="checkbox" id="ci_cpt_service_on_homepage" name="ci_cpt_service_on_homepage" value="enabled" <?php checked($on_homepage, 'enabled'); ?> /> <label for="ci_cpt_service_on_homepage"><?php _e('Show this service item on the homepage.', 'ci_theme'); ?></label></p>

	<p><?php _e('Services can display an image in listing pages and the homepage, while showing their Featured Image in single pages. You need to enter the ID of an <strong>attached image</strong> file, or click on the <strong>Upload</strong> button to upload and/or select an image file from within WordPress. If you don\'t want an image displayed for the service throughout the website, you can skip this step, although you might still want to set a Featured Image.', 'ci_theme'); ?></p>
	<?php if(!empty($secondary_id)): ?>
		<?php $img = wp_get_attachment_image_src($secondary_id, 'thumbnail'); ?>
		<div class="up-preview"><?php echo (isset($img[0]) ? '<img src="'.esc_attr($img[0]).'" />' : '' );  ?></div>
	<?php endif; ?>
	<label for="ci_cpt_secondary_featured_id"><?php _e('The ID of the image file:', 'ci_theme'); ?></label>
	<input id="ci_cpt_secondary_featured_id" type="text" class="code uploaded-id" name="ci_cpt_secondary_featured_id" size="10" value="<?php echo esc_attr($secondary_id); ?>" /> 
	<input id="ci-upload-secondary-featured-button" type="button" class="button ci-upload" value="<?php echo esc_attr(__('Upload Image', 'ci_theme')); ?>" />
	<input type="hidden" class="uploaded" />
	<?php
}
endif;

//
// Service post type custom admin list
//
add_filter("manage_edit-service_columns", "ci_cpt_service_edit_columns");  
add_action("manage_posts_custom_column",  "ci_cpt_service_custom_columns");  

if( !function_exists('ci_cpt_service_edit_columns') ):
function ci_cpt_service_edit_columns($columns){  

	$new_columns = array(  
		"cb" => $columns['cb'],  
		"title" => __('Service Name', 'ci_theme'),  
		"on_home" => __("On Homepage", 'ci_theme'),
		"date" => $columns['date']
	);  
	
	return $new_columns;
}  
endif;

if( !function_exists('ci_cpt_service_custom_columns') ):
function ci_cpt_service_custom_columns($column){  
	global $post, $wp_version;

	switch ($column)  
	{  
		case "on_home":  
			if (get_post_meta($post->ID, 'ci_cpt_service_on_homepage', true) == 'enabled') 
				echo "&radic;"; 
		break;  
	}  
}
endif;

?>
