<?php
/**
* Specific Post/Page Access routines.
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
* @package s2Member\SP_Access
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_sp_access"))
	{
		/**
		* Specific Post/Page Access routines.
		*
		* @package s2Member\SP_Access
		* @since 3.5
		*/
		class c_ws_plugin__s2member_sp_access
			{
				/**
				* Generates Specific Post/Page Access links.
				*
				* @package s2Member\SP_Access
				* @since 3.5
				*
				* @param string|int $sp_ids Comma-delimited list of Specific Post/Page IDs *(numerical)*.
				* @param int|string $hours Optional. A numeric expiration time for this link, in hours. Defaults to `72`.
				* @param bool $shrink Optional. Defaults to true. If false, the raw link will NOT be processed by the tinyURL API.
				* @return str|bool A Specific Post/Page Access Link, or false on failure.
				*/
				public static function sp_access_link_gen ($sp_ids = FALSE, $hours = 72, $shrink = TRUE)
					{
						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_before_sp_access_link_gen", get_defined_vars ());
						unset($__refs, $__v);

						if ((is_string ($sp_ids) || is_numeric ($sp_ids)) && ($sp_ids = preg_replace ("/[^0-9;,]/", "", $sp_ids)) && ($leading_id = preg_replace ("/^([0-9]+).*$/", "$1", $sp_ids)) && is_numeric ($hours))
							{
								$sp_access = c_ws_plugin__s2member_utils_encryption::encrypt ("sp_time_hours:.:|:.:" . $sp_ids . ":.:|:.:" . strtotime ("now") . ":.:|:.:" . $hours);

								$sp_access_link = add_query_arg ("s2member_sp_access", urlencode ($sp_access), get_permalink ($leading_id)); // Generate long URL/link.

								if ($shrink && ($shorter_url = c_ws_plugin__s2member_utils_urls::shorten ($sp_access_link)))
									$sp_access_link = $shorter_url . "#" . $_SERVER["HTTP_HOST"];
							}
						return apply_filters("ws_plugin__s2member_sp_access_link_gen", ((!empty($sp_access_link)) ? $sp_access_link : false), get_defined_vars ());
					}
				/**
				* Generates Specific Post/Page Access links via AJAX.
				*
				* @package s2Member\SP_Access
				* @since 3.5
				*
				* @attaches-to ``add_action("wp_ajax_ws_plugin__s2member_sp_access_link_via_ajax");``
				*
				* @return null Exits script execution after returning data for AJAX caller.
				*/
				public static function sp_access_link_via_ajax ()
					{
						do_action("ws_plugin__s2member_before_sp_access_link_via_ajax", get_defined_vars ());

						status_header (200); // Send a 200 OK status header.
						header ("Content-Type: text/plain; charset=UTF-8"); // Content-Type with UTF-8.
						while (@ob_end_clean ()); // Clean any existing output buffers.

						if (current_user_can ("create_users")) // Check privileges as well. Ability to create Users?

							if (!empty($_POST["ws_plugin__s2member_sp_access_link_via_ajax"]) && is_string ($nonce = $_POST["ws_plugin__s2member_sp_access_link_via_ajax"]) && wp_verify_nonce ($nonce, "ws-plugin--s2member-sp-access-link-via-ajax"))

								if (($_p = c_ws_plugin__s2member_utils_strings::trim_deep (stripslashes_deep ($_POST))) && isset ($_p["s2member_sp_access_link_ids"], $_p["s2member_sp_access_link_hours"]))
									$sp_access_link = c_ws_plugin__s2member_sp_access::sp_access_link_gen ((string)$_p["s2member_sp_access_link_ids"], (string)$_p["s2member_sp_access_link_hours"]);

						exit (apply_filters("ws_plugin__s2member_sp_access_link_via_ajax", ((!empty($sp_access_link)) ? $sp_access_link : ""), get_defined_vars ()));
					}
				/**
				* Handles Specific Post/Page Access authentication.
				*
				* @package s2Member\SP_Access
				* @since 3.5
				*
				* @param int|string $sp_id Numeric Post/Page ID in WordPress.
				* @param bool $read_only Optional. Defaults to false. If ``$read_only = true``,
				* 	no session cookies are set, no IP Restrictions are checked, and script execution is not exited on Link failure.
				* 	In other words, with ``$read_only = true``, this function will simply return true or false.
				* @return null|bool|string Returns `true` (or the SP access string), if access is indeed allowed in one way or another.
				* 	If access is denied with ``$read_only = true`` simply return false. If access is denied with ``$read_only = false``, return false; but if a Specific Post/Page Access Link is currently being used, we exit with a warning about Access Link expiration here.
				*/
				public static function sp_access ($sp_id = FALSE, $read_only = FALSE)
					{
						do_action("ws_plugin__s2member_before_sp_access", get_defined_vars ());

						$excluded = apply_filters("ws_plugin__s2member_sp_access_excluded", false, get_defined_vars ());

						if ($excluded || current_user_can (apply_filters("ws_plugin__s2member_sp_access_excluded_cap", "edit_posts", get_defined_vars ())))
							return apply_filters("ws_plugin__s2member_sp_access", true, get_defined_vars (), "auth-via-exclusion");

						else if ($sp_id && is_numeric ($sp_id) && ((!empty($_GET["s2member_sp_access"]) && ($_g["s2member_sp_access"] = trim (stripslashes ((string)$_GET["s2member_sp_access"]))) && is_array($sp_access_values = array($_g["s2member_sp_access"]))) || is_array($sp_access_values = c_ws_plugin__s2member_sp_access::sp_access_session ())) && !empty($sp_access_values))
							{
								foreach ($sp_access_values as $sp_access_value) // Supports multiple access values in a session. We go through each of them.
									{
										if (is_array($sp_access = preg_split ("/\:\.\:\|\:\.\:/", c_ws_plugin__s2member_utils_encryption::decrypt ($sp_access_value))))
											{
												if (count ($sp_access) === 4 && $sp_access[0] === "sp_time_hours" && in_array($sp_id, preg_split ("/[\r\n\t\s;,]+/", $sp_access[1])))
													{
														if (is_numeric ($sp_access[2]) && is_numeric ($sp_access[3]) && $sp_access[2] <= strtotime ("now") && ($sp_access[2] + ($sp_access[3] * 3600)) >= strtotime ("now"))
															{
																if (!$read_only && !empty($_g["s2member_sp_access"])) // Add to session?
																	c_ws_plugin__s2member_sp_access::sp_access_session ($_g["s2member_sp_access"]);

																if ($read_only || c_ws_plugin__s2member_ip_restrictions::ip_restrictions_ok ($_SERVER["REMOTE_ADDR"], $sp_access_value))
																	return apply_filters("ws_plugin__s2member_sp_access", $sp_access_value, get_defined_vars (), "auth-via-link-session");
															}
													}
											}
									}
								// Otherwise, authentication was NOT possible via link or session.
								if (!$read_only && /* A Specific Post/Page Access Link? */ !empty($_g["s2member_sp_access"]))
									{
										status_header (503);
										header ("Content-Type: text/html; charset=UTF-8");
										while (@ob_end_clean ()); // Clean any existing output buffers.
										exit (_x ('<strong>Your Link Expired:</strong><br />Please contact Support if you need assistance.', "s2member-front", "s2member"));
									}
								else // Else return false here.
									return apply_filters("ws_plugin__s2member_sp_access", false, get_defined_vars (), "no-auth-via-link-session");
							}

						else // Else return false here.
							return apply_filters("ws_plugin__s2member_sp_access", false, get_defined_vars (), "no-auth-no-link-session");
					}
				/**
				* Handles Specific Post/Page sessions, by writing access values into a cookie.
				*
				* Can be used to add a new value to the session, and/or to return the current set of values in the session.
				*
				* @package s2Member\SP_Access
				* @since 3.5
				*
				* @param string $add_sp_access_value Encrypted Specific Post/Page Access value.
				* @return array Array of Specific Post/Page Access values.
				*/
				public static function sp_access_session ($add_sp_access_value = FALSE)
					{
						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_before_sp_access_session", get_defined_vars ());
						unset($__refs, $__v);

						$sp_access_values = (!empty($_COOKIE["s2member_sp_access"])) ? preg_split ("/\:\.\:\|\:\.\:/", (string)$_COOKIE["s2member_sp_access"]) : array();

						if ($add_sp_access_value && is_string ($add_sp_access_value) && !in_array /* Not in session? */ ($add_sp_access_value, $sp_access_values))
							{
								$sp_access_values[] = $add_sp_access_value; // Add an access value, and update the delimited session cookie.
								$sp_access_values = array_unique ($sp_access_values); // Keep this array unique; disallow double-stacking.

								$cookie = implode (":.:|:.:", $sp_access_values); // Implode the access values into a delimited string.
								$cookie = (strlen ($cookie) >= 4096) ? $add_sp_access_value : $cookie; // Max cookie size is 4kbs.

								setcookie ("s2member_sp_access", $cookie, time () + 31556926, COOKIEPATH, COOKIE_DOMAIN);
								setcookie ("s2member_sp_access", $cookie, time () + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN);
								$_COOKIE["s2member_sp_access"] = $cookie; // Real-time cookie updates.

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("ws_plugin__s2member_during_sp_access_session", get_defined_vars ());
								unset($__refs, $__v);
							}
						return apply_filters("ws_plugin__s2member_sp_access_session", $sp_access_values, get_defined_vars ());
					}
			}
	}
