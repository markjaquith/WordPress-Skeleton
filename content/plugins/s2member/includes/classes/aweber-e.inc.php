<?php
/**
 * AWeber (Old via Email)
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

if(!class_exists('c_ws_plugin__s2member_aweber_e'))
{
	/**
	 * AWeber (Old via Email)
	 *
	 * @since 141004
	 * @package s2Member\List_Servers
	 */
	class c_ws_plugin__s2member_aweber_e extends c_ws_plugin__s2member_list_server_base
	{
		/**
		 * Subscribe.
		 *
		 * @since 141004
		 * @package s2Member\List_Servers
		 *
		 * @param array $args Input arguments.
		 *
		 * @return bool True if successful.
		 */
		public static function subscribe($args)
		{
			if(!($args = self::validate_args($args)))
				return FALSE; // Invalid args.

			if(!$args->opt_in) // Double check.
				return FALSE; // Must say explicitly.

			if(empty($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$args->level.'_aweber_list_ids']))
				return FALSE; // No list configured at this level.

			$aw_level_list_ids = $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$args->level.'_aweber_list_ids'];

			foreach(preg_split('/['."\r\n\t".'\s;,]+/', $aw_level_list_ids, NULL, PREG_SPLIT_NO_EMPTY) as $_aw_list)
			{
				$_aw = array(
					'args'       => $args,
					'function'   => __FUNCTION__,
					'list'       => trim($_aw_list),
					'list_id'    => trim($_aw_list),
					'api_method' => 'listSubscribe'
				);
				if(!$_aw['list']) continue; // List missing.

				$_aw['bcc']            = apply_filters('ws_plugin__s2member_aweber_bcc', FALSE, get_defined_vars());
				$_aw['pass_inclusion'] = apply_filters('ws_plugin__s2member_aweber_pass_inclusion', FALSE, get_defined_vars()) && $args->pass ? "\n".'Pass: '.$args->pass : '';

				if($_aw['wp_mail_response'] = wp_mail($_aw['list_id'].'@aweber.com', // Converts to email address @aweber.com.
					($_aw['wp_mail_sbj'] = apply_filters('ws_plugin__s2member_aweber_sbj', 's2Member Subscription Request', get_defined_vars())), // These filters make it possible to customize these emails.
					($_aw['wp_mail_msg'] = apply_filters('ws_plugin__s2member_aweber_msg', 's2Member Subscription Request'."\n".'s2Member w/ PayPal Email ID'."\n".'Ad Tracking: s2Member-'.(is_multisite() && !is_main_site() ? $GLOBALS['current_blog']->domain.$GLOBALS['current_blog']->path : $_SERVER['HTTP_HOST'])."\n".'EMail Address: '.$args->email."\n".'Buyer: '.$args->name."\n".'Full Name: '.$args->name."\n".'First Name: '.$args->fname."\n".'Last Name: '.$args->lname."\n".'IP Address: '.$args->ip."\n".'User ID: '.$args->user_id."\n".'Login: '.$args->login.$_aw['pass_inclusion']."\n".'Role: '.$args->role."\n".'Level: '.$args->level."\n".'CCaps: '.$args->ccaps."\n".' - end.', get_defined_vars())),
					($_aw['wp_mail_headers'] = 'From: "'.preg_replace('/"/', "'", $GLOBALS['WS_PLUGIN__']['s2member']['o']['reg_email_from_name']).'" <'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['reg_email_from_email'].'>'.($_aw['bcc'] ? "\r\n".'Bcc: '.$_aw['bcc'] : '')."\r\n".'Content-Type: text/plain; charset=UTF-8'))
				) $_aw['wp_mail_success'] = $success = TRUE; // Flag this as `TRUE`; assists with return value below.

				c_ws_plugin__s2member_utils_logs::log_entry('aweber-api', $_aw);
			}
			unset($_aw_list, $_aw); // Just a little housekeeping.

			return !empty($success); // If one suceeds.
		}

		/**
		 * Unsubscribe.
		 *
		 * @since 141004
		 * @package s2Member\List_Servers
		 *
		 * @param array $args Input arguments.
		 *
		 * @return bool True if successful.
		 */
		public static function unsubscribe($args)
		{
			if(!($args = self::validate_args($args)))
				return FALSE; // Invalid args.

			if(!$args->opt_out) // Double check.
				return FALSE; // Must say explicitly.

			if(empty($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$args->level.'_aweber_list_ids']))
				return FALSE; // No list configured at this level.

			$aw_level_list_ids = $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$args->level.'_aweber_list_ids'];

			$email_configs_were_on = c_ws_plugin__s2member_email_configs::email_config_status();
			if(!$email_configs_were_on) c_ws_plugin__s2member_email_configs::email_config(); // MUST be ON for removal requests.
			// `From:` address MUST match AWeber account. See: <http://www.aweber.com/faq/questions/62/Can+I+Unsubscribe+People+Via+Email%3F>.

			foreach(preg_split('/['."\r\n\t".'\s;,]+/', $aw_level_list_ids, NULL, PREG_SPLIT_NO_EMPTY) as $_aw_list)
			{
				$_aw = array(
					'args'       => $args,
					'function'   => __FUNCTION__,
					'list'       => trim($_aw_list),
					'list_id'    => trim($_aw_list),
					'api_method' => 'listUnsubscribe'
				);
				if(!$_aw['list']) continue; // List missing.

				$_aw['removal_bcc'] = apply_filters('ws_plugin__s2member_aweber_removal_bcc', FALSE, get_defined_vars());

				if($_aw['wp_mail_response'] = wp_mail($_aw['list_id'].'@aweber.com', // Converts to email address @aweber.com.
					($_aw['wp_mail_sbj'] = apply_filters('ws_plugin__s2member_aweber_removal_sbj', 'REMOVE#'.$args->email.'#s2Member#'.$_aw['list_id'], get_defined_vars())), // Bug fix. AWeber does not like dots (possibly other chars) in the Ad Tracking field. Now using just: `s2Member`.
					($_aw['wp_mail_msg'] = 'REMOVE'), ($_aw['wp_mail_headers'] = 'From: "'.preg_replace('/"/', "'", $GLOBALS['WS_PLUGIN__']['s2member']['o']['reg_email_from_name']).'" <'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['reg_email_from_email'].'>'.($_aw['removal_bcc'] ? "\r\n".'Bcc: '.$_aw['removal_bcc'] : '')."\r\n".'Content-Type: text/plain; charset=UTF-8'))
				) $_aw['wp_mail_success'] = $success = TRUE; // Flag this as `TRUE`; assists with return value below.

				c_ws_plugin__s2member_utils_logs::log_entry('aweber-api', $_aw);
			}
			unset($_aw_list, $_aw); // Just a little housekeeping.

			if(!$email_configs_were_on) // Turn them off now?
				c_ws_plugin__s2member_email_configs::email_config_release();

			return !empty($success); // If one suceeds.
		}

		/**
		 * Transition.
		 *
		 * @since 141004
		 * @package s2Member\List_Servers
		 *
		 * @param array $old_args Input arguments.
		 * @param array $new_args Input arguments.
		 *
		 * @return bool True if successful.
		 */
		public static function transition($old_args, $new_args)
		{
			return self::unsubscribe($old_args) && self::subscribe($new_args);
		}
	}
}