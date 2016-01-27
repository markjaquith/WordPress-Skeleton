<?php
/**
 * AWeber
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

if(!class_exists('c_ws_plugin__s2member_aweber'))
{
	/**
	 * AWeber
	 *
	 * @since 141004
	 * @package s2Member\List_Servers
	 */
	class c_ws_plugin__s2member_aweber extends c_ws_plugin__s2member_list_server_base
	{
		/**
		 * API instance.
		 *
		 * @since 141004
		 * @package s2Member\List_Servers
		 *
		 * @return AWeberAPI|null AWeber API instance.
		 */
		public static function aw_api()
		{
			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['aweber_api_key'])
				return NULL; // Not possible.

			if(!class_exists('AWeberAPI')) // Include the AWeber API class here.
				include_once dirname(dirname(__FILE__)).'/externals/aweber/aweber_api.php';

			if(count($key_parts = explode('|', $GLOBALS['WS_PLUGIN__']['s2member']['o']['aweber_api_key'])) < 5)
				return NULL; // It's an invalid API key; i.e., authorization code.

			list($consumerKey, $consumerSecret, $requestToken, $tokenSecret, $verifier) = $key_parts;
			$internal_api_key_checksum = md5($consumerKey.$consumerSecret.$requestToken.$tokenSecret.$verifier);

			if(count($internal_key_parts = explode('|', $GLOBALS['WS_PLUGIN__']['s2member']['o']['aweber_internal_api_key'])) >= 5)
				list(, , , , $checksum) = $internal_key_parts; // Only need checksum for now.

			if(empty($checksum) || $checksum !== $internal_api_key_checksum)
			{
				try // Catch any AWeber exceptions that occur here.
				{
					$aw_api                     = new AWeberAPI($consumerKey, $consumerSecret);
					$aw_api->user->requestToken = $requestToken;
					$aw_api->user->tokenSecret  = $tokenSecret;
					$aw_api->user->verifier     = $verifier;

					if(!is_array($accessToken = $aw_api->getAccessToken()) || count($accessToken) < 2)
						return NULL; // Not possible.

					list($accessTokenKey, $accessTokenSecret) = $accessToken;
					if(!$accessTokenKey || !$accessTokenSecret)
						return NULL; // Not possible.

					$GLOBALS['WS_PLUGIN__']['s2member']['o']['aweber_internal_api_key'] = $internal_api_key
						= $consumerKey.'|'.$consumerSecret.'|'.$accessTokenKey.'|'.$accessTokenSecret.'|'.$internal_api_key_checksum;

					c_ws_plugin__s2member_menu_pages::update_all_options
					(array('ws_plugin__s2member_aweber_internal_api_key' => $internal_api_key),
					 TRUE, FALSE, FALSE, FALSE, FALSE);
				}
				catch(Exception $exception)
				{
					return NULL; // API initialization failure.
				}
			}
			if(count($internal_key_parts = explode('|', $GLOBALS['WS_PLUGIN__']['s2member']['o']['aweber_internal_api_key'])) < 5)
				return NULL; // It's an invalid internal API key. Cannot continue.

			list($consumerKey, $consumerSecret, $accessTokenKey, $accessTokenSecret, $checksum) = $internal_key_parts;

			try // Catch any AWeber exceptions that occur here.
			{
				$aw_api             = new AWeberAPI($consumerKey, $consumerSecret);
				$aw_api->___account = $aw_api->getAccount($accessTokenKey, $accessTokenSecret);

				return $aw_api; // AWeberAPI instance.
			}
			catch(Exception $exception)
			{
				return NULL; // API initialization failure.
			}
		}

		/**
		 * Checks a countable obj.
		 *
		 * @since 141004
		 * @package s2Member\List_Servers
		 *
		 * @param Countable $countable Countable obj.
		 *
		 * @return bool True if has `count()` > `0`.
		 */
		public static function count($countable)
		{
			return $countable instanceof Countable && $countable->count();
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
			if($GLOBALS['WS_PLUGIN__']['s2member']['o']['aweber_api_type'] === 'email')
				return c_ws_plugin__s2member_aweber_e::subscribe($args);

			if(!($args = self::validate_args($args)))
				return FALSE; // Invalid args.

			if(!$args->opt_in) // Double check.
				return FALSE; // Must say explicitly.

			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['aweber_api_key'])
				return FALSE; // Not possible.

			if(empty($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$args->level.'_aweber_list_ids']))
				return FALSE; // No list configured at this level.

			if(!($aw_api = self::aw_api()) || !@$aw_api->___account->id)
				return FALSE; // Unable to acquire API instance.

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
				if(!$_aw['list'] || !$_aw['list_id'])
					continue; // List missing.

				try // Catch any AWeber exceptions that occur here.
				{
					if(self::count($_aw['foundLists'] = $aw_api->___account->lists->find(array('name' => $_aw['list_id']))))
						if(($_aw['listUrl'] = '/accounts/'.$aw_api->___account->id.'/lists/'.$_aw['foundLists'][0]->id))
							if(($_aw['list'] = $aw_api->___account->loadFromUrl($_aw['listUrl'])))
							{
								$_aw['subscriber_props']                = array(
									'name'          => $args->name,
									'email'         => $args->email,
									'ip_address'    => $args->ip,
									'ad_tracking'   => 's2-'.(is_multisite() && !is_main_site()
											? $GLOBALS['current_blog']->domain.$GLOBALS['current_blog']->path
											: $_SERVER['HTTP_HOST']),
									'custom_fields' => apply_filters('ws_plugin__s2member_aweber_custom_fields_array', array(), get_defined_vars()),
									'status'        => !$args->double_opt_in ? 'subscribed' : '', // Try to bypass confirmation?
								);
								$_aw['subscriber_props']['name']        = substr($_aw['subscriber_props']['name'], 0, 60);
								$_aw['subscriber_props']['email']       = substr($_aw['subscriber_props']['email'], 0, 50);
								$_aw['subscriber_props']['ip_address']  = substr($_aw['subscriber_props']['ip_address'], 0, 60);
								$_aw['subscriber_props']['ad_tracking'] = substr($_aw['subscriber_props']['ad_tracking'], 0, 20);

								if(!filter_var($_aw['subscriber_props']['ip_address'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
									$_aw['subscriber_props']['ip_address'] = ''; // IPv4 addresses only.

								foreach($_aw['subscriber_props'] as $_key => $_value)
									if(!$_value && $_value !== FALSE) // Empty?
										unset($_aw['subscriber_props'][$_key]);
								unset($_key, $_value); // Housekeeping.

								$_aw['findSubscriber'] = array('email' => $args->email);
								if(self::count($_aw['foundSubscribers'] = $_aw['list']->subscribers->find($_aw['findSubscriber']))
								   && $_aw['foundSubscribers'][0]->status !== 'unconfirmed' // i.e., `subscribed|unsubscribed`.
								) // Cannot modify an `unconfirmed` subscriber.
								{
									/** @var AWeberEntry $_existing_subscriber */
									$_existing_subscriber         = $_aw['foundSubscribers'][0];
									$_existing_subscriber->status = 'subscribed'; // Subscribe.

									foreach($_aw['subscriber_props'] as $_key => $_value)
										if(in_array($_key, array('name', 'ad_tracking', 'custom_fields'), TRUE))
											$_existing_subscriber->{$_key} = $_value;
									unset($_key, $_value); // Housekeeping.

									if($_existing_subscriber->save() && ($_aw['subscriber'] = $_existing_subscriber))
										$_aw['api_success'] = $success = TRUE; // Flag this as `TRUE`; assists with return value below.

									unset($_existing_subscriber); // Housekeeping.
								}
								else if(($_aw['subscriber'] = $_aw['list']->subscribers->create($_aw['subscriber_props'])) && @$_aw['subscriber']->id)
									$_aw['api_success'] = $success = TRUE; // Flag this as `TRUE`; assists with return value below.
							}
				}
				catch(Exception $exception)
				{
					$_aw['exception'] = $exception;
				}
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
			if($GLOBALS['WS_PLUGIN__']['s2member']['o']['aweber_api_type'] === 'email')
				return c_ws_plugin__s2member_aweber_e::unsubscribe($args);

			if(!($args = self::validate_args($args)))
				return FALSE; // Invalid args.

			if(!$args->opt_out) // Double check.
				return FALSE; // Must say explicitly.

			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['aweber_api_key'])
				return FALSE; // Not possible.

			if(empty($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$args->level.'_aweber_list_ids']))
				return FALSE; // No list configured at this level.

			if(!($aw_api = self::aw_api()) || !@$aw_api->___account->id)
				return FALSE; // Unable to acquire API instance.

			$aw_level_list_ids = $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$args->level.'_aweber_list_ids'];

			foreach(preg_split('/['."\r\n\t".'\s;,]+/', $aw_level_list_ids, NULL, PREG_SPLIT_NO_EMPTY) as $_aw_list)
			{
				$_aw = array(
					'args'       => $args,
					'function'   => __FUNCTION__,
					'list'       => trim($_aw_list),
					'list_id'    => trim($_aw_list),
					'api_method' => 'listUnsubscribe'
				);
				if(!$_aw['list'] || !$_aw['list_id'])
					continue; // List missing.

				try // Catch any AWeber exceptions that occur here.
				{
					if(self::count($_aw['foundLists'] = $aw_api->___account->lists->find(array('name' => $_aw['list_id']))))
						if(($_aw['listUrl'] = '/accounts/'.$aw_api->___account->id.'/lists/'.$_aw['foundLists'][0]->id))
							if(($_aw['list'] = $aw_api->___account->loadFromUrl($_aw['listUrl'])))
							{
								$_aw['findSubscriber'] = array('email' => $args->email, 'status' => 'subscribed');
								if(self::count($_aw['foundSubscribers'] = $_aw['list']->subscribers->find($_aw['findSubscriber'])))
								{
									/** @var AWeberEntry $_existing_subscriber */
									$_existing_subscriber         = $_aw['foundSubscribers'][0];
									$_existing_subscriber->status = 'unsubscribed'; // Unsubscribe.

									if($_existing_subscriber->save() && ($_aw['subscriber'] = $_existing_subscriber))
										$_aw['api_success'] = $success = TRUE; // Flag this as `TRUE`; assists with return value below.

									unset($_existing_subscriber); // Housekeeping.
								}
							}
				}
				catch(Exception $exception)
				{
					$_aw['exception'] = $exception;
				}
				c_ws_plugin__s2member_utils_logs::log_entry('aweber-api', $_aw);
			}
			unset($_aw_list, $_aw); // Just a little housekeeping.

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
			if($GLOBALS['WS_PLUGIN__']['s2member']['o']['aweber_api_type'] === 'email')
				return c_ws_plugin__s2member_aweber_e::transition($old_args, $new_args);

			return self::unsubscribe($old_args) && self::subscribe($new_args);
		}
	}
}
