<?php
/**
 * List Server Base
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
 * @since 141004
 * @package s2Member\List_Servers
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_list_server_base'))
{
	/**
	 * List Server Base
	 *
	 * @since 141004
	 * @package s2Member\List_Servers
	 */
	class c_ws_plugin__s2member_list_server_base
	{
		/**
		 * Validates args.
		 *
		 * @since 141004
		 * @package s2Member\List_Servers
		 *
		 * @param array $args Input arguments.
		 *
		 * @return \stdClass|null An object with only valid properties.
		 *    If unable to validate, this returns a `NULL` value.
		 */
		public static function validate_args($args)
		{
			if(!$args || !is_array($args))
				return NULL;

			$defaults = array(
				'role'          => '',
				'level'         => '',
				'ccaps'         => '',
				'login'         => '',
				'pass'          => '',
				'email'         => '',
				'fname'         => '',
				'lname'         => '',
				'ip'            => '',
				'opt_out'       => FALSE,
				'opt_in'        => FALSE,
				'double_opt_in' => FALSE,
				'user'          => NULL,
				'user_id'       => 0
			);
			$args     = array_merge($defaults, $args);
			$args     = (object)array_intersect_key($args, $defaults);

			foreach($args as $_key => &$_value)
				switch($_key) // Typify.
				{
					case 'role':
					case 'level':
						$_value = (string)$_value;
						break;

					case 'ccaps': // Input can be a string or an array.
						$_value = is_array($_value) ? implode(',', $_value) : (string)$_value;
						break;

					case 'login':
					case 'pass':
					case 'email':
					case 'fname':
					case 'lname':
					case 'ip':
						$_value = (string)$_value;
						break;

					case 'opt_in':
					case 'double_opt_in':
						$_value = (boolean)$_value;
						break;

					case 'user': // A `WP_User` object instance.
						$_value = $_value instanceof WP_User ? $_value : NULL;
						break;

					case 'user_id': // User ID.
						$_value = (integer)$_value;
						break;
				}
			unset($_key, $_value); // Housekeeping.

			if(!$args->user_id && $args->user && $args->user->exists())
				$args->user_id = $args->user->ID; // Use this ID.
			$args->user = new WP_User($args->user_id); // Always based on ID.

			$args->ccaps = implode(',', c_ws_plugin__s2member_user_access::user_access_ccaps($args->user));
			$args->fname = !$args->fname ? ucwords((string)strstr($args->email, '@', TRUE)) : $args->fname;
			$args->lname = !$args->lname ? '-' : $args->lname; // Default last name to `-` because MC requires this.
			$args->name  = $args->fname || $args->lname ? trim($args->fname.' '.$args->lname) : ucwords((string)strstr($args->email, '@', TRUE));

			if(!$args->role || !isset($args->level[0]) || !is_numeric($args->level)
			   || !$args->login || !$args->email || !is_email($args->email)
			   || !$args->user_id || !$args->user || !$args->user->exists()
			) return NULL; // Required arguments missing.

			return $args; // Now a \stdClass object.
		}
	}
}