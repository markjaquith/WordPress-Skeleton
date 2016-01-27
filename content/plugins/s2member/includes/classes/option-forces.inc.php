<?php
/**
* Forces WordPress options.
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
* @package s2Member\Option_Forces
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_option_forces"))
	{
		/**
		* Forces WordPress options.
		*
		* @package s2Member\Option_Forces
		* @since 3.5
		*/
		class c_ws_plugin__s2member_option_forces
			{
				/**
				* Forces a default Role for new registrations, NOT tied to an incoming payment.
				*
				* @package s2Member\Option_Forces
				* @since 3.5
				*
				* @attaches-to ``add_filter("pre_option_default_role");``
				*
				* @param string $default_role Expects a default Role to be passed by the Filter.
				* @return string Default Role, as configured by s2Member.
				*/
				public static function force_default_role ($default_role = FALSE)
					{
						do_action("ws_plugin__s2member_before_force_default_role", get_defined_vars ());

						return apply_filters("ws_plugin__s2member_force_default_role", ($default_role = "subscriber"), get_defined_vars ());
					}
				/**
				* Forces a default Role for new Multisite registrations (on the Main Site) NOT tied to an incoming payment.
				*
				* @package s2Member\Option_Forces
				* @since 3.5
				*
				* @attaches-to ``add_filter("pre_site_option_default_user_role");``
				*
				* @param string $default_role Expects a default Role to be passed by the Filter.
				* @return string Default Role, as configured by s2Member.
				*/
				public static function force_mms_default_role ($default_role = FALSE)
					{
						do_action("ws_plugin__s2member_before_force_mms_default_role", get_defined_vars ());

						return apply_filters("ws_plugin__s2member_force_mms_default_role", ($default_role = "subscriber"), get_defined_vars ());
					}
				/**
				* Forces a specific Role to demote to, whenever a Member is demoted in one way or another.
				*
				* Use by the PayPal IPN routines, and also by the Auto-EOT system.
				*
				* @package s2Member\Option_Forces
				* @since 3.5
				*
				* @param string $demotion_role Expects a demotion Role to be passed by the caller.
				* @return string Demotion Role, as configured by s2Member.
				*/
				public static function force_demotion_role ($demotion_role = FALSE)
					{
						do_action("ws_plugin__s2member_before_force_demotion_role", get_defined_vars ());

						return apply_filters("ws_plugin__s2member_force_demotion_role", ($demotion_role = "subscriber"), get_defined_vars ());
					}
				/**
				* Allows new Users to be created on a Multisite Network.
				*
				* @package s2Member\Option_Forces
				* @since 3.5
				*
				* @attaches-to ``add_filter("pre_site_option_add_new_users");``
				*
				* @param int|string $allow Numeric string (`1`) or (`0`), expected by the Filter.
				* @return string Numeric (`1`) or (`0`) indicating true or false. Forces to (`1`) true.
				*/
				public static function mms_allow_new_users ($allow = FALSE)
					{
						do_action("ws_plugin__s2member_before_mms_allow_new_users", get_defined_vars ());

						return apply_filters("ws_plugin__s2member_mms_allow_new_users", ($allow = "1"), get_defined_vars ());
					}
				/**
				* Forces a Multisite Dashboard Blog to be the Main Site.
				*
				* @package s2Member\Option_Forces
				* @since 3.5
				*
				* @attaches-to ``add_filter("pre_site_option_dashboard_blog");``
				*
				* @param int|string $dashboard_blog Numeric Dashboard Blog ID passed through by the Filter.
				* @return int|str Numeric Dashboard Blog ID, as configured by s2Member. Forces to the Main Site.
				*/
				public static function mms_dashboard_blog ($dashboard_blog = FALSE)
					{
						global /* For Multisite support. */ $current_site, $current_blog;

						do_action("ws_plugin__s2member_before_mms_dashboard_blog", get_defined_vars ());

						$main_site = ((is_multisite ()) ? $current_site->blog_id : "1"); // Forces the Main Site.

						return apply_filters("ws_plugin__s2member_mms_dashboard_blog", ($dashboard_blog = $main_site), get_defined_vars ());
					}
				/**
				* Allows access to the Registration Form.
				*
				* @package s2Member\Option_Forces
				* @since 3.5
				*
				* @attaches-to ``add_filter("pre_option_users_can_register");``
				*
				* @param int|string $users_can_register Numeric (`1`) or (`0`), indicating true or false; passed through by the Filter.
				* @return string Numeric value of (`1`) or (`0`), indicating true or false; depending on several factors.
				*/
				public static function check_register_access ($users_can_register = FALSE)
					{
						global $wpdb; // Global database object reference

						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_before_check_register_access", get_defined_vars ());
						unset($__refs, $__v);

						$by_default = $users_can_register = $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["allow_subscribers_in"];

						if (is_multisite () && c_ws_plugin__s2member_utils_conds::is_multisite_farm () && is_main_site ())
							return apply_filters("ws_plugin__s2member_check_register_access", ($users_can_register = "0"), get_defined_vars ());

						else if (!is_admin () && !$users_can_register) // Do NOT run these security checks on option pages; it's confusing.
							if (!is_multisite () || !c_ws_plugin__s2member_utils_conds::is_multisite_farm () || !is_main_site () || current_user_can ("create_users") || is_super_admin ())
								{
									if (current_user_can ("create_users") || (is_multisite () && is_super_admin ()) || c_ws_plugin__s2member_register_access::reg_cookies_ok ())
										{
											return apply_filters("ws_plugin__s2member_check_register_access", ($users_can_register = "1"), get_defined_vars ());
										}
								}

						return apply_filters("ws_plugin__s2member_check_register_access", $users_can_register, get_defined_vars ());
					}
				/**
				* Allows access to the main Multisite Registration Form.
				*
				* @package s2Member\Option_Forces
				* @since 3.5
				*
				* @attaches-to ``add_filter("pre_site_option_registration");``
				*
				* @param string $users_can_register Expects *( `none`, `all`, `blog`, `user` )*, passed through by the Filter.
				* @return string One of `none|all|user`; depending on several factors.
				*/
				public static function check_mms_register_access ($users_can_register = FALSE)
					{
						global $wpdb; // Global database object reference
						global /* For Multisite support. */ $current_site, $current_blog;

						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_before_check_register_access", get_defined_vars ());
						unset($__refs, $__v);

						$by_default = $users_can_register = $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["mms_registration_grants"];

						if (c_ws_plugin__s2member_utils_conds::bp_is_installed () && is_multisite () && /* BP Multisite / but NOT offering Blogs? */ !c_ws_plugin__s2member_utils_conds::is_multisite_farm ())
							return apply_filters("ws_plugin__s2member_check_mms_register_access", ($users_can_register = ((c_ws_plugin__s2member_option_forces::check_register_access ()) ? "user" : "none")), get_defined_vars ());

						else if (!is_multisite () || !c_ws_plugin__s2member_utils_conds::is_multisite_farm ()) // Blog Farm?
							return apply_filters("ws_plugin__s2member_check_mms_register_access", ($users_can_register = "none"), get_defined_vars ());

						else if (!is_network_admin () && $users_can_register !== "all") // Do NOT run these checks on Network option pages; it's confusing.
							{
								if ((is_main_site () && current_user_can ("create_users")) || is_super_admin ()) // Creates Users on the Main Site?
									{
										return apply_filters("ws_plugin__s2member_check_mms_register_access", ($users_can_register = "all"), get_defined_vars ());
									}

								else if (is_user_logged_in () && is_object ($user = wp_get_current_user ()) && $user->ID && is_object ($user = new WP_User ($user->ID, $current_site->blog_id)) && $user->ID && $user->has_cap ("access_s2member_level1"))
									{
										$mms_options = c_ws_plugin__s2member_utilities::mms_options ();
										$blogs_allowed = (int)@$mms_options["mms_registration_blogs_level" . c_ws_plugin__s2member_user_access::user_access_level ($user)];
										$user_blogs = (is_array($blogs = get_blogs_of_user ($user->ID))) ? count ($blogs) - 1 : 0;

										$user_blogs = ($user_blogs >= 0) ? $user_blogs : 0; // NOT less than zero.
										$blogs_allowed = ($blogs_allowed >= 0) ? $blogs_allowed : 0;

										if ($user_blogs < $blogs_allowed) // Are they within their limit?
											{
												return apply_filters("ws_plugin__s2member_check_mms_register_access", ($users_can_register = "all"), get_defined_vars ());
											}
									}
								else if (!is_user_logged_in () && is_main_site () && ($reg_cookies = c_ws_plugin__s2member_register_access::reg_cookies_ok ()) && extract ($reg_cookies))
									{
										if (preg_match ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["membership_item_number_w_level_regex"], $item_number, $m) && !empty($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["mms_registration_blogs_level" . $m[1]]))
											{
												return apply_filters("ws_plugin__s2member_check_mms_register_access", ($users_can_register = "all"), get_defined_vars ());
											}
										else // Otherwise, we MUST allow them to at least create an account; they paid for it! Defaults to `user`.
											{
												return apply_filters("ws_plugin__s2member_check_mms_register_access", ($users_can_register = "user"), get_defined_vars ());
											}
									}
							}

						else if (!is_network_admin () && $users_can_register === "all") // Do NOT run these security checks on option pages; it's confusing to a site owner.
							{
								if (is_user_logged_in () && !(is_main_site () && current_user_can ("create_users")) && !is_super_admin () && is_object ($user = wp_get_current_user ()) && $user->ID && is_object ($user = new WP_User ($user->ID, $current_site->blog_id)) && $user->ID)
									{
										$mms_options = c_ws_plugin__s2member_utilities::mms_options ();
										$blogs_allowed = (int)@$mms_options["mms_registration_blogs_level" . c_ws_plugin__s2member_user_access::user_access_level ($user)];
										$user_blogs = (is_array($blogs = get_blogs_of_user ($user->ID))) ? count ($blogs) - 1 : 0;

										$user_blogs = ($user_blogs >= 0) ? $user_blogs : 0; // NOT less than zero.
										$blogs_allowed = ($blogs_allowed >= 0) ? $blogs_allowed : 0;

										if ($user_blogs >= $blogs_allowed) // Are they at their limit?
											{
												return apply_filters("ws_plugin__s2member_check_mms_register_access", ($users_can_register = "none"), get_defined_vars ());
											}
									}
							}

						return apply_filters("ws_plugin__s2member_check_mms_register_access", $users_can_register, get_defined_vars ());
					}
				/**
				* Register access in BuddyPress, for Multisite compatibility.
				*
				* BuddyPress bypasses the default Filter `pre_site_option_registration`, and instead uses: ``bp_core_get_root_options()``.
				*
				* @package s2Member\Option_Forces
				* @since 3.5
				*
				* @attaches-to ``add_filter("bp_core_get_root_options");``
				* @attaches-to ``add_filter("bp_core_get_site_options");`` **(before BuddyPress v1.5)**.
				*
				* @param array $site_options Expects array of BuddyPress site options.
				* @return array Site options array, after having been Filtered by this routine.
				*/
				public static function check_bp_mms_register_access ($site_options = FALSE)
					{
						if (is_multisite ()) // Only if Multisite Networking is enabled. Pointless otherwise.
							$site_options["registration"] = c_ws_plugin__s2member_option_forces::check_mms_register_access ($site_options["registration"]);

						return apply_filters("ws_plugin__s2member_check_bp_mms_register_access", $site_options, get_defined_vars ());
					}
			}
	}
