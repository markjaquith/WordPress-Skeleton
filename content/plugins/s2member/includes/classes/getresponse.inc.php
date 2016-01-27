<?php
/**
 * GetResponse
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

if(!class_exists('c_ws_plugin__s2member_getresponse'))
{
	/**
	 * GetResponse
	 *
	 * @since 141004
	 * @package s2Member\List_Servers
	 */
	class c_ws_plugin__s2member_getresponse extends c_ws_plugin__s2member_list_server_base
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

			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['getresponse_api_key'])
				return FALSE; // Not possible.

			if(empty($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$args->level.'_getresponse_list_ids']))
				return FALSE; // No list configured at this level.

			$gr_level_list_ids = $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$args->level.'_getresponse_list_ids'];

			extract((array)$args); // Extract the arguments for back compat. w/ filters that relied upon them.

			foreach(preg_split('/['."\r\n\t".';,]+/', $gr_level_list_ids, NULL, PREG_SPLIT_NO_EMPTY) as $_gr_list)
			{
				$_gr = array(
					'args'       => $args,
					'function'   => __FUNCTION__,
					'list'       => trim($_gr_list),
					'list_id'    => trim($_gr_list),
					'api_method' => 'add_contact'
				);
				if(!$_gr['list'] || !$_gr['list_id'])
					continue; // List missing.

				$_gr['api_method']  = 'get_contacts'; // Check if exists.
				$_gr['api_headers'] = array('Content-Type' => 'application/json');
				$_gr['api_params']  = array($GLOBALS['WS_PLUGIN__']['s2member']['o']['getresponse_api_key'],
				                            array('campaigns' => array($_gr['list_id']), 'email' => array('EQUALS' => $args->email)));
				$_gr['api_request'] = json_encode(array('method' => $_gr['api_method'], 'params' => $_gr['api_params'], 'id' => uniqid('', TRUE)));
				if(is_object($_gr['api_response'] = json_decode(c_ws_plugin__s2member_utils_urls::remote('https://api2.getresponse.com', $_gr['api_request'], array('headers' => $_gr['api_headers'])))) && empty($_gr['api_response']->error)
				   && ($_gr['api_response_contact_ids'] = array_keys((array)$_gr['api_response']->result)) && ($_gr['api_response_contact_id'] = $_gr['api_response_contact_ids'][0])
				) // They already exist on this list, we need to update the existing subscription here instead of adding a new one.
				{
					$_gr['api_method']  = 'set_contact_name'; // Update.
					$_gr['api_headers'] = array('Content-Type' => 'application/json');
					$_gr['api_params']  = array($GLOBALS['WS_PLUGIN__']['s2member']['o']['getresponse_api_key'],
					                            array('contact' => $_gr['api_response_contact_id'], 'name' => $args->name));
					$_gr['api_request'] = json_encode(array('method' => $_gr['api_method'], 'params' => $_gr['api_params'], 'id' => uniqid('', TRUE)));
					if(is_object($_gr['api_response'] = json_decode(c_ws_plugin__s2member_utils_urls::remote('https://api2.getresponse.com', $_gr['api_request'], array('headers' => $_gr['api_headers'])))) && empty($_gr['api_response']->error))
					{
						$_gr['api_method']  = 'set_contact_customs'; // Update.
						$_gr['api_headers'] = array('Content-Type' => 'application/json');
						$_gr['api_params']  = array($GLOBALS['WS_PLUGIN__']['s2member']['o']['getresponse_api_key'],
						                            array('contact' => $_gr['api_response_contact_id'], 'customs' => apply_filters('ws_plugin__s2member_getresponse_customs_array', array(), get_defined_vars())));
						$_gr['api_request'] = json_encode(array('method' => $_gr['api_method'], 'params' => $_gr['api_params'], 'id' => uniqid('', TRUE)));
						if(is_object($_gr['api_response'] = json_decode(c_ws_plugin__s2member_utils_urls::remote('https://api2.getresponse.com', $_gr['api_request'], array('headers' => $_gr['api_headers'])))) && empty($_gr['api_response']->error))
							$_gr['api_success'] = $success = TRUE; // Flag this as `TRUE`; assists with return value below.
					}
				}
				else // Create a new contact; i.e., they do not exist yet.
				{
					$_gr['api_method']  = 'add_contact'; // Add new contact.
					$_gr['api_headers'] = array('Content-Type' => 'application/json');
					$_gr['api_params']  = array($GLOBALS['WS_PLUGIN__']['s2member']['o']['getresponse_api_key'],
					                            array('name'     => $args->name, 'email' => $args->email, 'ip' => $args->ip,
					                                  'campaign' => $_gr['list_id'], 'action' => 'standard', 'cycle_day' => 0,
					                                  'customs'  => apply_filters('ws_plugin__s2member_getresponse_customs_array', array(), get_defined_vars())));
					if(!$_gr['api_params'][1]['ip'] || $_gr['api_params'][1]['ip'] === 'unknown') unset($_gr['api_params'][1]['ip']);

					$_gr['api_request'] = json_encode(array('method' => $_gr['api_method'], 'params' => $_gr['api_params'], 'id' => uniqid('', TRUE)));
					if(is_object($_gr['api_response'] = json_decode(c_ws_plugin__s2member_utils_urls::remote('https://api2.getresponse.com', $_gr['api_request'], array('headers' => $_gr['api_headers'])))) && empty($_gr['api_response']->error) && $_gr['api_response']->result->queued)
						$_gr['api_success'] = $success = TRUE; // Flag this as `TRUE`; assists with return value below.
				}
				c_ws_plugin__s2member_utils_logs::log_entry('getresponse-api', $_gr);
			}
			unset($_gr_list, $_gr); // Just a little housekeeping.

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

			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['getresponse_api_key'])
				return FALSE; // Not possible.

			if(empty($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$args->level.'_getresponse_list_ids']))
				return FALSE; // No list configured at this level.

			$gr_level_list_ids = $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$args->level.'_getresponse_list_ids'];

			extract((array)$args); // Extract the arguments for back compat. w/ filters that relied upon them.

			foreach(preg_split('/['."\r\n\t".';,]+/', $gr_level_list_ids, NULL, PREG_SPLIT_NO_EMPTY) as $_gr_list)
			{
				$_gr = array(
					'args'       => $args,
					'function'   => __FUNCTION__,
					'list'       => trim($_gr_list),
					'list_id'    => trim($_gr_list),
					'api_method' => 'delete_contact'
				);
				if(!$_gr['list'] || !$_gr['list_id'])
					continue; // List missing.

				$_gr['api_method']  = 'get_contacts'; // Check if exists.
				$_gr['api_headers'] = array('Content-Type' => 'application/json');
				$_gr['api_params']  = array($GLOBALS['WS_PLUGIN__']['s2member']['o']['getresponse_api_key'],
				                            array('campaigns' => array($_gr['list_id']), 'email' => array('EQUALS' => $args->email)));
				$_gr['api_request'] = json_encode(array('method' => $_gr['api_method'], 'params' => $_gr['api_params'], 'id' => uniqid('', TRUE)));
				if(is_object($_gr['api_response'] = json_decode(c_ws_plugin__s2member_utils_urls::remote('https://api2.getresponse.com', $_gr['api_request'], array('headers' => $_gr['api_headers'])))) && empty($_gr['api_response']->error)
				   && ($_gr['api_response_contact_ids'] = array_keys((array)$_gr['api_response']->result)) && ($_gr['api_response_contact_id'] = $_gr['api_response_contact_ids'][0])
				)// They exist on this list, so we can remove theme here via `delete_contact`.
				{
					$_gr['api_method']  = 'delete_contact'; // Delete.
					$_gr['api_headers'] = array('Content-Type' => 'application/json');
					$_gr['api_params']  = array($GLOBALS['WS_PLUGIN__']['s2member']['o']['getresponse_api_key'],
					                            array('contact' => $_gr['api_response_contact_id']));
					$_gr['api_request'] = json_encode(array('method' => $_gr['api_method'], 'params' => $_gr['api_params'], 'id' => uniqid('', TRUE)));
					if(is_object($_gr['api_response'] = json_decode(c_ws_plugin__s2member_utils_urls::remote('https://api2.getresponse.com', $_gr['api_request'], array('headers' => $_gr['api_headers'])))) && empty($_gr['api_response']->error) && $_gr['api_response']->result->deleted)
						$_gr['api_success'] = $success = TRUE; // Flag this as `TRUE`; assists with return value below.
				}
				c_ws_plugin__s2member_utils_logs::log_entry('getresponse-api', $_gr);
			}
			unset($_gr_list, $_gr); // Just a little housekeeping.

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