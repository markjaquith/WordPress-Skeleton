<?php if ( ! defined( 'ABSPATH' ) ) exit;

add_action('add_meta_boxes', 'ninja_forms_add_custom_box');

/* Do something with the data entered */
add_action('save_post', 'ninja_forms_save_postdata');

/* Adds a box to the main column on the Post and Page edit screens */
function ninja_forms_add_custom_box() {
	add_meta_box(
		'ninja_forms_selector',
		__( 'Append A Ninja Form', 'ninja-forms'),
		'ninja_forms_inner_custom_box',
		'post',
		'side',
		'low'
	);
	add_meta_box(
		'ninja_forms_selector',
		__( 'Append A Ninja Form', 'ninja-forms'),
		'ninja_forms_inner_custom_box',
		'page',
		'side',
		'low'
	);
}

/* Prints the box content */
function ninja_forms_inner_custom_box() {

	$post_id = ! empty( $_REQUEST['post'] ) ? absint( $_REQUEST['post'] ) : 0;

	// Use nonce for verification
	wp_nonce_field( 'ninja_forms_append_form', 'nf_append_form' );

	// The actual fields for data entry
	?>
	<select id="ninja_form_select" name="ninja_form_select">
		<option value="0">-- <?php _e('None', 'ninja-forms');?></option>
		<?php
		$all_forms = ninja_forms_get_all_forms();
		$form_id = get_post_meta( $post_id, 'ninja_forms_form', true );
		foreach( $all_forms as $form ){
			$title = $form['data']['form_title'];
			$id    = $form['id'];
			?>
			<option value="<?php echo esc_attr( $id );?>"<?php selected( $id, $form_id );?>>
			<?php echo $title;?>
			</option>
			<?php
		}
		?>
	</select>
	<?php
}

/* When the post is saved, saves our custom data */
function ninja_forms_save_postdata( $post_id ) {
	global $wpdb;
	if(isset($_POST['nf_append_form'])){
		// verify if this is an auto save routine.
		// If it is our form has not been submitted, so we dont want to do anything
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		  return $post_id;

		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times

		if ( !wp_verify_nonce( $_POST['nf_append_form'], 'ninja_forms_append_form' ) )
		  return $post_id;

		// Check permissions
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) )
				return $post_id;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		// OK, we're authenticated: we need to find and save the data
		$post_id = absint( $_POST['post_ID'] );
		$form_id = absint( $_POST['ninja_form_select'] );
		if ( empty ( $form_id ) ) {
			delete_post_meta( $post_id, 'ninja_forms_form' );
		} else {
			update_post_meta( $post_id, 'ninja_forms_form', $form_id );
		}
	}
}