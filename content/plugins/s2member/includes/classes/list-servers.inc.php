<?php
/**
 * List Server integrations.
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
 * @package s2Member\List_Servers
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_list_servers'))
{
	/**
	 * List Server integrations.
	 *
	 * @package s2Member\List_Servers
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_list_servers
	{
		/**
		 * Processes list server integrations.
		 *
		 * @since 3.5
		 * @package s2Member\List_Servers
		 *
		 * @param string     $role A WP role.
		 * @param int|string $level A numeric level.
		 * @param string     $login Username for the user.
		 * @param string     $pass Plain text password for the User.
		 * @param string     $email Email address for the user.
		 * @param string     $fname First name for the user.
		 * @param string     $lname Last name for the user.
		 * @param string     $ip IP address for the user.
		 * @param bool       $opt_in Defaults to `FALSE`; must be set to `TRUE`.
		 * @param bool       $double_opt_in Defaults to `TRUE`. Use at your own risk.
		 * @param int|string $user_id A WordPress User ID, numeric string or integer.
		 *
		 * @return bool True if at least one list server is processed successfully.
		 */
		public static function process_list_servers($role = '', $level = '',
		                                            $login = '', $pass = '', $email = '', $fname = '', $lname = '', $ip = '',
		                                            $opt_in = FALSE, $double_opt_in = TRUE,
		                                            $user_id = 0)
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_process_list_servers', get_defined_vars());
			unset($__refs, $__v); // Allows vars to be modified by reference.

			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_opt_in'])
				$opt_in = true; // Accept as true; the double opt-in box is null.

			if(c_ws_plugin__s2member_list_servers::list_servers_integrated())
			{
				$args                = get_defined_vars(); // Function args.
				$mailchimp_success   = c_ws_plugin__s2member_mailchimp::subscribe($args);
				$getresponse_success = c_ws_plugin__s2member_getresponse::subscribe($args);
				$aweber_success      = c_ws_plugin__s2member_aweber::subscribe($args);
				$success             = $mailchimp_success || $getresponse_success || $aweber_success;

				if($user_id) update_user_option($user_id, 's2member_opt_in', '1');

				do_action('ws_plugin__s2member_during_process_list_servers', get_defined_vars());
			}
			do_action('ws_plugin__s2member_after_process_list_servers', get_defined_vars());

			return apply_filters('ws_plugin__s2member_process_list_servers', !empty($success), get_defined_vars());
		}

		/**
		 * Process list servers against current user.
		 *
		 * @since 141004
		 * @package s2Member\List_Servers
		 *
		 * See {@link process_list_servers()} for further details.
		 *
		 * @param bool $opt_in Defaults to `FALSE`; must be set to `TRUE`.
		 * @param bool $double_opt_in Defaults to `TRUE`. Use at your own risk.
		 * @param bool $clean_user_cache Defaults to `TRUE`; i.e., we start from a fresh copy of the current user.
		 *
		 * @return bool True if at least one list server is processed successfully.
		 */
		public static function process_list_servers_against_current_user($opt_in = FALSE, $double_opt_in = TRUE, $clean_user_cache = TRUE)
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_process_list_servers_against_current_user', get_defined_vars());
			unset($__refs, $__v); // Allows vars to be modified by reference.

			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_opt_in'])
				$opt_in = true; // Accept as true; the double opt-in box is null.

			if($clean_user_cache) // Start from a fresh user object here?
			{
				clean_user_cache(get_current_user_id());
				wp_cache_delete(get_current_user_id(), 'user_meta');
				$user = new WP_User(get_current_user_id());
			}
			else $user = wp_get_current_user();

			return self::process_list_servers(
				($role = c_ws_plugin__s2member_user_access::user_access_role($user)),
				($level = c_ws_plugin__s2member_user_access::user_access_level($user)),
				($login = $user->user_login),
				($pass = $user->user_pass),
				($email = $user->user_email),
				($fname = $user->first_name),
				($lname = $user->last_name),
				($ip = @$_SERVER['REMOTE_ADDR']),
				($opt_in = $opt_in),
				($double_opt_in = $double_opt_in),
				($user_id = $user->ID)
			);
		}

		/**
		 * Processes list server removals.
		 *
		 * @since 3.5
		 * @package s2Member\List_Servers
		 *
		 * @param string     $role A WP role.
		 * @param int|string $level A numeric level.
		 * @param string     $login Username for the user.
		 * @param string     $pass Plain text password for the User.
		 * @param string     $email Email address for the user.
		 * @param string     $fname First name for the user.
		 * @param string     $lname Last name for the user.
		 * @param string     $ip IP address for the user.
		 * @param bool       $opt_out Defaults to `FALSE`; must be set to `TRUE`.
		 * @param int|string $user_id A WordPress User ID, numeric string or integer.
		 *
		 * @return bool True if at least one list server removal is processed successfully.
		 */
		public static function process_list_server_removals($role = '', $level = '',
		                                                    $login = '', $pass = '', $email = '', $fname = '', $lname = '', $ip = '',
		                                                    $opt_out = FALSE,
		                                                    $user_id = 0)
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_process_list_server_removals', get_defined_vars());
			unset($__refs, $__v); // Allows vars to be modified by reference.

			if(c_ws_plugin__s2member_list_servers::list_servers_integrated())
			{
				$args                = get_defined_vars(); // Function args.
				$mailchimp_success   = c_ws_plugin__s2member_mailchimp::unsubscribe($args);
				$getresponse_success = c_ws_plugin__s2member_getresponse::unsubscribe($args);
				$aweber_success      = c_ws_plugin__s2member_aweber::unsubscribe($args);
				$success             = $mailchimp_success || $getresponse_success || $aweber_success;

				do_action('ws_plugin__s2member_during_process_list_server_removals', get_defined_vars());

				if($user_id) update_user_option($user_id, 's2member_opt_in', '0');
			}
			do_action('ws_plugin__s2member_after_process_list_server_removals', get_defined_vars());

			return apply_filters('ws_plugin__s2member_process_list_server_removals', !empty($success), get_defined_vars());
		}

		/**
		 * Process list server removals against current user.
		 *
		 * See {@link process_list_server_removals()} for further details.
		 *
		 * @since 141004
		 * @package s2Member\List_Servers
		 *
		 * @param bool $opt_out Defaults to `FALSE`; must be set to `TRUE`.
		 * @param bool $clean_user_cache Defaults to `TRUE`; i.e., we start from a fresh copy of the current user.
		 *
		 * @return bool True if at least one list server removal is processed successfully.
		 */
		public static function process_list_server_removals_against_current_user($opt_out = FALSE, $clean_user_cache = TRUE)
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_process_list_server_removals_against_current_user', get_defined_vars());
			unset($__refs, $__v); // Allows vars to be modified by reference.

			if($clean_user_cache) // Start from a fresh user object here?
			{
				clean_user_cache(get_current_user_id());
				wp_cache_delete(get_current_user_id(), 'user_meta');
				$user = new WP_User(get_current_user_id());
			}
			else $user = wp_get_current_user();

			return self::process_list_server_removals(
				($role = c_ws_plugin__s2member_user_access::user_access_role($user)),
				($level = c_ws_plugin__s2member_user_access::user_access_level($user)),
				($login = $user->user_login),
				($pass = $user->user_pass),
				($email = $user->user_email),
				($fname = $user->first_name),
				($lname = $user->last_name),
				($ip = @$_SERVER['REMOTE_ADDR']),
				($opt_out = $opt_out),
				($user_id = $user->ID)
			);
		}

		/**
		 * Listens to Collective EOT/MOD events processed by s2Member.
		 *
		 * @since 3.5
		 * @package s2Member\List_Servers
		 *
		 * @attaches-to `add_action('ws_plugin__s2member_during_collective_mods');`.
		 * @attaches-to `add_action('ws_plugin__s2member_during_collective_eots');`.
		 *
		 * @param int|string $user_id Required. A WordPress User ID, numeric string or integer.
		 * @param array      $vars Required. An array of defined variables passed by the calling hook.
		 * @param string     $event Required. A specific event that triggered this call from the action hook.
		 * @param string     $event_spec Required. A specific event specification *(a broader classification)*.
		 * @param string     $mod_new_role Required if `$event_spec === 'modification'`; but can be empty. User role.
		 * @param string     $mod_new_user Optional. If `$event_spec === 'modification'`, the new user object with current details.
		 * @param string     $mod_old_user Optional. If `$event_spec === 'modification'`, the old/previous user obj with old details.
		 *
		 * @note This is only applicable when `['custom_reg_auto_opt_outs']` contains related Event(s).
		 */
		public static function auto_process_list_server_removals($user_id, $vars, $event, $event_spec, $mod_new_role = NULL, $mod_new_user = NULL, $mod_old_user = NULL)
		{
			static $auto_processed = array(); // Static cache.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_auto_process_list_server_removals', get_defined_vars());
			unset($__refs, $__v); // Allows vars to be modified by reference.

			if(c_ws_plugin__s2member_list_servers::list_servers_integrated())
				if($user_id && is_numeric($user_id) && !isset($auto_processed[$user_id]))
					if(is_array($vars) && is_string($event = (string)$event) && is_string($event_spec = (string)$event_spec))
						if(($custom_reg_auto_op_outs = c_ws_plugin__s2member_utils_strings::wrap_deep($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_auto_opt_outs'], '/^', '$/i')))
							if(c_ws_plugin__s2member_utils_arrays::in_regex_array($event, $custom_reg_auto_op_outs) || c_ws_plugin__s2member_utils_arrays::in_regex_array($event_spec, $custom_reg_auto_op_outs))
								if(is_object($dynamic_user = $user_now = new WP_User($user_id)) && $dynamic_user->exists() && !empty($dynamic_user->ID))
								{
									$mod_new_role = $event_spec === 'modification' && is_string($mod_new_role) ? $mod_new_role : ''; // Might be empty.
									$mod_new_user = $event_spec === 'modification' && !empty($mod_new_user->ID) && $mod_new_user->ID === $dynamic_user->ID ? $mod_new_user : NULL;
									$mod_old_user = $event_spec === 'modification' && !empty($mod_old_user->ID) && $mod_old_user->ID === $dynamic_user->ID ? $mod_old_user : NULL;
									$dynamic_user = $event_spec === 'modification' && $mod_old_user ? $mod_old_user : $user_now; // Use old user when applicable.

									if( // Secondary conditionals.

									($event_spec !== 'modification' // Not a modification.

									 || ($event_spec === 'modification' // Or it is, with a role change!
									     && $mod_new_role !== c_ws_plugin__s2member_user_access::user_access_role($dynamic_user)
									     && strtotime($dynamic_user->user_registered) < strtotime('-10 seconds') // Hackety hack.
									     && ($event !== 'user-role-change' // Ignore this event, UNLESS it has confirmation.
									         || ($event === 'user-role-change' // An admin has specifically asked for this to occur?
									             && !empty($vars['_p']['ws_plugin__s2member_custom_reg_auto_opt_out_transitions']))))
									)
									) // Let us proceed now; with list removals at the very least.
									{
										$auto_processed[$dynamic_user->ID] = -1; // Flag as auto-processed!

										$auto_removal_success = c_ws_plugin__s2member_list_servers::process_list_server_removals
										(
											c_ws_plugin__s2member_user_access::user_access_role($dynamic_user), // Old role w/ modifications.
											c_ws_plugin__s2member_user_access::user_access_level($dynamic_user), // Old level w/ modifications.
											$dynamic_user->user_login, '', $dynamic_user->user_email, $dynamic_user->first_name, $dynamic_user->last_name, '', TRUE, $dynamic_user->ID
										);
										if( // Now let's determine if they should be subscribed to the new lists.

											$event_spec === 'modification' && $mod_new_role // And they have a role now?
											&& ($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_auto_opt_out_transitions'] === '2'
											    || ($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_auto_opt_out_transitions'] === '1' && $auto_removal_success))

										) // Let us proceed now; we want to subscribe the user to the list they should be on now; based on role/level.
										{
											$dynamic_user = $event_spec === 'modification' && $mod_new_user ? $mod_new_user : $user_now; // New user; when applicable.

											$auto_transition_success = c_ws_plugin__s2member_list_servers::process_list_servers
											(
												$mod_new_role, // Subscribe to lists associated w/ their new role/level.
												c_ws_plugin__s2member_user_access::user_access_role_to_level($mod_new_role),
												$dynamic_user->user_login, '', $dynamic_user->user_email, $dynamic_user->first_name, $dynamic_user->last_name,
												'', TRUE, ($auto_removal_success ? FALSE : TRUE), $dynamic_user->ID
											);
											do_action('ws_plugin__s2member_during_auto_process_list_server_removal_transitions', get_defined_vars());
										}
										do_action('ws_plugin__s2member_during_auto_process_list_server_removals', get_defined_vars());
									}
								}
			do_action('ws_plugin__s2member_after_auto_process_list_server_removals', get_defined_vars());
		}

		/**
		 * List servers have been integrated?
		 *
		 * @since 3.5
		 * @package s2Member\List_Servers
		 *
		 * @return bool True if list servers have been integrated.
		 */
		public static function list_servers_integrated()
		{
			do_action('ws_plugin__s2member_before_list_servers_integrated', get_defined_vars());

			for($n = 0; $n <= $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n++ /* Go through each Level; looking for a configured list. */)
				if(!empty($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_mailchimp_list_ids']) || !empty($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_getresponse_list_ids']) || !empty($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_aweber_list_ids']))
					return apply_filters('ws_plugin__s2member_list_servers_integrated', TRUE, get_defined_vars());

			return apply_filters('ws_plugin__s2member_list_servers_integrated', FALSE, get_defined_vars());
		}
	}
}
