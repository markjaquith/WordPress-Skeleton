<?php
/**
 * MailChimp
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

if(!class_exists('c_ws_plugin__s2member_mailchimp'))
{
	/**
	 * MailChimp
	 *
	 * @since 141004
	 * @package s2Member\List_Servers
	 */
	class c_ws_plugin__s2member_mailchimp extends c_ws_plugin__s2member_list_server_base
	{
		/**
		 * API instance.
		 *
		 * @since 141004
		 * @package s2Member\List_Servers
		 *
		 * @return Mailchimp|null MailChimp API instance.
		 */
		public static function mc_api()
		{
			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['mailchimp_api_key'])
				return NULL; // Not possible.

			if(!class_exists('Mailchimp')) // Include the MailChimp API class here.
				include_once dirname(dirname(__FILE__)).'/externals/mailchimp/Mailchimp.php';
			return new Mailchimp($GLOBALS['WS_PLUGIN__']['s2member']['o']['mailchimp_api_key'], array('timeout' => 30));
		}

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

			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['mailchimp_api_key'])
				return FALSE; // Not possible.

			if(empty($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$args->level.'_mailchimp_list_ids']))
				return FALSE; // No list configured at this level.

			if(!($mc_api = self::mc_api())) return FALSE; // Unable to acquire API instance.

			$mc_level_list_ids = $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$args->level.'_mailchimp_list_ids'];

			extract((array)$args); // Extract the arguments for back compat. w/ filters that relied upon them.

			foreach(preg_split('/['."\r\n\t".';,]+/', $mc_level_list_ids, NULL, PREG_SPLIT_NO_EMPTY) as $_mc_list)
			{
				$_mc = array(
					'args'           => $args,
					'function'       => __FUNCTION__,
					'list'           => trim($_mc_list),
					'list_id'        => trim($_mc_list),
					'api_method'     => 'listSubscribe',
					'api_properties' => $mc_api
				);
				if(!$_mc['list'] || !$_mc['list_id'])
					continue; // List missing.

				if(strpos($_mc['list'], '::') !== FALSE) // Contains Interest Groups?
				{
					list($_mc['list_id'], $_mc['interest_groups_title'], $_mc['interest_groups']) = preg_split('/\:\:/', $_mc['list'], 3);

					if(($_mc['interest_groups_title'] = trim($_mc['interest_groups_title'])))
						if(($_mc['interest_groups'] = $_mc['interest_groups'] ? preg_split('/\|/', trim($_mc['interest_groups']), NULL, PREG_SPLIT_NO_EMPTY) : array()))
							$_mc['interest_groups'] = array('GROUPINGS' => array(array('name' => $_mc['interest_groups_title'], 'groups' => $_mc['interest_groups'])));

					if(!$_mc['list_id']) continue; // List ID is missing now; after parsing interest groups.
				}
				$_mc['merge_array'] = array('MERGE1' => $args->fname, 'MERGE2' => $args->lname, 'OPTIN_IP' => $args->ip, 'OPTIN_TIME' => date('Y-m-d H:i:s'));
				$_mc['merge_array'] = !empty($_mc['interest_groups']) ? array_merge($_mc['merge_array'], $_mc['interest_groups']) : $_mc['merge_array'];
				$_mc['merge_array'] = apply_filters('ws_plugin__s2member_mailchimp_array', $_mc['merge_array'], get_defined_vars()); // Deprecated!
				// Filter: `ws_plugin__s2member_mailchimp_array` deprecated in v110523. Please use Filter: `ws_plugin__s2member_mailchimp_merge_array`.

				try // Catch any Mailchimp exceptions that occur here.
				{
					if(($_mc['api_response'] = $mc_api->lists->subscribe($_mc['list_id'], array('email' => $args->email), // See: `http://apidocs.mailchimp.com/` for full details.
							($_mc['api_merge_array'] = apply_filters('ws_plugin__s2member_mailchimp_merge_array', $_mc['merge_array'], get_defined_vars())), // Configured merge array above.
							($_mc['api_email_type'] = apply_filters('ws_plugin__s2member_mailchimp_email_type', 'html', get_defined_vars())), // Type of email to receive (i.e., html,text,mobile).
							($_mc['api_double_optin'] = apply_filters('ws_plugin__s2member_mailchimp_double_optin', $args->double_opt_in, get_defined_vars())), // Abuse of this may cause account suspension.
							($_mc['api_update_existing'] = apply_filters('ws_plugin__s2member_mailchimp_update_existing', TRUE, get_defined_vars())), // Existing subscribers should be updated with this?
							($_mc['api_replace_interests'] = apply_filters('ws_plugin__s2member_mailchimp_replace_interests', TRUE, get_defined_vars())), // Replace interest groups? (only if provided).
							($_mc['api_send_welcome'] = apply_filters('ws_plugin__s2member_mailchimp_send_welcome', FALSE, get_defined_vars()))))
					   && !empty($_mc['api_response']['email'])
					) $_mc['api_success'] = $success = TRUE;
				}
				catch(Exception $exception)
				{
					$_mc['exception'] = $exception;
				}
				c_ws_plugin__s2member_utils_logs::log_entry('mailchimp-api', $_mc);
			}
			unset($_mc_list, $_mc); // Just a little housekeeping.

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

			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['mailchimp_api_key'])
				return FALSE; // Not possible.

			if(empty($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$args->level.'_mailchimp_list_ids']))
				return FALSE; // No list configured at this level.

			if(!($mc_api = self::mc_api())) return FALSE; // Unable to acquire API instance.

			$mc_level_list_ids = $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$args->level.'_mailchimp_list_ids'];

			extract((array)$args); // Extract the arguments for back compat. w/ filters that relied upon them.

			foreach(preg_split('/['."\r\n\t".';,]+/', $mc_level_list_ids, NULL, PREG_SPLIT_NO_EMPTY) as $_mc_list)
			{
				$_mc = array(
					'args'           => $args,
					'function'       => __FUNCTION__,
					'list'           => trim($_mc_list),
					'list_id'        => trim($_mc_list),
					'api_method'     => 'listUnsubscribe',
					'api_properties' => $mc_api
				);
				if(!$_mc['list'] || !$_mc['list_id'])
					continue; // List missing.

				if(strpos($_mc['list'], '::') !== FALSE) // Contains Interest Groups?
				{
					list($_mc['list_id'], $_mc['interest_groups_title'], $_mc['interest_groups']) = preg_split('/\:\:/', $_mc['list'], 3);

					if(($_mc['interest_groups_title'] = trim($_mc['interest_groups_title'])))
						if(($_mc['interest_groups'] = $_mc['interest_groups'] ? preg_split('/\|/', trim($_mc['interest_groups']), NULL, PREG_SPLIT_NO_EMPTY) : array()))
							$_mc['interest_groups'] = array('GROUPINGS' => array(array('name' => $_mc['interest_groups_title'], 'groups' => $_mc['interest_groups'])));

					if(!$_mc['list_id']) continue; // List ID is missing now; after parsing interest groups.
				}
				try // Catch any Mailchimp exceptions that occur here.
				{
					if(($_mc['api_response'] = $mc_api->lists->unsubscribe($_mc['list_id'], array('email' => $args->email), // See: `http://apidocs.mailchimp.com/`.
							($_mc['api_delete_member'] = apply_filters('ws_plugin__s2member_mailchimp_removal_delete_member', FALSE, get_defined_vars())), // Completely delete?
							($_mc['api_send_goodbye'] = apply_filters('ws_plugin__s2member_mailchimp_removal_send_goodbye', FALSE, get_defined_vars())), // Send goodbye letter?
							($_mc['api_send_notify'] = apply_filters('ws_plugin__s2member_mailchimp_removal_send_notify', FALSE, get_defined_vars()))))
					   && !empty($_mc['api_response']['complete'])
					) $_mc['api_success'] = $success = TRUE;
				}
				catch(Exception $exception)
				{
					$_mc['exception'] = $exception;
				}
				c_ws_plugin__s2member_utils_logs::log_entry('mailchimp-api', $_mc);
			}
			unset($_mc_list, $_mc); // Just a little housekeeping.

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
