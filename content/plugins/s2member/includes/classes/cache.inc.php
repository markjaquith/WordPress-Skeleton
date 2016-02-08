<?php
/**
* s2Member's caching routines.
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
* @package s2Member\Cache
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_cache"))
	{
		/**
		* s2Member's caching routines.
		*
		* @package s2Member\Cache
		* @since 3.5
		*/
		class c_ws_plugin__s2member_cache
			{
				/**
				* Page links needed for Constants.
				*
				* Page links are cached into the s2Member options on 15 min intervals.
				* This allows the API Constants to provide quick access to them without being
				* forced to execute {@link http://codex.wordpress.org/Function_Reference/get_page_link get_page_link()}
				* all the time, which piles up DB queries.
				*
				* @package s2Member\Cache
				* @since 3.5
				*
				* @return array Array of cached Page links.
				*/
				public static function cached_page_links ()
					{
						do_action("ws_plugin__s2member_before_cached_page_links", get_defined_vars ());

						$lwp = $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_welcome_page"];
						$mop = $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_options_page"];
						$fdlep = $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["file_download_limit_exceeded_page"];

						$lwp_cache = @$GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["cache"]["login_welcome_page"];
						$mop_cache = @$GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["cache"]["membership_options_page"];
						$fdlep_cache = @$GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["cache"]["file_download_limit_exceeded_page"];

						$links = array("login_welcome_page" => "", "membership_options_page" => "", "file_download_limit_exceeded_page" => "");

						if (isset ($lwp_cache["page"], $lwp_cache["time"], $lwp_cache["link"]) && $lwp_cache["page"] === $lwp && $lwp_cache["time"] >= strtotime ("-15 minutes") && $lwp_cache["link"])
							{
								$links["login_welcome_page"] = $lwp_cache["link"];
							}
						else // Otherwise, query the database using ``get_page_link()`` and update the cache.
							{
								$GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["cache"]["login_welcome_page"]["page"] = $lwp;
								$GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["cache"]["login_welcome_page"]["time"] = time ();
								$links["login_welcome_page"] = $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["cache"]["login_welcome_page"]["link"] = ($lwp) ? get_page_link ($lwp) : home_url("/");

								$cache_needs_updating = /* Flag for cache update. */ true;
							}
						if (isset ($mop_cache["page"], $mop_cache["time"], $mop_cache["link"]) && $mop_cache["page"] === $mop && $mop_cache["time"] >= strtotime ("-15 minutes") && $mop_cache["link"])
							{
								$links["membership_options_page"] = $mop_cache["link"];
							}
						else // Otherwise, query the database using ``get_page_link()`` and update the cache.
							{
								$GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["cache"]["membership_options_page"]["page"] = $mop;
								$GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["cache"]["membership_options_page"]["time"] = time ();
								$links["membership_options_page"] = $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["cache"]["membership_options_page"]["link"] = ($mop) ? get_page_link ($mop) : home_url("/");

								$cache_needs_updating = /* Flag for cache update. */ true;
							}
						if (isset ($fdlep_cache["page"], $fdlep_cache["time"], $fdlep_cache["link"]) && $fdlep_cache["page"] === $fdlep && $fdlep_cache["time"] >= strtotime ("-15 minutes") && $fdlep_cache["link"])
							{
								$links["file_download_limit_exceeded_page"] = $fdlep_cache["link"];
							}
						else // Otherwise, query the database using ``get_page_link()`` and update the cache.
							{
								$GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["cache"]["file_download_limit_exceeded_page"]["page"] = $fdlep;
								$GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["cache"]["file_download_limit_exceeded_page"]["time"] = time ();
								$links["file_download_limit_exceeded_page"] = $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["cache"]["file_download_limit_exceeded_page"]["link"] = ($fdlep) ? get_page_link ($fdlep) : home_url("/");

								$cache_needs_updating = /* Flag for cache update. */ true;
							}
						if /* Cache is also reset dynamically during back-end option updates. */ (isset($cache_needs_updating) && $cache_needs_updating)
							{
								update_option ("ws_plugin__s2member_cache", $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["cache"]);
							}
						$scheme = /* SSL mode? */ (is_ssl ()) ? "https" : "http";
						foreach /* Conversions for SSL and non-SSL mode. */ ($links as &$link)
							$link = preg_replace ("/^https?\:\/\//i", $scheme . "://", $link);

						return apply_filters("ws_plugin__s2member_cached_page_links", $links, get_defined_vars ());
					}
			}
	}
