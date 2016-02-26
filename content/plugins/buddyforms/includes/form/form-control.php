<?php

/**
 * Process the post and Validate all. Saves or update the post and post meta.
 *
 * @package BuddyForms
 * @since 0.3 beta
 */

function buddyforms_process_post($args = Array()) {
    global $current_user, $buddyforms;

    do_action( 'buddyforms_process_post_start', $args );

    $hasError = false;
    $info_message = '';

    get_currentuserinfo();

    extract(shortcode_atts(array(
        'post_type' 	=> '',
        'the_post'		=> 0,
        'post_id'		=> 0,
        'post_parent'   => 0,
        'revision_id' 	=> false,
        'form_slug' 	=> 0,
        'redirect_to'   => $_SERVER['REQUEST_URI'],
    ), $args));

    if(isset($_POST['bf_post_type']))
        $post_type = $_POST['bf_post_type'];

    if($post_id != 0) {

        if(!empty($revision_id)) {
            $the_post	= get_post( $revision_id );
        } else {
            $post_id = apply_filters('bf_create_edit_form_post_id', $post_id);
            $the_post	= get_post( $post_id );
        }

        // Check if the user is author of the post
        $user_can_edit = false;
        if ($the_post->post_author == $current_user->ID){
            $user_can_edit = true;
        }
        $user_can_edit = apply_filters( 'buddyforms_user_can_edit', $user_can_edit );
        if ( $user_can_edit == false ){
            $args = array(
                'hasError' 	    => true,
                'error_message'	=> __('You are not allowed to edit this post. What are you doing here?', 'buddyforms'),
            );
            return $args;
        }

    }

    // check if the user has the roles and capabilities
    $user_can_edit = false;
    if( $post_id == 0 && current_user_can('buddyforms_' . $form_slug . '_create')) {
        $user_can_edit = true;
    } elseif( $post_id != 0 && current_user_can('buddyforms_' . $form_slug . '_edit')){
        $user_can_edit = true;
    }
    $user_can_edit = apply_filters( 'buddyforms_user_can_edit', $user_can_edit );
    if ( $user_can_edit == false ){
        $args = array(
            'hasError' 	    => true,
            'error_message'	=> __('You do not have the required user role to use this form', 'buddyforms'),
        );
        return $args;
    }

    // If post_id == 0 a new post is created
    if($post_id == 0){
        require_once(ABSPATH . 'wp-admin/includes/admin.php');
        $the_post = get_default_post_to_edit($post_type);
    }

    if(isset($buddyforms[$form_slug]['form_fields']))
        $customfields = $buddyforms[$form_slug]['form_fields'];

    $comment_status = $buddyforms[$form_slug]['comment_status'];
    if(isset($_POST['comment_status']))
        $comment_status = $_POST['comment_status'];

    $post_excerpt = '';
    if(isset($_POST['post_excerpt']))
        $post_excerpt = $_POST['post_excerpt'];

    $action			= 'save';
    $post_status	= $buddyforms[$form_slug]['status'];
    if($post_id != 0){
        $action = 'update';
        $post_status = get_post_status( $post_id );
    }
    if(isset($_POST['status']))
        $post_status = $_POST['status'];

    $args = Array(
        'post_id'		    => $post_id,
        'action'			=> $action,
        'form_slug'			=> $form_slug,
        'post_type' 		=> $post_type,
        'post_excerpt'		=> $post_excerpt,
        'post_author' 		=> $current_user->ID,
        'post_status' 		=> $post_status,
        'post_parent'       => $post_parent,
        'comment_status'	=> $comment_status,
    );

    extract($args = buddyforms_update_post($args));

    /*
     * Check if the update or insert was successful
     */
    if(!is_wp_error($post_id)){

        // Check if the post has post meta / custom fields
        if(isset($customfields))
            $customfields = bf_update_post_meta($post_id, $customfields);

        if(isset($_POST['featured_image'])){
            set_post_thumbnail($post_id, $_POST['featured_image']);
        } else {
            delete_post_thumbnail($post_id);
        }

        // Save the Form slug as post meta
        update_post_meta($post_id, "_bf_form_slug", $form_slug);

    } else {
        $hasError = true;
        $error_message = $post_id->get_error_message();
    }

    // Display the message
    if( !$hasError ) :
        if(isset( $_POST['post_id'] ) && ! empty( $_POST['post_id'] )){
            $info_message .= __('The ', 'buddyforms') . $buddyforms[$form_slug]['singular_name']. __(' 1has been successfully updated ', 'buddyforms');
            $form_notice = '<div class="info alert">'.$info_message.'</div>';
        } else {
            $info_message .= __('The ', 'buddyforms') . $buddyforms[$form_slug]['singular_name']. __(' has been successfully created ', 'buddyforms');
            $form_notice = '<div class="info alert">'.$info_message.'</div>';
        }

    else:
        if(empty($error_message))
            $error_message = __('Error! There was a problem submitting the post ;-(', 'buddyforms');
        $form_notice = '<div class="error alert">'.$error_message.'</div>';

        if(!empty($fileError))
            $form_notice = '<div class="error alert">'.$fileError.'</div>';

    endif;

    do_action('buddyforms_after_save_post', $post_id);

    $args2 = array(
        'hasError' 	    => $hasError,
        'form_notice'	=> $form_notice,
        'customfields'  => $customfields,
        //'post_id'		=> $post_id,
        //'revision_id' 	=> $revision_id,
        //'post_parent'   => $post_parent,
        'redirect_to'   => $redirect_to,
        'form_slug' 	=> $form_slug,
    );

    $args =  array_merge($args, $args2);

    do_action( 'buddyforms_process_post_end', $args );

    return $args;

}

