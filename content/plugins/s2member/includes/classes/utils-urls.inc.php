<?php
/**
* URL utilities.
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
* @package s2Member\Utilities
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_utils_urls'))
	{
		/**
		* URL utilities.
		*
		* @package s2Member\Utilities
		* @since 3.5
		*/
		class c_ws_plugin__s2member_utils_urls
			{
				/**
				* Builds a WordPress signup URL to `/wp-signup.php`.
				*
				* @package s2Member\Utilities
				* @since 3.5
				*
				* @return string Full URL to `/wp-signup.php`.
				*/
				public static function wp_signup_url()
					{
						return apply_filters('wp_signup_location', site_url('/wp-signup.php'));
					}

				/**
				* Builds a WordPress registration URL to `/wp-login.php?action=register`.
				*
				* @package s2Member\Utilities
				* @since 3.5
				*
				* @return string Full URL to `/wp-login.php?action=register`.
				*/
				public static function wp_register_url()
					{
						return apply_filters('wp_register_location', add_query_arg('action', urlencode('register'), wp_login_url()), get_defined_vars());
					}

				/**
				* Builds a BuddyPress registration URL to `/register`.
				*
				* @package s2Member\Utilities
				* @since 111009
				*
				* @return str|bool Full URL to `/register`, if BuddyPress is installed; else false.
				*/
				public static function bp_register_url()
					{
						if(c_ws_plugin__s2member_utils_conds::bp_is_installed())
							return home_url(function_exists('bp_get_signup_slug') ? bp_get_signup_slug().'/' : BP_REGISTER_SLUG.'/');

						return false;
					}

				/**
				* Filters content redirection status *(uses 302s for browsers)*.
				*
				* @package s2Member\Utilities
				* @since 3.5
				*
				* @attaches-to ``add_filter('ws_plugin__s2member_content_redirect_status');``
				*
				* @param int|string $status A numeric redirection status code.
				* @return int|str A numeric status redirection code, possibly modified to a value of `302`.
				*
				* @see https://en.wikipedia.org/wiki/Web_browser_engine
				*/
				public static function redirect_browsers_using_302_status($status = FALSE)
					{
						$engines = 'msie|trident|gecko|webkit|presto|konqueror|playstation';

						if((int)$status === 301 && !empty($_SERVER['HTTP_USER_AGENT']))
							if(($is_browser = preg_match('/('.$engines.')[\/ ]([0-9\.]+)/i', $_SERVER['HTTP_USER_AGENT'])))
								$status = 302; // Use 302 for browser engines.

						return $status;
					}

				/**
				* Encodes all types of amperands to `amp;`, for use in XHTML code.
				*
				* Note however, this is usually NOT necessary. Just use WordPress ``esc_html()`` or ``esc_attr()``.
				*
				* @package s2Member\Utilities
				* @since 111106
				*
				* @param string $url_uri_query A full URL, a partial URI, or just the query string.
				* @return string A full URL, a partial URI, or just the query string; after having been encoded by this routine.
				*/
				public static function e_amps($url_uri_query = FALSE)
					{
						return str_replace('&', '&amp;', c_ws_plugin__s2member_utils_urls::n_amps((string)$url_uri_query));
					}

				/**
				* Normalizes amperands to `&` when working with URLs, URIs, and/or query strings.
				*
				* @package s2Member\Utilities
				* @since 111106
				*
				* @param string $url_uri_query A full URL, a partial URI, or just the query string.
				* @return string A full URL, a partial URI, or just the query string; after having been normalized by this routine.
				*/
				public static function n_amps($url_uri_query = FALSE)
					{
						$amps = implode('|', array_keys(c_ws_plugin__s2member_utils_strings::$ampersand_entities));

						return preg_replace('/(?:'.$amps.')/', '&', (string)$url_uri_query);
					}

				/**
				* Parses out a full valid URI, from either a full URL, or a partial URI.
				*
				* Uses {@link s2Member\Utilities\c_ws_plugin__s2member_utils_urls::parse_url()}.
				*
				* @package s2Member\Utilities
				* @since 3.5
				*
				* @param string $url_uri Either a full URL, or a partial URI.
				* @return string A valid URI, starting with `/` on success, else an empty string.
				*/
				public static function parse_uri($url_uri = FALSE)
					{
						if(is_string($url_uri) && is_array($parse = c_ws_plugin__s2member_utils_urls::parse_url($url_uri)))
							{
								$parse['path'] = !empty($parse['path']) ? (strpos($parse['path'], '/') === 0 ? $parse['path'] : '/'.$parse['path']) : '/';

								return !empty($parse['query']) ? $parse['path'].'?'.$parse['query'] : $parse['path'];
							}
						return ''; // Default return value.
					}

				/**
				* Parses a URL/URI with same args as PHP's ``parse_url()`` function.
				*
				* This works around issues with this PHP function in versions prior to 5.3.8.
				*
				* @package s2Member\Utilities
				* @since 111017
				*
				* @param string $url_uri Either a full URL, or a partial URI to parse.
				* @param bool|int $component Optional. See PHP documentation on ``parse_url()`` function.
				* @param bool $clean_path Defaults to true. s2Member will cleanup any return array `path`.
				* @return str|array|bool The return value from PHP's ``parse_url()`` function.
				* 	However, if ``$component`` is passed, s2Member forces a string return.
				*/
				public static function parse_url($url_uri = FALSE, $component = FALSE, $clean_path = TRUE)
					{
						$component = $component === FALSE || $component === -1 ? -1 : $component;

						if(is_string($url_uri) && strpos($url_uri, '?') !== FALSE)
							{
								list($_, $query) = preg_split /* Split @ query string marker. */('/\?/', $url_uri, 2);
								$query = /* See: <https://bugs.php.net/bug.php?id=38143>. */ str_replace('://', urlencode('://'), $query);
								$url_uri = /* Put it all back together again, after the above modifications. */ $_.'?'.$query;
								unset($_, $query); // A little housekeeping here. Unset these vars.
							}
						$parse = @parse_url($url_uri, $component); // Let PHP work its magic via ``parse_url()``.

						if($clean_path && is_array($parse) && !empty($parse['path']) && is_string($parse['path']))
							$parse['path'] = preg_replace('/\/+/', '/', $parse['path']);

						return $component !== -1 ? (string)$parse : $parse;
					}

				/**
				* Responsible for all remote communications processed by s2Member.
				*
				* Uses ``wp_remote_request()`` through the `WP_Http` class.
				*
				* @package s2Member\Utilities
				* @since 3.5
				*
				* @param string $url Full URL with possible query string parameters.
				* @param string|array $post_body Optional. Either a string of POST data, or an array.
				* @param array $args Optional. An array of additional arguments used by ``wp_remote_request()``.
				* @param bool $return_array Optional. If true, instead of a string, we return an array with elements:
				* 	`code` *(http response code)*, `message` *(http response message)*, `headers` *(an array of lowercase headers)*, `body` *(the response body string)*, `response` *(full response array)*.
				* @return str|array|bool Requested response str|array from remote location *(see ``$return_array`` parameter )*; else (bool)`false` on failure.
				*/
				public static function remote($url = FALSE, $post_body = FALSE, $args = FALSE, $return_array = FALSE)
					{
						if(!$url || !is_string($url))
							return false;

						$args = !is_array($args) ? array() : $args;

						$args['s2member']    = WS_PLUGIN__S2MEMBER_VERSION; // s2Member connection.
						$args['httpversion'] = !isset($args['httpversion']) ? '1.1' : $args['httpversion'];
						$args['user-agent']  = !isset($args['user-agent']) ? 's2Member v'.WS_PLUGIN__S2MEMBER_VERSION.'; '.home_url() : $args['user-agent'];

						if(!isset($args['sslverify']) && c_ws_plugin__s2member_utils_conds::is_localhost())
							$args['sslverify'] = FALSE; // Force this off on localhost installs.

						else if(!isset($args['sslverify']) && strcasecmp(self::parse_url($url, PHP_URL_HOST), $_SERVER['HTTP_HOST']) === 0)
							$args['sslverify'] = FALSE; // Don't require verification when posting to self.

						if($post_body && (is_array($post_body) || is_string($post_body)))
							$args = array_merge($args, array('method' => 'POST', 'body' => $post_body));

						if(!empty($args['method']) && strcasecmp((string)$args['method'], 'DELETE') === 0 && version_compare(get_bloginfo('version'), '3.4', '<'))
							add_filter('use_curl_transport', '__return_false', /* ID via priority. */ 111209554);

						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action('ws_plugin__s2member_before_wp_remote_request', get_defined_vars());
						unset($__refs, $__v); // Housekeeping.

						$response = wp_remote_request($url, $args);

						remove_filter('use_curl_transport', '__return_false', 111209554);

						if($return_array && !is_wp_error($response) && is_array($response))
							{
								$a = array('code' => (int)wp_remote_retrieve_response_code($response));
								$a = array_merge($a, array('message' => wp_remote_retrieve_response_message($response)));
								$a = array_merge($a, array('headers' => wp_remote_retrieve_headers($response)));
								$a = array_merge($a, array('body' => wp_remote_retrieve_body($response)));
								$a = array_merge($a, array('response' => $response));

								return $a; // Return array w/ ``$response`` too.
							}
						if(!is_wp_error($response) && is_array($response))
							return wp_remote_retrieve_body($response);

						return false; // Remote request failed, return false.
					}

				/**
				* Shortens a long URL, based on s2Member configuration.
				*
				* @package s2Member\Utilities
				* @since 111002
				*
				* @param string $url A full/long URL to be shortened.
				* @param string $api_sp Optional. A specific URL shortening API to use. Defaults to that which is configured in the s2Member Dashboard. Normally `tiny_url`, by default.
				* @param bool $try_backups Defaults to true. If a failure occurs with the first API, we'll try others until we have success.
				* @return str|bool The shortened URL on success, else false on failure.
				*/
				public static function shorten($url = '', $api_sp = '', $try_backups = TRUE)
					{
						$url                              = $url && is_string($url) ? $url : FALSE;
						$apis                             = array('tiny_url', 'bitly', 'goo_gl'); // Supported APIs.
						$api_sp                           = $api_sp && is_string($api_sp) ? strtolower($api_sp) : FALSE;
						$default_url_shortener            = $GLOBALS['WS_PLUGIN__']['s2member']['o']['default_url_shortener'];
						$default_url_shortener_key        = $GLOBALS['WS_PLUGIN__']['s2member']['o']['default_url_shortener_key'];
						$default_custom_str_url_shortener = $GLOBALS['WS_PLUGIN__']['s2member']['o']['default_custom_str_url_shortener'];
						$api                              = $api_sp ? $api_sp : $default_url_shortener;

						if($url && $api) // If specific, use it. Otherwise, try customs, else use the default shortening API.
							{
								if(!$api_sp // If not a specific API, give filters a chance to shorten it here.
										&& ($custom_url = trim(apply_filters('ws_plugin__s2member_url_shorten', FALSE, get_defined_vars())))
										&& stripos($custom_url, 'http') === 0)
									return ($shorter_url = $custom_url);

								else if(!$api_sp // If not specific, try custom settings.
										&& stripos($default_custom_str_url_shortener, 'http') === 0
										&& ($custom_url = trim(self::remote(str_ireplace(array('%%s2_long_url%%', '%%s2_long_url_md5%%'), array(rawurlencode($url), urlencode(md5($url))), $default_custom_str_url_shortener))))
										&& stripos($custom_url, 'http') === 0)
									return ($shorter_url = $custom_url);

								else if($api === 'tiny_url' // Using the TinyURL API in this case?
										&& ($tiny_url = trim(self::remote('http://tinyurl.com/api-create.php?url='.rawurlencode($url))))
										&& stripos($tiny_url, 'http') === 0)
									return ($shorter_url = $tiny_url);

								else if($api === 'bitly' // Using the Bitly API in this case?
										&& ($bitly_endpoint          = 'https://api-ssl.bitly.com/v3/shorten')
										&& ($bitly_endpoint_key      = $default_url_shortener_key) // Must be configured by site owner.
										&& ($bitly_endpoint          = add_query_arg('access_token', urlencode($bitly_endpoint_key), $bitly_endpoint))
										&& ($bitly_endpoint          = add_query_arg('longUrl', urlencode($url), $bitly_endpoint))
										&& ($bitly_response          = json_decode(trim(self::remote($bitly_endpoint))))
										&& !empty($bitly_response->data->url) && stripos($bitly_url = $bitly_response->data->url, 'http') === 0)
									return ($shorter_url = $bitly_url);

								else if($api === 'goo_gl' // Using the Google API in this case?
										&& ($goo_gl_endpoint          = 'https://www.googleapis.com/urlshortener/v1/url')
										&& ($goo_gl_endpoint_headers  = array('headers' => array('Content-Type' => 'application/json')))
										&& ($goo_gl_endpoint_key      = $default_url_shortener_key) // Must be configured by site owner.
										&& ($goo_gl_endpoint          = add_query_arg('key', urlencode($goo_gl_endpoint_key), $goo_gl_endpoint))
										&& ($goo_gl_response          = json_decode(trim(self::remote($goo_gl_endpoint, json_encode(array('longUrl' => $url)), $goo_gl_endpoint_headers))))
										&& !empty($goo_gl_response->id) && stripos($goo_gl_url = $goo_gl_response->id, 'http') === 0)
									return ($shorter_url = $goo_gl_url);

								else if($try_backups && count($apis) > 1) // Try backups?
									{
										foreach(array_diff($apis, array($api)) as $_backup_api)
											if(($_backup_api_url = self::shorten($url, $_backup_api, FALSE)))
												return ($shorter_url = $_backup_api_url);
										unset($_backup_api, $_backup_api_url); // Housekeeping.
									}
							}
						return FALSE; // Default return value.
					}
				/**
				* Removes all s2Member-generated signatures from a full URL, a partial URI, or just a query string.
				*
				* @package s2Member\Utilities
				* @since 111106
				*
				* @param string $url_uri_query A full URL, a partial URI, or just the query string; to remove s2Member-generated signatures from.
				* @param string $sig_var Optional. The name of the s2Member-generated signature variable. Defaults to `_s2member_sig`.
				* @return string A full URL, a partial URI, or just the query string; without any s2Member-generated signatures.
				*/
				public static function remove_s2member_sigs($url_uri_query = FALSE, $sig_var = FALSE)
					{
						$url_uri_query = c_ws_plugin__s2member_utils_strings::trim((string)$url_uri_query, false, '?&=');
						$sig_var       = ($sig_var && is_string($sig_var)) ? $sig_var : '_s2member_sig';
						$sigs          = array_unique(array($sig_var, '_s2member_sig'));

						return trim(remove_query_arg($sigs, $url_uri_query), '?&=');
					}

				/**
				* Adds an s2Member-generated signature onto a full URL, a partial URI, or just a query string.
				*
				* @package s2Member\Utilities
				* @since 111106
				*
				* @param string $url_uri_query A full URL, a partial URI, or just a query string; to append the s2Member-generated signature onto.
				* @param string $sig_var Optional. The name of the s2Member-generated signature variable. Defaults to `_s2member_sig`.
				* @return string A full URL, a partial URI, or just a query string; with an s2Member-generated signature.
				*/
				public static function add_s2member_sig($url_uri_query = FALSE, $sig_var = FALSE)
					{
						$url_uri_query = $query = c_ws_plugin__s2member_utils_strings::trim((string)$url_uri_query, false, '?&=');
						$sig_var       = $sig_var && is_string($sig_var) ? $sig_var : '_s2member_sig';

						$url_uri_query = $query = c_ws_plugin__s2member_utils_urls::remove_s2member_sigs($url_uri_query, $sig_var);
						if(preg_match('/^(?:[a-z]+\:\/\/|\/)/i', $url_uri_query)) // Is this a full URL or a partial URI?
							$query = trim(c_ws_plugin__s2member_utils_urls::parse_url($url_uri_query, PHP_URL_QUERY), '?&=');

						$key = c_ws_plugin__s2member_utils_encryption::key(); // Obtain key.

						if($url_uri_query && is_string($query)) // We DO allow empty query strings. So we can sign a URL without one.
							{
								wp_parse_str($query, $vars); // Parse the query string into an array of ``$vars``.
								$vars = c_ws_plugin__s2member_utils_arrays::remove_0b_strings(c_ws_plugin__s2member_utils_strings::trim_deep($vars));
								$vars = serialize(c_ws_plugin__s2member_utils_arrays::ksort_deep($vars));

								$sig = ($time = time()).'-'.md5($key.$time.$vars);
								$url_uri_query = add_query_arg($sig_var, urlencode($sig), $url_uri_query);
							}
						return $url_uri_query; // Possibly with a ``$sig_var`` variable.
					}

				/**
				* Verifies an s2Member-generated signature; in a full URL, a partial URI, or in just a query string.
				*
				* @package s2Member\Utilities
				* @since 111106
				*
				* @param string $url_uri_query A full URL, a partial URI, or just a query string. Must have an s2Member-generated signature to validate.
				* @param bool $check_time Optional. Defaults to false. If true, s2Member will also check if the signature has expired, based on ``$exp_secs``.
				* @param string|int $exp_secs Optional. Defaults to (int)10. If ``$check_time`` is true, s2Member will check if the signature has expired, based on ``$exp_secs``.
				* @param string $sig_var Optional. The name of the s2Member-generated signature variable. Defaults to `_s2member_sig`.
				* @return bool True if the s2Member-generated signature is OK, else false.
				*/
				public static function s2member_sig_ok($url_uri_query = FALSE, $check_time = FALSE, $exp_secs = FALSE, $sig_var = FALSE)
					{
						$url_uri_query = $query = c_ws_plugin__s2member_utils_strings::trim((string)$url_uri_query, false, '?&=');
						if(preg_match('/^(?:[a-z]+\:\/\/|\/)/i', $url_uri_query)) // Is this a full URL or a partial URI?
							$query = trim(c_ws_plugin__s2member_utils_urls::parse_url($url_uri_query, PHP_URL_QUERY), '?&=');

						$check_time = (bool)$check_time; // Check time?
						$exp_secs   = is_numeric($exp_secs) ? (int)$exp_secs : 10;
						$sig_var    = $sig_var && is_string($sig_var) ? $sig_var : '_s2member_sig';

						$key = c_ws_plugin__s2member_utils_encryption::key(); // Obtain key.

						if(preg_match_all('/'.preg_quote($sig_var, '/').'\=([0-9]+)-([^&$]+)/', $query, $sigs))
							{
								$query = c_ws_plugin__s2member_utils_urls::remove_s2member_sigs($query, $sig_var);

								wp_parse_str($query, $vars); // Parse the query string into an array of ``$vars``.
								$vars = c_ws_plugin__s2member_utils_arrays::remove_0b_strings(c_ws_plugin__s2member_utils_strings::trim_deep($vars));
								$vars = serialize(c_ws_plugin__s2member_utils_arrays::ksort_deep($vars));

								$i         = count($sigs[1]) - 1; // Last one.
								$time      = $sigs[1][$i]; // Timestamp.
								$sig       = $sigs[2][$i]; // Signature.
								$valid_sig = md5($key.$time.$vars);

								if($check_time) // This must NOT be older than ``$exp_secs`` seconds ago.
									return $sig === $valid_sig && $time >= strtotime('-'.$exp_secs.' seconds');

								return $sig === $valid_sig;
							}
						return false; // False, it's NOT ok.
					}
			}
	}
