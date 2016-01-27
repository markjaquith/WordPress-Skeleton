<?php
/**
 * Installation routines for s2Member.
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
 * @package s2Member\Installation
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_installation'))
{
	/**
	 * Installation routines for s2Member.
	 *
	 * @package s2Member\Installation
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_installation
	{
		/**
		 * Activation routines for s2Member.
		 *
		 * @package s2Member\Installation
		 * @since 3.5
		 *
		 * @param string $reactivation_reason Optional.
		 */
		public static function activate($reactivation_reason = '')
		{
			global $wpdb;
			/** @var $wpdb wpdb */
			global $current_site, $current_blog; // Multisite.

			do_action('ws_plugin__s2member_before_activation', get_defined_vars());

			c_ws_plugin__s2member_roles_caps::config_roles(); // Config Roles/Caps.
			update_option('ws_plugin__s2member_activated_levels', $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']);

			if(!is_dir($files_dir = $GLOBALS['WS_PLUGIN__']['s2member']['c']['files_dir']))
				if(is_writable(dirname(c_ws_plugin__s2member_utils_dirs::strip_dir_app_data($files_dir))))
					mkdir($files_dir, 0777, TRUE);

			if(is_dir($files_dir) && is_writable($files_dir))
				if(!is_file($htaccess = $files_dir.'/.htaccess') || !apply_filters('ws_plugin__s2member_preserve_files_dir_htaccess', !is_writable($files_dir.'/.htaccess'), get_defined_vars()))
					file_put_contents($htaccess, trim(c_ws_plugin__s2member_utilities::evl(file_get_contents($GLOBALS['WS_PLUGIN__']['s2member']['c']['files_dir_htaccess']))));

			c_ws_plugin__s2member_files::write_no_gzip_into_root_htaccess(); // Handle the root `.htaccess` file as well now, for GZIP exclusions.

			if(!is_dir($logs_dir = $GLOBALS['WS_PLUGIN__']['s2member']['c']['logs_dir']))
				if(is_writable(dirname(c_ws_plugin__s2member_utils_dirs::strip_dir_app_data($logs_dir))))
					mkdir($logs_dir, 0777, TRUE);

			if(is_dir($logs_dir) && is_writable($logs_dir))
				if(!is_file($htaccess = $logs_dir.'/.htaccess') || !apply_filters('ws_plugin__s2member_preserve_logs_dir_htaccess', !is_writable($logs_dir.'/.htaccess'), get_defined_vars()))
					file_put_contents($htaccess, trim(c_ws_plugin__s2member_utilities::evl(file_get_contents($GLOBALS['WS_PLUGIN__']['s2member']['c']['logs_dir_htaccess']))));

			(!is_array(get_option('ws_plugin__s2member_cache'))) ? update_option('ws_plugin__s2member_cache', array()) : NULL;
			(!is_array(get_option('ws_plugin__s2member_notices'))) ? update_option('ws_plugin__s2member_notices', array()) : NULL;
			(!is_array(get_option('ws_plugin__s2member_options'))) ? update_option('ws_plugin__s2member_options', array()) : NULL;
			(!is_numeric(get_option('ws_plugin__s2member_configured'))) ? update_option('ws_plugin__s2member_configured', '0') : NULL;

			if($GLOBALS['WS_PLUGIN__']['s2member']['c']['configured']) // If we are re-activating.
			{
				$v = get_option('ws_plugin__s2member_activated_version'); // Currently.

				if(!$v || !version_compare($v, '3.2', '>=')) // Needs to be upgraded?
					// Version 3.2 is where `meta_key` names were changed. They're prefixed now.
				{
					$like = "`meta_key` LIKE 's2member\\_%' AND `meta_key` NOT LIKE '%s2member\\_originating\\_blog%'";
					$wpdb->query("UPDATE `".$wpdb->usermeta."` SET `meta_key` = CONCAT('".$wpdb->prefix."', `meta_key`) WHERE ".$like);
				}
				if(!$v || !version_compare($v, '3.2.5', '>=')) // Needs to be upgraded?
					// Version 3.2.5 is where transient names were changed. They're prefixed now.
				{
					$wpdb->query("DELETE FROM `".$wpdb->options."` WHERE `option_name` LIKE '\\_transient\\_%'");
				}
				if(!$v || !version_compare($v, '3.2.6', '>=')) // Needs to be upgraded?
					// Version 3.2.6 fixed `s2member_ccaps_req` being stored empty and/or w/ one empty element in the array.
				{
					$wpdb->query("DELETE FROM `".$wpdb->postmeta."` WHERE `meta_key` = 's2member_ccaps_req' AND `meta_value` IN('','a:0:{}','a:1:{i:0;s:0:\"\";}')");
				}
				if(!$v || !version_compare($v, '110912', '>=') && $GLOBALS['WS_PLUGIN__']['s2member']['o']['filter_wp_query'] === array('all'))
					// s2Member v110912 changed the way the 'all' option for Alternative Views was handled.
				{
					$notice = '<strong>IMPORTANT:</strong> This version of s2Member changes the way your <code>Alternative View Protections</code> work. Please review your options under: <strong>s2Member → Restriction Options → Alternative View Protections</strong>.';
					c_ws_plugin__s2member_admin_notices::enqueue_admin_notice($notice, array('blog|network:plugins.php', 'blog|network:ws-plugin--s2member-start', 'blog|network:ws-plugin--s2member-mms-ops', 'blog|network:ws-plugin--s2member-gen-ops', 'blog|network:ws-plugin--s2member-res-ops'));
				}
				if($v && version_compare($v, '130316', '<=')) // This version disables logging by default.
				{
					c_ws_plugin__s2member_menu_pages::update_all_options(array('ws_plugin__s2member_gateway_debug_logs' => '0', 'ws_plugin__s2member_gateway_debug_logs_extensive' => '0'), TRUE, FALSE, FALSE, FALSE, FALSE);

					$notice = '<strong>IMPORTANT:</strong> This version of s2Member disables s2Member\'s debug logging by default (for added security). Please see: <a href="'.esc_attr(admin_url('/admin.php?page=ws-plugin--s2member-logs')).'">s2Member → Log Files (Debug) → Configuration</a> for further details.';
					c_ws_plugin__s2member_admin_notices::enqueue_admin_notice($notice, array('blog|network:plugins.php', 'blog|network:ws-plugin--s2member-start', 'blog|network:ws-plugin--s2member-mms-ops', 'blog|network:ws-plugin--s2member-gen-ops', 'blog|network:ws-plugin--s2member-res-ops'));

					$notice = '<strong>IMPORTANT / Regarding s2Member Security Badges:</strong> If debug logging is enabled, your site will no longer qualify for an s2Member Security Badge until you disable logging (and you must also download, and then delete any existing log files from the past). Please see KB Article: <a href="http://www.s2member.com/kb/security-badges/" target="_blank" rel="external">s2Member Security Badges</a> for further details. If you have existing s2Member log files, you will need to delete those files from the server before your s2Member Security Badge can be re-enabled. s2Member stores log files here: <code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($GLOBALS['WS_PLUGIN__']['s2member']['c']['logs_dir'])).'</code>. See also: <a href="'.esc_attr(admin_url('/admin.php?page=ws-plugin--s2member-logs')).'">s2Member → Log Files (Debug) → Configuration</a> for further details.';
					c_ws_plugin__s2member_admin_notices::enqueue_admin_notice($notice, array('blog|network:plugins.php', 'blog|network:ws-plugin--s2member-start', 'blog|network:ws-plugin--s2member-mms-ops', 'blog|network:ws-plugin--s2member-gen-ops', 'blog|network:ws-plugin--s2member-res-ops'));
				}
				if($v && version_compare($v, '140128', '<=')) // This version introduces support for partial refunds.
				{
					if($GLOBALS['WS_PLUGIN__']['s2member']['o']['triggers_immediate_eot'] === 'refunds,reversals') // `refunds,reversals` => `refunds,partial_refunds,reversals`
						c_ws_plugin__s2member_menu_pages::update_all_options(array('ws_plugin__s2member_triggers_immediate_eot' => 'refunds,partial_refunds,reversals'), TRUE, FALSE, FALSE, FALSE, FALSE);
				}
				$notice = '<strong>s2Member</strong> has been <strong>reactivated</strong>, with '.(($reactivation_reason === 'levels') ? '<code>'.esc_html($GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']).'</code> Membership Levels' : 'the latest version').'.<br />';
				$notice .= 'You now have version '.esc_html(WS_PLUGIN__S2MEMBER_VERSION).'. Your existing configuration remains.';

				if(!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()) // No Changelog on a Multisite Blog Farm.
					$notice .= '<br />Have fun, <a href="'.esc_attr(c_ws_plugin__s2member_readmes::parse_readme_value('Changelog URI')).'" target="_blank">read the Changelog</a>, and make some money! :-)';

				c_ws_plugin__s2member_admin_notices::enqueue_admin_notice($notice, array('blog|network:plugins.php', 'blog|network:ws-plugin--s2member-start', 'blog|network:ws-plugin--s2member-mms-ops', 'blog|network:ws-plugin--s2member-gen-ops', 'blog|network:ws-plugin--s2member-res-ops'));
			}
			else // Otherwise (initial activation); we'll help the Site Owner out by giving them a link to the Getting Started section.
			{
				$notice = '<strong>Note:</strong> s2Member adds some new data columns to your list of Users/Members. If your list gets overcrowded, please use the <strong>Screen Options</strong> tab <em>(upper right-hand corner)</em>. With WordPress Screen Options, you can add/remove specific data columns; thereby making the most important data easier to read. For example, if you create Custom Registration/Profile Fields with s2Member, those Custom Fields will result in new data columns; which can cause your list of Users/Members to become nearly unreadable. So just use the Screen Options tab to clean things up.';
				c_ws_plugin__s2member_admin_notices::enqueue_admin_notice($notice, 'blog:users.php', FALSE, FALSE, TRUE);

				$notice = '<strong>s2Member v'.esc_html(WS_PLUGIN__S2MEMBER_VERSION).' has been activated. Nice work!</strong><br />';
				$notice .= 'We suggest a review of the "Getting Started" page to familiarize yourself with s2Member terminology and basic configuration.<br />&#8627;&nbsp; Would you like to review now? &nbsp;&nbsp;&nbsp; <a href="'.esc_attr(admin_url('/admin.php?page=ws-plugin--s2member-start')).'"><strong>Yes (explain)</strong></a>';

				c_ws_plugin__s2member_admin_notices::enqueue_admin_notice($notice, array('blog|network:plugins.php', 'blog|network:ws-plugin--s2member-start', 'blog|network:ws-plugin--s2member-mms-ops', 'blog|network:ws-plugin--s2member-gen-ops', 'blog|network:ws-plugin--s2member-res-ops'));
			}
			if(is_multisite() && is_main_site()) // Network activation routines.
			{
				$wpdb->query("INSERT INTO `".$wpdb->usermeta."` (`user_id`, `meta_key`, `meta_value`) SELECT `ID`, 's2member_originating_blog', '".esc_sql($current_site->blog_id)."' FROM `".$wpdb->users."` WHERE `ID` NOT IN (SELECT `user_id` FROM `".$wpdb->usermeta."` WHERE `meta_key` = 's2member_originating_blog')");

				$notice = '<strong>Multisite Network</strong> updated automatically by <strong>s2Member</strong> v'.esc_html(WS_PLUGIN__S2MEMBER_VERSION).'.<br />';
				$notice .= 'You\'ll want to configure s2Member\'s Multisite options now.<br />';
				$notice .= 'In the Dashboard for your Main Site, see:<br />';
				$notice .= '<strong>s2Member → Multisite (Config)</strong>.';

				c_ws_plugin__s2member_admin_notices::enqueue_admin_notice($notice, array('blog|network:plugins.php', 'blog|network:ws-plugin--s2member-start', 'blog|network:ws-plugin--s2member-mms-ops', 'blog|network:ws-plugin--s2member-gen-ops', 'blog|network:ws-plugin--s2member-res-ops'));

				update_site_option('ws_plugin__s2member_options', (array)get_option('ws_plugin__s2member_options'));

				update_option('ws_plugin__s2member_activated_mms_version', WS_PLUGIN__S2MEMBER_VERSION);
			}
			update_option('ws_plugin__s2member_activated_version', WS_PLUGIN__S2MEMBER_VERSION);

			do_action('ws_plugin__s2member_after_activation', get_defined_vars());
		}

		/**
		 * Deactivation routines for s2Member.
		 *
		 * @package s2Member\Installation
		 * @since 3.5
		 */
		public static function deactivate()
		{
			global $wpdb;
			/** @var $wpdb wpdb */
			global $current_site, $current_blog; // Multisite.

			do_action('ws_plugin__s2member_before_deactivation', get_defined_vars());
			do_action('ws_plugin__s2member_after_deactivation', get_defined_vars());
		}

		/**
		 * Uninstall routines for s2Member.
		 *
		 * @package s2Member\Installation
		 * @since 3.5
		 */
		public static function uninstall()
		{
			global $wpdb;
			/** @var $wpdb wpdb */
			global $current_site, $current_blog; // Multisite.

			do_action('ws_plugin__s2member_before_uninstall', get_defined_vars());

			if($GLOBALS['WS_PLUGIN__']['s2member']['o']['run_uninstall_routines'])
			{
				c_ws_plugin__s2member_roles_caps::unlink_roles();

				c_ws_plugin__s2member_files::remove_no_gzip_from_root_htaccess();

				if(is_dir($files_dir = $GLOBALS['WS_PLUGIN__']['s2member']['c']['files_dir']))
				{
					if(file_exists($htaccess = $files_dir.'/.htaccess'))
						if(is_writable($htaccess))
							unlink($htaccess);

					@rmdir($files_dir).@rmdir(c_ws_plugin__s2member_utils_dirs::strip_dir_app_data($files_dir));
				}
				if(is_dir($logs_dir = $GLOBALS['WS_PLUGIN__']['s2member']['c']['logs_dir']))
				{
					foreach(scandir($logs_dir) as $log_file)
						if(is_file($log_file = $logs_dir.'/'.$log_file))
							if(is_writable($log_file))
								unlink($log_file);

					@rmdir($logs_dir).@rmdir(c_ws_plugin__s2member_utils_dirs::strip_dir_app_data($logs_dir));
				}
				delete_option('ws_plugin__s2member_cache');
				delete_option('ws_plugin__s2member_notices');
				delete_option('ws_plugin__s2member_options');
				delete_option('ws_plugin__s2member_configured');
				delete_option('ws_plugin__s2member_activated_levels');
				delete_option('ws_plugin__s2member_activated_version');
				delete_option('ws_plugin__s2member_activated_mms_version');

				if(is_multisite() && is_main_site() /* Site options? */)
					delete_site_option('ws_plugin__s2member_options');

				$wpdb->query("DELETE FROM `".$wpdb->postmeta."` WHERE `meta_key` LIKE '".esc_sql(c_ws_plugin__s2member_utils_strings::like_escape('s2m_'))."%'");
				$wpdb->query("DELETE FROM `".$wpdb->postmeta."` WHERE `meta_key` LIKE '%".esc_sql(c_ws_plugin__s2member_utils_strings::like_escape('_s2m_'))."%'");
				$wpdb->query("DELETE FROM `".$wpdb->postmeta."` WHERE `meta_key` LIKE '%".esc_sql(c_ws_plugin__s2member_utils_strings::like_escape('s2member_'))."%'");

				$wpdb->query("DELETE FROM `".$wpdb->usermeta."` WHERE `meta_key` LIKE '".esc_sql(c_ws_plugin__s2member_utils_strings::like_escape('s2m_'))."%'");
				$wpdb->query("DELETE FROM `".$wpdb->usermeta."` WHERE `meta_key` LIKE '%".esc_sql(c_ws_plugin__s2member_utils_strings::like_escape('_s2m_'))."%'");
				$wpdb->query("DELETE FROM `".$wpdb->usermeta."` WHERE `meta_key` LIKE '%".esc_sql(c_ws_plugin__s2member_utils_strings::like_escape('s2member_'))."%'");

				$wpdb->query("DELETE FROM `".$wpdb->options."` WHERE `option_name` LIKE '".esc_sql(c_ws_plugin__s2member_utils_strings::like_escape('s2m_'))."%'");
				$wpdb->query("DELETE FROM `".$wpdb->options."` WHERE `option_name` LIKE '%".esc_sql(c_ws_plugin__s2member_utils_strings::like_escape('_s2m_'))."%'");
				$wpdb->query("DELETE FROM `".$wpdb->options."` WHERE `option_name` LIKE '%".esc_sql(c_ws_plugin__s2member_utils_strings::like_escape('s2member_'))."%'");

				do_action('ws_plugin__s2member_during_uninstall', get_defined_vars());
			}
			do_action('ws_plugin__s2member_after_uninstall', get_defined_vars());
		}

		/**
		 * Disallow automatic updates of s2Member w/ Pro is installed.
		 *
		 * @package s2Member\Installation
		 * @since 150717
		 *
		 * @attaches-to `auto_update_plugin` filter.
		 */
		public static function auto_update_filter($update = NULL, $item = NULL)
		{
			if(is_object($item) && !empty($item->slug) && $item->slug === 's2member')
				if(c_ws_plugin__s2member_utils_conds::pro_is_installed())
					$update = FALSE; // Disallow.

			return $update; // Filter through.
		}
	}
}
