<?php
/**
 * Administrative menu pages.
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
 * @package s2Member\Menu_Pages
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_menu_pages'))
{
	/**
	 * Administrative menu pages.
	 *
	 * @package s2Member\Menu_Pages
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_menu_pages
	{
		/**
		 * Pre-display errors.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 111209
		 *
		 * @var array
		 */
		public static $pre_display_errors = array();

		/**
		 * Saves all options from any menu page.
		 *
		 * Can also be self-verified; and configured extensively with function parameters.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 *
		 * @param null|array $new_options Optional. Force feed an array of new options. Defaults to ``$_POST`` vars.
		 *   If ``$new_options`` are passed in, be SURE that you've already applied ``stripslashes_deep()``.
		 * @param bool       $verified Optional. Defaults to false. If true, ``wp_verify_nonce()`` is skipped in this routine.
		 * @param bool       $update_other Optional. Defaults to true. If false, other option-dependent routines will not be processed.
		 * @param bool|array $display_notices Optional. Defaults to true. Can be false, or an array of certain notices that can be displayed.
		 * @param bool|array $enqueue_notices Optional. Defaults to false. Can be true, or an array of certain notices that should be enqueued.
		 * @param bool       $request_refresh Optional. Defaults to false. If true, resulting `success` notice will include a link to refresh the menu page.
		 *
		 * @return bool True if all s2Member options were updated successfully, else false.
		 */
		public static function update_all_options($new_options = NULL, $verified = FALSE, $update_other = TRUE, $display_notices = TRUE, $enqueue_notices = FALSE, $request_refresh = FALSE)
		{
			$updated_all_options = FALSE; // Initializing this variable here makes it an available reference-variable to Hooks/Filters.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_update_all_options', get_defined_vars()); // If you use this Hook, be sure to use ``wp_verify_nonce()``.
			unset($__refs, $__v); // Housekeeping.

			if($verified || (!empty($_POST['ws_plugin__s2member_options_save']) && ($nonce = $_POST['ws_plugin__s2member_options_save']) && wp_verify_nonce($nonce, 'ws-plugin--s2member-options-save')))
			{
				$options = $GLOBALS['WS_PLUGIN__']['s2member']['o']; // Acquire the full existing configuration options array here.

				$new_options = (is_array($new_options)) ? $new_options : ((!empty($_POST) && is_array($_POST)) ? stripslashes_deep($_POST) : array());
				$new_options = c_ws_plugin__s2member_utils_strings::trim_deep($new_options);

				foreach($new_options as $key => $value) // Find all keys contained within ``$new_options`` matching `^ws_plugin__s2member_`.
					if(strpos($key, 'ws_plugin__s2member_') === 0) // A relevant ``$new_options`` key matching `^ws_plugin__s2member_`?

						if($key === 'ws_plugin__s2member_configured') // s2Member is now configured (according to these options)?
							($GLOBALS['WS_PLUGIN__']['s2member']['c']['configured'] = $value).update_option('ws_plugin__s2member_configured', $value);

						else if(!is_array($value) || (is_array($value) && array_shift($value) === 'update-signal')) // Updating an array?
							$options[preg_replace('/^'.preg_quote('ws_plugin__s2member_', '/').'/', '', $key)] = $value;

				unset($key, $value); // Unset these utility variables now. This prevents bleeding vars into Hooks/Filters that are of no use.

				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_update_all_options', get_defined_vars());
				unset($__refs, $__v); // Housekeeping.

				$options = ws_plugin__s2member_configure_options_and_their_defaults(($options = array_merge($options, array('options_version' => (string)($options['options_version'] + 0.001)))));
				update_option('ws_plugin__s2member_options', $options).((is_multisite() && is_main_site()) ? update_site_option('ws_plugin__s2member_options', $options) : NULL).update_option('ws_plugin__s2member_cache', array());

				if($update_other === TRUE || in_array('auto_eot_system', (array)$update_other)) // Handle the Auto-EOT System now (enable/disable).
					($options['auto_eot_system_enabled'] == 1) ? c_ws_plugin__s2member_auto_eots::add_auto_eot_system() : c_ws_plugin__s2member_auto_eots::delete_auto_eot_system();

				if(($display_notices === TRUE || in_array('success', (array)$display_notices)) && ($notice = '<strong>Options saved.'.(($request_refresh) ? ' Please <a href="'.esc_attr($_SERVER['REQUEST_URI']).'">refresh</a>.' : '').'</strong>'))
					($enqueue_notices === TRUE || in_array('success', (array)$enqueue_notices)) ? c_ws_plugin__s2member_admin_notices::enqueue_admin_notice($notice, '*:*') : c_ws_plugin__s2member_admin_notices::display_admin_notice($notice);

				if(empty($_GET['page']) || $_GET['page'] !== 'ws-plugin--s2member-mms-ops') // Do NOT display page-conflict-warnings on the Main Multisite Configuration panel.
				{
					if(!$options['membership_options_page'] && ($display_notices === TRUE || in_array('page-conflict-warnings', (array)$display_notices)) && ($notice = '<strong>NOTE:</strong> s2Member security restrictions will NOT be enforced until you\'ve configured a Membership Options Page. See: <strong>s2Member → General Options → Membership Options Page</strong>.'))
						($enqueue_notices === TRUE || in_array('page-conflict-warnings', (array)$enqueue_notices)) ? c_ws_plugin__s2member_admin_notices::enqueue_admin_notice($notice, '*:*', TRUE) : c_ws_plugin__s2member_admin_notices::display_admin_notice($notice, TRUE);

					if($options['login_welcome_page'] && $options['login_welcome_page'] === $options['membership_options_page'] && ($display_notices === TRUE || in_array('page-conflict-warnings', (array)$display_notices)) && ($notice = '<strong>s2Member:</strong> Your Login Welcome Page is the same as your Membership Options Page. Please correct this. See: <strong>s2Member → General Options → Login Welcome Page</strong>.'))
						($enqueue_notices === TRUE || in_array('page-conflict-warnings', (array)$enqueue_notices)) ? c_ws_plugin__s2member_admin_notices::enqueue_admin_notice($notice, '*:*', TRUE) : c_ws_plugin__s2member_admin_notices::display_admin_notice($notice, TRUE);

					if($options['membership_options_page'] && (string)get_option('page_on_front') === $options['membership_options_page'] && ($display_notices === TRUE || in_array('page-conflict-warnings', (array)$display_notices)) && ($notice = '<strong>s2Member:</strong> Your Membership Options Page is currently configured as your Home Page (i.e., static page) for WordPress. This causes internal conflicts with s2Member. Your Membership Options Page MUST stand alone. Please correct this. See: <strong>WordPress → Reading Options</strong>. Or change: <strong>s2Member → General Options → Membership Options Page</strong>.'))
						($enqueue_notices === TRUE || in_array('page-conflict-warnings', (array)$enqueue_notices)) ? c_ws_plugin__s2member_admin_notices::enqueue_admin_notice($notice, '*:*', TRUE) : c_ws_plugin__s2member_admin_notices::display_admin_notice($notice, TRUE);

					if($options['login_welcome_page'] && (string)get_option('page_on_front') === $options['login_welcome_page'] && ($display_notices === TRUE || in_array('page-conflict-warnings', (array)$display_notices)) && ($notice = '<strong>s2Member:</strong> Your Login Welcome Page is currently configured as your Home Page (i.e., static page) for WordPress. This causes internal conflicts with s2Member. Your Login Welcome Page MUST stand alone. Please correct this. See: <strong>WordPress → Reading Options</strong>. Or change: <strong>s2Member → General Options → Login Welcome Page</strong>.'))
						($enqueue_notices === TRUE || in_array('page-conflict-warnings', (array)$enqueue_notices)) ? c_ws_plugin__s2member_admin_notices::enqueue_admin_notice($notice, '*:*', TRUE) : c_ws_plugin__s2member_admin_notices::display_admin_notice($notice, TRUE);

					if($options['membership_options_page'] && (string)get_option('page_for_posts') === $options['membership_options_page'] && ($display_notices === TRUE || in_array('page-conflict-warnings', (array)$display_notices)) && ($notice = '<strong>s2Member:</strong> Your Membership Options Page is currently configured as your Posts Page (i.e., static page) for WordPress. This causes internal conflicts with s2Member. Your Membership Options Page MUST stand alone. Please correct this. See: <strong>WordPress → Reading Options</strong>. Or change: <strong>s2Member → General Options → Membership Options Page</strong>.'))
						($enqueue_notices === TRUE || in_array('page-conflict-warnings', (array)$enqueue_notices)) ? c_ws_plugin__s2member_admin_notices::enqueue_admin_notice($notice, '*:*', TRUE) : c_ws_plugin__s2member_admin_notices::display_admin_notice($notice, TRUE);

					if($options['login_welcome_page'] && (string)get_option('page_for_posts') === $options['login_welcome_page'] && ($display_notices === TRUE || in_array('page-conflict-warnings', (array)$display_notices)) && ($notice = '<strong>s2Member:</strong> Your Login Welcome Page is currently configured as your Posts Page (i.e., static page) for WordPress. This causes internal conflicts with s2Member. Your Login Welcome Page MUST stand alone. Please correct this. See: <strong>WordPress → Reading Options</strong>. Or change: <strong>s2Member → General Options → Login Welcome Page</strong>.'))
						($enqueue_notices === TRUE || in_array('page-conflict-warnings', (array)$enqueue_notices)) ? c_ws_plugin__s2member_admin_notices::enqueue_admin_notice($notice, '*:*', TRUE) : c_ws_plugin__s2member_admin_notices::display_admin_notice($notice, TRUE);

					if($options['file_download_limit_exceeded_page'] && $options['file_download_limit_exceeded_page'] === $options['membership_options_page'] && ($display_notices === TRUE || in_array('page-conflict-warnings', (array)$display_notices)) && ($notice = '<strong>s2Member:</strong> Your Download Limit Exceeded Page is the same as your Membership Options Page. Please correct this. See: <strong>s2Member → Download Options</strong>.'))
						($enqueue_notices === TRUE || in_array('page-conflict-warnings', (array)$enqueue_notices)) ? c_ws_plugin__s2member_admin_notices::enqueue_admin_notice($notice, '*:*', TRUE) : c_ws_plugin__s2member_admin_notices::display_admin_notice($notice, TRUE);
				}
				$updated_all_options = TRUE; // Flag indicating this routine was processed successfully; and that all s2Member options have been updated successfully.
			}
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_after_update_all_options', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			return apply_filters('ws_plugin__s2member_update_all_options', (($updated_all_options) ? TRUE : FALSE), get_defined_vars());
		}

		/**
		 * Adds option menus / sub-menus.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('admin_menu');``
		 */
		public static function add_admin_options()
		{
			do_action('ws_plugin__s2member_before_add_admin_options', get_defined_vars());

			add_filter('plugin_action_links', 'c_ws_plugin__s2member_menu_pages::_add_settings_link', 10, 2);

			if(apply_filters('ws_plugin__s2member_during_add_admin_options_create_menu_items', TRUE, get_defined_vars()))
			{
				if((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) || apply_filters('ws_plugin__s2member_during_add_admin_options_clear_right_side', FALSE, get_defined_vars()))
					$GLOBALS['WS_PLUGIN__']['s2member']['c']['menu_pages'] = array(); // Clear right side.

				$menu = apply_filters('ws_plugin__s2member_during_add_admin_options_menu_slug', 'ws-plugin--s2member-start', get_defined_vars());

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_menu_page', TRUE, get_defined_vars()))
					add_menu_page(((c_ws_plugin__s2member_utils_conds::pro_is_installed()) ? 's2Member (Pro)' : 's2Member'), ((c_ws_plugin__s2member_utils_conds::pro_is_installed()) ? 's2Member (Pro)' : 's2Member'),
						'create_users', $menu, 'c_ws_plugin__s2member_menu_pages::start_page', $GLOBALS['WS_PLUGIN__']['s2member']['c']['dir_url'].'/images/brand-favicon.png');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_start_page', TRUE, get_defined_vars()))
					add_submenu_page($menu, 'Getting Started w/ s2Member', 'Getting Started', 'create_users', 'ws-plugin--s2member-start', 'c_ws_plugin__s2member_menu_pages::start_page');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_help_page', !is_multisite () || !c_ws_plugin__s2member_utils_conds::is_multisite_farm () || is_main_site (), get_defined_vars()))
					add_submenu_page($menu, 'Getting Help w/ s2Member', 'Getting Help', 'create_users', 'ws-plugin--s2member-help', 'c_ws_plugin__s2member_menu_pages::help_page');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_divider_1', TRUE, get_defined_vars()))
					add_submenu_page($menu, '', '<span style="display:block; margin:1px 0 1px -5px; padding:0; height:1px; line-height:1px; background:#CCCCCC;"></span>', 'create_users', '#');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_mms_ops_page', (!is_multisite() || is_main_site()), get_defined_vars()))
					add_submenu_page($menu, 's2Member Multisite Configuration', 'Multisite (Config)', 'create_users', 'ws-plugin--s2member-mms-ops', 'c_ws_plugin__s2member_menu_pages::mms_ops_page');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_gen_ops_page', TRUE, get_defined_vars()))
					add_submenu_page($menu, 's2Member General Options', 'General Options', 'create_users', 'ws-plugin--s2member-gen-ops', 'c_ws_plugin__s2member_menu_pages::gen_ops_page');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_res_ops_page', TRUE, get_defined_vars()))
					add_submenu_page($menu, 's2Member Restriction Options', 'Restriction Options', 'create_users', 'ws-plugin--s2member-res-ops', 'c_ws_plugin__s2member_menu_pages::res_ops_page');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_down_ops_page', (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()), get_defined_vars()))
					add_submenu_page($menu, 's2Member Download Options', 'Download Options', 'create_users', 'ws-plugin--s2member-down-ops', 'c_ws_plugin__s2member_menu_pages::down_ops_page');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_divider_2', TRUE, get_defined_vars()))
					add_submenu_page($menu, '', '<span style="display:block; margin:1px 0 1px -5px; padding:0; height:1px; line-height:1px; background:#CCCCCC;"></span>', 'create_users', '#');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_paypal_ops_page', TRUE, get_defined_vars()))
					add_submenu_page($menu, 's2Member PayPal Options', 'PayPal Options', 'create_users', 'ws-plugin--s2member-paypal-ops', 'c_ws_plugin__s2member_menu_pages::paypal_ops_page');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_paypal_buttons_page', TRUE, get_defined_vars()))
					add_submenu_page($menu, 's2Member PayPal Buttons', 'PayPal Buttons', 'create_users', 'ws-plugin--s2member-paypal-buttons', 'c_ws_plugin__s2member_menu_pages::paypal_buttons_page');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_divider_3', TRUE, get_defined_vars()))
					add_submenu_page($menu, '', '<span style="display:block; margin:1px 0 1px -5px; padding:0; height:1px; line-height:1px; background:#CCCCCC;"></span>', 'create_users', '#');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_trk_ops_page', TRUE, get_defined_vars()))
					add_submenu_page($menu, 's2Member API / Tracking', 'API / Tracking', 'create_users', 'ws-plugin--s2member-trk-ops', 'c_ws_plugin__s2member_menu_pages::trk_ops_page');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_els_ops_page', TRUE, get_defined_vars()))
					add_submenu_page($menu, 's2Member API / List Servers', 'API / List Servers', 'create_users', 'ws-plugin--s2member-els-ops', 'c_ws_plugin__s2member_menu_pages::els_ops_page');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_api_ops_page', TRUE, get_defined_vars()))
					add_submenu_page($menu, 's2Member API / Notifications', 'API / Notifications', 'create_users', 'ws-plugin--s2member-api-ops', 'c_ws_plugin__s2member_menu_pages::api_ops_page');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_scripting_page', TRUE, get_defined_vars()))
					add_submenu_page($menu, 's2Member API / Scripting', 'API / Scripting', 'create_users', 'ws-plugin--s2member-scripting', 'c_ws_plugin__s2member_menu_pages::scripting_page');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_divider_4', TRUE, get_defined_vars()))
					add_submenu_page($menu, '', '<span style="display:block; margin:1px 0 1px -5px; padding:0; height:1px; line-height:1px; background:#CCCCCC;"></span>', 'create_users', '#');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_integrations_page', (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()), get_defined_vars()))
					add_submenu_page($menu, 's2Member / Other Integrations', 'Other Integrations', 'create_users', 'ws-plugin--s2member-integrations', 'c_ws_plugin__s2member_menu_pages::integrations_page');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_divider_5', TRUE, get_defined_vars()))
					add_submenu_page($menu, '', '<span style="display:block; margin:1px 0 1px -5px; padding:0; height:1px; line-height:1px; background:#CCCCCC;"></span>', 'create_users', '#');

				if(apply_filters('ws_plugin__s2member_during_add_admin_options_add_logs_page', (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()), get_defined_vars()))
					add_submenu_page($menu, 's2Member Logs', 'Log Files (Debug)', 'create_users', 'ws-plugin--s2member-logs', 'c_ws_plugin__s2member_menu_pages::logs_page');

				do_action('ws_plugin__s2member_during_add_admin_options_additional_pages', get_defined_vars());
			}
			do_action('ws_plugin__s2member_after_add_admin_options', get_defined_vars());
		}

		/**
		 * Adds network option menus / sub-menus.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('network_admin_menu');``
		 */
		public static function add_network_admin_options()
		{
			do_action('ws_plugin__s2member_before_add_network_admin_options', get_defined_vars());

			if(apply_filters('ws_plugin__s2member_during_add_network_admin_options_create_menu_items', TRUE, get_defined_vars()))
			{
				if((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) || apply_filters('ws_plugin__s2member_during_add_network_admin_options_clear_right_side', FALSE, get_defined_vars()))
					$GLOBALS['WS_PLUGIN__']['s2member']['c']['menu_pages'] = array(); // Clear right side.

				$menu = 'ws-plugin--s2member-mms-ops'; // Used below for nesting additional sub-menu pages.

				add_menu_page('s2Member', 's2Member', 'create_users', $menu, 'c_ws_plugin__s2member_menu_pages::mms_ops_page', $GLOBALS['WS_PLUGIN__']['s2member']['c']['dir_url'].'/images/brand-favicon.png');

				add_submenu_page($menu, 's2Member Multisite (Configuration)', 'Multisite (Config)', 'create_users', 'ws-plugin--s2member-mms-ops', 'c_ws_plugin__s2member_menu_pages::mms_ops_page');

				do_action('ws_plugin__s2member_during_add_network_admin_options_additional_pages', get_defined_vars());
			}
			do_action('ws_plugin__s2member_after_add_network_admin_options', get_defined_vars());
		}

		/**
		 * A sort of callback function to add the settings link.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 *
		 * @attaches-to ``add_filter('plugin_action_links');``
		 *
		 * @param array  $actions Expects an existing array of actions links, passed in by the Filter.
		 * @param string $plugin_file Expects path to a plugin file. We need to test against this for s2Member.
		 *
		 * @return array An array of links, Filtered by this routine.
		 */
		public static function _add_settings_link($actions = array(), $plugin_file = '')
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('_ws_plugin__s2member_before_add_settings_link', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			if($plugin_file === $GLOBALS['WS_PLUGIN__']['s2member']['c']['plugin_basename'] && is_array($actions))
			{
				$settings = '<a href="'.esc_attr(admin_url('/admin.php?page=ws-plugin--s2member-gen-ops')).'">Settings</a>';
				array_unshift($actions, apply_filters('ws_plugin__s2member_add_settings_link', $settings, get_defined_vars()));

				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('_ws_plugin__s2member_during_add_settings_link', get_defined_vars());
				unset($__refs, $__v); // Housekeeping.
			}
			return apply_filters('_ws_plugin__s2member_add_settings_link', $actions, get_defined_vars());
		}

		/**
		 * Enqueue scripts for administrative menu pages.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('admin_print_scripts');``
		 */
		public static function add_admin_scripts()
		{
			do_action('ws_plugin__s2member_before_add_admin_scripts', get_defined_vars());

			if(!empty($_GET['page']) && preg_match('/ws-plugin--s2member-/', $_GET['page']))
			{
				wp_enqueue_script('jquery');
				wp_enqueue_script('thickbox');
				wp_enqueue_script('media-upload');
				wp_enqueue_script('jquery-ui-core');
				wp_enqueue_script('jquery-sprintf', $GLOBALS['WS_PLUGIN__']['s2member']['c']['dir_url'].'/includes/jquery/jquery.sprintf/jquery.sprintf-min.js', array('jquery'), c_ws_plugin__s2member_utilities::ver_checksum());
				wp_enqueue_script('jquery-json-ps', $GLOBALS['WS_PLUGIN__']['s2member']['c']['dir_url'].'/includes/jquery/jquery.json-ps/jquery.json-ps-min.js', array('jquery'), c_ws_plugin__s2member_utilities::ver_checksum());
				wp_enqueue_script('jquery-ui-effects', $GLOBALS['WS_PLUGIN__']['s2member']['c']['dir_url'].'/includes/jquery/jquery.ui-effects/jquery.ui-effects-min.js', array('jquery', 'jquery-ui-core'), c_ws_plugin__s2member_utilities::ver_checksum());
				wp_enqueue_script('ws-plugin--s2member-menu-pages', admin_url('admin.php?ws_plugin__s2member_menu_pages_js='.urlencode(mt_rand()), is_ssl() ? 'https' : 'http'), array('jquery', 'thickbox', 'media-upload', 'jquery-sprintf', 'jquery-json-ps', 'jquery-ui-core', 'jquery-ui-effects', 'password-strength-meter'), c_ws_plugin__s2member_utilities::ver_checksum());

				do_action('ws_plugin__s2member_during_add_admin_scripts', get_defined_vars());
			}
			do_action('ws_plugin__s2member_after_add_admin_scripts', get_defined_vars());
		}

		/**
		 * Enqueue styles for administrative menu pages.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('admin_print_styles');``
		 */
		public static function add_admin_styles()
		{
			do_action('ws_plugin__s2member_before_add_admin_styles', get_defined_vars());

			if(!empty($_GET['page']) && preg_match('/ws-plugin--s2member-/', $_GET['page']))
			{
				wp_enqueue_style('thickbox');
				wp_enqueue_style('ws-plugin--s2member-menu-pages', admin_url('admin.php?ws_plugin__s2member_menu_pages_css='.urlencode(mt_rand()), is_ssl() ? 'https' : 'http'), array('thickbox'), c_ws_plugin__s2member_utilities::ver_checksum(), 'all');

				do_action('ws_plugin__s2member_during_add_admin_styles', get_defined_vars());
			}
			do_action('ws_plugin__s2member_after_add_admin_styles', get_defined_vars());
		}

		/**
		 * Handles log file downloads.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 120310
		 */
		public static function log_file_downloader()
		{
			if(!current_user_can('create_users')) return;
			if(is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site())
				return; // We do NOT provide this functionality on Child Blogs of a Blog Farm Network.

			if(!empty($_GET['ws_plugin__s2member_download_log_file']) && is_string($log_file = $_GET['ws_plugin__s2member_download_log_file']) && strpos($log_file, '..') === FALSE && strpos(basename($log_file), '.') !== 0)
				if(!empty($_GET['ws_plugin__s2member_download_log_file_v']) && is_string($nonce = $_GET['ws_plugin__s2member_download_log_file_v']) && wp_verify_nonce($nonce, 'ws-plugin--s2member-download-log-file-v'))
				{
					$logs_dir = $GLOBALS['WS_PLUGIN__']['s2member']['c']['logs_dir'];

					if(file_exists($logs_dir.'/'.$log_file))
						$log_file_contents = file_get_contents($logs_dir.'/'.$log_file);
					else $log_file_contents = '';

					@set_time_limit(0);
					@ini_set('memory_limit', apply_filters('admin_memory_limit', WP_MAX_MEMORY_LIMIT));

					@ini_set('zlib.output_compression', 0);
					if(function_exists('apache_setenv'))
						@apache_setenv('no-gzip', '1');

					while(@ob_end_clean()) ;

					status_header(200); // 200 OK status header.

					header('Content-Encoding: none');
					header('Accept-Ranges: none');
					header('Content-Type: text/plain; charset=UTF-8');
					header('Content-Length: '.strlen($log_file_contents));
					header('Expires: '.gmdate('D, d M Y H:i:s', strtotime('-1 week')).' GMT');
					header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
					header('Cache-Control: no-cache, must-revalidate, max-age=0');
					header('Cache-Control: post-check=0, pre-check=0', FALSE);
					header('Pragma: no-cache');

					header('Content-Disposition: attachment; filename="'.$log_file.'"');

					exit($log_file_contents); // Log file.
				}
		}

		/**
		 * Handles log file downloads (in ZIP format).
		 *
		 * @package s2Member\Menu_Pages
		 * @since 120310
		 */
		public static function logs_zip_downloader()
		{
			if(!current_user_can('create_users')) return;
			if(is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site())
				return; // We do NOT provide this functionality on Child Blogs of a Blog Farm Network.

			if(!empty($_POST['ws_plugin__s2member_logs_download_zip']) && is_string($nonce = $_POST['ws_plugin__s2member_logs_download_zip']) && wp_verify_nonce($nonce, 'ws-plugin--s2member-logs-download-zip'))
			{
				$logs_dir          = $GLOBALS['WS_PLUGIN__']['s2member']['c']['logs_dir'];
				$s2member_logs_zip = $logs_dir.'/'.$_SERVER['HTTP_HOST'].'--s2member-logs.zip';

				if(is_dir($logs_dir)) // Do we have a logs directory?
				{
					include_once ABSPATH.'wp-admin/includes/class-pclzip.php';

					if(file_exists($s2member_logs_zip) && is_writable($s2member_logs_zip))
						unlink($s2member_logs_zip);

					$archive = new PclZip($s2member_logs_zip);
					$archive->create($logs_dir, PCLZIP_OPT_REMOVE_ALL_PATH);
				}
				if(file_exists($s2member_logs_zip))
					$s2member_logs_zip_size = filesize($s2member_logs_zip);
				else $s2member_logs_zip_size = 0;

				@set_time_limit(0);
				@ini_set('memory_limit', apply_filters('admin_memory_limit', WP_MAX_MEMORY_LIMIT));

				@ini_set('zlib.output_compression', 0);
				if(function_exists('apache_setenv'))
					@apache_setenv('no-gzip', '1');

				while(@ob_end_clean()) ;

				status_header(200); // 200 OK status header.

				header('Content-Encoding: none');
				header('Accept-Ranges: none');
				header('Content-Type: application/zip');
				header('Content-Length: '.$s2member_logs_zip_size);
				header('Expires: '.gmdate('D, d M Y H:i:s', strtotime('-1 week')).' GMT');
				header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
				header('Cache-Control: no-cache, must-revalidate, max-age=0');
				header('Cache-Control: post-check=0, pre-check=0', FALSE);
				header('Pragma: no-cache');

				header('Content-Disposition: attachment; filename="'.basename($s2member_logs_zip).'"');

				if($s2member_logs_zip_size && is_resource($resource = fopen($s2member_logs_zip, 'rb')))
				{
					$_bytes_to_read = $s2member_logs_zip_size; // Total bytes we need to read for this file.

					$chunk_size = apply_filters('ws_plugin__s2member_file_downloads_chunk_size', 2097152, get_defined_vars());

					while($_bytes_to_read) // We have bytes to read here.
					{
						$_bytes_to_read -= ($_reading = ($_bytes_to_read > $chunk_size) ? $chunk_size : $_bytes_to_read);
						echo fread($resource, $_reading); // Serve file in chunks (default chunk size is 2MB).
						flush(); // Flush each chunk to the browser as it is served (avoids high memory consumption).
					}
					fclose($resource); // Close file resource handle.
					unset($_bytes_to_read, $_reading); // Housekeeping.
				}
				exit; // Clean exit after serving file.
			}
		}

		/**
		 * Archives existing log files and starts fresh with new logs.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 120310
		 */
		public static function archive_logs_start_fresh()
		{
			if(!current_user_can('create_users')) return;
			if(is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site())
				return; // We do NOT provide this functionality on Child Blogs of a Blog Farm Network.

			if(!empty($_POST['ws_plugin__s2member_logs_archive_start_fresh']) && is_string($nonce = $_POST['ws_plugin__s2member_logs_archive_start_fresh']) && wp_verify_nonce($nonce, 'ws-plugin--s2member-logs-archive-start-fresh'))
			{
				if(is_dir($logs_dir = $GLOBALS['WS_PLUGIN__']['s2member']['c']['logs_dir']))
					foreach(scandir($logs_dir) as $log_file) // Archive existing log files here.
					{
						if(preg_match('/\.log$/', $log_file) && stripos($log_file, '-ARCHIVED-') === FALSE)
							if(is_file($log_dir_file = $logs_dir.'/'.$log_file) && is_writable($log_dir_file))
								if(!rename($log_dir_file, preg_replace('/\.log$/i', '', $log_dir_file).'-ARCHIVED-'.date('m-d-Y').'-'.time().'.log'))
									$error = TRUE;
					}
				if(!empty($error))
					c_ws_plugin__s2member_admin_notices::display_admin_notice('Unknown error when attempting to archive log files. Please check directory permissions.', TRUE);
				else c_ws_plugin__s2member_admin_notices::display_admin_notice('All log files have been archived succesfully.');
			}
		}

		/**
		 * Deletes existing log files and starts fresh with new logs.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 120312
		 */
		public static function delete_logs_start_fresh()
		{
			if(!current_user_can('create_users')) return;
			if(is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site())
				return; // We do NOT provide this functionality on Child Blogs of a Blog Farm Network.

			if(!empty($_POST['ws_plugin__s2member_logs_delete_start_fresh']) && is_string($nonce = $_POST['ws_plugin__s2member_logs_delete_start_fresh']) && wp_verify_nonce($nonce, 'ws-plugin--s2member-logs-delete-start-fresh'))
			{
				if(is_dir($logs_dir = $GLOBALS['WS_PLUGIN__']['s2member']['c']['logs_dir']))
					foreach(scandir($logs_dir) as $log_file) // Delete existing log files here.
					{
						if(preg_match('/\.log$/', $log_file))
							if(is_file($log_dir_file = $logs_dir.'/'.$log_file) && is_writable($log_dir_file))
								if(!unlink($log_dir_file)) $error = TRUE;
					}
				if(!empty($error))
					c_ws_plugin__s2member_admin_notices::display_admin_notice('Unknown error when attempting to delete log files. Please check directory permissions.', TRUE);
				else c_ws_plugin__s2member_admin_notices::display_admin_notice('All log files have been deleted succesfully.');
			}
		}

		/**
		 * Builds and handles the Getting Started page.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 */
		public static function start_page()
		{
			do_action('ws_plugin__s2member_before_start_page', get_defined_vars());

			include_once dirname(dirname(__FILE__)).'/menu-pages/start.inc.php';

			do_action('ws_plugin__s2member_after_start_page', get_defined_vars());
		}

		/**
		 * Builds and handles the Getting Help page.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 151218
		 */
		public static function help_page()
		{
			do_action('ws_plugin__s2member_before_help_page', get_defined_vars());

			include_once dirname(dirname(__FILE__)).'/menu-pages/help.inc.php';

			do_action('ws_plugin__s2member_after_help_page', get_defined_vars());
		}

		/**
		 * Builds and handles the Main Multisite Options page.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 */
		public static function mms_ops_page()
		{
			do_action('ws_plugin__s2member_before_mms_ops_page', get_defined_vars());

			include_once dirname(dirname(__FILE__)).'/menu-pages/mms-ops.inc.php';

			do_action('ws_plugin__s2member_after_mms_ops_page', get_defined_vars());
		}

		/**
		 * Builds and handles the General Options page.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 */
		public static function gen_ops_page()
		{
			do_action('ws_plugin__s2member_before_gen_ops_page', get_defined_vars());

			c_ws_plugin__s2member_menu_pages::update_all_options();

			include_once dirname(dirname(__FILE__)).'/menu-pages/gen-ops.inc.php';

			do_action('ws_plugin__s2member_after_gen_ops_page', get_defined_vars());
		}

		/**
		 * Builds and handles the Restriction Options page.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 */
		public static function res_ops_page()
		{
			do_action('ws_plugin__s2member_before_res_ops_page', get_defined_vars());

			c_ws_plugin__s2member_menu_pages::update_all_options();

			include_once dirname(dirname(__FILE__)).'/menu-pages/res-ops.inc.php';

			do_action('ws_plugin__s2member_after_res_ops_page', get_defined_vars());
		}

		/**
		 * Builds and handles the Paypal Options page.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 */
		public static function paypal_ops_page()
		{
			do_action('ws_plugin__s2member_before_paypal_ops_page', get_defined_vars());

			c_ws_plugin__s2member_menu_pages::update_all_options();

			$logs_dir = $GLOBALS['WS_PLUGIN__']['s2member']['c']['logs_dir'];

			if(!is_dir($logs_dir) && is_writable(dirname(c_ws_plugin__s2member_utils_dirs::strip_dir_app_data($logs_dir))))
				mkdir($logs_dir, 0777, TRUE).clearstatcache();

			$htaccess          = $GLOBALS['WS_PLUGIN__']['s2member']['c']['logs_dir'].'/.htaccess';
			$htaccess_contents = trim(c_ws_plugin__s2member_utilities::evl(file_get_contents($GLOBALS['WS_PLUGIN__']['s2member']['c']['logs_dir_htaccess'])));

			if(is_dir($logs_dir) && is_writable($logs_dir) && !file_exists($htaccess))
				file_put_contents($htaccess, $htaccess_contents).clearstatcache();

			if($GLOBALS['WS_PLUGIN__']['s2member']['o']['gateway_debug_logs']) // Logging enabled?
			{
				if(!is_dir($logs_dir)) // If the security-enabled logs directory does not exist yet.
					c_ws_plugin__s2member_admin_notices::display_admin_notice('The security-enabled logs directory (<code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($logs_dir)).'</code>) does not exist. Please create this directory manually &amp; make it writable (chmod 777).', TRUE);

				else if(!is_writable($logs_dir)) // If the logs directory is not writable yet.
					c_ws_plugin__s2member_admin_notices::display_admin_notice('Permissions error. The security-enabled logs directory (<code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($logs_dir)).'</code>) is not writable. Please make this directory writable (chmod 777).', TRUE);

				if(!file_exists($htaccess)) // If the .htaccess file has not been created yet.
					c_ws_plugin__s2member_admin_notices::display_admin_notice('The .htaccess protection file (<code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($htaccess)).'</code>) does not exist. Please create this file manually. Inside your .htaccess file, add this:<br /><pre>'.esc_html($htaccess_contents).'</pre>', TRUE);

				else if(!preg_match('/deny from all/i', file_get_contents($htaccess))) // Else if the .htaccess file does not offer the required protection.
					c_ws_plugin__s2member_admin_notices::display_admin_notice('Unprotected. The .htaccess protection file (<code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($htaccess)).'</code>) does not contain <code>deny from all</code>. Inside your .htaccess file, add this:<br /><pre>'.esc_html($htaccess_contents).'</pre>', TRUE);
			}
			include_once dirname(dirname(__FILE__)).'/menu-pages/paypal-ops.inc.php';

			do_action('ws_plugin__s2member_after_paypal_ops_page', get_defined_vars());
		}

		/**
		 * Builds and handles the Download Options page.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 */
		public static function down_ops_page()
		{
			do_action('ws_plugin__s2member_before_down_ops_page', get_defined_vars());

			c_ws_plugin__s2member_menu_pages::update_all_options();

			if(!empty($_REQUEST['ws_plugin__s2member_cf_options_reset'])
			   && wp_verify_nonce($_REQUEST['ws_plugin__s2member_cf_options_reset'], 'ws-plugin--s2member-cf-options-reset')
			)
			{
				c_ws_plugin__s2member_files_in::reset_aws_cf_config_values(); // A full CloudFront reset.
				c_ws_plugin__s2member_admin_notices::display_admin_notice('Amazon CloudFront configuration reset successfully.');
			}
			$files_dir = $GLOBALS['WS_PLUGIN__']['s2member']['c']['files_dir'];

			$htaccess          = $GLOBALS['WS_PLUGIN__']['s2member']['c']['files_dir'].'/.htaccess';
			$htaccess_contents = trim(c_ws_plugin__s2member_utilities::evl(file_get_contents($GLOBALS['WS_PLUGIN__']['s2member']['c']['files_dir_htaccess'])));

			$no_gzip_htaccess          = ABSPATH.'.htaccess'; // Always located in the absolute root path for WordPress.
			$no_gzip_htaccess_contents = trim(c_ws_plugin__s2member_utilities::evl(file_get_contents($GLOBALS['WS_PLUGIN__']['s2member']['c']['files_no_gzip_htaccess'])));

			if(!c_ws_plugin__s2member_files::no_gzip_rules_in_root_htaccess()) // If s2Member's GZIP exclusions do NOT yet exist in the root `.htaccess` file.
				c_ws_plugin__s2member_files::write_no_gzip_into_root_htaccess().clearstatcache(); // Handle the root `.htaccess` file now.

			if(!is_dir($files_dir) && is_writable(dirname(c_ws_plugin__s2member_utils_dirs::strip_dir_app_data($files_dir))))
				mkdir($files_dir, 0777, TRUE).clearstatcache(); // Create this directory structure now.

			if(is_dir($files_dir) && is_writable($files_dir) && !file_exists($htaccess)) // This file does NOT exist yet?
				file_put_contents($htaccess, $htaccess_contents).clearstatcache(); // Create the `.htaccess` file now.

			if(!c_ws_plugin__s2member_files::no_gzip_rules_in_root_htaccess()) // If s2Member's GZIP exclusions do NOT yet exist in the root `.htaccess` file.
				c_ws_plugin__s2member_admin_notices::display_admin_notice('Possible GZIP conflict on server. Unable to write GZIP exclusions into root .htaccess file (<code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($no_gzip_htaccess)).'</code>). Please read the panel below: <strong>Preventing GZIP Conflicts</strong>, and add this section yourself:<br /><pre>'.esc_html($no_gzip_htaccess_contents).'</pre>', TRUE);

			if(!is_dir($files_dir)) // If the security-enabled files directory does not exist yet.
				c_ws_plugin__s2member_admin_notices::display_admin_notice('The security-enabled files directory (<code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($files_dir)).'</code>) does not exist. Please create this directory manually.', TRUE);

			if(!file_exists($htaccess)) // If the `.htaccess` file has not been created yet.
				c_ws_plugin__s2member_admin_notices::display_admin_notice('The .htaccess protection file (<code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($htaccess)).'</code>) does not exist. Please create this file manually. Inside your .htaccess file, add this:<br /><pre>'.esc_html($htaccess_contents).'</pre>', TRUE);

			else if(!preg_match('/deny from all/i', file_get_contents($htaccess))) // Else if the `.htaccess` file does not offer the required protection.
				c_ws_plugin__s2member_admin_notices::display_admin_notice('Unprotected. The .htaccess protection file (<code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($htaccess)).'</code>) does not contain <code>deny from all</code>. Inside your .htaccess file, add this:<br /><pre>'.esc_html($htaccess_contents).'</pre>', TRUE);

			if(!empty($_POST['ws_plugin__s2member_amazon_cf_files_auto_configure_distros']) && ($nonce = $_POST['ws_plugin__s2member_amazon_cf_files_auto_configure_distros']) && wp_verify_nonce($nonce, 'ws-plugin--s2member-amazon-cf-files-auto-configure-distros'))
				if(($amazon_cf_auto_configure_distros = c_ws_plugin__s2member_files_in::amazon_cf_auto_configure_distros()) && $amazon_cf_auto_configure_distros['success'])
					c_ws_plugin__s2member_admin_notices::display_admin_notice('Amazon CloudFront Distributions auto-configured successfully. Please allow 30 minutes for initial propagation. <strong>Tip:</strong> If you try to stream over the RTMP protocol using something like the <code>[s2Stream /]</code> shortcode, and you keep getting an "ID Not Found" error while using JW Player; please note that it can <em>sometimes</em> take a full 24 hours for RTMP (i.e., streaming distributions) to begin working properly. This is because there are a few initialization routines that must complete on the AWS side when you first integrate with CloudFront. Please be patient.'.(($GLOBALS['WS_PLUGIN__']['s2member']['o']['amazon_cf_files_distro_downloads_cname']) ? '<br /><em>Downloads Distribution CNAME: <code>'.esc_html($GLOBALS['WS_PLUGIN__']['s2member']['o']['amazon_cf_files_distro_downloads_cname']).' &mdash;&raquo; '.esc_html($GLOBALS['WS_PLUGIN__']['s2member']['o']['amazon_cf_files_distro_downloads_dname']).'</code></em>' : '').(($GLOBALS['WS_PLUGIN__']['s2member']['o']['amazon_cf_files_distro_streaming_cname']) ? '<br /><em>Streaming Distribution CNAME: <code>'.esc_html($GLOBALS['WS_PLUGIN__']['s2member']['o']['amazon_cf_files_distro_streaming_cname']).' &mdash;&raquo; '.esc_html($GLOBALS['WS_PLUGIN__']['s2member']['o']['amazon_cf_files_distro_streaming_dname']).'</code></em>' : ''));
				else // Else there was an error. We need to report this back to the site owner so they can understand what's going on.
					(c_ws_plugin__s2member_menu_pages::$pre_display_errors['cf_files_auto_configure_distros'] = TRUE).c_ws_plugin__s2member_admin_notices::display_admin_notice('Unable to auto-configure Amazon CloudFront Distributions.<br />Error code: <code>'.esc_html($amazon_cf_auto_configure_distros['code']).'</code>. Error Message: <code>'.esc_html($amazon_cf_auto_configure_distros['message']).'</code>', TRUE);

			if(!empty($_POST['ws_plugin__s2member_amazon_s3_files_auto_configure_acls']) && ($nonce = $_POST['ws_plugin__s2member_amazon_s3_files_auto_configure_acls']) && wp_verify_nonce($nonce, 'ws-plugin--s2member-amazon-s3-files-auto-configure-acls'))
				if(($amazon_s3_auto_configure_acls = c_ws_plugin__s2member_files_in::amazon_s3_auto_configure_acls()) && $amazon_s3_auto_configure_acls['success'])
					c_ws_plugin__s2member_admin_notices::display_admin_notice('Amazon S3 ACLs auto-configured successfully.');
				else // Else there was an error. We need to report this back to the site owner so they can understand what's going on.
					(c_ws_plugin__s2member_menu_pages::$pre_display_errors['s3_files_auto_configure_acls'] = TRUE).c_ws_plugin__s2member_admin_notices::display_admin_notice('Unable to auto-configure Amazon S3 ACLs.<br />Error code: <code>'.esc_html($amazon_s3_auto_configure_acls['code']).'</code>. Error Message: <code>'.esc_html($amazon_s3_auto_configure_acls['message']).'</code>', TRUE);

			include_once dirname(dirname(__FILE__)).'/menu-pages/down-ops.inc.php';

			do_action('ws_plugin__s2member_after_down_ops_page', get_defined_vars());
		}

		/**
		 * Builds and handles the API Tracking options page.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 */
		public static function trk_ops_page()
		{
			do_action('ws_plugin__s2member_before_trk_ops_page', get_defined_vars());

			c_ws_plugin__s2member_menu_pages::update_all_options();

			include_once dirname(dirname(__FILE__)).'/menu-pages/trk-ops.inc.php';

			do_action('ws_plugin__s2member_after_trk_ops_page', get_defined_vars());
		}

		/**
		 * Builds and handles the API List Server options page.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 */
		public static function els_ops_page()
		{
			do_action('ws_plugin__s2member_before_els_ops_page', get_defined_vars());

			c_ws_plugin__s2member_menu_pages::update_all_options();

			include_once dirname(dirname(__FILE__)).'/menu-pages/els-ops.inc.php';

			do_action('ws_plugin__s2member_after_els_ops_page', get_defined_vars());
		}

		/**
		 * Builds and handles the API Notifications page.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 */
		public static function api_ops_page()
		{
			do_action('ws_plugin__s2member_before_api_ops_page', get_defined_vars());

			c_ws_plugin__s2member_menu_pages::update_all_options();

			include_once dirname(dirname(__FILE__)).'/menu-pages/api-ops.inc.php';

			do_action('ws_plugin__s2member_after_api_ops_page', get_defined_vars());
		}

		/**
		 * Builds and handles the PayPal Button Generator page.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 */
		public static function paypal_buttons_page()
		{
			do_action('ws_plugin__s2member_before_paypal_buttons_page', get_defined_vars());

			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['paypal_business'] || !$GLOBALS['WS_PLUGIN__']['s2member']['o']['paypal_merchant_id'] || !$GLOBALS['WS_PLUGIN__']['s2member']['o']['paypal_api_username'] || !$GLOBALS['WS_PLUGIN__']['s2member']['o']['paypal_api_password'] || !$GLOBALS['WS_PLUGIN__']['s2member']['o']['paypal_api_signature'])
				c_ws_plugin__s2member_admin_notices::display_admin_notice('Please configure <strong>s2Member → PayPal Options</strong> first. Once all of your PayPal Options are configured; including your Email Address, Merchant ID, API Username, Password, and Signature; return to this page &amp; generate your PayPal Button(s).', TRUE);

			include_once dirname(dirname(__FILE__)).'/menu-pages/paypal-buttons.inc.php';

			do_action('ws_plugin__s2member_after_paypal_buttons_page', get_defined_vars());
		}

		/**
		 * Builds and handles the API Scripting page.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 */
		public static function scripting_page()
		{
			do_action('ws_plugin__s2member_before_scripting_page', get_defined_vars());

			c_ws_plugin__s2member_menu_pages::update_all_options();

			include_once dirname(dirname(__FILE__)).'/menu-pages/scripting.inc.php';

			do_action('ws_plugin__s2member_after_scripting_page', get_defined_vars());
		}

		/**
		 * Builds and handles the Integrations page.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 3.5
		 */
		public static function integrations_page()
		{
			do_action('ws_plugin__s2member_before_integrations_page', get_defined_vars());

			include_once dirname(dirname(__FILE__)).'/menu-pages/integrations.inc.php';

			do_action('ws_plugin__s2member_after_integrations_page', get_defined_vars());
		}

		/**
		 * Builds and handles the Logs page.
		 *
		 * @package s2Member\Menu_Pages
		 * @since 120310
		 */
		public static function logs_page()
		{
			do_action('ws_plugin__s2member_before_logs_page', get_defined_vars());

			c_ws_plugin__s2member_menu_pages::update_all_options();
			c_ws_plugin__s2member_menu_pages::archive_logs_start_fresh();
			c_ws_plugin__s2member_menu_pages::delete_logs_start_fresh();

			$logs_dir = $GLOBALS['WS_PLUGIN__']['s2member']['c']['logs_dir'];

			if(!is_dir($logs_dir) && is_writable(dirname(c_ws_plugin__s2member_utils_dirs::strip_dir_app_data($logs_dir))))
				mkdir($logs_dir, 0777, TRUE).clearstatcache();

			$htaccess          = $GLOBALS['WS_PLUGIN__']['s2member']['c']['logs_dir'].'/.htaccess';
			$htaccess_contents = trim(c_ws_plugin__s2member_utilities::evl(file_get_contents($GLOBALS['WS_PLUGIN__']['s2member']['c']['logs_dir_htaccess'])));

			if(is_dir($logs_dir) && is_writable($logs_dir) && !file_exists($htaccess))
				file_put_contents($htaccess, $htaccess_contents).clearstatcache();

			if(!is_dir($logs_dir)) // If the security-enabled logs directory does not exist yet.
				c_ws_plugin__s2member_admin_notices::display_admin_notice('The security-enabled logs directory (<code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($logs_dir)).'</code>) does not exist. Please create this directory manually &amp; make it writable (chmod 777).', TRUE);

			else if(!is_writable($logs_dir)) // If the logs directory is not writable yet.
				c_ws_plugin__s2member_admin_notices::display_admin_notice('Permissions error. The security-enabled logs directory (<code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($logs_dir)).'</code>) is not writable. Please make this directory writable (chmod 777).', TRUE);

			if(!file_exists($htaccess)) // If the .htaccess file has not been created yet.
				c_ws_plugin__s2member_admin_notices::display_admin_notice('The .htaccess protection file (<code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($htaccess)).'</code>) does not exist. Please create this file manually. Inside your .htaccess file, add this:<br /><pre>'.esc_html($htaccess_contents).'</pre>', TRUE);

			else if(!preg_match('/deny from all/i', file_get_contents($htaccess))) // Else if the .htaccess file does not offer the required protection.
				c_ws_plugin__s2member_admin_notices::display_admin_notice('Unprotected. The .htaccess protection file (<code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($htaccess)).'</code>) does not contain <code>deny from all</code>. Inside your .htaccess file, add this:<br /><pre>'.esc_html($htaccess_contents).'</pre>', TRUE);

			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['gateway_debug_logs']) // Logging disabled?
				c_ws_plugin__s2member_admin_notices::display_admin_notice('Logging is currently disabled by your configuration.');

			include_once dirname(dirname(__FILE__)).'/menu-pages/logs.inc.php';

			do_action('ws_plugin__s2member_after_logs_page', get_defined_vars());
		}
	}
}