function buddyforms_update_post($args){

    extract( $args = apply_filters( 'buddyforms_update_post_args', $args ) );

    $buddyforms_form_nonce_value = $_POST['_wpnonce'];

    if ( !wp_verify_nonce( $buddyforms_form_nonce_value, 'buddyforms_form_nonce' ) ) {
        return false;
    }

    // Check if post is new or edit
    if( $action == 'update' ) {

        $bf_post = array(
            'ID'        		=> $_POST['post_id'],
            'post_title' 		=> apply_filters('bf_update_editpost_title',isset($_POST['editpost_title']) && !empty($_POST['editpost_title']) ? $_POST['editpost_title'] : 'none'),
            'post_content' 		=> apply_filters('bf_update_editpost_content', isset($_POST['editpost_content']) && !empty($_POST['editpost_content']) ? $_POST['editpost_content'] : ''),
            'post_type' 		=> $post_type,
            'post_status' 		=> $post_status,
            'comment_status'	=> $comment_status,
            'post_excerpt'		=> $post_excerpt,
            'post_parent'       => $post_parent,
        );

        // Update the new post
        $post_id = wp_update_post( $bf_post, true );

    } else {

        if(isset($_POST['status']) && $_POST['status'] == 'future' && $_POST['schedule'])
            $post_date = date('Y-m-d H:i:s',strtotime($_POST['schedule']));

        $bf_post = array(
            'post_parent'       => $post_parent,
            'post_author' 		=> $post_author,
            'post_title' 		=> apply_filters('bf_update_editpost_title',isset($_POST['editpost_title']) && !empty($_POST['editpost_title']) ? $_POST['editpost_title'] : 'none'),
            'post_content' 		=> apply_filters('bf_update_editpost_content', isset($_POST['editpost_content']) && !empty($_POST['editpost_content']) ? $_POST['editpost_content'] : ''),
            'post_type' 		=> $post_type,
            'post_status' 		=> $post_status,
            'comment_status'	=> $comment_status,
            'post_excerpt'		=> $post_excerpt,
            'post_date'         => isset($_POST['post_date'])? $_POST['post_date'] : '',
            'post_date_gmt'     => isset($_POST['post_date'])? $_POST['post_date'] : '',
        );

        // Insert the new form
        $post_id = wp_insert_post( $bf_post, true );

    }
    $bf_post['post_id'] = $post_id;

    return $bf_post;
}

