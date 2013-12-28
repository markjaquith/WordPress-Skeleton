<?php
//
// Clients Post Type related functions.
//
add_action('init', 'ci_create_cpt_client');
add_action('admin_init', 'ci_add_cpt_client_meta');
add_action('save_post', 'ci_update_cpt_client_meta');

if( !function_exists('ci_create_cpt_client') ):
function ci_create_cpt_client() {
	$labels = array(
		'name' => _x('Clients', 'post type general name', 'ci_theme'),
		'singular_name' => _x('Client', 'post type singular name', 'ci_theme'),
		'add_new' => __('New Client', 'ci_theme'),
		'add_new_item' => __('Add New Client', 'ci_theme'),
		'edit_item' => __('Edit Client', 'ci_theme'),
		'new_item' => __('New Client', 'ci_theme'),
		'view_item' => __('View Client', 'ci_theme'),
		'search_items' => __('Search Clients', 'ci_theme'),
		'not_found' =>  __('No Clients found', 'ci_theme'),
		'not_found_in_trash' => __('No Clients found in the trash', 'ci_theme'),
		'parent_item_colon' => __('Parent Client:', 'ci_theme')
	);

	$args = array(
		'labels' => $labels,
		'singular_label' => __('Client', 'ci_theme'),
		'public' => true,
		'show_ui' => true,
		'capability_type' => 'page',
		'hierarchical' => false,
		'has_archive' => false,
		'rewrite' => true,
		'menu_position' => 5,
		'supports' => array('title', 'editor', 'thumbnail', 'excerpt')
	);

	register_post_type( 'client' , $args );
}
endif;

if( !function_exists('ci_add_cpt_client_meta') ):
function ci_add_cpt_client_meta()
{
	add_meta_box( "ci_cpt_client_meta", __( 'Client Details', 'ci_theme' ), "ci_add_cpt_client_meta_box", "client", "normal", "high" );
}
endif;

if( !function_exists('ci_update_cpt_client_meta') ):
function ci_update_cpt_client_meta( $post_id )
{
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if (isset($_POST['post_view']) and $_POST['post_view']=='list') return;

	if ( isset( $_POST['post_type'] ) && $_POST['post_type'] == "client" ) {
		update_post_meta( $post_id, "ci_cpt_client_on_homepage", ( isset( $_POST["ci_cpt_client_on_homepage"] ) ? $_POST["ci_cpt_client_on_homepage"] : '' ) );
		update_post_meta( $post_id, "ci_cpt_link_url", ( isset( $_POST["ci_cpt_link_url"] ) ? $_POST["ci_cpt_link_url"] : '' ) );
	}
}
endif;

if( !function_exists('ci_add_cpt_client_meta_box') ):
function ci_add_cpt_client_meta_box()
{
	global $post;
	$homepage = get_post_meta( $post->ID, 'ci_cpt_client_on_homepage', true );
	$url = get_post_meta( $post->ID, 'ci_cpt_link_url', true );
	?>
	<p><?php _e( 'Add a URL if you want the client\'s icon to be a link. For example, you might want to link specific clients to specific Works.', 'ci_theme' ); ?></p>
	<label for="ci_slider_url"><?php _e( 'Link URL', 'ci_theme' ); ?></label>
	<input id="ci_slider_url" name="ci_cpt_link_url" type="text" value="<?php echo esc_attr($url); ?>" class="code widefat" />

	<p><input type="checkbox" id="ci_cpt_client_on_homepage" name="ci_cpt_client_on_homepage" value="enabled" <?php checked($homepage, 'enabled'); ?> /> <label for="ci_cpt_client_on_homepage"><?php _e('Show this client on the homepage.', 'ci_theme'); ?></label></p>
	<?php
}
endif;
?>
