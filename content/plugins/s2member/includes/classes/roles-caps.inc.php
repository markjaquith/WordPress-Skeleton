<?php
/**
 * Roles/Capabilities.
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
 * @package s2Member\Roles_Caps
 * @since 110524RC
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_roles_caps'))
{
	/**
	 * Roles/Capabilities.
	 *
	 * @package s2Member\Roles_Caps
	 * @since 110524RC
	 */
	class c_ws_plugin__s2member_roles_caps
	{
		/**
		 * Configures Roles/Capabilities.
		 *
		 * @package s2Member\Roles_Caps
		 * @since 110524RC
		 *
		 * @return null
		 */
		public static function config_roles()
		{
			do_action('ws_plugin__s2member_before_config_roles', get_defined_vars());

			if(!apply_filters('ws_plugin__s2member_lock_roles_caps', FALSE))
			{
				c_ws_plugin__s2member_roles_caps::unlink_roles();

				if(function_exists('bbp_get_dynamic_roles') && function_exists('bbp_get_caps_for_role') && function_exists('bbp_get_participant_role') /* bbPress v2.2+ integration. */)
				{
					foreach(bbp_get_caps_for_role(bbp_get_participant_role()) as $bbp_participant_cap => $bbp_participant_cap_is)
						if($bbp_participant_cap_is /* Is this capability enabled? */)
							$bbp_participant_caps[$bbp_participant_cap] = TRUE;
				}
				else if(function_exists('bbp_get_caps_for_role') && function_exists('bbp_get_participant_role') /* bbPress < v2.2 integration. */)
				{
					foreach(bbp_get_caps_for_role(bbp_get_participant_role()) as $bbp_participant_cap)
						$bbp_participant_caps[$bbp_participant_cap] = TRUE;
				}
				if(0 === 0) // Subscriber Role is required by s2Member.
				{
					$caps = array('read' => TRUE, 'level_0' => TRUE);
					$caps = array_merge($caps, array('access_s2member_level0' => TRUE));
					$caps = (!empty($bbp_participant_caps)) ? array_merge($caps, $bbp_participant_caps) : $caps;

					if(!($role = get_role('subscriber')))
					{
						add_role('subscriber', 'Subscriber');
						$role = get_role('subscriber');
					}
					foreach(array_keys($caps) as $cap)
						$role->add_cap($cap);
				}
				for($n = 1; $n <= $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n++)
				{
					for($i = 0, $caps = array('read' => TRUE, 'level_0' => TRUE); $i <= $n; $i++)
						$caps = array_merge($caps, array('access_s2member_level'.$i => TRUE));
					$caps = (!empty($bbp_participant_caps)) ? array_merge($caps, $bbp_participant_caps) : $caps;

					if(!($role = get_role('s2member_level'.$n)))
					{
						add_role('s2member_level'.$n, 's2Member Level '.$n);
						$role = get_role('s2member_level'.$n);
					}
					foreach(array_keys($caps) as $cap)
						$role->add_cap($cap);
				}
				$full_access_roles = array('administrator', 'editor', 'author', 'contributor');

				if(!function_exists('bbp_get_dynamic_roles') && function_exists('bbp_get_caps_for_role') && function_exists('bbp_get_moderator_role') /* bbPress < v2.2 integration. */)
					$full_access_roles = array_merge($full_access_roles, (array)bbp_get_moderator_role());

				foreach($full_access_roles as $role)
				{
					if(($role = get_role($role)))
						for($n = 0; $n <= $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n++)
							$role->add_cap('access_s2member_level'.$n);
				}
			}
			do_action('ws_plugin__s2member_after_config_roles', get_defined_vars());
		}

		/**
		 * Adds support for bbPress v2.2+ dynamic roles.
		 *
		 * @package s2Member\Roles_Caps
		 * @since 112512
		 *
		 * @param array  $caps Array of BBP capabilities.
		 * @param string $role Role ID in WordPress.
		 *
		 * @attaches-to ``add_filter('bbp_get_caps_for_role');``
		 *
		 * @return array
		 */
		public static function bbp_dynamic_role_caps($caps = array(), $role = '')
		{
			if(function_exists('bbp_get_dynamic_roles') && function_exists('bbp_get_blocked_role') && $role !== bbp_get_blocked_role())
				if(!did_action('bbp_deactivation') && !did_action('bbp_uninstall') && function_exists('bbp_get_keymaster_role') && function_exists('bbp_get_moderator_role'))
				{
					$caps = array_merge($caps, array('read' => TRUE, 'level_0' => TRUE));
					$caps = array_merge($caps, array('access_s2member_level0' => TRUE));

					if(in_array($role, array(bbp_get_keymaster_role(), bbp_get_moderator_role()), TRUE))
					{
						for($n = 0; $n <= $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n++)
							$caps = array_merge($caps, array('access_s2member_level'.$n => TRUE));
					}
				}
			return $caps;
		}

		/**
		 * Unlinks Roles/Capabilities.
		 *
		 * @package s2Member\Roles_Caps
		 * @since 110524RC
		 *
		 * @return null
		 */
		public static function unlink_roles()
		{
			do_action('ws_plugin__s2member_before_unlink_roles', get_defined_vars());

			if(!apply_filters('ws_plugin__s2member_lock_roles_caps', FALSE))
			{
				if(($role = get_role('subscriber')))
					$role->remove_cap('access_s2member_level0');

				for($n = 1; $n <= $GLOBALS['WS_PLUGIN__']['s2member']['c']['max_levels']; $n++)
					remove_role('s2member_level'.$n);

				$full_access_roles = array('administrator', 'editor', 'author', 'contributor');

				if(!function_exists('bbp_get_dynamic_roles') && function_exists('bbp_get_caps_for_role') && function_exists('bbp_get_moderator_role'))
					$full_access_roles = array_merge($full_access_roles, (array)bbp_get_moderator_role());

				foreach($full_access_roles as $role)
				{
					if(($role = get_role($role)))
						for($n = 0; $n <= $GLOBALS['WS_PLUGIN__']['s2member']['c']['max_levels']; $n++)
							$role->remove_cap('access_s2member_level'.$n);
				}
			}
			do_action('ws_plugin__s2member_after_unlink_roles', get_defined_vars());
		}

		/**
		 * Updates Roles/Capabilities via AJAX.
		 *
		 * @package s2Member\Roles_Caps
		 * @since 110524RC
		 *
		 * @attaches-to ``add_action('wp_ajax_ws_plugin__s2member_update_roles_via_ajax');``
		 */
		public static function update_roles_via_ajax()
		{
			do_action('ws_plugin__s2member_before_update_roles_via_ajax', get_defined_vars());

			status_header(200); // Send a 200 OK status header.
			header('Content-Type: text/plain; charset=UTF-8'); // Content-Type with UTF-8.
			while(@ob_end_clean()) ; // Clean any existing output buffers.

			if(current_user_can('create_users')) // Check privileges. Ability to create Users?

				if(!empty($_POST['ws_plugin__s2member_update_roles_via_ajax']))
					if(($nonce = $_POST['ws_plugin__s2member_update_roles_via_ajax']))
						if(wp_verify_nonce($nonce, 'ws-plugin--s2member-update-roles-via-ajax'))

							if(!apply_filters('ws_plugin__s2member_lock_roles_caps', FALSE))
							{
								c_ws_plugin__s2member_roles_caps::config_roles();
								$success = TRUE; // Roles updated.
							}
							else // Else flag as having been locked here.
								$locked = TRUE;

			exit(apply_filters('ws_plugin__s2member_update_roles_via_ajax', // Also handle ``$locked`` here.
				((isset($success) && $success) ? '1' : ((isset($locked) && $locked) ? 'l' : '0')), get_defined_vars()));
		}
	}
}