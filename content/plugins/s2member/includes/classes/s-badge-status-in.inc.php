<?php
/**
* Security Badge Status API (inner processing routines).
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
* @package s2Member\Security_Badges
* @since 110524RC
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_s_badge_status_in"))
	{
		/**
		* Security Badge Status API (inner processing routines).
		*
		* @package s2Member\Security_Badges
		* @since 110524RC
		*/
		class c_ws_plugin__s2member_s_badge_status_in
			{
				/**
				* Handles Security Badge Status API.
				*
				* @package s2Member\Security_Badges
				* @since 110524RC
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null Exits script execution after status output.
				*/
				public static function s_badge_status ()
					{
						do_action("ws_plugin__s2member_before_s_badge_status", get_defined_vars ());

						if (!empty($_GET["s2member_s_badge_status"]))
							{
								status_header (200); // Send a 200 OK status.

								header ("Content-Type: text/plain; charset=UTF-8");

								@ini_set("zlib.output_compression", 0);
								if(function_exists("apache_setenv"))
									@apache_setenv("no-gzip", "1");

								while (@ob_end_clean ()); // Clean any existing output buffers.

								if ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["s_badge_status_enabled"])
									{
										if (strlen ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["sec_encryption_key"]) >= 56)
											{
												if (defined ("AUTH_KEY") && strlen (AUTH_KEY) >= 60 && stripos (AUTH_KEY, "unique phrase") === false)
													if (defined ("SECURE_AUTH_KEY") && strlen (SECURE_AUTH_KEY) >= 60 && stripos (SECURE_AUTH_KEY, "unique phrase") === false)
														{
															if (defined ("AUTH_SALT") && strlen (AUTH_SALT) >= 60 && stripos (AUTH_SALT, "unique phrase") === false)
																if (defined ("SECURE_AUTH_SALT") && strlen (SECURE_AUTH_SALT) >= 60 && stripos (SECURE_AUTH_SALT, "unique phrase") === false)
																	{
																		if (defined ("LOGGED_IN_KEY") && strlen (LOGGED_IN_KEY) >= 60 && stripos (LOGGED_IN_KEY, "unique phrase") === false)
																			if (defined ("LOGGED_IN_SALT") && strlen (LOGGED_IN_SALT) >= 60 && stripos (LOGGED_IN_SALT, "unique phrase") === false)
																				{
																					if (defined ("NONCE_KEY") && strlen (NONCE_KEY) >= 60 && stripos (NONCE_KEY, "unique phrase") === false)
																						if (defined ("NONCE_SALT") && strlen (NONCE_SALT) >= 60 && stripos (NONCE_SALT, "unique phrase") === false)
																							{
																								if (defined ("DB_USER") && DB_USER && defined ("DB_PASSWORD") && DB_PASSWORD && DB_USER !== DB_PASSWORD)
																									{
																										if ((!apply_filters("ws_plugin__s2member_disable_all_ip_restrictions", false, get_defined_vars ()) && $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["max_ip_restriction"])
																												|| ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["max_simultaneous_logins"]) /* Either of these are acceptable. */)
																											if ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["max_failed_login_attempts"])
																												{
																													if ((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["gateway_debug_logs"] && !glob($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["logs_dir"].'/*'))
																														|| $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["logs_dir"] !== $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["default_logs_dir"])
																														{
																															exit ("1"); // OK good. Things look pretty secure here.
																														}
																												}
																									}
																							}
																				}
																	}
														}
											}
										exit ("0"); // Else, NOT secure.
									}
								else exit ("-"); // Else, service NOT enabled.
							}

						do_action("ws_plugin__s2member_after_s_badge_status", get_defined_vars ());
					}
			}
	}
