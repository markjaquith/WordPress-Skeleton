<?php
/**
 * These functions are taken from the WORDPRESS 3.6-BETA3-24432 release and heavily modified to work for us in the frontend. 
 *
 * @package BuddyForms
 * @since 0.1 beta
 */
function buddyforms_wp_list_post_revisions( $post_id = 0, $type = 'all' ) {
	if ( ! $post = get_post( $post_id ) )
		return;

	// $args array with (parent, format, right, left, type) deprecated since 3.6
	if ( is_array( $type ) ) {
		$type = ! empty( $type['type'] ) ? $type['type']  : $type;
		_deprecated_argument( __FUNCTION__, '3.6' );
	}

	if ( ! $revisions = buddyforms_wp_get_post_revisions( $post->ID ) )
		return;

	$rows = '';
	foreach ( $revisions as $revision ) {
		if ( ! current_user_can( 'read_post', $revision->ID ) )
			continue;

		$is_autosave = wp_is_post_autosave( $revision );
		if ( ( 'revision' === $type && $is_autosave ) || ( 'autosave' === $type && ! $is_autosave ) )
			continue;

		$rows .= "\t<li>" . buddyforms_wp_post_revision_title_expanded( $revision,$post_id ) . "</li>\n";
	}
	echo '<div class="revision">';
	echo '<h3>'. __('Revision', 'buddyforms') .'</h3>';
	echo "<ul class='post-revisions'>\n";
	echo $rows;

	// if the post was previously restored from a revision
	// show the restore event details
	if ( $restored_from_meta = get_post_meta( $post->ID, '_post_restored_from', true ) ) {
		$author = get_user_by( 'id', $restored_from_meta[ 'restored_by_user' ] );
		/* translators: revision date format, see http://php.net/date */
		$datef = _x( 'j F, Y @ G:i:s', 'revision date format');
		$date = date_i18n( $datef, strtotime( $restored_from_meta[ 'restored_time' ] ) );
		$time_diff = human_time_diff( $restored_from_meta[ 'restored_time' ] ) ;
		?>
		<hr />
		<div id="revisions-meta-restored">
			<?php
			printf(
				/* translators: restored revision details: 1: gravatar image, 2: author name, 3: time ago, 4: date */
				__( 'Previously restored by %1$s %2$s, %3$s ago (%4$s)' ),
				get_avatar( $author->ID, 24 ),
				$author->display_name,
				$time_diff,
				$date
			);
			?>
		</div>
		<?php
	}
	echo "</ul>";
	echo "</div>";
	
}

function buddyforms_wp_revisions_to_keep( $post ) {
	$num = WP_POST_REVISIONS;
	
	if ( true === $num )
		$num = -1;
	else
		$num = intval( $num );

	if ( ! post_type_supports( $post->post_type, 'revisions' ) )
		$num = 0;

	return (int) apply_filters( 'wp_revisions_to_keep', $num, $post );
}

function buddyforms_wp_revisions_enabled( $post ) {
	return buddyforms_wp_revisions_to_keep( $post ) != 0;
}

function buddyforms_wp_get_post_revisions( $post_id = 0, $args = null ) {
	$post = get_post( $post_id );
	if ( ! $post || empty( $post->ID ) || ! buddyforms_wp_revisions_enabled( $post ) )
		return array();

	$defaults = array( 'order' => 'DESC', 'orderby' => 'date' );
	$args = wp_parse_args( $args, $defaults );
	$args = array_merge( $args, array( 'post_parent' => $post->ID, 'post_type' => 'revision', 'post_status' => 'inherit' ) );

	if ( ! $revisions = get_children( $args ) )
		return array();

	return $revisions;
}

function buddyforms_wp_post_revision_title_expanded( $revision,$post_id, $link = true ) {
	global $wp_query, $buddyforms, $form_slug;
	
	if ( !$revision = get_post( $revision ) )
		return $revision;

	if ( !in_array( $revision->post_type, array( 'post', 'page', 'revision' ) ) )
		return false;
	

	if(isset($wp_query->query_vars['bf_form_slug']))
		$form_slug = $wp_query->query_vars['bf_form_slug'];
	
	$form_slug = apply_filters('buddyforms_wp_post_revision_title_expanded_form_slug', $form_slug);
	
	if(isset($wp_query->query_vars['bf_post_id']))
		$post_id = $wp_query->query_vars['bf_post_id'];
	
	$permalink = '';
	
	if(isset($form_slug) && isset($buddyforms[$form_slug]['attached_page']))
		$permalink = get_permalink( $buddyforms[$form_slug]['attached_page'] );

	$author = get_the_author_meta( 'display_name', $revision->post_author );
	/* translators: revision date format, see http://php.net/date */
	$datef = _x( 'j F, Y @ G:i:s', 'revision date format');

	$gravatar = get_avatar( $revision->post_author, 24 );

	$date = date_i18n( $datef, strtotime( $revision->post_modified ) );
	if ( $link && current_user_can( 'edit_post', $revision->ID ) && isset($permalink) && $link = $permalink.'revision/'.$form_slug.'/'.$post_id.'/'.$revision->ID )
		$date = "<a href='$link'>$date</a>";

	$revision_date_author = sprintf(
		/* translators: post revision title: 1: author avatar, 2: author name, 3: time ago, 4: date */
		_x( '%1$s %2$s, %3$s ago (%4$s)', 'post revision title' ),
		$gravatar,
		$author,
		human_time_diff( strtotime( $revision->post_modified ), current_time( 'timestamp' ) ),
		$date
	);

	$autosavef = __( '%1$s [Autosave]' );
	$currentf  = __( '%1$s [Current Revision]' );

	if ( !wp_is_post_revision( $revision ) )
		$revision_date_author = sprintf( $currentf, $revision_date_author );
	elseif ( wp_is_post_autosave( $revision ) )
		$revision_date_author = sprintf( $autosavef, $revision_date_author );

	return $revision_date_author;
}