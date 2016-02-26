<?php
/**
 * s2Member translations.
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
 * @package s2Member\Translations
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if(!class_exists("c_ws_plugin__s2member_translations"))
{
	/**
	 * s2Member translations.
	 *
	 * @package s2Member\Translations
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_translations
	{
		/**
		 * Loads s2Member's text domain for translations.
		 *
		 * @package s2Member\Translations
		 * @since 110815
		 *
		 * @attaches-to ``add_action("init");``
		 *
		 * @return null
		 */
		public static function load()
		{
			load_plugin_textdomain("s2member", FALSE, c_ws_plugin__s2member_utils_dirs::rel_path(WP_PLUGIN_DIR, dirname(dirname(__FILE__))."/translations"));
			load_plugin_textdomain("s2member"); // Allows `.mo` file to be loaded from the `/wp-content/plugins/s2member-[locale].mo`.

			do_action("ws_plugin__s2member_during_translations_load", get_defined_vars());

			add_filter("gettext", "c_ws_plugin__s2member_translations::translation_mangler", 10, 3);
		}

		/**
		 * Handles internal translations via `gettext` Filter.
		 *
		 * Important note. Because this routine also uses translation functionality by WordPress,
		 * anything translated by this routine MUST be different, otherwise it will result in a recursive loop,
		 * because the ``__()`` family of functions would be called upon recursively by this routine.
		 *
		 * If you're translating s2Member into a different language, your MO file for s2Member will automagically deal with
		 * everything you see below. No worries. Just build your translation file for s2Member, and you're all set.
		 *
		 * @package s2Member\Translations
		 * @since 3.5
		 *
		 * @attaches-to ``add_filter("gettext");``
		 *
		 * @param string $translated Expects already-translated string passed in by Filter.
		 * @param string $original Expects original text string passed in by Filter.
		 * @param string $domain Expects translation domain passed in by Filter.
		 *
		 * @return string Translated string, possibly modified by this routine.
		 */
		public static function translation_mangler($translated = '', $original = '', $domain = '')
		{
			global $current_site, $current_blog; // In support of Multisite Networking.
			static $s = array(); // This static array optimizes all of these routines.

			if((isset ($s["is_wp_login"]) && $s["is_wp_login"]) || (!isset ($s["is_wp_login"]) && ($s["is_wp_login"] = (strpos($_SERVER["REQUEST_URI"], "/wp-login.php") !== FALSE && empty($_REQUEST["action"]) && empty($_REQUEST["checkemail"])) ? TRUE : FALSE)))
			{
				if($original === "Username") // Give Filters a chance here.
				{
					$translated = apply_filters("ws_plugin__s2member_translation_mangler", _x("Username:", "s2member-front", "s2member"), get_defined_vars());
				}
				else if($original === "Password") // Give Filters a chance here.
				{
					$translated = apply_filters("ws_plugin__s2member_translation_mangler", _x("My Password:", "s2member-front", "s2member"), get_defined_vars());
				}
			}
			else if((isset ($s["is_wp_login_register"]) && $s["is_wp_login_register"]) || (!isset ($s["is_wp_login_register"]) && ($s["is_wp_login_register"] = (strpos($_SERVER["REQUEST_URI"], "/wp-login.php") !== FALSE && !empty($_REQUEST["action"]) && $_REQUEST["action"] === "register") ? TRUE : FALSE)))
			{
				if($original === "Username") // Give Filters a chance here.
				{
					$translated = apply_filters("ws_plugin__s2member_translation_mangler", _x("Username *", "s2member-front", "s2member"), get_defined_vars());
				}
				else if($original === "Password") // Give Filters a chance here.
				{
					$translated = apply_filters("ws_plugin__s2member_translation_mangler", _x("Password *", "s2member-front", "s2member"), get_defined_vars());
				}
				else if($original === "E-mail") // Give Filters a chance here.
				{
					$translated = apply_filters("ws_plugin__s2member_translation_mangler", _x("Email Address *", "s2member-front", "s2member"), get_defined_vars());
				}
			}
			else if((isset ($s["is_wp_login_checkemail"]) && $s["is_wp_login_checkemail"]) || (!isset ($s["is_wp_login_checkemail"]) && ($s["is_wp_login_checkemail"] = (strpos($_SERVER["REQUEST_URI"], "/wp-login.php") !== FALSE && empty($_REQUEST["action"]) && !empty($_REQUEST["checkemail"]) && $_REQUEST["checkemail"] === "registered") ? TRUE : FALSE)))
			{
				if($original === "Registration complete. Please check your e-mail." && $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_password"])
				{
					$translated = apply_filters("ws_plugin__s2member_translation_mangler", _x("Registration complete. Please log in.", "s2member-front", "s2member"), get_defined_vars());
				}
			}
			else if((isset ($s["is_user_new"]) && $s["is_user_new"]) || (!isset ($s["is_user_new"]) && ($s["is_user_new"] = (strpos($_SERVER["REQUEST_URI"], "/wp-admin/user-new.php") !== FALSE) ? TRUE : FALSE)))
			{
				if($original === "Hi,\n\nYou have been invited to join '%s' at\n%s as a %s.\nPlease click the following link to confirm the invite:\n%s\n" && !empty($_REQUEST["role"]) && preg_match("/^(subscriber|s2member_level[0-9]+)$/", $_REQUEST["role"]))
				{
					$translated = apply_filters("ws_plugin__s2member_translation_mangler", _x("You have been invited to join `%1\$s` at\n%2\$s as a Member.\nPlease click the following link to confirm the invite:\n%4\$s\n", "s2member-front", "s2member"), get_defined_vars());
				}
			}
			else if((isset ($s["is_wp_activate"]) && $s["is_wp_activate"]) || (!isset ($s["is_wp_activate"]) && ($s["is_wp_activate"] = (strpos($_SERVER["REQUEST_URI"], "/wp-activate.php") !== FALSE) ? TRUE : FALSE)))
			{
				if($original === 'Your account is now activated. <a href="%1$s">View your site</a> or <a href="%2$s">Log in</a>')
				{
					$translated = apply_filters("ws_plugin__s2member_translation_mangler", _x('Your account is now active. <a href="%1$s">Visit site</a> or <a href="%2$s">Log in</a>.', "s2member-front", "s2member"), get_defined_vars());
				}
			}
			else if((isset ($s["is_wp_signup"]) && $s["is_wp_signup"]) || (!isset ($s["is_wp_signup"]) && ($s["is_wp_signup"] = (strpos($_SERVER["REQUEST_URI"], "/wp-signup.php") !== FALSE) ? TRUE : FALSE)))
			{
				if($original === "If you&#8217;re not going to use a great site domain, leave it for a new user. Now have at it!")
				{
					$translated = apply_filters("ws_plugin__s2member_translation_mangler", "", get_defined_vars());
				}
				else if($original === "Welcome back, %s. By filling out the form below, you can <strong>add another site to your account</strong>. There is no limit to the number of sites you can have, so create to your heart&#8217;s content, but write responsibly!")
				{
					if(is_user_logged_in() && !(is_main_site() && current_user_can("create_users")) && !is_super_admin() && is_object($user = wp_get_current_user()) && $user->ID && is_object($user = new WP_User ($user->ID, $current_site->blog_id)) && $user->ID)
					{
						$mms_options   = c_ws_plugin__s2member_utilities::mms_options();
						$blogs_allowed = (int)@$mms_options["mms_registration_blogs_level".c_ws_plugin__s2member_user_access::user_access_level($user)];
						$user_blogs    = (is_array($blogs = get_blogs_of_user($user->ID))) ? count($blogs) - 1 : 0;

						$user_blogs    = ($user_blogs >= 0) ? $user_blogs : 0; // NOT less than zero.
						$blogs_allowed = ($blogs_allowed >= 0) ? $blogs_allowed : 0;

						$translated = apply_filters("ws_plugin__s2member_translation_mangler", _x('By filling out the form below, you can <strong>add a site to your account</strong>.', "s2member-front", "s2member").(($blogs_allowed > 1) ? '<br />'.sprintf(_nx('You may create <strong>%s</strong> site.', 'You may create up to <strong>%s</strong> sites.', $blogs_allowed, "s2member-front", "s2member"), $blogs_allowed) : ''), get_defined_vars());
					}
				}
			}
			else if((isset ($s["is_bp_blog_creation"]) && $s["is_bp_blog_creation"]) || (!isset ($s["is_bp_blog_creation"]) && ($s["is_bp_blog_creation"] = (c_ws_plugin__s2member_utils_conds::bp_is_installed() && bp_is_create_blog()) ? TRUE : FALSE)))
			{
				if($original === "If you&#8217;re not going to use a great domain, leave it for a new user. Now have at it!")
				{
					$translated = apply_filters("ws_plugin__s2member_translation_mangler", "", get_defined_vars());
				}
				else if($original === "By filling out the form below, you can <strong>add a site to your account</strong>. There is no limit to the number of sites that you can have, so create to your heart's content, but blog responsibly!")
				{
					if(is_user_logged_in() && !(is_main_site() && current_user_can("create_users")) && !is_super_admin() && is_object($user = wp_get_current_user()) && $user->ID && is_object($user = new WP_User ($user->ID, $current_site->blog_id)) && $user->ID)
					{
						$mms_options   = c_ws_plugin__s2member_utilities::mms_options();
						$blogs_allowed = (int)@$mms_options["mms_registration_blogs_level".c_ws_plugin__s2member_user_access::user_access_level($user)];
						$user_blogs    = (is_array($blogs = get_blogs_of_user($user->ID))) ? count($blogs) - 1 : 0;

						$user_blogs    = ($user_blogs >= 0) ? $user_blogs : 0; // NOT less than zero.
						$blogs_allowed = ($blogs_allowed >= 0) ? $blogs_allowed : 0;

						$translated = apply_filters("ws_plugin__s2member_translation_mangler", _x('By filling out the form below, you can <strong>add a site to your account</strong>.', "s2member-front", "s2member").(($blogs_allowed > 1) ? '<br />'.sprintf(_nx('You may create up to <strong>%s</strong> site.', 'You may create up to <strong>%s</strong> sites.', $blogs_allowed, "s2member-front", "s2member"), $blogs_allowed) : ''), get_defined_vars());
					}
				}
			}
			return $translated; // No Filters.
		}
	}
}
