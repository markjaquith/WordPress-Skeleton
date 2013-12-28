<?php
//
// Menu Item Post Type related functions.
//
add_action('init', 'ci_create_cpt_work');
add_action('admin_init', 'ci_add_cpt_work_meta');
add_action('save_post', 'ci_update_cpt_work_meta');

if( !function_exists('ci_create_cpt_work') ):
function ci_create_cpt_work() {
	$labels = array(
		'name' => _x('Works', 'post type general name', 'ci_theme'),
		'singular_name' => _x('Work', 'post type singular name', 'ci_theme'),
		'add_new' => __('New Work', 'ci_theme'),
		'add_new_item' => __('Add New Work', 'ci_theme'),
		'edit_item' => __('Edit Work', 'ci_theme'),
		'new_item' => __('New Work', 'ci_theme'),
		'view_item' => __('View Work', 'ci_theme'),
		'search_items' => __('Search Works', 'ci_theme'),
		'not_found' =>  __('No Works found', 'ci_theme'),
		'not_found_in_trash' => __('No Works found in the trash', 'ci_theme'), 
		'parent_item_colon' => __('Parent Work:', 'ci_theme')
	);

	$args = array(
		'labels' => $labels,
		'singular_label' => __('Work', 'ci_theme'),
		'public' => true,
		'show_ui' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'has_archive' => false,
		'rewrite' => true,
		'menu_position' => 5,
		'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments')
	);

	register_post_type( 'work' , $args );
}
endif;

if( !function_exists('ci_add_cpt_work_meta') ):
function ci_add_cpt_work_meta(){
	add_meta_box("ci_cpt_work_meta", __('Work Details', 'ci_theme'), "ci_add_cpt_work_meta_box", "work", "normal", "high");
}
endif;

if( !function_exists('ci_update_cpt_work_meta') ):
function ci_update_cpt_work_meta($post_id){
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
	if (isset($_POST['post_view']) and $_POST['post_view']=='list') return;

	if (isset($_POST['post_type']) && $_POST['post_type'] == "work")
	{
		update_post_meta($post_id, "ci_cpt_on_homepage_slider", (isset($_POST["ci_cpt_on_homepage_slider"]) ? $_POST["ci_cpt_on_homepage_slider"] : '') );
		update_post_meta($post_id, "ci_cpt_work_internal_slider", (isset($_POST["ci_cpt_work_internal_slider"]) ? $_POST["ci_cpt_work_internal_slider"] : '') );
		update_post_meta($post_id, "ci_cpt_work_url", (isset($_POST["ci_cpt_work_url"]) ? $_POST["ci_cpt_work_url"] : '') );
		update_post_meta($post_id, "ci_cpt_project_info_fields", (isset($_POST["ci_cpt_project_info_fields"]) ? $_POST["ci_cpt_project_info_fields"] : '') );
	}
}
endif;

if( !function_exists('ci_add_cpt_work_meta_box') ):
function ci_add_cpt_work_meta_box(){
	global $post;
	$internal_slider = get_post_meta($post->ID, 'ci_cpt_work_internal_slider', true);
	$slider = get_post_meta($post->ID, 'ci_cpt_on_homepage_slider', true);
	$subtitle = get_post_meta($post->ID, 'ci_cpt_work_url', true);
	?>

	<p><?php _e('You should upload your work you want to promote. You need to upload at least 2 images on this work for the slider to work. You should also upload and/or select a Featured Image, so that it will be used as the cover of the work.', 'ci_theme'); ?></p>
	<input id="ci_cpt_work_upload" type="button" class="button ci-upload" value="<?php _e('Upload', 'ci_theme'); ?>" />
	<p><input type="checkbox" id="ci_cpt_on_homepage_slider" name="ci_cpt_on_homepage_slider" value="slider" <?php checked($slider, 'slider'); ?> /> <label for="ci_cpt_on_homepage_slider"><?php _e('Show this work item on the homepage slider.', 'ci_theme'); ?></label></p>

	<p><input type="checkbox" id="ci_cpt_work_internal_slider" name="ci_cpt_work_internal_slider" value="disabled" <?php checked($internal_slider, 'disabled'); ?> /> <label for="ci_cpt_work_internal_slider"><?php _e('Disable the internal work slider (displayed when this work is viewed).', 'ci_theme'); ?></label></p>

	<p><?php _e('You can provide a link to this work (for example, the live website you have designed). Do not forget to include the http://.', 'ci_theme'); ?></p>
	<input id="ci_cpt_work_url" name="ci_cpt_work_url" type="text" value="<?php echo esc_attr($subtitle); ?>" class="code widefat" />

	<p><?php _e('You can create a Project Information table bellow. For headings fill in the left input boxes, and for their descriptions fill in the right input boxes. You can drag and drop each line to rearrange them as you see fit.' , 'ci_theme'); ?></p>
	<fieldset id="project_info_fields">
		<label><?php _e('Project Information', 'ci_theme'); ?></label>
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
// Work post type custom admin list
//
add_filter("manage_edit-work_columns", "ci_cpt_work_edit_columns");  
add_action("manage_posts_custom_column",  "ci_cpt_work_custom_columns");  

if( !function_exists('ci_cpt_work_edit_columns') ):
function ci_cpt_work_edit_columns($columns){  

	$new_columns = array(  
		"cb" => $columns['cb'],  
		"title" => __('Work Name', 'ci_theme'),  
		"taxonomy-skill" => __('Skills', 'ci_theme'),  
		"slider" => __("Has Slider", 'ci_theme'),
		"home_slider" => __("Homepage Slider", 'ci_theme'),
		"date" => $columns['date']
	);  
	
	return $new_columns;
}  
endif;
  
if( !function_exists('ci_cpt_work_custom_columns') ):
function ci_cpt_work_custom_columns($column){  
	global $post, $wp_version;

	if(get_post_type()!='work') return;

	switch ($column)  
	{  
		case "slider":  
			if (get_post_meta($post->ID, 'ci_cpt_work_internal_slider', true) != 'disabled') 
				echo "&radic;"; 
		break;  
		case "home_slider":
			if (get_post_meta($post->ID, 'ci_cpt_on_homepage_slider', true) == 'slider')
				echo "&radic;"; 
		break;  
		case "taxonomy-skill": 
			if(version_compare($wp_version, '3.5', '<'))
			{
				$terms = wp_get_post_terms($post->ID, 'skill'); 
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