function bf_update_post_meta($post_id, $customfields){

    if(!isset($customfields))
		return;


	foreach( $customfields as $key => $customfield ) : 
	   
		if( $customfield['type'] == 'taxonomy' ){
				
			$taxonomy = get_taxonomy($customfield['taxonomy']);

            if(isset($customfield['multiple'])) {

                if (isset($taxonomy->hierarchical) && $taxonomy->hierarchical == true) {

                    if (isset($_POST[$customfield['slug']]))
                        $tax_item = $_POST[$customfield['slug']];

                    if ($tax_item[0] == -1 && !empty($customfield['taxonomy_default'])) {
                        //$taxonomy_default = explode(',', $customfield['taxonomy_default'][0]);
                        foreach ($customfield['taxonomy_default'] as $key => $tax) {
                            $tax_item[$key] = $tax;
                        }
                    }


                    wp_set_post_terms($post_id, $tax_item, $customfield['taxonomy'], false);
                } else {

                    $slug = Array();

                    if (isset($_POST[$customfield['slug']])) {
                        $postCategories = $_POST[$customfield['slug']];

                        foreach ($postCategories as $postCategory) {
                            $term = get_term_by('id', $postCategory, $customfield['taxonomy']);
                            $slug[] = $term->slug;
                        }
                    }

                    wp_set_post_terms($post_id, $slug, $customfield['taxonomy'], false);

                }

                if (isset($_POST[$customfield['slug'] . '_creat_new_tax']) && !empty($_POST[$customfield['slug'] . '_creat_new_tax'])) {
                    $creat_new_tax = explode(',', $_POST[$customfield['slug'] . '_creat_new_tax']);
                    if (is_array($creat_new_tax)) {
                        foreach ($creat_new_tax as $key => $new_tax) {
                            $wp_insert_term = wp_insert_term($new_tax, $customfield['taxonomy']);
                            wp_set_post_terms($post_id, $wp_insert_term, $customfield['taxonomy'], true);
                        }
                    }

                }
            } else {
                wp_delete_object_term_relationships( $post_id, $customfield['taxonomy'] );
                if (isset($_POST[$customfield['slug'] . '_creat_new_tax']) && !empty($_POST[$customfield['slug'] . '_creat_new_tax'])) {
                    $creat_new_tax = explode(',', $_POST[$customfield['slug'] . '_creat_new_tax']);
                    if (is_array($creat_new_tax)) {
                        foreach ($creat_new_tax as $key => $new_tax) {
                            $wp_insert_term = wp_insert_term($new_tax, $customfield['taxonomy']);
                            wp_set_post_terms($post_id, $wp_insert_term, $customfield['taxonomy'], true);
                        }
                    }

                } else {

                    if (isset($taxonomy->hierarchical) && $taxonomy->hierarchical == true) {

                        if (isset($_POST[$customfield['slug']]))
                            $tax_item = $_POST[$customfield['slug']];

                        if ($tax_item[0] == -1 && !empty($customfield['taxonomy_default'])) {
                            //$taxonomy_default = explode(',', $customfield['taxonomy_default'][0]);
                            foreach ($customfield['taxonomy_default'] as $key => $tax) {
                                $tax_item[$key] = $tax;
                            }
                        }

                        wp_set_post_terms($post_id, $tax_item, $customfield['taxonomy'], false);
                    } else {

                        $slug = Array();

                        if (isset($_POST[$customfield['slug']])) {
                            $postCategories = $_POST[$customfield['slug']];

                            foreach ($postCategories as $postCategory) {
                                $term = get_term_by('id', $postCategory, $customfield['taxonomy']);
                                $slug[] = $term->slug;
                            }
                        }

                        wp_set_post_terms($post_id, $slug, $customfield['taxonomy'], false);

                    }
                }

            }
		}
		
		// Update meta do_action to hook into. This can be interesting if you added new form elements and want to manipulate how they get saved.
		do_action('buddyforms_update_post_meta',$customfield, $post_id);
       
	   	if(isset($customfield['slug']))
	   		$slug = $customfield['slug'];	
		
		if(empty($slug))
			$slug = sanitize_title($customfield['name']);


		// Update the post
		if(isset($_POST[$slug] )){
			update_post_meta($post_id, $slug, $_POST[$slug] );
      //      $customfields[$key]['value'] = $_POST[$slug];
		} else {
			update_post_meta($post_id, $slug, '' );
        //    $customfields[$key]['value'] = '';
		}
			 		                   
    endforeach;

    return $customfields;
}

add_filter('wp_handle_upload_prefilter', 'buddyforms_wp_handle_upload_prefilter');
function buddyforms_wp_handle_upload_prefilter($file) {
    if (isset($_POST['allowed_type']) && !empty($_POST['allowed_type'])){
        //this allows you to set multiple types seperated by a pipe "|"
        $allowed = explode(",", $_POST['allowed_type']);
        $ext     =  $file['type'];

        //first check if the user uploaded the right type
        if (!in_array($ext, (array)$allowed)){
            $file['error'] = $file['type'].__("Sorry, you cannot upload this file type for this field.");
            return $file;
        }

        //check if the type is allowed at all by WordPress
        foreach (get_allowed_mime_types() as $key => $value) {
            if ( $value == $ext)
                return $file;
        }
        $file['error'] = __("Sorry, you cannot upload this file type for this field.");
    }
    return $file;
}