<?php
/**
* Option panel notices.
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
* @package s2Member\Option_Notices
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_op_notices"))
	{
		/**
		* Option panel notices.
		*
		* @package s2Member\Option_Notices
		* @since 3.5
		*/
		class c_ws_plugin__s2member_op_notices
			{
				/**
				* Describes the General Option overrides for clarity.
				*
				* @package s2Member\Option_Notices
				* @since 3.5
				*
				* @attaches-to ``add_action("load-options-general.php");``
				*
				* @return null
				*/
				public static function general_ops_notice ()
					{
						global $pagenow; // Need this global variable.

						do_action("ws_plugin__s2member_before_general_ops_notice", get_defined_vars ());

						if (is_blog_admin () && $pagenow === "options-general.php" && !isset ($_GET["page"]) && !is_multisite ()) // Multisite does NOT provide these options.
							{
								$notice = "<em>* Note: The s2Member plugin has control over two options on this page.<br /><code>Anyone Can Register = " . esc_html (get_option ("users_can_register")) . "</code>, and <code>New User Default Role = " . esc_html (get_option ("default_role")) . "</code>.<br />For further details, see: <strong>s2Member → General Options → Open Registration</strong>.</em>";

								$js = '<script type="text/javascript">';
								$js .= "jQuery(document).ready(function(\$){ \$('input#users_can_register, select#default_role').attr('disabled', 'disabled'); });";
								$js .= '</script>';

								do_action("ws_plugin__s2member_during_general_ops_notice", get_defined_vars ());

								c_ws_plugin__s2member_admin_notices::enqueue_admin_notice ($notice . $js, "blog:" . $pagenow);
							}

						do_action("ws_plugin__s2member_after_general_ops_notice", get_defined_vars ());

						return /* Return for uniformity. */;
					}
				/**
				* Describes the Multisite Option overrides for clarity.
				*
				* @package s2Member\Option_Notices
				* @since 3.5
				*
				* @attaches-to ``add_action("load-settings.php");``
				*
				* @return null
				*/
				public static function multisite_ops_notice ()
					{
						global $pagenow; // Need this global variable.

						do_action("ws_plugin__s2member_before_multisite_ops_notice", get_defined_vars ());

						if (is_multisite () && is_network_admin () && in_array($pagenow, array("settings.php")) && !isset ($_GET["page"]))
							{
								$notice = "<em>* Note: The s2Member plugin has control over two options on this page.<br /><code>Allow Open Registration = " . esc_html (get_site_option ("registration")) . "</code> and <code>Add New Users = " . esc_html (get_site_option ("add_new_users")) . "</code>.<br />Please check: <strong>s2Member → Multisite (Config)</strong>.</em>";

								$js = '<script type="text/javascript">';
								$js .= "jQuery(document).ready(function(\$){ \$('input[name=registration], input#add_new_users').attr('disabled', 'disabled'); });";
								$js .= '</script>';

								do_action("ws_plugin__s2member_during_multisite_ops_notice", get_defined_vars ());

								c_ws_plugin__s2member_admin_notices::enqueue_admin_notice ($notice . $js, "network:" . $pagenow);
							}

						do_action("ws_plugin__s2member_after_multisite_ops_notice", get_defined_vars ());

						return /* Return for uniformity. */;
					}
				/**
				* Deals with Reading Option conflicts.
				*
				* @package s2Member\Option_Notices
				* @since 3.5
				*
				* @attaches-to ``add_action("load-options-reading.php");``
				*
				* @return null
				*/
				public static function reading_ops_notice ()
					{
						global $pagenow; // Need this global variable.

						do_action("ws_plugin__s2member_before_reading_ops_notice", get_defined_vars ());

						if (is_blog_admin () && $pagenow === "options-reading.php" && !isset ($_GET["page"]))
							{
								do_action("ws_plugin__s2member_during_reading_ops_notice", get_defined_vars ()); // Now check for conflicts.

								if ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_options_page"] && (string)get_option ("page_on_front") === $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_options_page"]
								&& ($notice = '<strong>NOTE:</strong> Your Membership Options Page for s2Member is currently configured as your Home Page (i.e., static page) for WordPress. This causes internal conflicts with s2Member. Your Membership Options Page MUST stand alone. Please correct this.'))
									c_ws_plugin__s2member_admin_notices::enqueue_admin_notice ($notice, "blog:" . $pagenow, true);

								if ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_welcome_page"] && (string)get_option ("page_on_front") === $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_welcome_page"]
								&& ($notice = '<strong>NOTE:</strong> Your Login Welcome Page for s2Member is currently configured as your Home Page (i.e., static page) for WordPress. This causes internal conflicts with s2Member. Your Login Welcome Page MUST stand alone. Please correct this.'))
									c_ws_plugin__s2member_admin_notices::enqueue_admin_notice ($notice, "blog:" . $pagenow, true);

								if ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_options_page"] && (string)get_option ("page_for_posts") === $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_options_page"]
								&& ($notice = '<strong>NOTE:</strong> Your Membership Options Page for s2Member is currently configured as your Posts Page (i.e., static page) for WordPress. This causes internal conflicts with s2Member. Your Membership Options Page MUST stand alone. Please correct this.'))
									c_ws_plugin__s2member_admin_notices::enqueue_admin_notice ($notice, "blog:" . $pagenow, true);

								if ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_welcome_page"] && (string)get_option ("page_for_posts") === $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_welcome_page"]
								&& ($notice = '<strong>NOTE:</strong> Your Login Welcome Page for s2Member is currently configured as your Posts Page (i.e., static page) for WordPress. This causes internal conflicts with s2Member. Your Login Welcome Page MUST stand alone. Please correct this.'))
									c_ws_plugin__s2member_admin_notices::enqueue_admin_notice ($notice, "blog:" . $pagenow, true);
							}

						do_action("ws_plugin__s2member_after_reading_ops_notice", get_defined_vars ());

						return /* Return for uniformity. */;
					}
			}
	}
