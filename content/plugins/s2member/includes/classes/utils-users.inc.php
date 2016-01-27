<?php
/**
 * User utilities.
 *
 * Copyright: © 2009-2011
 * {@link http://websharks-inc.com/ WebSharks, Inc.}
 * (coded in the USA)
 *
 * Released under the terms of the GNU General Public License.
 * You should have received a copy of the GNU General Public License,
 * along with this software. In the main directory, see: /licensing/
 * If not, see: {@link http://www.gnu.org/licenses/}.
 *
 * @package s2Member\Utilities
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_utils_users'))
{
	/**
	 * User utilities.
	 *
	 * @package s2Member\Utilities
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_utils_users
	{
		/**
		 * Determines the total Users/Members in the database.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @return int Number of Users in the database, total.
		 */
		public static function users_in_database()
		{
			global $wpdb;
			/** @var wpdb $wpdb */

			$wpdb->query("SELECT SQL_CALC_FOUND_ROWS `".$wpdb->users."`.`ID` FROM `".$wpdb->users."`, `".$wpdb->usermeta."` WHERE `".$wpdb->users."`.`ID` = `".$wpdb->usermeta."`.`user_id` AND `".$wpdb->usermeta."`.`meta_key` = '".esc_sql($wpdb->prefix."capabilities")."' LIMIT 1");
			$users = (int)$wpdb->get_var("SELECT FOUND_ROWS()");

			return $users;
		}

		/**
		 * Obtains Custom String for an existing Member, referenced by a Subscr. or Transaction ID.
		 *
		 * A second lookup parameter can be provided as well *(optional)*.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string $subscr_txn_baid_cid_id Either a Paid Subscr. ID, or a Paid Transaction ID.
		 * @param string $os0 Optional. A second lookup parameter, usually the `os0` value for PayPal integrations.
		 *
		 * @return string|bool The Custom String value on success, else false on failure.
		 */
		public static function get_user_custom_with($subscr_txn_baid_cid_id = '', $os0 = '')
		{
			global $wpdb;
			/** @var wpdb $wpdb */

			if($subscr_txn_baid_cid_id && $os0) // This case includes some additional routines that can use the ``$os0`` value.
			{
				if(($q = $wpdb->get_row("SELECT `user_id` FROM `".$wpdb->usermeta."` WHERE (`meta_key` = '".$wpdb->prefix."s2member_subscr_id' OR `meta_key` = '".$wpdb->prefix."s2member_subscr_baid' OR `meta_key` = '".$wpdb->prefix."s2member_subscr_cid' OR `meta_key` = '".$wpdb->prefix."s2member_first_payment_txn_id') AND (`meta_value` = '".esc_sql($subscr_txn_baid_cid_id)."' OR `meta_value` = '".esc_sql($os0)."') LIMIT 1"))
				   || ($q = $wpdb->get_row("SELECT `ID` AS `user_id` FROM `".$wpdb->users."` WHERE `ID` = '".esc_sql($os0)."' LIMIT 1"))
				) if(($custom = get_user_option('s2member_custom', $q->user_id)))
					return $custom;
			}
			else if($subscr_txn_baid_cid_id) // Otherwise, if all we have is a Subscr./Txn. ID value.
			{
				if(($q = $wpdb->get_row("SELECT `user_id` FROM `".$wpdb->usermeta."` WHERE (`meta_key` = '".$wpdb->prefix."s2member_subscr_id' OR `meta_key` = '".$wpdb->prefix."s2member_subscr_baid' OR `meta_key` = '".$wpdb->prefix."s2member_subscr_cid' OR `meta_key` = '".$wpdb->prefix."s2member_first_payment_txn_id') AND `meta_value` = '".esc_sql($subscr_txn_baid_cid_id)."' LIMIT 1")))
					if(($custom = get_user_option('s2member_custom', $q->user_id)))
						return $custom;
			}
			return FALSE; // Otherwise, return false.
		}

		/**
		 * Obtains the User ID for an existing Member, referenced by a Subscr. or Transaction ID.
		 *
		 * A second lookup parameter can be provided as well *(optional)*.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string $subscr_txn_baid_cid_id Either a Paid Subscr. ID, or a Paid Transaction ID.
		 * @param string $os0 Optional. A second lookup parameter, usually the `os0` value for PayPal integrations.
		 *
		 * @return integer|bool A WordPress User ID on success, else false on failure.
		 */
		public static function get_user_id_with($subscr_txn_baid_cid_id = '', $os0 = '')
		{
			global $wpdb;
			/** @var wpdb $wpdb */

			if($subscr_txn_baid_cid_id && $os0) // This case includes some additional routines that can use the ``$os0`` value.
			{
				if(($q = $wpdb->get_row("SELECT `user_id` FROM `".$wpdb->usermeta."` WHERE (`meta_key` = '".$wpdb->prefix."s2member_subscr_id' OR `meta_key` = '".$wpdb->prefix."s2member_subscr_baid' OR `meta_key` = '".$wpdb->prefix."s2member_subscr_cid' OR `meta_key` = '".$wpdb->prefix."s2member_first_payment_txn_id') AND (`meta_value` = '".esc_sql($subscr_txn_baid_cid_id)."' OR `meta_value` = '".esc_sql($os0)."') LIMIT 1"))
				   || ($q = $wpdb->get_row("SELECT `ID` AS `user_id` FROM `".$wpdb->users."` WHERE `ID` = '".esc_sql($os0)."' LIMIT 1"))
				) return $q->user_id;
			}
			else if($subscr_txn_baid_cid_id) // Otherwise, if all we have is a Subscr./Txn. ID value.
			{
				if(($q = $wpdb->get_row("SELECT `user_id` FROM `".$wpdb->usermeta."` WHERE (`meta_key` = '".$wpdb->prefix."s2member_subscr_id' OR `meta_key` = '".$wpdb->prefix."s2member_subscr_baid' OR `meta_key` = '".$wpdb->prefix."s2member_subscr_cid' OR `meta_key` = '".$wpdb->prefix."s2member_first_payment_txn_id') AND `meta_value` = '".esc_sql($subscr_txn_baid_cid_id)."' LIMIT 1")))
					return $q->user_id;
			}
			return FALSE; // Otherwise, return false.
		}

		/**
		 * Obtains the Email Address for an existing Member, referenced by a Subscr. or Transaction ID.
		 *
		 * A second lookup parameter can be provided as well *(optional)*.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string $subscr_txn_baid_cid_id Either a Paid Subscr. ID, or a Paid Transaction ID.
		 * @param string $os0 Optional. A second lookup parameter, usually the `os0` value for PayPal integrations.
		 *
		 * @return int|bool A User's Email Address on success, else false on failure.
		 */
		public static function get_user_email_with($subscr_txn_baid_cid_id = '', $os0 = '')
		{
			global $wpdb;
			/** @var wpdb $wpdb */

			if($subscr_txn_baid_cid_id && $os0) // This case includes some additional routines that can use the ``$os0`` value.
			{
				if(($q = $wpdb->get_row("SELECT `user_id` FROM `".$wpdb->usermeta."` WHERE (`meta_key` = '".$wpdb->prefix."s2member_subscr_id' OR `meta_key` = '".$wpdb->prefix."s2member_subscr_baid' OR `meta_key` = '".$wpdb->prefix."s2member_subscr_cid' OR `meta_key` = '".$wpdb->prefix."s2member_first_payment_txn_id') AND (`meta_value` = '".esc_sql($subscr_txn_baid_cid_id)."' OR `meta_value` = '".esc_sql($os0)."') LIMIT 1"))
				   || ($q = $wpdb->get_row("SELECT `ID` AS `user_id` FROM `".$wpdb->users."` WHERE `ID` = '".esc_sql($os0)."' LIMIT 1"))
				) if(is_object($user = new WP_User ($q->user_id)) && !empty($user->ID) && ($email = $user->user_email))
					return $email;
			}
			else if($subscr_txn_baid_cid_id) // Otherwise, if all we have is a Subscr./Txn. ID value.
			{
				if(($q = $wpdb->get_row("SELECT `user_id` FROM `".$wpdb->usermeta."` WHERE (`meta_key` = '".$wpdb->prefix."s2member_subscr_id' OR `meta_key` = '".$wpdb->prefix."s2member_subscr_baid' OR `meta_key` = '".$wpdb->prefix."s2member_subscr_cid' OR `meta_key` = '".$wpdb->prefix."s2member_first_payment_txn_id') AND `meta_value` = '".esc_sql($subscr_txn_baid_cid_id)."' LIMIT 1")))
					if(is_object($user = new WP_User ($q->user_id)) && !empty($user->ID) && ($email = $user->user_email))
						return $email;
			}
			return FALSE; // Otherwise, return false.
		}

		/**
		 * Retrieves IPN Signup Vars & validates their Subscription ID.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param integer|string $user_id Optional. A numeric WordPress User ID.
		 *
		 * @param string         $subscr_txn_baid_cid_id Optional. Can be used instead of passing in a ``$user_id``.
		 *   If ``$subscr_baid_cid_id`` is passed in, it has to match the one found inside the resulting IPN Signup Vars collected by this routine.
		 *   If neither of these parameters are passed in, the current User is assumed instead, obtained through ``wp_get_current_user()``.
		 *
		 * @return array|bool A User's IPN Signup Vars on success, else false on failure.
		 */
		public static function get_user_ipn_signup_vars($user_id = 0, $subscr_txn_baid_cid_id = '')
		{
			if($user_id || ($subscr_txn_baid_cid_id && ($user_id = c_ws_plugin__s2member_utils_users::get_user_id_with($subscr_txn_baid_cid_id)))
			   || (!$user_id && !$subscr_txn_baid_cid_id && is_object($user = wp_get_current_user()) && !empty($user->ID) && ($user_id = $user->ID))
			)
			{
				$_subscr_baid = get_user_option('s2member_subscr_baid', $user_id);
				$_subscr_cid  = get_user_option('s2member_subscr_cid', $user_id);
				$_subscr_id   = get_user_option('s2member_subscr_id', $user_id);

				if($_subscr_id && (!$subscr_txn_baid_cid_id || $subscr_txn_baid_cid_id === $_subscr_id || $subscr_txn_baid_cid_id === $_subscr_baid || $subscr_txn_baid_cid_id === $_subscr_cid))
					if(is_array($ipn_signup_vars = get_user_option('s2member_ipn_signup_vars', $user_id)))
						if($ipn_signup_vars['subscr_id'] === $_subscr_id)
							return $ipn_signup_vars;
			}
			return FALSE; // Otherwise, return false.
		}

		/**
		 * Retrieves IPN Signup Var & validates their Subscription ID.
		 *
		 * The ``$user_id`` can be passed in directly; or a lookup can be performed with ``$subscr_id``.
		 *
		 * @package s2Member\Utilities
		 * @since 110912
		 *
		 * @param string     $var Required. The requested Signup Var.
		 * @param int|string $user_id Optional. A numeric WordPress User ID.
		 * @param string     $subscr_txn_baid_cid_id Optional. Can be used instead of passing in a ``$user_id``.
		 *   If ``$subscr_id`` is passed in, it has to match the one found inside the resulting IPN Signup Vars collected by this routine.
		 *   If neither of these parameters are passed in, the current User is assumed instead, obtained through ``wp_get_current_user()``.
		 *
		 * @return mixed|bool A User's IPN Signup Var on success, else false on failure.
		 */
		public static function get_user_ipn_signup_var($var = '', $user_id = 0, $subscr_txn_baid_cid_id = '')
		{
			if(!empty($var) && is_array($user_ipn_signup_vars = c_ws_plugin__s2member_utils_users::get_user_ipn_signup_vars($user_id, $subscr_txn_baid_cid_id)))
			{
				if(isset($user_ipn_signup_vars[$var]))
					return $user_ipn_signup_vars[$var];
			}
			return FALSE; // Otherwise, return false.
		}

		/**
		 * Obtains a User's Paid Subscr. ID *(if available)*; otherwise their WP User ID.
		 *
		 * If ``$user`` IS passed in, this function will return data from a specific ``$user``, or fail if not possible.
		 * If ``$user`` is NOT passed in, check the current User/Member.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param object $user Optional. A `WP_User` object.
		 *   In order to check the current User, you must call this function with no arguments/parameters.
		 *
		 * @return int|string|bool If possible, the User's Paid Subscr. ID, else their WordPress User ID, else false.
		 */
		public static function get_user_subscr_or_wp_id($user = NULL)
		{
			if((func_num_args() && (!is_object($user) || empty($user->ID)))
			   || (!func_num_args() && (!is_object($user = (is_user_logged_in()) ? wp_get_current_user() : FALSE) || empty($user->ID)))
			) return FALSE; // The ``$user`` was passed in but is NOT an object; or nobody is logged in.

			return ($subscr_id = get_user_option('s2member_subscr_id', $user->ID)) ? $subscr_id : $user->ID;
		}

		/**
		 * Determines whether or not a Username/Email is already in the database.
		 *
		 * Returns the WordPress User ID if they exist.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string $user_login A User's Username.
		 * @param string $user_email A User's Email Address.
		 *
		 * @return int|bool If exists, a WordPress User ID, else false.
		 */
		public static function user_login_email_exists($user_login = '', $user_email = '')
		{
			global $wpdb;
			/** @var wpdb $wpdb */

			if($user_login && $user_email) // Only if we have both of these.
				if(($user_id = $wpdb->get_var("SELECT `ID` FROM `".$wpdb->users."` WHERE `user_login` LIKE '".esc_sql(c_ws_plugin__s2member_utils_strings::like_escape($user_login))."' AND `user_email` LIKE '".esc_sql(c_ws_plugin__s2member_utils_strings::like_escape($user_email))."' LIMIT 1")))
					return $user_id; // Return the associated WordPress ID.

			return FALSE; // Otherwise, return false.
		}

		/**
		 * Determines whether or not a Username/Email is already in the database for this Blog.
		 *
		 * Returns the WordPress User ID if they exist.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string     $user_login A User's Username.
		 * @param string     $user_email A User's Email Address.
		 * @param int|string $blog_id A numeric WordPress Blog ID.
		 *
		 * @return int|bool If exists *(but not on Blog)*, a WordPress User ID, else false.
		 */
		public static function ms_user_login_email_exists_but_not_on_blog($user_login = '', $user_email = '', $blog_id = 0)
		{
			if($user_login && $user_email) // Only if we have both of these.
				if(is_multisite() && ($user_id = c_ws_plugin__s2member_utils_users::user_login_email_exists($user_login, $user_email)) && !is_user_member_of_blog($user_id, $blog_id))
					return $user_id;

			return FALSE; // Otherwise, return false.
		}

		/**
		 * Determines whether or not a Username/Email is already in the database for this Blog.
		 *
		 * This is an alias for: `c_ws_plugin__s2member_utils_users::ms_user_login_email_exists_but_not_on_blog()`.
		 *
		 * Returns the WordPress User ID if they exist.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string     $user_login A User's Username.
		 * @param string     $user_email A User's Email Address.
		 * @param int|string $blog_id A numeric WordPress Blog ID.
		 *
		 * @return int|bool If exists *(but not on Blog)*, a WordPress User ID, else false.
		 */
		public static function ms_user_login_email_can_join_blog($user_login = '', $user_email = '', $blog_id = 0)
		{
			return c_ws_plugin__s2member_utils_users::ms_user_login_email_exists_but_not_on_blog($user_login, $user_email, $blog_id);
		}

		/**
		 * Retrieves a field value. Also supports Custom Fields.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string     $field_id Required. A unique Custom Registration/Profile Field ID, that you configured with s2Member.
		 *   Or, this could be set to any property that exists on the WP_User object for a particular User;
		 *   ( i.e., `id`, `ID`, `user_login`, `user_email`, `first_name`, `last_name`, `display_name`, `ip`, `IP`,
		 *   `s2member_registration_ip`, `s2member_custom`, `s2member_subscr_id`, `s2member_subscr_or_wp_id`,
		 *   `s2member_subscr_gateway`, `s2member_custom_fields`, `s2member_file_download_access_[log|arc]`,
		 *   `s2member_auto_eot_time`, `s2member_last_payment_time`, `s2member_paid_registration_times`,
		 *   `s2member_access_role`, `s2member_access_level`, `s2member_access_label`,
		 *   `s2member_access_ccaps`, etc, etc. ).
		 * @param int|string $user_id Optional. Defaults to the current User's ID.
		 * @param array $args Any additional behavioral args. Since v150717.
		 *
		 * @return mixed The value of the requested field, or false if the field does not exist.
		 */
		public static function get_user_field($field_id = '', $user_id = 0, $args = array())
		{
			global $wpdb; /** @var wpdb $wpdb Reference for IDEs. */

			$current_user = wp_get_current_user(); // Current user.

			if(is_object($user = $user_id ? new WP_User($user_id) : $current_user)
				&& !empty($user->ID) && ($user_id = $user->ID))
			{
				$args = (array)$args; // Force array.

				if(isset($user->{$field_id}))
					return $user->{$field_id};

				else if(isset($user->data->{$field_id}))
					return $user->data->{$field_id};

				else if(isset($user->{$wpdb->prefix.$field_id}))
					return $user->{$wpdb->prefix.$field_id};

				else if(isset($user->data->{$wpdb->prefix.$field_id}))
					return $user->data->{$wpdb->prefix.$field_id};

				else if(strcasecmp($field_id, 'full_name') === 0)
					return trim($user->first_name.' '.$user->last_name);

				else if(preg_match('/^(?:email|user_email)$/i', $field_id))
					return $user->user_email;

				else if(preg_match('/^(?:login|user_login)$/i', $field_id))
					return $user->user_login;

				else if(preg_match('/^(?:s2member_)?registration_time$/i', $field_id))
					return $user->user_registered;

				else if(strcasecmp($field_id, 's2member_access_role') === 0)
					return c_ws_plugin__s2member_user_access::user_access_role($user);

				else if(strcasecmp($field_id, 's2member_access_level') === 0)
					return c_ws_plugin__s2member_user_access::user_access_level($user);

				else if(strcasecmp($field_id, 's2member_access_label') === 0)
					return c_ws_plugin__s2member_user_access::user_access_label($user);

				else if(strcasecmp($field_id, 's2member_access_ccaps') === 0)
					return c_ws_plugin__s2member_user_access::user_access_ccaps($user);

				else if(strcasecmp($field_id, 'ip') === 0 && !empty($current_user->ID) && $current_user->ID === $user_id)
					return $_SERVER['REMOTE_ADDR']; // Current IP address.

				else if(strcasecmp($field_id, 's2member_registration_ip') === 0 || strcasecmp($field_id, 'reg_ip') === 0 || strcasecmp($field_id, 'ip') === 0)
					return get_user_option('s2member_registration_ip', $user_id);

				else if(strcasecmp($field_id, 's2member_subscr_or_wp_id') === 0)
					return ($subscr_id = get_user_option('s2member_subscr_id', $user_id)) ? $subscr_id : $user_id;

				else if(strcasecmp($field_id, 'avatar') === 0) // Avatar with a specific size?
					return get_avatar($user_id, !empty($args['size']) ? $args['size'] : 96);

				else if(is_array($fields = get_user_option('s2member_custom_fields', $user_id)))
					{
						$field_var = preg_replace('/[^a-z0-9]/i', '_', strtolower($field_id));
						if(isset($fields[$field_var])) return $fields[$field_var];
					}
			}
			return FALSE; // Otherwise, return false.
		}

		/**
		 * Auto EOT time, else NPR (next payment time).
		 *
		 * @package s2Member\Utilities
		 * @since 150713
		 *
		 * @param int|string $user_id Optional. Defaults to the current User's ID.
		 * @param bool $check_gateway Defaults to a true value. If this is false, it is only possible to return a fixed EOT time.
		 * 	In other words, if this is false and there is no EOT time, empty values will be returned. Be careful with this, because not checking
		 * 	the payment gateway can result in an inaccurate return value. Only set to false if you want to limit the check to a fixed hard-coded EOT time.
		 * @param string $favor Defaults to a value of `fixed`; i.e., if a fixed EOT time is available, that is returned in favor of a next payment time.
		 * 	You can set this to `next` if you'd like to favor a next payment time (when applicable) instead of returning a fixed EOT time.
		 *
		 * @return array An associative array of EOT details; with the following elements.
		 *
		 * - `type` One of `fixed` (a fixed EOT time), `next` (next payment time; i.e., an ongoing recurring subscription); or an empty string if there is no EOT for the user.
		 * - `time` The timestamp (UTC time) that represents the EOT (End Of Term); else `0` if there is no EOT time.
		 * - `tense` If time is now (or earlier) this will be `past`. If time is in the future, this will be `future`. If there is no time, this is an empty string.
		 * - `debug` A string of details that explain to a developer what was returned. For debugging only.
		 */
		public static function get_user_eot($user_id = 0, $check_gateway = TRUE, $favor = 'fixed')
		{
			if(!($user_id = (integer)$user_id)) // Empty user ID in this call?
				$user_id = get_current_user_id(); // Assume current user.

			if(!$favor || !in_array($favor, array('fixed', 'next'), TRUE))
				$favor = 'fixed'; // Default behavior.

			$now            = time(); // Current timestamp.
			$grace_time     = (integer)$GLOBALS['WS_PLUGIN__']['s2member']['o']['eot_grace_time'];
			$grace_time     = (integer)apply_filters('ws_plugin__s2member_eot_grace_time', $grace_time);
			$demotion_role  = c_ws_plugin__s2member_option_forces::force_demotion_role('subscriber');
			$empty_response = array('type' => '', 'time' => 0, 'tense' => '', 'debug' => '');

			if(!$user_id || !($user = new WP_User($user_id)) || !$user->ID)
				return array_merge($empty_response, array('debug' => 'Invalid user ID.'));

			$ipn_signup_vars     = self::get_user_ipn_signup_vars($user->ID);
			$subscr_gateway      = (string)get_user_option('s2member_subscr_gateway', $user->ID);
			$subscr_id           = (string)get_user_option('s2member_subscr_id', $user->ID);
			$subscr_cid          = (string)get_user_option('s2member_subscr_cid', $user->ID);
			$last_auto_eot_time  = (integer)get_user_option('s2member_last_auto_eot_time', $user->ID);
			$auto_eot_time       = (integer)get_user_option('s2member_auto_eot_time', $user->ID);

			if($auto_eot_time) // They have a hard-coded EOT time at present?
				return array('type' => 'fixed', 'time' => $auto_eot_time, 'tense' => $auto_eot_time <= $now ? 'past' : 'future',
					'debug' => 'This is a fixed EOT time recorded by s2Member. It can be altered in the WordPress Dashboard for this user.');

			if(!$subscr_gateway && !$subscr_id && !$subscr_cid && $last_auto_eot_time // EOTd?
				&& (!user_can($user->ID, 'access_s2member_level1') || c_ws_plugin__s2member_user_access::user_access_role($user) === $demotion_role)
				&& !c_ws_plugin__s2member_user_access::user_access_ccaps($user) // And no CCAPs either?
			) return array('type' => 'fixed', 'time' => $last_auto_eot_time, 'tense' => $last_auto_eot_time <= $now ? 'past' : 'future',
				'debug' => 'This is an archived/fixed EOT time recorded by s2Member; i.e., the date this customer\'s access expired.');

			if(!$subscr_gateway || !$subscr_id || !is_array($ipn_signup_vars) || !$ipn_signup_vars)
				return array_merge($empty_response, array('debug' => 'This user has no subscription; i.e., missing `subscr_id`, `subscr_gateway` or `ipn_signup_vars`.'));

			if(empty($ipn_signup_vars['txn_type']) || $ipn_signup_vars['txn_type'] !== 'subscr_signup')
				return array_merge($empty_response, array('debug' => 'This user has no subscription; i.e., `txn_type` != `subscr_signup`.'));

			$auto_eot_time // Update this now; i.e., build a new EOT time based on IPN signup vars.
				= c_ws_plugin__s2member_utils_time::auto_eot_time($user->ID, $ipn_signup_vars['period1'], $ipn_signup_vars['period3']);

			if($check_gateway) switch($subscr_gateway) // A bit different for each payment gateway.
			{
				case 'paypal': // PayPal (PayPal Pro only).

					if(!c_ws_plugin__s2member_utils_conds::pro_is_installed()
						|| !class_exists('c_ws_plugin__s2member_pro_paypal_utilities')
						|| !$GLOBALS['WS_PLUGIN__']['s2member']['o']['paypal_api_username']
						|| !$GLOBALS['WS_PLUGIN__']['s2member']['o']['paypal_api_password']
						|| !$GLOBALS['WS_PLUGIN__']['s2member']['o']['paypal_api_signature']
					) return array_merge($empty_response, array('debug' => 'PayPal Pro API credentials missing in your s2Member configuration.'));

					if($GLOBALS['WS_PLUGIN__']['s2member']['o']['paypal_payflow_api_username'])
					{
						if(!($api_response = c_ws_plugin__s2member_pro_paypal_utilities::payflow_get_profile($subscr_id)) || !empty($api_response['__error']))
							return array_merge($empty_response, array('debug' => 'No fixed EOT, and the PayPal Pro API says there is no subscription for this user.'));

						if(preg_match('/^(?:Pending|PendingProfile)$/i', $api_response['STATUS']))
							return array_merge($empty_response, array('debug' => 'No fixed EOT, and the PayPal Pro API says the subscription for this user is currently pending changes. Unable to determine at this moment. Please try again in 15 minutes.'));

						if(!preg_match('/^(?:Active|ActiveProfile)$/i', $api_response['STATUS']))
							return array('type' => 'fixed', 'time' => $auto_eot_time, 'tense' => $auto_eot_time <= $now ? 'past' : 'future',
								'debug' => 'This is the estimated EOT time. The PayPal Pro API says this subscription is no longer active, and thus, access should be terminated at this time.');

						if($api_response['TERM'] > 0 && $api_response['PAYMENTSLEFT'] <= 0)
							return array('type' => 'fixed', 'time' => $auto_eot_time, 'tense' => $auto_eot_time <= $now ? 'past' : 'future',
								'debug' => 'This is the estimated EOT time. The PayPal Pro API says this subscription has reached its last payment, and thus, access should be terminated at this time.');

						if($api_response['TERM'] <= 0 || $api_response['PAYMENTSLEFT'] > 0)
							if($api_response['NEXTPAYMENT'] && strlen($api_response['NEXTPAYMENT']) === 8) // MMDDYYYY format is not `strtotime()` compatible.
								if(($time = strtotime(substr($api_response['NEXTPAYMENT'], -4).'-'.substr($api_response['NEXTPAYMENT'], 0, 2).'-'.substr($api_response['NEXTPAYMENT'], 2, 2))) > $now)
									return array('type' => 'next', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
										'debug' => 'The PayPal Pro API says this is the next payment time.');
					}
					else // Use PayPal Pro API (old flavor).
					{
						$api_args = array(
							'METHOD'    => 'GetRecurringPaymentsProfileDetails',
							'PROFILEID' => $subscr_id,
						);
						if(!($api_response = c_ws_plugin__s2member_paypal_utilities::paypal_api_response($api_args)) || !empty($api_response['__error']))
							return array_merge($empty_response, array('debug' => 'No fixed EOT, and the PayPal Pro API says there is no subscription for this user.'));

						if(preg_match('/^(?:Pending|PendingProfile)$/i', $api_response['STATUS']))
							return array_merge($empty_response, array('debug' => 'No fixed EOT, and the PayPal Pro API says the subscription for this user is currently pending changes. Unable to determine at this moment. Please try again in 15 minutes.'));

						if(!preg_match('/^(?:Active|ActiveProfile)$/i', $api_response['STATUS']))
							return array('type' => 'fixed', 'time' => $auto_eot_time, 'tense' => $auto_eot_time <= $now ? 'past' : 'future',
								'debug' => 'This is the estimated EOT time. The PayPal Pro API says this subscription is no longer active, and thus, access should be terminated at this time.');

						if($api_response['TOTALBILLINGCYCLES'] > 0 && $api_response['NUMCYCLESREMAINING'] <= 0)
							return array('type' => 'fixed', 'time' => $auto_eot_time, 'tense' => $auto_eot_time <= $now ? 'past' : 'future',
								'debug' => 'This is the estimated EOT time. The PayPal Pro API says this subscription has reached its last payment, and thus, access should be terminated at this time.');

						if($api_response['TOTALBILLINGCYCLES'] <= 0 || $api_response['NUMCYCLESREMAINING'] > 0)
							if($api_response['NEXTBILLINGDATE'] && ($time = strtotime($api_response['NEXTBILLINGDATE'])) > $now)
								return array('type' => 'next', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
									'debug' => 'The PayPal Pro API says this is the next payment time.');
					}
					return array_merge($empty_response, array('debug' => 'No fixed EOT, and there are no more payments needed from this user.'));

					break; // Break switch.

				case 'authnet': // Authorize.Net (EOT only; w/ limited functionality).

					if(!c_ws_plugin__s2member_utils_conds::pro_is_installed()
						|| !class_exists('c_ws_plugin__s2member_pro_authnet_utilities')
						|| !$GLOBALS['WS_PLUGIN__']['s2member']['o']['pro_authnet_api_login_id']
						|| !$GLOBALS['WS_PLUGIN__']['s2member']['o']['pro_authnet_api_trans_key']
					) return array_merge($empty_response, array('debug' => 'Authorize.Net API credentials missing in your s2Member configuration.'));

					$api_args = array(
						'x_method'          => 'status',
						'x_subscription_id' => $subscr_id,
					);
					if(!($api_response = c_ws_plugin__s2member_pro_authnet_utilities::authnet_arb_response($api_args)) || !empty($api_response['__error']))
						return array_merge($empty_response, array('debug' => 'No fixed EOT, and the Authorize.Net API says there is no subscription for this user.'));

					if(!preg_match('/^(?:active)$/i', $api_response['subscription_status']))
						return array('type' => 'fixed', 'time' => $auto_eot_time, 'tense' => $auto_eot_time <= $now ? 'past' : 'future',
							'debug' => 'This is the estimated EOT time. The Authorize.Net API says this subscription is no longer active, and thus, access should be terminated at this time.');

					// Next payment time not possible with Authorize.Net at this time.
					// Fixed recurring intervals not possible to query with Authorize.Net at this time.
					return array_merge($empty_response, array('debug' => 'Partially-supported payment gateway; unable to determine.'));

					break; // Break switch.

				case 'stripe': // Stripe payment gateway (best).

					if(!c_ws_plugin__s2member_utils_conds::pro_is_installed()
						|| !class_exists('c_ws_plugin__s2member_pro_stripe_utilities')
						|| !$GLOBALS['WS_PLUGIN__']['s2member']['o']['pro_stripe_api_publishable_key']
						|| !$GLOBALS['WS_PLUGIN__']['s2member']['o']['pro_stripe_api_secret_key']
					) return array_merge($empty_response, array('debug' => 'Stripe API credentials missing in your s2Member configuration.'));

					if(!$subscr_cid) return array_merge($empty_response, array('debug' => 'No fixed EOT, and no `subscr_cid` on file. Unable to determine.'));

					if(!is_object($stripe_subscription = c_ws_plugin__s2member_pro_stripe_utilities::get_customer_subscription($subscr_cid, $subscr_id)) || empty($stripe_subscription->id))
						return array_merge($empty_response, array('debug' => 'No fixed EOT, and the Stripe API says there is no subscription for this user.'));

					if((integer)$stripe_subscription->ended_at > 0) // Done?
					{
						$time = $stripe_subscription->ended_at + $grace_time;
						return array('type' => 'fixed', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
							'debug' => 'The Stripe API says this subscription reached an expiration on this date + grace time.');
					}
					if(in_array($stripe_subscription->status, array('canceled', 'unpaid'), TRUE) || $stripe_subscription->cancel_at_period_end)
					{
						$time = $stripe_subscription->current_period_end + $grace_time;
						return array('type' => 'fixed', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
							'debug' => 'The Stripe API says this subscription was cancelled, and thus, should EOT on this date + grace time.');
					}
					if(isset($stripe_subscription->plan->metadata->recurring, $stripe_subscription->plan->metadata->recurring_times)
						&& !$stripe_subscription->plan->metadata->recurring) // Non-recurring subscription?
					{
						$time = (integer)$stripe_subscription->start;
						$time += $stripe_subscription->plan->trial_period_days * DAY_IN_SECONDS;

						switch($stripe_subscription->plan->interval)
						{
							case 'day': // Every X days in this case.
								$time += (DAY_IN_SECONDS * $stripe_subscription->plan->interval_count) * 1;
								break; // Break switch now.

							case 'week': // Every X weeks in this case.
								$time += (WEEK_IN_SECONDS * $stripe_subscription->plan->interval_count) * 1;
								break; // Break switch now.

							case 'month': // Every X months in this case.
								$time += ((WEEK_IN_SECONDS * 4) * $stripe_subscription->plan->interval_count) * 1;
								break; // Break switch now.

							case 'year': // Every X years in this case.
								$time += (YEAR_IN_SECONDS * $stripe_subscription->plan->interval_count) * 1;
								break; // Break switch now.
						}
						if($favor === 'next' && $stripe_subscription->current_period_end + 1 < $time)
						{
							if($stripe_subscription->current_period_end + 1 > $now)
							{
								$time = $stripe_subscription->current_period_end + 1;
								return array('type' => 'next', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
									'debug' => 'The Stripe API says this is the next payment time.');
							}
							return array_merge($empty_response, array('debug' => 'Stripe says no more payments needed from this user.'));
						}
						$time += $grace_time; // Now add grace to the final EOT time.
						return array('type' => 'fixed', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
							'debug' => 'The Stripe API says this subscription will be completely over on this date + grace time.');
					}
					if(isset($stripe_subscription->plan->metadata->recurring, $stripe_subscription->plan->metadata->recurring_times)
						&& $stripe_subscription->plan->metadata->recurring && $stripe_subscription->plan->metadata->recurring_times <= 0)
					{
						if($stripe_subscription->current_period_end + 1 > $now)
						{
							$time = $stripe_subscription->current_period_end + 1;
							return array('type' => 'next', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
								'debug' => 'The Stripe API says this is the next payment time.');
						}
						return array_merge($empty_response, array('debug' => 'Stripe says no more payments needed from this user.'));
					}
					if(isset($stripe_subscription->plan->metadata->recurring, $stripe_subscription->plan->metadata->recurring_times)
						&& $stripe_subscription->plan->metadata->recurring && $stripe_subscription->plan->metadata->recurring_times > 0)
					{
						$time = (integer)$stripe_subscription->start;
						$time += $stripe_subscription->plan->trial_period_days * DAY_IN_SECONDS;

						switch($stripe_subscription->plan->interval)
						{
							case 'day': // Every X days in this case.
								$time += (DAY_IN_SECONDS * $stripe_subscription->plan->interval_count)
									* $stripe_subscription->plan->metadata->recurring_times;
								break; // Break switch now.

							case 'week': // Every X weeks in this case.
								$time += (WEEK_IN_SECONDS * $stripe_subscription->plan->interval_count)
									* $stripe_subscription->plan->metadata->recurring_times;
								break; // Break switch now.

							case 'month': // Every X months in this case.
								$time += ((WEEK_IN_SECONDS * 4) * $stripe_subscription->plan->interval_count)
									* $stripe_subscription->plan->metadata->recurring_times;
								break; // Break switch now.

							case 'year': // Every X years in this case.
								$time += (YEAR_IN_SECONDS * $stripe_subscription->plan->interval_count)
									* $stripe_subscription->plan->metadata->recurring_times;
								break; // Break switch now.
						}
						if($favor === 'next' && $stripe_subscription->current_period_end + 1 < $time)
						{
							if($stripe_subscription->current_period_end + 1 > $now)
							{
								$time = $stripe_subscription->current_period_end + 1;
								return array('type' => 'next', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
									'debug' => 'The Stripe API says this is the next payment time.');
							}
							return array_merge($empty_response, array('debug' => 'Stripe says no more payments needed from this user.'));
						}
						$time += $grace_time; // Now add grace to the final EOT time.
						return array('type' => 'fixed', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
							'debug' => 'The Stripe API says this subscription will be completely over on this date + grace time.');
					}
					if($stripe_subscription->current_period_end + 1 > $now)
					{
						$time = $stripe_subscription->current_period_end + 1;
						return array('type' => 'next', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
							'debug' => 'The Stripe API says this is the next payment time.');
					}
					return array_merge($empty_response, array('debug' => 'No fixed EOT, and Stripe says there are no more payments needed from this user.'));

					break; // Break switch.

				case 'clickbank': // ClickBank (limited functionality).

					if(!c_ws_plugin__s2member_utils_conds::pro_is_installed()
						|| !class_exists('c_ws_plugin__s2member_pro_clickbank_utilities')
						|| !$GLOBALS['WS_PLUGIN__']['s2member']['o']['pro_clickbank_username']
						|| !$GLOBALS['WS_PLUGIN__']['s2member']['o']['pro_clickbank_clerk_key']
						|| !$GLOBALS['WS_PLUGIN__']['s2member']['o']['pro_clickbank_developer_key']
						|| !$GLOBALS['WS_PLUGIN__']['s2member']['o']['pro_clickbank_secret_key']
					) return array_merge($empty_response, array('debug' => 'ClickBank API credentials missing in your s2Member configuration.'));

					if(empty($ipn_signup_vars['txn_id'])) // ClickBank receipt number.
						return array_merge($empty_response, array('debug' => 'No fixed EOT, and no `txn_id` on file. Unable to determine.'));

					if(!($api_response = c_ws_plugin__s2member_pro_clickbank_utilities::clickbank_api_order($ipn_signup_vars['txn_id'])))
						return array_merge($empty_response, array('debug' => 'No fixed EOT, and the ClickBank API says there is no subscription for this user.'));

					if(!preg_match('/^(?:TEST_)?SALE$/i', $api_response['txnType']) || !$api_response['recurring'])
						return array_merge($empty_response, array('debug' => 'No fixed EOT, and the ClickBank API says there is no recurring subscription for this user.'));

					if(strcasecmp($api_response['status'], 'active') !== 0 || $api_response['futurePayments'] <= 0)
						return array('type' => 'fixed', 'time' => $auto_eot_time, 'tense' => $auto_eot_time <= $now ? 'past' : 'future',
							'debug' => 'This is the estimated EOT time. The ClickBank API says this subscription no longer active, or it has reached its last payment, and thus, access should be terminated at this time.');

					if($api_response['nextPaymentDate'] && ($time = strtotime($api_response['nextPaymentDate'])) > $now)
						return array('type' => 'next', 'time' => $time, 'tense' => $time <= $now ? 'past' : 'future',
							'debug' => 'The ClickBank API says this is the next payment time.');

					return array_merge($empty_response, array('debug' => 'No fixed EOT, and there are no more payments needed from this user.'));

					break; // Break switch.

				default: // Default case handler.
					return array_merge($empty_response, array('debug' => 'Partially-supported payment gateway; unable to determine.'));
			}
			return array_merge($empty_response, array('debug' => 'Payment gateway check disabled; unable to determine.'));
		}
	}
}
