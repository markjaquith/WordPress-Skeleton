<?php

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function bf_add_custom_box() {
    global $buddyforms;

    if(!$buddyforms)
        return;

    $screens = Array();
    foreach ($buddyforms as $key => $buddyform) {
        if(isset($buddyform['post_type']))
            array_push($screens, $buddyform['post_type']);
    }

    foreach ( $screens as $screen ) {
        add_meta_box(
            'bf_sectionid',
            __( 'Attach a BuddyForm', 'buddyforms' ),
            'bf_inner_custom_box',
            $screen,
            'side',
            'high'
        );
    }
}
add_action( 'add_meta_boxes', 'bf_add_custom_box' );

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function bf_inner_custom_box( $post ) {
    global $buddyforms;

    // Add an nonce field so we can check for it later.
    wp_nonce_field( 'bf_inner_custom_box', 'bf_inner_custom_box_nonce' );

    /*
     * Use get_post_meta() to retrieve an existing value
     * from the database and use the value for the form.
     */
    $value = get_post_meta( $post->ID, '_bf_form_slug', true );

    echo '<label for="_bf_form_slug">';
    _e( "Select the form", 'buddyforms' );
    echo '</label> ';
    //echo '<input type="text" id="_bf_form_slug" name="_bf_form_slug" value="' . esc_attr( $value ) . '" size="25" />';
    echo ' <p><select name="_bf_form_slug" id="_bf_form_slug">';
    echo ' <option value="none" '.$selected.' > none </option>';

    foreach ($buddyforms as $key => $buddyform) {
        $selected = '';
        if($buddyform['slug'] == $value)
            $selected = 'selected';

        if($buddyform['post_type'] == get_post_type($post))
            echo ' <option value="' . $buddyform['slug'] . '" '.$selected.' >' . $buddyform['name'] . '</option>';
    }

    echo '</select></p>';

    do_action('buddyforms_post_edit_meta_box_select_form', $post);
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function bf_save_postdata( $post_id ) {

    /*
     * We need to verify this came from the our screen and with proper authorization,
     * because save_post can be triggered at other times.
     */

    // Check if our nonce is set.
    if ( ! isset( $_POST['bf_inner_custom_box_nonce'] ) )
        return $post_id;

    $nonce = $_POST['bf_inner_custom_box_nonce'];

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $nonce, 'bf_inner_custom_box' ) )
        return $post_id;

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    // Check the user's permissions.
    if ( 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) )
            return $post_id;

    } else {

        if ( ! current_user_can( 'edit_post', $post_id ) )
            return $post_id;
    }

    /* OK, its safe for us to save the data now. */

    // Sanitize user input.
    $mydata = sanitize_text_field( $_POST['_bf_form_slug'] );

    // Update the meta field in the database.
    update_post_meta( $post_id, '_bf_form_slug', $mydata );
}
add_action( 'save_post', 'bf_save_postdata' );