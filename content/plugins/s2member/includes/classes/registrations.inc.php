<?php
/**
 * Registration handlers.
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
 * @package s2Member\Registrations
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_registrations'))
{
	/**
	 * Registration handlers.
	 *
	 * @package s2Member\Registrations
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_registrations
	{
		/**
		 * Custom password; else randomly generated password.
		 *
		 * @package s2Member\Registrations
		 * @since 150826
		 *
		 * @param string $password Expects plain text pass.
		 *
		 * @return string Password.
		 */
		public static function maybe_custom_pass(&$password)
		{
			$GLOBALS['ws_plugin__s2member_custom_password'] = ''; // Initialize.
			$password                                       = trim(stripslashes((string)$password));

			if($password && $GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_password'])
				{
					$GLOBALS['ws_plugin__s2member_custom_password']         = $password;
					return ($GLOBALS['ws_plugin__s2member_plain_text_pass'] = $GLOBALS['ws_plugin__s2member_custom_password']);
				}
			if($password && c_ws_plugin__s2member_utils_conds::pro_is_installed() && c_ws_plugin__s2member_pro_remote_ops::is_remote_op('create_user'))
				{
					$GLOBALS['ws_plugin__s2member_custom_password']         = $password;
					return ($GLOBALS['ws_plugin__s2member_plain_text_pass'] = $GLOBALS['ws_plugin__s2member_custom_password']);
				}
			return ($GLOBALS['ws_plugin__s2member_plain_text_pass'] = wp_generate_password());
		}

		/**
		 * Filters WordPress-generated passwords.
		 *
		 * This can ONLY be fired through `/wp-login.php` on the front-side.
		 *   Or through `/register` via BuddyPress.
		 *
		 * @package s2Member\Registrations
		 * @since 3.5
		 *
		 * @attaches-to ``add_filter('random_password');``
		 *
		 * @param string $password Expects a plain text password passed through by the filter.
		 *
		 * @return string Plain text password value.
		 */
		public static function generate_password($password = '')
		{
			static $did_generate_password = false; // Once only.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_generate_password', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			$ci = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';

			if(!$did_generate_password && !is_admin() && (preg_match('/\/wp-login\.php/'.$ci, $_SERVER['REQUEST_URI']) || (c_ws_plugin__s2member_utils_conds::bp_is_installed() && bp_is_register_page())))
				{
					$GLOBALS['ws_plugin__s2member_custom_wp_login_bp_password'] = false; // Initialize.

					if(!empty($_POST['ws_plugin__s2member_custom_reg_field_user_pass1']) && preg_match('/\/wp-login\.php/'.$ci, $_SERVER['REQUEST_URI']))
						{
							$password = self::maybe_custom_pass($_POST['ws_plugin__s2member_custom_reg_field_user_pass1']);
							$GLOBALS['ws_plugin__s2member_custom_wp_login_bp_password'] = !empty($GLOBALS['ws_plugin__s2member_custom_password']) && $password === $GLOBALS['ws_plugin__s2member_custom_password'];
						}
					$GLOBALS['ws_plugin__s2member_plain_text_wp_login_bp_pass'] = $password; // Plain-text password.
					$GLOBALS['ws_plugin__s2member_plain_text_pass']             = $password; // Plain-text password.

					remove_filter('random_password', 'c_ws_plugin__s2member_registrations::generate_password');
					$did_generate_password = true; // One time only.
				}
			return apply_filters('ws_plugin__s2member_generate_password', $password, get_defined_vars());
		}

		/**
		 * Intersects with ``register_new_user()`` in the WordPress core.
		 *
		 * This function Filters registration errors inside `/wp-login.php` via ``register_new_user()``.
		 *
		 * This can ONLY be fired through `/wp-login.php` on the front-side.
		 *
		 * @package s2Member\Registrations
		 * @since 140518
		 *
		 * @attaches-to ``add_filter('registration_errors');``
		 *
		 * @param WP_Error $errors Expects a `WP_Error` object passed in by the Filter.
		 * @param string   $user_login Expects the User's Username, passed in by the Filter.
		 * @param string   $user_email Expects the User's Email Address, passed in by the Filter.
		 *
		 * @return WP_Error A `WP_Error` object instance.
		 */
		public static function custom_registration_field_errors($errors = NULL, $user_login = '', $user_email = '')
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_custom_registration_field_errors', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			$ci = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';

			if(!is_admin() && preg_match('/\/wp-login\.php/'.$ci, $_SERVER['REQUEST_URI']))
				if(is_wp_error($errors) && !empty($_POST) && is_array($_POST))
				{
					foreach(c_ws_plugin__s2member_utils_strings::trim_deep(stripslashes_deep($_POST)) as $_key => $_value)
						if(strpos($_key, 'ws_plugin__s2member_custom_reg_field_') === 0)
							$input[str_replace('ws_plugin__s2member_custom_reg_field_', '', $_key)] = $_value;

					$fields_to_validate = c_ws_plugin__s2member_custom_reg_fields::custom_fields_configured_at_level('auto-detection', 'registration', TRUE);
					$validation_errors  = c_ws_plugin__s2member_custom_reg_fields::validation_errors(!empty($input) ? $input : array(), $fields_to_validate);

					if($validation_errors) foreach($validation_errors as $_field_var => $_error)
						$errors->add('custom_reg_field_'.$_field_var, $_error);
					unset($_field_var, $_error);
				}
			return apply_filters('ws_plugin__s2member_custom_registration_field_errors', $errors, get_defined_vars());
		}

		/**
		 * Intersects with ``bp_core_screen_signup()`` in the BuddyPress core.
		 *
		 * This can ONLY be fired through `/register` via BuddyPress.
		 *
		 * @package s2Member\Registrations
		 * @since 140518
		 *
		 * @attaches-to ``add_action('bp_signup_validate');``
		 */
		public static function custom_registration_field_errors_4bp()
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_custom_registration_field_errors_4bp', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			if(!is_admin() && c_ws_plugin__s2member_utils_conds::bp_is_installed() && bp_is_register_page())
				if(in_array('registration', $GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields_4bp']))
					if(apply_filters('ws_plugin__s2member_custom_registration_fields_4bp_display', TRUE, get_defined_vars()))
						if(!empty($GLOBALS['bp']->signup) && is_object($GLOBALS['bp']->signup) && !empty($_POST) && is_array($_POST))
						{
							foreach(c_ws_plugin__s2member_utils_strings::trim_deep(stripslashes_deep($_POST)) as $_key => $_value)
								if(strpos($_key, 'ws_plugin__s2member_custom_reg_field_') === 0)
									$input[str_replace('ws_plugin__s2member_custom_reg_field_', '', $_key)] = $_value;

							$fields_to_validate = c_ws_plugin__s2member_custom_reg_fields::custom_fields_configured_at_level('auto-detection', 'registration', TRUE);
							$validation_errors  = c_ws_plugin__s2member_custom_reg_fields::validation_errors(!empty($input) ? $input : array(), $fields_to_validate);

							if($validation_errors) foreach($validation_errors as $_field_var => $_error)
								$GLOBALS['bp']->signup->errors['custom_reg_field_'.$_field_var] = $_error;
							unset($_field_var, $_error);
						}
		}

		/**
		 * Filters Multisite User validation.
		 *
		 * This can ONLY be fired through `/wp-signup.php` on the front-side.
		 *   Or through `/register` via BuddyPress.
		 *
		 * @package s2Member\Registrations
		 * @since 3.5
		 *
		 * @attaches-to ``add_filter('wpmu_validate_user_signup');``
		 *
		 * @param array $result Expects a ``$result`` array to be passed through by the Filter.
		 *
		 * @return array The Filtered ``$result`` array. Possibly containing errors introduced by s2Member.
		 */
		public static function ms_validate_user_signup($result = array())
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_ms_validate_user_signup', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			$ci = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';

			if(is_multisite()) // This event should ONLY be processed with Multisite Networking.
				if(!is_admin() && isset ($result['user_name'], $result['user_email'], $result['errors']) && ((preg_match('/\/wp-signup\.php/'.$ci, $_SERVER['REQUEST_URI']) && !empty($_POST['stage']) && preg_match('/^validate-(user|blog)-signup$/', (string)$_POST['stage'])) || (c_ws_plugin__s2member_utils_conds::bp_is_installed() && bp_is_register_page())))
				{
					$errors =& $result['errors'];
					/** @var $errors WP_Error Reference for IDEs. Needed in the routines below. */

					if(in_array($errors->get_error_code(), array('user_name', 'user_email', 'user_email_used')))
						if(c_ws_plugin__s2member_utils_users::ms_user_login_email_exists_but_not_on_blog($result['user_name'], $result['user_email']))
						{
							unset($errors->errors['user_name'], $errors->errors['user_email'], $errors->errors['user_email_used']);
							unset($errors->error_data['user_name'], $errors->error_data['user_email'], $errors->error_data['user_email_used']);
						}
					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action('ws_plugin__s2member_during_ms_validate_user_signup', get_defined_vars());
					unset($__refs, $__v); // Housekeeping.
				}
			return apply_filters('ws_plugin__s2member_ms_validate_user_signup', $result, get_defined_vars());
		}

		/**
		 * Adds hidden fields for ``$_POST`` vars on signup.
		 *
		 * This can ONLY be fired through `/wp-signup.php` on the front-side.
		 *   Or through `/register` via BuddyPress.
		 *
		 * @package s2Member\Registrations
		 * @since 3.5
		 *
		 * @attaches-to ``add_filter('signup_hidden_fields');``
		 */
		public static function ms_process_signup_hidden_fields()
		{
			do_action('ws_plugin__s2member_before_ms_process_signup_hidden_fields', get_defined_vars());

			$ci = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';

			if(is_multisite()) // This event should ONLY be processed with Multisite Networking.
				if(!is_admin() && !empty($_POST) && is_array($_POST) && ((preg_match('/\/wp-signup\.php/'.$ci, $_SERVER['REQUEST_URI']) && !empty($_POST['stage']) && preg_match('/^validate-(user|blog)-signup$/', (string)$_POST['stage'])) || (c_ws_plugin__s2member_utils_conds::bp_is_installed() && bp_is_register_page())))
				{
					foreach(c_ws_plugin__s2member_utils_strings::trim_deep(stripslashes_deep($_POST)) as $key => $value)
						if(preg_match('/^ws_plugin__s2member_(custom_reg_field|user_new)_/', $key))
							if($key = preg_replace('/_user_new_/', '_custom_reg_field_', $key))
								echo '<input type="hidden" name="'.esc_attr($key).'" value="'.esc_attr(maybe_serialize($value)).'" />'."\n";

					do_action('ws_plugin__s2member_during_ms_process_signup_hidden_fields', get_defined_vars());
				}
			do_action('ws_plugin__s2member_after_ms_process_signup_hidden_fields', get_defined_vars());
		}

		/**
		 * Adds Customs Fields to ``$meta`` on signup.
		 *
		 * This can ONLY be fired through `/wp-signup.php` on the front-side.
		 *   Or possibly through `/user-new.php` in the admin.
		 *   Or through `/register` via BuddyPress.
		 *
		 * @package s2Member\Registrations
		 * @since 3.5
		 *
		 * @attaches-to ``add_filter('add_signup_meta');``
		 * @attaches-to ``add_filter('bp_signup_usermeta');``
		 *
		 * @param array $meta Expects an array of meta-data to be passed in by the Filter.
		 *
		 * @return array Full ``$meta`` array with s2Member Custom Fields included.
		 */
		public static function ms_process_signup_meta($meta = array())
		{
			global /* Multisite Networking. */
			$current_site, $current_blog;
			global $pagenow; // Need this to detect the current admin page.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_ms_process_signup_meta', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			$ci = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';

			if(is_multisite()) // This event should ONLY be processed with Multisite Networking.
				if(!empty($_POST) && is_array($_POST) && ((is_blog_admin() && $pagenow === 'user-new.php') || (!is_admin() && ((preg_match('/\/wp-signup\.php/'.$ci, $_SERVER['REQUEST_URI']) && !empty($_POST['stage']) && preg_match('/^validate-(user|blog)-signup$/', (string)$_POST['stage'])) || (c_ws_plugin__s2member_utils_conds::bp_is_installed() && bp_is_register_page())))))
				{
					c_ws_plugin__s2member_email_configs::email_config(); // Configures `From:` header used in notifications.

					$meta['add_to_blog'] = (empty($meta['add_to_blog'])) ? $current_blog->blog_id : $meta['add_to_blog'];
					$meta['new_role']    = (empty($meta['new_role'])) ? get_option('default_role') : $meta['new_role'];

					foreach(c_ws_plugin__s2member_utils_strings::trim_deep(stripslashes_deep($_POST)) as $key => $value)
						if(preg_match('/^ws_plugin__s2member_(custom_reg_field|user_new)_/', $key))
							if($key = preg_replace('/_user_new_/', '_custom_reg_field_', $key))
								$meta['s2member_ms_signup_meta'][$key] = maybe_unserialize($value);
				}
			return apply_filters('ws_plugin__s2member_ms_process_signup_meta', $meta, get_defined_vars());
		}

		/**
		 * Intersects with ``wpmu_activate_signup()`` through s2Member's Multisite Networking patch.
		 *
		 * This function should return the same array that `wpmu_activate_signup()` returns; with the assumption that ``$user_already_exists``.
		 *   Which is exactly where this function intersects inside the `/wp-includes/ms-functions.php`.
		 *
		 * This can ONLY be fired through `/wp-activate.php` on the front-side.
		 *   Or through `/activate` via BuddyPress.
		 *
		 * @package s2Member\Registrations
		 * @since 3.5
		 *
		 * @attaches-to ``add_filter('_wpmu_activate_existing_error_');``
		 *
		 * @param WP_Error $_error Expects a `WP_Error` object to be passed through by the Filter.
		 * @param array    $vars Expects the defined variables from the scope of the calling Filter.
		 *
		 * @return WP_Error|array If unable to add an existing User, the original ``$_error`` obj is returned.
		 *   Otherwise we return an array of User details for continued processing by the caller.
		 */
		public static function ms_activate_existing_user($_error = NULL, $vars = array())
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_ms_activate_existing_user', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			extract($vars); // Extract all variables from ``wpmu_activate_signup()`` function.

			$ci = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';

			if(is_multisite()) // This event should ONLY be processed with Multisite Networking.
				if(!is_admin() && ((preg_match('/\/wp-activate\.php/'.$ci, $_SERVER['REQUEST_URI'])) || (c_ws_plugin__s2member_utils_conds::bp_is_installed() && bp_is_activation_page())))
				{
					if(!empty($user_id) && !empty($user_login) && !empty($user_email) && !empty($password) && !empty($meta) && !empty($meta['add_to_blog']) && !empty($meta['new_role']))
						if(!empty($user_already_exists) && c_ws_plugin__s2member_utils_users::ms_user_login_email_exists_but_not_on_blog($user_login, $user_email, $meta['add_to_blog']))
						{
							add_user_to_blog($meta['add_to_blog'], $user_id, $meta['new_role']); // Add this User to the specified Blog.
							wp_update_user(wp_slash(array('ID' => $user_id, 'user_pass' => $password))); // Update Password so it's the same as in the following msg.
							wpmu_welcome_user_notification($user_id, $password, $meta); // Send welcome letter via email just like ``wpmu_activate_signup()`` does.

							do_action('wpmu_activate_user', $user_id, $password, $meta); // Process Hook that would have been fired inside ``wpmu_activate_signup()``.

							return apply_filters('ws_plugin__s2member_ms_activate_existing_user', array('user_id' => $user_id, 'password' => $password, 'meta' => $meta), get_defined_vars());
						}
				}
			return apply_filters('ws_plugin__s2member_ms_activate_existing_user', $_error, get_defined_vars()); // Else, return the standardized error.
		}

		/**
		 * Configures new Users on a Multisite Network installation.
		 *
		 * This can ONLY be fired in the admin via `/user-new.php`.
		 *   Or also during an actual activation; through `/wp-activate.php`.
		 *   Or also during an actual activation; through `/activate` via BuddyPress.
		 *
		 * @package s2Member\Registrations
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('wpmu_activate_user');``
		 *
		 * @param int|string $user_id A numeric WordPress User ID.
		 * @param string     $password Plain text Password should be passed through by the Action Hook.
		 * @param array      $meta Expects an array of ``$meta`` details, passed through by the Action Hook.
		 */
		public static function configure_user_on_ms_user_activation($user_id = '', $password = '', $meta = array())
		{
			global $pagenow; // Detect the current admin page.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_configure_user_on_ms_user_activation', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			$ci = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';

			if(is_multisite()) // This event should ONLY be processed with Multisite Networking.
				if((is_blog_admin() && $pagenow === 'user-new.php' && isset ($_POST['noconfirmation'])) || (!is_admin() && ((preg_match('/\/wp-activate\.php/'.$ci, $_SERVER['REQUEST_URI'])) || (c_ws_plugin__s2member_utils_conds::bp_is_installed() && bp_is_activation_page()))))
				{
					c_ws_plugin__s2member_registrations::configure_user_registration($user_id, $password, ((isset ($meta['s2member_ms_signup_meta']) && is_array($meta['s2member_ms_signup_meta'])) ? $meta['s2member_ms_signup_meta'] : array()));
					delete_user_meta($user_id, 's2member_ms_signup_meta');
				}
			do_action('ws_plugin__s2member_after_configure_user_on_ms_user_activation', get_defined_vars());
		}

		/**
		 * Configures new Users on a Multisite Network installation.
		 *
		 * This does NOT fire for a Super Admin managing Network Blogs.
		 * Actually it does; BUT it's blocked by the routine below.
		 * A Super Admin should NOT trigger this event.
		 *
		 * This function should ONLY be fired through `/wp-activate.php`.
		 *   Or also through `/activate` via BuddyPress.
		 *
		 * @package s2Member\Registrations
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('wpmu_activate_blog');``
		 *
		 * @param int|string $blog_id A numeric WordPress Blog ID.
		 * @param int|string $user_id A numeric WordPress User ID.
		 * @param string     $password Plain text Password should be passed through by the Action Hook.
		 * @param string     $title The title that a User chose during signup; for their new Blog on the Network.
		 * @param array      $meta Expects an array of ``$meta`` details, passed through by the Action Hook.
		 */
		public static function configure_user_on_ms_blog_activation($blog_id = '', $user_id = '', $password = '', $title = '', $meta = array())
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_configure_user_on_ms_blog_activation', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			$ci = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';

			if(is_multisite()) // This event should ONLY be processed with Multisite Networking.
				if(!is_admin() && ((preg_match('/\/wp-activate\.php/'.$ci, $_SERVER['REQUEST_URI'])) || (c_ws_plugin__s2member_utils_conds::bp_is_installed() && bp_is_activation_page())))
				{
					c_ws_plugin__s2member_registrations::configure_user_registration($user_id, $password, ((isset ($meta['s2member_ms_signup_meta']) && is_array($meta['s2member_ms_signup_meta'])) ? $meta['s2member_ms_signup_meta'] : array()));
					delete_user_meta($user_id, 's2member_ms_signup_meta');
				}
			do_action('ws_plugin__s2member_after_configure_user_on_ms_blog_activation', get_defined_vars());
		}

		/**
		 * Assigns the proper role/ccaps on BP user activation.
		 *
		 * @attaches-to `bp_core_activated_user`
		 *
		 * @param string|integer $user_id Passed in by hook.
		 */
		public static function bp_user_activation($user_id)
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_bp_user_activation', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			if(is_multisite() || !$user_id) return; // Nothing to do.

			$role  = get_user_option('s2member_bp_activation_role', $user_id);
			$ccaps = get_user_option('s2member_bp_activation_ccaps', $user_id);

			delete_user_option($user_id, 's2member_bp_activation_role');
			delete_user_option($user_id, 's2member_bp_activation_ccaps');

			if($role && ($user = new WP_User($user_id)) && $user->ID)
			{
				$user->set_role($role);
				if($ccaps && is_array($ccaps)) foreach($ccaps as $_ccap)
					$user->add_cap('access_s2member_ccap_'.$_ccap);
				unset($_ccap); // Housekeeping.
			}
		}

		/**
		 * Intersects with ``register_new_user()`` through s2Member's Multisite Networking patch.
		 *
		 * This function Filters registration errors inside `/wp-login.php` via ``register_new_user()``.
		 * When an existing Multisite User is registering, this takes over registration processing.
		 *
		 * This can ONLY be fired through `/wp-login.php` on the front-side.
		 *
		 * @package s2Member\Registrations
		 * @since 3.5
		 *
		 * @attaches-to ``add_filter('registration_errors');``
		 *
		 * @param WP_Error $errors Expects a `WP_Error` object passed in by the Filter.
		 * @param string   $user_login Expects the User's Username, passed in by the Filter.
		 * @param string   $user_email Expects the User's Email Address, passed in by the Filter.
		 *
		 * @return WP_Error A `WP_Error` object, or exits script execution after handling registration redirection.
		 */
		public static function ms_register_existing_user($errors = NULL, $user_login = '', $user_email = '')
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_ms_register_existing_user', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			/** @var $ms_errors WP_Error Reference for IDEs. This is needed below. */
			$ci = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';

			if(is_multisite()) // This event should ONLY be processed with Multisite Networking.
				if(!is_admin() && preg_match('/\/wp-login\.php/'.$ci, $_SERVER['REQUEST_URI']))
					if(is_wp_error($errors) && $errors->get_error_codes()) // Errors?
					{
						if(($user_id = c_ws_plugin__s2member_utils_users::ms_user_login_email_exists_but_not_on_blog($user_login, $user_email)))
						{
							foreach($errors->get_error_codes() as $error_code)
								if(!in_array($error_code, array('username_exists', 'email_exists')))
									$other_important_errors_exist[] = $error_code;

							if(empty($other_important_errors_exist)) // Only if/when NO other important errors exist already.
							{
								$user_pass = wp_generate_password(); // Generate password for this user.
								$has_custom_password = !empty($GLOBALS['ws_plugin__s2member_custom_password'])
													&& $user_pass === $GLOBALS['ws_plugin__s2member_custom_password'];
								c_ws_plugin__s2member_registrations::ms_create_existing_user($user_login, $user_email, $user_pass, $user_id);

								update_user_option($user_id, 'default_password_nag', $has_custom_password ? false : true, true);

								if (version_compare(get_bloginfo('version'), '4.3.1', '>='))
									wp_new_user_notification($user_id, null, $has_custom_password ? 'admin' : 'both', $user_pass);
								else if (version_compare(get_bloginfo('version'), '4.3', '>='))
									wp_new_user_notification($user_id, $has_custom_password ? 'admin' : 'both', $user_pass);
								else wp_new_user_notification($user_id, $user_pass);

								$redirect_to = !empty($_REQUEST['redirect_to']) ? trim(stripslashes($_REQUEST['redirect_to'])) : FALSE;
								$redirect_to = $redirect_to ? $redirect_to // Note: the `checkemail` message is translated if using custom passwords.
									: add_query_arg('checkemail', urlencode('registered'), wp_login_url());

								do_action('ws_plugin__s2member_during_ms_register_existing_user', get_defined_vars());

								wp_safe_redirect($redirect_to).exit (); // Safe, like: ``register_new_user()``.
							}
						}
					}
					else if(($ms = wpmu_validate_user_signup($user_login, $user_email)) && isset ($ms['errors']) && is_wp_error($ms_errors = $ms['errors']) && $ms_errors->get_error_code())
						$errors->add($ms_errors->get_error_code(), $ms_errors->get_error_message());

			return apply_filters('ws_plugin__s2member_ms_register_existing_user', $errors, get_defined_vars());
		}

		/**
		 * For Multisite Networks, this function is used to add a User to an existing Blog; and to simulate ``wp_create_user()`` behavior.
		 *
		 * The ``$user_id`` value will be returned by this function, just like ``wp_create_user()`` does.
		 * This function will fire the Hook `user_register`.
		 *
		 * @package s2Member\Registrations
		 * @since 3.5
		 *
		 * @param string     $user_login Expects the User's Username.
		 * @param string     $user_email Expects the User's Email Address.
		 * @param string     $user_pass Expects the User's plain text Password.
		 * @param int|string $user_id Optional. A numeric WordPress User ID.
		 *   If unspecified, a lookup is performed with ``$user_login`` and ``$user_email``.
		 *
		 * @return int|false Returns numeric ``$user_id`` on success, else false on failure.
		 */
		public static function ms_create_existing_user($user_login = '', $user_email = '', $user_pass = '', $user_id = '')
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_ms_create_existing_user', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			if(is_multisite()) // This event should ONLY be processed with Multisite Networking.
			{
				if(($user_id || ($user_id = c_ws_plugin__s2member_utils_users::ms_user_login_email_exists_but_not_on_blog($user_login, $user_email))) && $user_pass)
				{
					$role = get_option('default_role'); // Use default Role.
					add_existing_user_to_blog(array('user_id' => $user_id, 'role' => $role)); // Add User.
					wp_update_user(wp_slash(array('ID' => $user_id, 'user_pass' => $user_pass))); // Update to ``$user_pass``.

					do_action('ws_plugin__s2member_during_ms_create_existing_user', get_defined_vars());
					do_action('user_register', $user_id); // So s2Member knows a User is registering.

					return apply_filters('ws_plugin__s2member_ms_create_existing_user', $user_id, get_defined_vars());
				}
			}
			return apply_filters('ws_plugin__s2member_ms_create_existing_user', FALSE, get_defined_vars());
		}

		/**
		 * Configures all new Users.
		 *
		 * The Hook `user_register` is also fired by calling:
		 * ``c_ws_plugin__s2member_registrations::ms_create_existing_user()`` and/or ``wpmu_create_user()``.
		 *
		 * This function also receives hand-offs from s2Member's handlers for these two Hooks:
		 * `wpmu_activate_user` and `wpmu_activate_blog`.
		 *
		 * @package s2Member\Registrations
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('user_register');``
		 *
		 * @param int|string $user_id A numeric WordPress User ID.
		 * @param string     $password Optional in most cases. A User's plain text Password. If unspecified, attempts are made to collect the plain text Password from other sources.
		 * @param array      $meta Optional in most cases. Defaults to false. An array of meta data for a User/Member.
		 *
		 * @TODO Impossible to delete cookies when fired inside: `/wp-activate.php`?
		 */
		public static function configure_user_registration($user_id = '', $password = '', $meta = array())
		{
			global $wpdb; // Global database object reference.
			global $pagenow; // We need this to detect the current administration page.
			global $current_site, $current_blog; // Adds support for Multisite Networking.
			static $email_config, $processed; // Static vars prevent duplicate processing.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_configure_user_registration', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			// With Multisite Networking, we need this to run on `user_register` ahead of `wpmu_activate_[user|blog]`.
			if(!isset ($email_config) && ($email_config = TRUE)) // Anytime this routine is fired; we configure email.
				c_ws_plugin__s2member_email_configs::email_config(); // Configures `From:` email header.

			$_p  = (isset ($_POST)) ? $_POST : NULL; // Grab global ``$_POST`` array here, if it's possible to do so.
			$rvs = (isset ($GLOBALS['ws_plugin__s2member_registration_vars'])) ? $GLOBALS['ws_plugin__s2member_registration_vars'] : NULL;

			if(!$processed /* Process only once. Safeguard this routine against duplicate processing via plugins (or even WordPress itself). */)

				if(is_array($_p) || is_array($meta) || is_array($rvs) /* We MUST have at least ONE of these three arrays. Any of these will do in most cases. */)

					if(!(is_multisite() && is_blog_admin() && $pagenow === 'user-new.php' && isset ($_p['noconfirmation']) && is_super_admin() && !is_array($meta)))
						if(!(preg_match('/\/wp-activate\.php/', $_SERVER['REQUEST_URI']) && !is_array($meta)) /* If activating; we absolutely MUST have a ``$meta`` array. */)
							if(!(c_ws_plugin__s2member_utils_conds::bp_is_installed() && bp_is_activation_page() && !is_array($meta)) /* If activating; MUST have ``$meta``. */)
								if(!(c_ws_plugin__s2member_utils_conds::pro_is_installed() && c_ws_plugin__s2member_pro_remote_ops::is_remote_op('create_user') && !is_array($rvs)))

									if($user_id && is_object($user = new WP_User ($user_id)) && !empty($user->ID) && ($user_id = $user->ID) && ($processed = TRUE))
									{
										settype($_p, 'array').settype($meta, 'array').settype($rvs, 'array'); // Force arrays here.

										$_p   = c_ws_plugin__s2member_utils_strings::trim_deep(stripslashes_deep($_p));
										$meta = c_ws_plugin__s2member_utils_strings::trim_deep(stripslashes_deep($meta));
										$rvs  = c_ws_plugin__s2member_utils_strings::trim_deep($rvs /* Do NOT strip. */);

										foreach($_p as $_key => $_value) // Scan ``$_p`` vars; adding `custom_reg_field` keys.
											if(preg_match('/^ws_plugin__s2member_user_new_/', $_key)) // Look for keys.
												if($_key = str_replace('_user_new_', '_custom_reg_field_', $_key))
													$_p[$_key] = $_value; // Add each of these key conversions.
										unset ($_key, $_value /* Just a little housekeeping here. */);

										if(!is_admin() && (isset ($_p['ws_plugin__s2member_custom_reg_field_s2member_subscr_gateway']) || isset ($_p['ws_plugin__s2member_custom_reg_field_s2member_subscr_id']) || isset ($_p['ws_plugin__s2member_custom_reg_field_s2member_subscr_baid']) || isset ($_p['ws_plugin__s2member_custom_reg_field_s2member_subscr_cid']) || isset ($_p['ws_plugin__s2member_custom_reg_field_s2member_custom']) || isset ($_p['ws_plugin__s2member_custom_reg_field_s2member_ccaps']) || isset ($_p['ws_plugin__s2member_custom_reg_field_s2member_auto_eot_time']) || isset ($_p['ws_plugin__s2member_custom_reg_field_s2member_notes'])))
											exit (_x('s2Member security violation. You attempted to POST administrative variables that will NOT be trusted in a NON-administrative zone!', 's2member-front', 's2member'));

										$_pmr = array_merge($_p, $meta, $rvs); // Merge all of these arrays together now, in this specific order.
										unset($_p, $meta, $rvs); // These variables can all be unset now; we have them all in the ``$_pmr`` array.

										$custom_reg_display_name = $GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_display_name']; // Can be configured by the site owner.

										if(!is_admin() && (!c_ws_plugin__s2member_utils_conds::pro_is_installed() || !c_ws_plugin__s2member_pro_remote_ops::is_remote_op('create_user')) && ($reg_cookies = c_ws_plugin__s2member_register_access::reg_cookies_ok()) && extract($reg_cookies))
										{ /* This routine could be processed through `/wp-login.php?action=register`, `/wp-activate.php`, or `/activate` via BuddyPress`.
																	This may also be processed through a standard BuddyPress installation, or another plugin calling `user_register`.
																	If processed through `/wp-activate.php`, it could've originated inside the admin—via `/user-new.php`. */
											/**
											 * @var $subscr_gateway string Reference for IDEs.
											 * @var $subscr_id string Reference for IDEs.
											 * @var $custom string Reference for IDEs.
											 * @var $item_number string Reference for IDEs.
											 */
											$processed = 'yes'; // Mark this as yes.

											$current_role = c_ws_plugin__s2member_user_access::user_access_role($user);
											@list ($level, $ccaps, $eotper) = preg_split('/\:/', $item_number, 3);
											$role = 's2member_level'.$level; // Membership Level.

											$email       = $user->user_email;
											$login       = $user->user_login;
											$ip          = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_registration_ip'];
											$ip          = (!$ip) ? $_SERVER['REMOTE_ADDR'] : $ip; // Else use environment variable.
											$subscr_baid = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_subscr_baid'];
											$subscr_cid  = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_subscr_cid'];

											if(!($auto_eot_time = '') && $eotper) // If a specific EOT Period is included.
												$auto_eot_time = c_ws_plugin__s2member_utils_time::auto_eot_time('', '', '', $eotper);

											$notes = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_notes'];

											$opt_in = (!$GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_opt_in']) ? TRUE : FALSE;
											$opt_in = (!$opt_in && !empty($_pmr['ws_plugin__s2member_custom_reg_field_opt_in'])) ? TRUE : $opt_in;

											if(!($fname = $user->first_name))
												if(!empty($_pmr['ws_plugin__s2member_custom_reg_field_first_name']))
													$fname = (string)$_pmr['ws_plugin__s2member_custom_reg_field_first_name'];

											if(!$fname) // Also try BuddyPress.
												if(!empty($_pmr['field_1'])) // BuddyPress?
													$fname = trim(preg_replace('/ (.*)$/', '', (string)$_pmr['field_1']));

											if(!($lname = $user->last_name))
												if(!empty($_pmr['ws_plugin__s2member_custom_reg_field_last_name']))
													$lname = (string)$_pmr['ws_plugin__s2member_custom_reg_field_last_name'];

											if(!$lname) // Also try BuddyPress.
												if(!empty($_pmr['field_1']) && preg_match('/^(.+?) (.+)$/', (string)$_pmr['field_1']))
													$lname = trim(preg_replace('/^(.+?) (.+)$/', '$2', (string)$_pmr['field_1']));

											if($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_names'] && !$fname && $login)
											{
												$fname = trim($login);
												$lname = ''; // Username and empty Last Name.
											}
											$name = trim($fname.' '.$lname); // Both names.

											if(!($pass = $password)) // Try s2Member's generator.
												if(!empty($GLOBALS['ws_plugin__s2member_plain_text_pass']))
													$pass = (string)$GLOBALS['ws_plugin__s2member_plain_text_pass'];

											if(!$pass) // Also try BuddyPress Password.
												if(!empty($_pmr['signup_password'])) // BuddyPress?
													$pass = (string)$_pmr['signup_password'];

											if($pass) // No Password nag. Update this globally.
											{
												(!headers_sent()) ? delete_user_setting('default_password_nag', $user_id) : NULL;
												update_user_option($user_id, 'default_password_nag', FALSE, TRUE);
											}
											update_user_option($user_id, 's2member_registration_ip', $ip);
											update_user_option($user_id, 's2member_auto_eot_time', $auto_eot_time);
											update_user_option($user_id, 's2member_subscr_gateway', $subscr_gateway);
											update_user_option($user_id, 's2member_subscr_id', $subscr_id);
											update_user_option($user_id, 's2member_subscr_baid', $subscr_baid);
											update_user_option($user_id, 's2member_subscr_cid', $subscr_cid);
											update_user_option($user_id, 's2member_custom', $custom);
											update_user_option($user_id, 's2member_notes', $notes);

											if(!$user->first_name && $fname)
												update_user_meta($user_id, 'first_name', $fname);

											if(!$user->last_name && $lname)
												update_user_meta($user_id, 'last_name', $lname);

											if(!$user->display_name || $user->display_name === $user->user_login)
											{
												if($custom_reg_display_name === 'full' && $name)
													wp_update_user(wp_slash(array('ID' => $user_id, 'display_name' => $name)));
												else if($custom_reg_display_name === 'first' && $fname)
													wp_update_user(wp_slash(array('ID' => $user_id, 'display_name' => $fname)));
												else if($custom_reg_display_name === 'last' && $lname)
													wp_update_user(wp_slash(array('ID' => $user_id, 'display_name' => $lname)));
												else if($custom_reg_display_name === 'login' && $login)
													wp_update_user(wp_slash(array('ID' => $user_id, 'display_name' => $login)));
											}
											if(is_multisite()) // Should we handle Main Site permissions and Originating Blog ID#?
											{
												if(!is_main_site() && strtotime($user->user_registered) >= strtotime('-10 seconds'))
													remove_user_from_blog($user_id, $current_site->blog_id); // No Main Site Role.

												if(!get_user_meta($user_id, 's2member_originating_blog', TRUE)) // Recorded yet?
													update_user_meta($user_id, 's2member_originating_blog', $current_blog->blog_id);
											}
											if($current_role !== $role) // Only if NOT the current Role.
												$user->set_role($role); // s2Member.

											if($ccaps && preg_match('/^-all/', str_replace('+', '', $ccaps)))
												foreach($user->allcaps as $cap => $cap_enabled)
													if(preg_match('/^access_s2member_ccap_/', $cap))
														$user->remove_cap($ccap = $cap);

											if($ccaps && preg_replace('/^-all['."\r\n\t".'\s;,]*/', '', str_replace('+', '', $ccaps)))
												foreach(preg_split('/['."\r\n\t".'\s;,]+/', preg_replace('/^-all['."\r\n\t".'\s;,]*/', '', str_replace('+', '', $ccaps))) as $ccap)
													if(strlen($ccap = trim(strtolower(preg_replace('/[^a-z_0-9]/i', '', $ccap)))))
														$user->add_cap('access_s2member_ccap_'.$ccap);

											if(!($fields = array()) && $GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields'])
												foreach(json_decode($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields'], TRUE) as $field)
												{
													$field_var      = preg_replace('/[^a-z0-9]/i', '_', strtolower($field['id']));
													$field_id_class = preg_replace('/_/', '-', $field_var);

													if(isset ($_pmr['ws_plugin__s2member_custom_reg_field_'.$field_var]))
														$fields[$field_var] = $_pmr['ws_plugin__s2member_custom_reg_field_'.$field_var];
												}
											unset($field, $field_var, $field_id_class); // Housekeeping.

											if(!empty($fields)) // Only if NOT empty.
												update_user_option($user_id, 's2member_custom_fields', $fields);

											if($level > 0) // We ONLY process this if they are higher than Level #0.
											{
												$pr_times                 = get_user_option('s2member_paid_registration_times', $user_id);
												$pr_times['level']        = (empty($pr_times['level'])) ? time() : $pr_times['level'];
												$pr_times['level'.$level] = (empty($pr_times['level'.$level])) ? time() : $pr_times['level'.$level];
												update_user_option($user_id, 's2member_paid_registration_times', $pr_times); // Update now.
											}
											if(!is_multisite() && c_ws_plugin__s2member_utils_conds::bp_is_installed() && bp_is_register_page())
											{
												update_user_option($user_id, 's2member_bp_activation_role', $role);
												update_user_option($user_id, 's2member_bp_activation_ccaps', c_ws_plugin__s2member_user_access::user_access_ccaps($user));
											}
											if(($transient = 's2m_'.md5('s2member_transient_ipn_signup_vars_'.$subscr_id)) && is_array($ipn_signup_vars = get_transient($transient)))
											{
												update_user_option($user_id, 's2member_ipn_signup_vars', $ipn_signup_vars); // For future reference.
												delete_transient($transient); // This can be deleted now.
											}
											if(($transient = 's2m_'.md5('s2member_transient_ipn_subscr_payment_'.$subscr_id)) && is_array($subscr_payment = get_transient($transient)) && !empty($subscr_payment['subscr_gateway']))
											{
												$proxy = array('s2member_paypal_proxy' => stripslashes((string)$subscr_payment['subscr_gateway']), 's2member_paypal_proxy_verification' => c_ws_plugin__s2member_paypal_utilities::paypal_proxy_key_gen());
												c_ws_plugin__s2member_utils_urls::remote(home_url('/?s2member_paypal_notify=1'), array_merge(stripslashes_deep($subscr_payment), $proxy), array('timeout' => 20));
												delete_transient($transient); // This can be deleted now.
											}
											if(($transient = 's2m_'.md5('s2member_transient_ipn_subscr_eot_'.$subscr_id)) && is_array($subscr_eot = get_transient($transient)) && !empty($subscr_eot['subscr_gateway']))
											{
												$proxy = array('s2member_paypal_proxy' => stripslashes((string)$subscr_eot['subscr_gateway']), 's2member_paypal_proxy_verification' => c_ws_plugin__s2member_paypal_utilities::paypal_proxy_key_gen());
												c_ws_plugin__s2member_utils_urls::remote(home_url('/?s2member_paypal_notify=1'), array_merge(stripslashes_deep($subscr_eot), $proxy), array('timeout' => 20));
												delete_transient($transient); // This can be deleted now.
											}
											if(!headers_sent()) // Only if headers are NOT yet sent. Here we establish both Signup and Payment Tracking Cookies.
												@setcookie('s2member_tracking', ($s2member_tracking = c_ws_plugin__s2member_utils_encryption::encrypt($subscr_id)), time() + 31556926, COOKIEPATH, COOKIE_DOMAIN).
												 @setcookie('s2member_tracking', $s2member_tracking, time() + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN).
												  ($_COOKIE['s2member_tracking'] = $s2member_tracking);

											foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
											do_action('ws_plugin__s2member_during_configure_user_registration_front_side_paid', get_defined_vars());
											do_action('ws_plugin__s2member_during_configure_user_registration_front_side', get_defined_vars());
											unset($__refs, $__v);
										}
										else if(!is_admin() && (!c_ws_plugin__s2member_utils_conds::pro_is_installed() || !c_ws_plugin__s2member_pro_remote_ops::is_remote_op('create_user')))
										{ /* This routine could be processed through `/wp-login.php?action=register`, `/wp-activate.php`, or `/activate` via BuddyPress`.
																	This may also be processed through a standard BuddyPress installation, or another plugin calling `user_register`.
																	If processed through `/wp-activate.php`, it could've originated inside the admin, via `/user-new.php`. */

											$processed = 'yes'; // Mark this as yes.

											$current_role = c_ws_plugin__s2member_user_access::user_access_role($user);
											$role         = ''; // Initialize ``$role`` to an empty string here, before processing.
											$role         = (!$role && ($level = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_level']) > 0) ? 's2member_level'.$level : $role;
											$role         = (!$role && ($level = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_level']) === '0') ? 'subscriber' : $role;
											$role         = (!$role && $current_role) ? $current_role : $role; // Use existing Role?
											$role         = (!$role) ? get_option('default_role') : $role; // Otherwise default.

											$level = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_level'];
											$level = (!$level && preg_match('/^(administrator|editor|author|contributor)$/i', $role)) ? $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels'] : $level;
											$level = (!$level && preg_match('/^s2member_level[1-9][0-9]*$/i', $role)) ? preg_replace('/^s2member_level/', '', $role) : $level;
											$level = (!$level && preg_match('/^subscriber$/i', $role)) ? '0' : $level;
											$level = (!$level) ? '0' : $level;

											$ccaps = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_ccaps'];

											$email          = $user->user_email;
											$login          = $user->user_login;
											$ip             = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_registration_ip'];
											$ip             = (!$ip) ? $_SERVER['REMOTE_ADDR'] : $ip; // Else use environment variable.
											$custom         = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_custom'];
											$subscr_id      = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_subscr_id'];
											$subscr_baid    = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_subscr_baid'];
											$subscr_cid     = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_subscr_cid'];
											$subscr_gateway = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_subscr_gateway'];

											$auto_eot_time = ($eot = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_auto_eot_time']) ? strtotime($eot) : '';
											$notes         = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_notes'];

											$opt_in = (!$GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_opt_in']) ? TRUE : FALSE;
											$opt_in = (!$opt_in && !empty($_pmr['ws_plugin__s2member_custom_reg_field_opt_in'])) ? TRUE : $opt_in;

											if(!($fname = $user->first_name))
												if(!empty($_pmr['ws_plugin__s2member_custom_reg_field_first_name']))
													$fname = (string)$_pmr['ws_plugin__s2member_custom_reg_field_first_name'];

											if(!$fname) // Also try BuddyPress.
												if(!empty($_pmr['field_1'])) // BuddyPress?
													$fname = trim(preg_replace('/ (.*)$/', '', (string)$_pmr['field_1']));

											if(!($lname = $user->last_name))
												if(!empty($_pmr['ws_plugin__s2member_custom_reg_field_last_name']))
													$lname = (string)$_pmr['ws_plugin__s2member_custom_reg_field_last_name'];

											if(!$lname) // Also try BuddyPress.
												if(!empty($_pmr['field_1']) && preg_match('/^(.+?) (.+)$/', (string)$_pmr['field_1']))
													$lname = trim(preg_replace('/^(.+?) (.+)$/', '$2', (string)$_pmr['field_1']));

											if($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_names'] && !$fname && $login)
											{
												$fname = trim($login);
												$lname = ''; // Username and empty Last Name.
											}
											$name = trim($fname.' '.$lname); // Both names.

											if(!($pass = $password)) // Try s2Member's generator.
												if(!empty($GLOBALS['ws_plugin__s2member_plain_text_pass']))
													$pass = (string)$GLOBALS['ws_plugin__s2member_plain_text_pass'];

											if(!$pass) // Also try BuddyPress Password.
												if(!empty($_pmr['signup_password'])) // BuddyPress?
													$pass = (string)$_pmr['signup_password'];

											if($pass) // No Password nag. Update this globally.
											{
												(!headers_sent()) ? delete_user_setting('default_password_nag', $user_id) : NULL;
												update_user_option($user_id, 'default_password_nag', FALSE, TRUE);
											}
											update_user_option($user_id, 's2member_registration_ip', $ip);
											update_user_option($user_id, 's2member_auto_eot_time', $auto_eot_time);
											update_user_option($user_id, 's2member_subscr_gateway', $subscr_gateway);
											update_user_option($user_id, 's2member_subscr_id', $subscr_id);
											update_user_option($user_id, 's2member_subscr_baid', $subscr_baid);
											update_user_option($user_id, 's2member_subscr_cid', $subscr_cid);
											update_user_option($user_id, 's2member_custom', $custom);
											update_user_option($user_id, 's2member_notes', $notes);

											if(!$user->first_name && $fname)
												update_user_meta($user_id, 'first_name', $fname);

											if(!$user->last_name && $lname)
												update_user_meta($user_id, 'last_name', $lname);

											if(!$user->display_name || $user->display_name === $user->user_login)
											{
												if($custom_reg_display_name === 'full' && $name)
													wp_update_user(wp_slash(array('ID' => $user_id, 'display_name' => $name)));
												else if($custom_reg_display_name === 'first' && $fname)
													wp_update_user(wp_slash(array('ID' => $user_id, 'display_name' => $fname)));
												else if($custom_reg_display_name === 'last' && $lname)
													wp_update_user(wp_slash(array('ID' => $user_id, 'display_name' => $lname)));
												else if($custom_reg_display_name === 'login' && $login)
													wp_update_user(wp_slash(array('ID' => $user_id, 'display_name' => $login)));
											}
											if(is_multisite( /* Should we handle Main Site permissions and Originating Blog ID#? */))
											{
												if(!is_main_site() && strtotime($user->user_registered) >= strtotime('-10 seconds'))
													remove_user_from_blog($user_id, $current_site->blog_id); // No Main Site Role.

												if(!get_user_meta($user_id, 's2member_originating_blog', TRUE)) // Recorded yet?
													update_user_meta($user_id, 's2member_originating_blog', $current_blog->blog_id);
											}
											if($current_role !== $role) // Only if NOT the current Role.
												$user->set_role($role); // s2Member.

											if($ccaps && preg_match('/^-all/', str_replace('+', '', $ccaps)))
												foreach($user->allcaps as $cap => $cap_enabled)
													if(preg_match('/^access_s2member_ccap_/', $cap))
														$user->remove_cap($ccap = $cap);

											if($ccaps && preg_replace('/^-all['."\r\n\t".'\s;,]*/', '', str_replace('+', '', $ccaps)))
												foreach(preg_split('/['."\r\n\t".'\s;,]+/', preg_replace('/^-all['."\r\n\t".'\s;,]*/', '', str_replace('+', '', $ccaps))) as $ccap)
													if(strlen($ccap = trim(strtolower(preg_replace('/[^a-z_0-9]/i', '', $ccap)))))
														$user->add_cap('access_s2member_ccap_'.$ccap);

											if(!($fields = array()) && $GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields'])
												foreach(json_decode($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields'], TRUE) as $field)
												{
													$field_var      = preg_replace('/[^a-z0-9]/i', '_', strtolower($field['id']));
													$field_id_class = preg_replace('/_/', '-', $field_var);

													if(isset ($_pmr['ws_plugin__s2member_custom_reg_field_'.$field_var]))
														$fields[$field_var] = $_pmr['ws_plugin__s2member_custom_reg_field_'.$field_var];
												}
											unset($field, $field_var, $field_id_class); // Housekeeping.

											if(!empty($fields)) // Only if NOT empty.
												update_user_option($user_id, 's2member_custom_fields', $fields);

											if($level > 0) // We ONLY process this if they are higher than Level#0.
											{
												$pr_times                 = get_user_option('s2member_paid_registration_times', $user_id);
												$pr_times['level']        = (empty($pr_times['level'])) ? time() : $pr_times['level'];
												$pr_times['level'.$level] = (empty($pr_times['level'.$level])) ? time() : $pr_times['level'.$level];
												update_user_option($user_id, 's2member_paid_registration_times', $pr_times); // Update now.
											}
											if(!is_multisite() && c_ws_plugin__s2member_utils_conds::bp_is_installed() && bp_is_register_page())
											{
												update_user_option($user_id, 's2member_bp_activation_role', $role);
												update_user_option($user_id, 's2member_bp_activation_ccaps', c_ws_plugin__s2member_user_access::user_access_ccaps($user));
											}
											foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
											do_action('ws_plugin__s2member_during_configure_user_registration_front_side_free', get_defined_vars());
											do_action('ws_plugin__s2member_during_configure_user_registration_front_side', get_defined_vars());
											unset($__refs, $__v);
										}
										else if((is_blog_admin() && $pagenow === 'user-new.php') || (c_ws_plugin__s2member_utils_conds::pro_is_installed() && c_ws_plugin__s2member_pro_remote_ops::is_remote_op('create_user')))
										{ // Can only be processed through `/user-new.php` in the Admin panel, or through Remote Op: `create_user`.

											$processed = 'yes'; // Mark this as yes, to indicate that a routine was processed.

											$current_role = c_ws_plugin__s2member_user_access::user_access_role($user);
											$role         = ''; // Initialize $role to an empty string here, before processing.
											$role         = (!$role && ($level = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_level']) > 0) ? 's2member_level'.$level : $role;
											$role         = (!$role && ($level = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_level']) === '0') ? 'subscriber' : $role;
											$role         = (!$role && $current_role) ? $current_role : $role; // Use existing Role?
											$role         = (!$role) ? get_option('default_role') : $role; // Otherwise default.

											$level = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_level'];
											$level = (!$level && preg_match('/^(administrator|editor|author|contributor)$/i', $role)) ? $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels'] : $level;
											$level = (!$level && preg_match('/^s2member_level[1-9][0-9]*$/i', $role)) ? preg_replace('/^s2member_level/', '', $role) : $level;
											$level = (!$level && preg_match('/^subscriber$/i', $role)) ? '0' : $level;
											$level = (!$level) ? '0' : $level;

											$ccaps = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_ccaps'];

											$email          = $user->user_email;
											$login          = $user->user_login;
											$ip             = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_registration_ip'];
											$custom         = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_custom'];
											$subscr_id      = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_subscr_id'];
											$subscr_baid    = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_subscr_baid'];
											$subscr_cid     = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_subscr_cid'];
											$subscr_gateway = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_subscr_gateway'];

											$auto_eot_time = ($eot = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_auto_eot_time']) ? strtotime($eot) : '';
											$notes         = (string)@$_pmr['ws_plugin__s2member_custom_reg_field_s2member_notes'];

											$opt_in = (!empty($_pmr['ws_plugin__s2member_custom_reg_field_opt_in'])) ? TRUE : FALSE;

											if(!($fname = $user->first_name)) // `Users → Add New`.
												if(!empty($_pmr['ws_plugin__s2member_custom_reg_field_first_name']))
													$fname = (string)$_pmr['ws_plugin__s2member_custom_reg_field_first_name'];

											if(!($lname = $user->last_name)) // `Users → Add New`.
												if(!empty($_pmr['ws_plugin__s2member_custom_reg_field_last_name']))
													$lname = (string)$_pmr['ws_plugin__s2member_custom_reg_field_last_name'];

											if($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_names'] && !$fname && $login)
											{
												$fname = trim($login);
												$lname = ''; // Username and empty Last Name.
											}
											$name = trim($fname.' '.$lname); // Both names.

											if(!($pass = $password)) // Try s2Member's generator.
												if(!empty($GLOBALS['ws_plugin__s2member_plain_text_pass']))
													$pass = (string)$GLOBALS['ws_plugin__s2member_plain_text_pass'];

											if(!$pass) // Also try the `Users → Add New` form.
												if(!empty($_pmr['pass1'])) // Field in `/user-new.php`.
													$pass = (string)$_pmr['pass1'];

											if($pass) // No Password nag. Update this globally.
											{
												(!headers_sent()) ? delete_user_setting('default_password_nag', $user_id) : NULL;
												update_user_option($user_id, 'default_password_nag', FALSE, TRUE);
											}
											update_user_option($user_id, 's2member_registration_ip', $ip);
											update_user_option($user_id, 's2member_auto_eot_time', $auto_eot_time);
											update_user_option($user_id, 's2member_subscr_gateway', $subscr_gateway);
											update_user_option($user_id, 's2member_subscr_id', $subscr_id);
											update_user_option($user_id, 's2member_subscr_baid', $subscr_baid);
											update_user_option($user_id, 's2member_subscr_cid', $subscr_cid);
											update_user_option($user_id, 's2member_custom', $custom);
											update_user_option($user_id, 's2member_notes', $notes);

											if(!$user->first_name && $fname)
												update_user_meta($user_id, 'first_name', $fname);

											if(!$user->last_name && $lname)
												update_user_meta($user_id, 'last_name', $lname);

											if(!$user->display_name || $user->display_name === $user->user_login)
											{
												if($custom_reg_display_name === 'full' && $name)
													wp_update_user(wp_slash(array('ID' => $user_id, 'display_name' => $name)));
												else if($custom_reg_display_name === 'first' && $fname)
													wp_update_user(wp_slash(array('ID' => $user_id, 'display_name' => $fname)));
												else if($custom_reg_display_name === 'last' && $lname)
													wp_update_user(wp_slash(array('ID' => $user_id, 'display_name' => $lname)));
												else if($custom_reg_display_name === 'login' && $login)
													wp_update_user(wp_slash(array('ID' => $user_id, 'display_name' => $login)));
											}
											if(is_multisite()) // Should we handle Main Site permissions and Originating Blog ID#?
											{
												if(!is_main_site() && strtotime($user->user_registered) >= strtotime('-10 seconds'))
													remove_user_from_blog($user_id, $current_site->blog_id); // No Main Site Role.

												if(!get_user_meta($user_id, 's2member_originating_blog', TRUE)) // Recorded yet?
													update_user_meta($user_id, 's2member_originating_blog', $current_blog->blog_id);
											}
											if($current_role !== $role) // Only if NOT the current Role.
												$user->set_role($role); // s2Member.

											if($ccaps && preg_match('/^-all/', str_replace('+', '', $ccaps)))
												foreach($user->allcaps as $cap => $cap_enabled)
													if(preg_match('/^access_s2member_ccap_/', $cap))
														$user->remove_cap($ccap = $cap);

											if($ccaps && preg_replace('/^-all['."\r\n\t".'\s;,]*/', '', str_replace('+', '', $ccaps)))
												foreach(preg_split('/['."\r\n\t".'\s;,]+/', preg_replace('/^-all['."\r\n\t".'\s;,]*/', '', str_replace('+', '', $ccaps))) as $ccap)
													if(strlen($ccap = trim(strtolower(preg_replace('/[^a-z_0-9]/i', '', $ccap)))))
														$user->add_cap('access_s2member_ccap_'.$ccap);

											if(!($fields = array()) && $GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields'])
												foreach(json_decode($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields'], TRUE) as $field)
												{
													$field_var      = preg_replace('/[^a-z0-9]/i', '_', strtolower($field['id']));
													$field_id_class = preg_replace('/_/', '-', $field_var);

													if(isset ($_pmr['ws_plugin__s2member_custom_reg_field_'.$field_var]))
														$fields[$field_var] = $_pmr['ws_plugin__s2member_custom_reg_field_'.$field_var];
												}
											unset($field, $field_var, $field_id_class); // Housekeeping.

											if(!empty($fields)) // Only if NOT empty.
												update_user_option($user_id, 's2member_custom_fields', $fields);

											if($level > 0) // We ONLY process this if they are higher than Level#0.
											{
												$pr_times                 = get_user_option('s2member_paid_registration_times', $user_id);
												$pr_times['level']        = (empty($pr_times['level'])) ? time() : $pr_times['level'];
												$pr_times['level'.$level] = (empty($pr_times['level'.$level])) ? time() : $pr_times['level'.$level];
												update_user_option($user_id, 's2member_paid_registration_times', $pr_times); // Update now.
											}
											foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
											do_action('ws_plugin__s2member_during_configure_user_registration_admin_side', get_defined_vars());
											unset($__refs, $__v);
										}
										if($processed === 'yes') // If registration was processed by one of the routines above.
										{
											/**
											 * If processed, all of these will have been defined by now.
											 *
											 * @var $role string Reference for IDEs.
											 * @var $level string Reference for IDEs.
											 * @var $ccaps string Reference for IDEs.
											 * @var $auto_eot_time string|integer Reference for IDEs.
											 * @var $fname string Reference for IDEs.
											 * @var $lname string Reference for IDEs.
											 * @var $name string Reference for IDEs.
											 * @var $email string Reference for IDEs.
											 * @var $login string Reference for IDEs.
											 * @var $pass string Reference for IDEs.
											 * @var $ip string Reference for IDEs.
											 * @var $opt_in boolean Reference for IDEs.
											 * @var $fields array Reference for IDEs.
											 */
											if($urls = $GLOBALS['WS_PLUGIN__']['s2member']['o']['registration_notification_urls'])

												foreach(preg_split('/['."\r\n\t".']+/', $urls) as $url) // Notify each of the URLs.

													if(($url = c_ws_plugin__s2member_utils_strings::fill_cvs($url, $custom, true)))
														if(($url = preg_replace('/%%role%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($role)), $url)))
															if(($url = preg_replace('/%%level%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($level)), $url)))
																if(($url = preg_replace('/%%ccaps%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($ccaps)), $url)))
																	if(($url = preg_replace('/%%auto_eot_time%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($auto_eot_time)), $url)))
																		if(($url = preg_replace('/%%user_first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($fname)), $url)))
																			if(($url = preg_replace('/%%user_last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($lname)), $url)))
																				if(($url = preg_replace('/%%user_full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($name)), $url)))
																					if(($url = preg_replace('/%%user_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($email)), $url)))
																						if(($url = preg_replace('/%%user_login%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($login)), $url)))
																							if(($url = preg_replace('/%%user_pass%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($pass)), $url)))
																								if(($url = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($ip)), $url)))
																									if(($url = preg_replace('/%%user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_id)), $url)))
																									{
																										foreach($fields as $var => $val) // Custom Fields.
																											if(!($url = preg_replace('/%%'.preg_quote($var, '/').'%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode(maybe_serialize($val))), $url)))
																												break;

																										if(($url = trim(preg_replace('/%%(.+?)%%/i', '', $url))))
																											c_ws_plugin__s2member_utils_urls::remote($url);
																									}
											unset($urls, $url, $var, $val); // Housekeeping.

											if($GLOBALS['WS_PLUGIN__']['s2member']['o']['registration_notification_recipients'])
											{
												$email_configs_were_on = c_ws_plugin__s2member_email_configs::email_config_status();
												c_ws_plugin__s2member_email_configs::email_config_release();

												$msg = $sbj = '(s2Member / API Notification Email) - Registration';
												$msg .= "\n\n"; // Spacing in the message body.

												$msg .= 'role: %%role%%'."\n";
												$msg .= 'level: %%level%%'."\n";
												$msg .= 'ccaps: %%ccaps%%'."\n";
												$msg .= 'auto_eot_time: %%auto_eot_time%%'."\n";
												$msg .= 'user_first_name: %%user_first_name%%'."\n";
												$msg .= 'user_last_name: %%user_last_name%%'."\n";
												$msg .= 'user_full_name: %%user_full_name%%'."\n";
												$msg .= 'user_email: %%user_email%%'."\n";
												$msg .= 'user_login: %%user_login%%'."\n";
												$msg .= 'user_pass: %%user_pass%%'."\n";
												$msg .= 'user_ip: %%user_ip%%'."\n";
												$msg .= 'user_id: %%user_id%%'."\n";

												foreach($fields as $var => $val)
													$msg .= $var.': %%'.$var.'%%'."\n";

												$msg .= 'cv0: %%cv0%%'."\n";
												$msg .= 'cv1: %%cv1%%'."\n";
												$msg .= 'cv2: %%cv2%%'."\n";
												$msg .= 'cv3: %%cv3%%'."\n";
												$msg .= 'cv4: %%cv4%%'."\n";
												$msg .= 'cv5: %%cv5%%'."\n";
												$msg .= 'cv6: %%cv6%%'."\n";
												$msg .= 'cv7: %%cv7%%'."\n";
												$msg .= 'cv8: %%cv8%%'."\n";
												$msg .= 'cv9: %%cv9%%';

												if(($msg = c_ws_plugin__s2member_utils_strings::fill_cvs($msg, $custom)))
													if(($msg = preg_replace('/%%role%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($role), $msg)))
														if(($msg = preg_replace('/%%level%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($level), $msg)))
															if(($msg = preg_replace('/%%ccaps%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($ccaps), $msg)))
																if(($msg = preg_replace('/%%auto_eot_time%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($auto_eot_time), $msg)))
																	if(($msg = preg_replace('/%%user_first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($fname), $msg)))
																		if(($msg = preg_replace('/%%user_last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($lname), $msg)))
																			if(($msg = preg_replace('/%%user_full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($name), $msg)))
																				if(($msg = preg_replace('/%%user_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($email), $msg)))
																					if(($msg = preg_replace('/%%user_login%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($login), $msg)))
																						if(($msg = preg_replace('/%%user_pass%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($pass), $msg)))
																							if(($msg = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($ip), $msg)))
																								if(($msg = preg_replace('/%%user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_id), $msg)))
																								{
																									foreach($fields as $var => $val) // Custom Fields.
																										if(!($msg = preg_replace('/%%'.preg_quote($var, '/').'%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(maybe_serialize($val)), $msg)))
																											break;

																									if($sbj && ($msg = trim(preg_replace('/%%(.+?)%%/i', '', $msg)))) // Still have a ``$sbj`` and a ``$msg``?

																										foreach(c_ws_plugin__s2member_utils_strings::parse_emails($GLOBALS['WS_PLUGIN__']['s2member']['o']['registration_notification_recipients']) as $recipient)
																											wp_mail($recipient, apply_filters('ws_plugin__s2member_registration_notification_email_sbj', $sbj, get_defined_vars()), apply_filters('ws_plugin__s2member_registration_notification_email_msg', $msg, get_defined_vars()), 'Content-Type: text/plain; charset=UTF-8');
																								}
												if($email_configs_were_on) // Back on?
													c_ws_plugin__s2member_email_configs::email_config();

												unset($sbj, $msg, $var, $val, $recipient, $email_configs_were_on); // Housekeeping.
											}

											if(!empty($GLOBALS['ws_plugin__s2member_registration_return_url']) && ($url = $GLOBALS['ws_plugin__s2member_registration_return_url']))

												if(($url = c_ws_plugin__s2member_utils_strings::fill_cvs($url, $custom, true)))
													if(($url = preg_replace('/%%role%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($role)), $url)))
														if(($url = preg_replace('/%%level%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($level)), $url)))
															if(($url = preg_replace('/%%ccaps%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($ccaps)), $url)))
																if(($url = preg_replace('/%%auto_eot_time%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($auto_eot_time)), $url)))
																	if(($url = preg_replace('/%%user_first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($fname)), $url)))
																		if(($url = preg_replace('/%%user_last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($lname)), $url)))
																			if(($url = preg_replace('/%%user_full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($name)), $url)))
																				if(($url = preg_replace('/%%user_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($email)), $url)))
																					if(($url = preg_replace('/%%user_login%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($login)), $url)))
																						if(($url = preg_replace('/%%user_pass%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($pass)), $url)))
																							if(($url = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($ip)), $url)))
																								if(($url = preg_replace('/%%user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_id)), $url)))
																								{
																									foreach($fields as $var => $val) // Custom Fields.
																										if(!($url = preg_replace('/%%'.preg_quote($var, '/').'%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode(maybe_serialize($val))), $url)))
																											break;

																									if(($url = trim($url))) // Preserve remaining Replacements; because the parent routine may perform replacements too.
																										$GLOBALS['ws_plugin__s2member_registration_return_url'] = $url;
																								}
											unset($url, $var, $val); // Housekeeping.

											c_ws_plugin__s2member_list_servers::process_list_servers($role, $level, $login, $pass, $email, $fname, $lname, $ip, $opt_in, TRUE, $user_id);
											/*
											Suppress errors here in case this routine is fired in unexpected locations; or with odd output buffering techniques.
												@TODO It may also be impossible to delete cookies when fired inside: `/wp-activate.php`.
											*/
											if(!headers_sent()) // Only if headers are NOT yet sent.
											{
												@setcookie('s2member_subscr_gateway', '', time() + 31556926, COOKIEPATH, COOKIE_DOMAIN).@setcookie('s2member_subscr_gateway', '', time() + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN);
												@setcookie('s2member_subscr_id', '', time() + 31556926, COOKIEPATH, COOKIE_DOMAIN).@setcookie('s2member_subscr_id', '', time() + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN);
												@setcookie('s2member_custom', '', time() + 31556926, COOKIEPATH, COOKIE_DOMAIN).@setcookie('s2member_custom', '', time() + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN);
												@setcookie('s2member_item_number', '', time() + 31556926, COOKIEPATH, COOKIE_DOMAIN).@setcookie('s2member_item_number', '', time() + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN);
											}
											/* If debugging/logging is enabled; we need to append ``$reg_vars`` to the log file.
												Logging now supports Multisite Networking as well. */

											$reg_vars = get_defined_vars(); // All defined vars.
											$reg_vars['_COOKIE'] = $_COOKIE; // Record cookies also.
											// No need to include these in the logs. Unset before log entry.
											unset($reg_vars['wpdb'], $reg_vars['current_site'], $reg_vars['current_blog']);
											c_ws_plugin__s2member_utils_logs::log_entry('reg-handler', $reg_vars);

											foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
											do_action('ws_plugin__s2member_during_configure_user_registration', get_defined_vars());
											unset($__refs, $__v);
										}
									}
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_after_configure_user_registration', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.
		}
	}
}
