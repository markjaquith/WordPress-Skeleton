<?php

/**
 * Add a layer to avoid dependency on BuddyPress
 */

/**
 * Get the URl to user profile/posts
 * @param type $user_id
 * @return type
 */
function mpp_get_user_url( $user_id ) {

	if ( function_exists( 'bp_core_get_user_domain' ) ) {
		return bp_core_get_user_domain( $user_id );
	}

	return get_author_posts_url( $user_id );
}

/**
 * Get user display name
 * 
 * @param int $user_id
 * @return string user display name
 */
function mpp_get_user_display_name( $user_id ) {

	if ( function_exists( 'bp_core_get_user_displayname' ) ) {
		return bp_core_get_user_displayname( $user_id );
	}

	$user = get_user_by( 'id', $user_id );

	if ( ! $user ) {
		return '';
	}

	$display_name = $user->display_name;
	if ( ! $display_name && ( $user->first_name || $user->last_name ) ) {
		$display_name = trim( $user->first_name . ' ' . $user->last_name );
	}
	//if it is still not set, set it to user_login
	if ( ! $display_name ) {
		$display_name = $user->user_login;
	}

	return $display_name;
}

function mpp_get_user_email( $user_id ) {

	$user = get_user_by( 'id', $user_id );

	if ( ! $user ) {
		return '';
	}

	return $user->user_email;
}

function mpp_get_user_link( $user_id, $no_anchor = false, $just_link = false ) {

	if ( function_exists( 'bp_core_get_userlink' ) ) {
		return bp_core_get_userlink( $user_id, $no_anchor, $just_link );
	}

	$display_name = mpp_get_user_display_name( $user_id );

	if ( empty( $display_name ) ) {
		return false;
	}

	if ( ! empty( $no_anchor ) ) {
		return $display_name;
	}

	if ( ! $url = mpp_get_user_url( $user_id ) ) {
		return false;
	}

	if ( ! empty( $just_link ) ) {
		return $url;
	}


	return apply_filters( 'mpp_get_user_link', '<a href="' . $url . '" title="' . $display_name . '">' . $display_name . '</a>', $user_id );
}
