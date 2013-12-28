<?php
//
// Slider Post Type related functions.
//
add_action( 'init', 'ci_create_cpt_slider' );
add_action( 'admin_init', 'ci_add_cpt_slider_meta' );
add_action( 'save_post', 'ci_update_cpt_slider_meta' );

if( !function_exists('ci_create_cpt_slider') ):
function ci_create_cpt_slider()
{
	$labels = array(
		'name'               => _x( 'Slider Items', 'post type general name', 'ci_theme' ),
		'singular_name'      => _x( 'Slider Item', 'post type singular name', 'ci_theme' ),
		'add_new'            => __( 'New Slider Item', 'ci_theme' ),
		'add_new_item'       => __( 'Add New Slider Item', 'ci_theme' ),
		'edit_item'          => __( 'Edit Slider Item', 'ci_theme' ),
		'new_item'           => __( 'New Slider Item', 'ci_theme' ),
		'view_item'          => __( 'View Slider Item', 'ci_theme' ),
		'search_items'       => __( 'Search Slider Items', 'ci_theme' ),
		'not_found'          => __( 'No Slider Items found', 'ci_theme' ),
		'not_found_in_trash' => __( 'No Slider Items found in the trash', 'ci_theme' ),
		'parent_item_colon'  => __( 'Parent Slider Item:', 'ci_theme' )
	);

	$args = array(
		'labels'          => $labels,
		'singular_label'  => __( 'Slider Item', 'ci_theme' ),
		'public'          => true,
		'show_ui'         => true,
		'capability_type' => 'post',
		'hierarchical'    => false,
		'has_archive'     => false,
		'rewrite'         => true,
		'menu_position'   => 5,
		'supports'        => array( 'title', 'editor', 'thumbnail', 'excerpt' )
		//'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats')
	);

	register_post_type( 'slider', $args );
}
endif;

if( !function_exists('ci_add_cpt_slider_meta') ):
function ci_add_cpt_slider_meta()
{
	add_meta_box( "ci_cpt_slider_meta", __( 'Slider Details', 'ci_theme' ), "ci_add_cpt_slider_meta_box", "slider", "normal", "high" );
}
endif;

if( !function_exists('ci_update_cpt_slider_meta') ):
function ci_update_cpt_slider_meta( $post_id )
{
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if (isset($_POST['post_view']) and $_POST['post_view']=='list') return;

	if ( isset( $_POST['post_type'] ) && $_POST['post_type'] == "slider" ) {
		update_post_meta( $post_id, "ci_cpt_slider_url", ( isset( $_POST["ci_cpt_slider_url"] ) ? $_POST["ci_cpt_slider_url"] : '' ) );
		
		// Set this to always be 'slider'
		// It will simplify the slider's query as the attribute is shared among other post types.
		update_post_meta( $post_id, "ci_cpt_on_homepage_slider", 'slider');
	}
}
endif;

if( !function_exists('ci_add_cpt_slider_meta_box') ):
function ci_add_cpt_slider_meta_box()
{
	global $post;
	$url = get_post_meta( $post->ID, 'ci_cpt_slider_url', true );

	wp_enqueue_script('wplink');

	?>
	<p><?php _e( 'Instructions: Assign a Featured Image for this slider item, so that it appears on the home page slider. Also, set a URL below if you need to redirect the user (i.e. to a product or a sale). Leaving the URL blank, will make the item navigate to the post.', 'ci_theme' ); ?></p>
	<label for="ci_slider_url"><?php _e( 'Slider Item URL', 'ci_theme' ); ?></label>
	<input id="ci_slider_url" name="ci_cpt_slider_url" type="text" value="<?php echo esc_url($url); ?>" class="code widefat" />
	<?php
}
endif;

?>
