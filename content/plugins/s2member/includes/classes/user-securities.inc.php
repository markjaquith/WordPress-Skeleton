<?php
/**
 * User securities.
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
 * @package s2Member\User_Securities
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_user_securities'))
{
	/**
	 * User securities.
	 *
	 * @package s2Member\User_Securities
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_user_securities
	{
		/**
		 * Initializes Filter for `user_has_cap`.
		 *
		 * It's very important that this is NOT attached before WordPress creates `$current_user` via `$wp->init()`.
		 * This prevents crashes when other plugins attempt to call upon `current_user_can()` before WordPress is initialized.
		 * For instance, some plugins attempt to use `current_user_can()` on the `plugins_loaded` Hook, which they should not do.
		 *
		 * @package s2Member\User_Securities
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('init');``
		 */
		public static function initialize() // Initializes the Filter for `user_has_cap`.
		{
			add_filter('user_has_cap', 'c_ws_plugin__s2member_user_securities::user_capabilities', 10, 3);
		}

		/**
		 * Deals w/ bbPress-specific issues on a Multisite Network.
		 *
		 * @package s2Member\User_Securities
		 * @since 150224
		 *
		 * @attaches-to ``add_action('after_setup_theme');``
		 */
		public static function set_current_user()
		{
			if(is_multisite() && is_user_logged_in() && !current_user_can('read'))
				remove_action('bbp_setup_current_user', 'bbp_set_current_user_default_role');
		}

		/**
		 * Alters `WP_User->has_cap()` in special cases for Administrators.
		 *
		 * @package s2Member\User_Securities
		 * @since 110815
		 *
		 * @attaches-to ``add_filter('user_has_cap');``
		 *
		 * @param array $capabilities Expects an array of Capabilities passed in by the Filter.
		 *   This array contains all of the Capabilities that the User has *( i.e., ``$user->allcaps`` )*.
		 * @param array $caps_map An array of Capabilities mapped out by the ``map_meta_cap`` function.
		 * @param array $args Array of arguments originally passed through the ``has_cap()`` function.
		 *   However, WordPress modifies this array of arguments in the following way.
		 *   Argument `[0]` is the Capability test string itself *(this is normal)*.
		 *   Argument `[1]` is added by WordPress; it's the ID of the User.
		 *   Other arguments starting from array index `[2]` are normal.
		 *
		 * @return array An array of Capabilities.
		 */
		public static function user_capabilities($capabilities, $caps_map, $args)
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_user_capabilities', get_defined_vars());
			unset($__refs, $__v);

			if(!empty($capabilities['access_s2member_ccap_all_ccaps']) && !empty($args[0]) && preg_match('/^access_s2member_ccap_/i', $args[0]) && apply_filters('ws_plugin__s2member_all_ccaps_enable', TRUE, get_defined_vars()))
				$capabilities = array_merge((array)$capabilities, array($args[0] => 1));

			else if(!is_multisite() && !empty($capabilities['administrator']) && !empty($args[0]) && preg_match('/^access_s2member_ccap_/i', $args[0]) && apply_filters('ws_plugin__s2member_admins_have_all_ccaps', TRUE, get_defined_vars()))
				$capabilities = array_merge((array)$capabilities, array($args[0] => 1));

			else if(is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && (is_super_admin() || !empty($capabilities['administrator'])) && !empty($args[0]) && ($args[0] === 'edit_user' || $args[0] === 'edit_users'))
				if($args[0] === 'edit_users' || ($args[0] === 'edit_user' && !empty($args[2]) && ((!empty($args[1]) && (int)$args[1] === (int)$args[2]) || is_user_member_of_blog($args[2]))))
					$capabilities = array_merge((array)$capabilities, array('edit_users' => 1));

			return apply_filters('ws_plugin__s2member_user_capabilities', $capabilities, get_defined_vars());
		}

		/**
		 * Alters this Filter inside `/wp-admin/user-edit.php`.
		 *
		 * @package s2Member\User_Securities
		 * @since 3.5
		 *
		 * @attaches-to ``add_filter('enable_edit_any_user_configuration');``
		 *
		 * @param bool $allow Expects boolean value passed through by the Filter.
		 *
		 * @return bool True if the current User is allowed to edit any User, else existing value.
		 */
		public static function ms_allow_edits($allow = FALSE)
		{
			global $user_id; // Available inside `/wp-admin/user-edit.php`.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_ms_allow_edits', get_defined_vars());
			unset($__refs, $__v);

			if(is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm())
				if(is_super_admin() || (current_user_can('administrator') && $user_id && is_user_member_of_blog($user_id)))
					$allow = TRUE; // Yes, allow Administrators to edit User Profiles.

			return apply_filters('ws_plugin__s2member_ms_allow_edits', $allow, get_defined_vars());
		}

		/**
		 * Hides Password fields for Demo Users; and deals with Password fields on Multisite Blog Farms.
		 *
		 * Demo accounts *( where the Username MUST be 'demo' )*, will NOT be allowed to change their Password.
		 * Any other restrictions you need to impose must be done through custom programming, using s2Member's Conditionals.
		 *   See `s2Member → API Scripting`.
		 *
		 * @package s2Member\User_Securities
		 * @since 3.5
		 *
		 * @attaches-to ``add_filter('show_password_fields');``
		 *
		 * @param bool    $show Expects boolean value passed through by the Filter.
		 * @param WP_User $user Expects a `WP_User` object passed through by the Filter.
		 *    If this is NOT passed (it isn't always), then we assume the current user.
		 *
		 * @return bool False if the Password is locked for this User, else existing value.
		 */
		public static function hide_password_fields($show, $user = NULL)
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_hide_password_fields', get_defined_vars());
			unset($__refs, $__v);

			if($show && is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm())
				if(!is_super_admin() && is_object($user) && !empty($user->ID) && is_object($current_user = wp_get_current_user()) && !empty($current_user->ID))
					if($user->ID !== $current_user->ID)
						$show = FALSE;

			if($show && is_object($user) && !empty($user->ID) && $user->user_login === 'demo')
				$show = FALSE; // Lock Password on Demos.

			return apply_filters('ws_plugin__s2member_hide_password_fields', $show, get_defined_vars());
		}

		/**
		 * Acquires password minimum length.
		 *
		 * @package s2Member\User_Securities
		 * @since 150717
		 *
		 * @param string $password The password to score.
		 *
		 * @return integer Password minimum length.
		 */
		public static function min_password_length()
		{
			$min = (integer)$GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_password_min_length'];
			return max(6, (integer)apply_filters('ws_plugin__s2member_min_password_length', $min > 0 ? $min : 0));
		}

		/**
		 * Acquires minimum password strength code.
		 *
		 * @package s2Member\User_Securities
		 * @since 150717
		 *
		 * @return string Minimum password strength code.
		 */
		public static function min_password_strength_code()
		{
			$code = $GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_password_min_strength'];
			return apply_filters('ws_plugin__s2member_min_password_strength_code', trim($code));
		}

		/**
		 * Acquires minimum password strength label.
		 *
		 * @package s2Member\User_Securities
		 * @since 150717
		 *
		 * @return string Minimum password strength label.
		 */
		public static function min_password_strength_label()
		{
			switch(self::min_password_strength_code())
			{
				case 'weak': return _x('`weak`, `good`, or `strong`', 's2member-front', 's2member');
				case 'good': return _x('`good` or `strong` (i.e., use numbers, letters, and mixed caSe)', 's2member-front', 's2member');
				case 'strong': return _x('`strong` (i.e., use numbers, letters, mixed caSe, and punctuation)', 's2member-front', 's2member');
			}
			return ''; // Default behavior.
		}

		/**
		 * Acquires minimum password strength score.
		 *
		 * @package s2Member\User_Securities
		 * @since 150717
		 *
		 * @return integer Minimum password strength score.
		 */
		public static function min_password_strength_score()
		{
			$score = 0; // Default behavior.

			switch(self::min_password_strength_code())
				{
					case 'n/a': $score = 0; break;
					case 'weak': $score = 10; break;
					case 'good': $score = 30; break;
					case 'strong': $score = 50; break;
				}
			return apply_filters('ws_plugin__s2member_min_password_strength_score', $score > 0 ? $score : 0);
		}

		/**
		 * Acquires password strength score.
		 *
		 * @package s2Member\User_Securities
		 * @since 150717
		 *
		 * @param string $password The password to score.
		 *
		 * @return integer Password strength score.
		 */
		public static function password_strength_score($password)
		{
			$score = 0; // Initialize score.

			if(strlen($password) < 1)
				return $score;

		 	else if(strlen($password) < self::min_password_length())
				return $score;

			if(preg_match('/[0-9]/', $password))
				$score += 10;

			if(preg_match('/[a-z]/', $password))
				$score += 10;

			if(preg_match('/[A-Z]/', $password))
				$score += 10;

			if(preg_match('/[^0-9a-zA-Z]/', $password))
				$score += $score === 30 ? 20 : 10;

			return apply_filters('ws_plugin__s2member_password_strength_score', $score > 0 ? $score : 0);
		}
	}
}
