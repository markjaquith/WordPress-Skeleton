<?php

/**
 * User meta entries management.
 *
 * Meta entries management based on {@see Ai1ec_Meta} class.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Model
 */
class Ai1ec_Meta_User extends Ai1ec_Meta {

	/**
	 * Get meta value for current user.
	 *
	 * @param string $meta_key Name of meta entry to get for current user.
	 * @param mixed  $default  Value to return if no entry found.
	 *
	 * @return mixed Current user's option or $default if none found.
	 */
	public function get_current( $meta_key, $default = null ) {
		$user_id = 0;
		if ( is_callable( 'wp_get_current_user' ) ) {
			$user    = wp_get_current_user();
			$user_id = (int)$user->ID;
			unset( $user );
		}
		if ( $user_id <= 0 ) {
			return $default;
		}
		return $this->get( $user_id, $meta_key, $default );
	}

	/**
	 * user_selected_tz method
	 *
	 * Get/set user selected (preferred) timezone.
	 * If only {@see $user_id} is provided - method acts as getter.
	 * Otherwise it acts as setter.
	 *
	 * @param int    $user_id      ID of user whose timezone is being checked/changed
	 * @param string $new_value    New timezone string value to set user preferrence
	 * @param bool   $force_update Set to true to force value update instead of add
	 *
	 * @return mixed Return value depends on activity:
	 *     - [getter] string User preferred timezone name (might be empty string)
	 *     - [setter] bool   Success of preferrence change
	 */
	public function user_selected_tz(
		$user_id,
		$new_value    = NULL,
		$force_update = false
	) {
		$meta_key  = 'ai1ec_timezone';
		$user_id   = (int)$user_id;
		$old_value = $this->get(
			$user_id,
			$meta_key,
			NULL,
			true
		);
		if ( NULL !== $new_value ) {
			if ( ! in_array( $new_value, timezone_identifiers_list() ) ) {
				return false;
			}
			$success = false;
			if ( true === $force_update || ! empty( $old_value ) ) {
				$success = update_user_meta(
					$user_id,
					$meta_key,
					$new_value,
					$old_value
				);
			} else {
				$success = add_user_meta(
					$user_id,
					$meta_key,
					$new_value,
					true
				);
				if ( false === $success ) {
					return $this->user_selected_tz(
						$user_id,
						$new_value,
						true
					);
				}
			}
			return $success;
		}
		return $old_value;
	}

}