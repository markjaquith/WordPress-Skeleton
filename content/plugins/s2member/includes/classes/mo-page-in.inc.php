<?php
/**
 * Membership Options Page (inner processing routines).
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
 * @package s2Member\Membership_Options_Page
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if(!class_exists("c_ws_plugin__s2member_mo_page_in"))
	{
		/**
		 * Membership Options Page (inner processing routines).
		 *
		 * @package s2Member\Membership_Options_Page
		 * @since 3.5
		 */
		class c_ws_plugin__s2member_mo_page_in
		{
			/**
			 * Forces a redirection to the Membership Options Page for s2Member.
			 *
			 * This can be used by 3rd party apps that are not aware of which Page is currently set as the Membership Options Page.
			 * Example usage: `http://example.com/?s2member_membership_options_page=1`
			 *
			 * Redirection URLs containing array brackets MUST be URL encoded to get through: ``wp_sanitize_redirect()``.
			 *   So we pass everything to ``urlencode_deep()``, as an array. It handles this via ``_http_build_query()``.
			 *   See bug report here: {@link http://core.trac.wordpress.org/ticket/17052}
			 *
			 * @package s2Member\Membership_Options_Page
			 * @since 3.5
			 *
			 * @attaches-to ``add_action("init");``
			 *
			 * @return null Or exits script execution after redirection w/ `301` status.
			 */
			public static function membership_options_page() // Real Membership Options Page.
				{
					do_action("ws_plugin__s2member_before_membership_options_page", get_defined_vars());

					if(!empty($_GET["s2member_membership_options_page"]) && is_array($_g = c_ws_plugin__s2member_utils_strings::trim_deep(stripslashes_deep($_GET))))
						{
							$args = array(); // Initialize this to an empty array value.

							foreach($_g as $var => $value) // Include all of the `_?s2member_` variables.
								// Do NOT include `s2member_membership_options_page`; creates a redirection loop.
								if(preg_match("/^_?s2member_/", $var) && $var !== "s2member_membership_options_page")
									$args[$var] = $value; // Supports nested arrays.

							wp_redirect(add_query_arg(urlencode_deep($args), get_page_link($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_options_page"])), 301).exit ();
						}
					do_action("ws_plugin__s2member_after_membership_options_page", get_defined_vars());
				}

			/**
			 * Redirects to Membership Options Page w/ MOP Vars.
			 *
			 * Redirection URLs containing array brackets MUST be URL encoded to get through: ``wp_sanitize_redirect()``.
			 *   So we pass everything to ``urlencode_deep()``, as an array. It handles this via ``_http_build_query()``.
			 *   See bug report here: {@link http://core.trac.wordpress.org/ticket/17052}
			 *
			 * @package s2Member\Membership_Options_Page
			 * @since 111101
			 *
			 * @param string     $seeking_type Seeking content type. One of: `post|page|catg|ptag|file|ruri`.
			 * @param string|int $seeking_type_value Seeking content type data. String, or a Post/Page ID.
			 * @param string     $req_type Access requirement type. One of these values: `level|ccap|sp`.
			 * @param string|int $req_type_value Access requirement. String, or a Post/Page ID.
			 * @param string     $seeking_uri The full URI that access was attempted on.
			 * @param string     $res_type Restriction type that's preventing access.
			 *   One of: `post|page|catg|ptag|file|ruri|ccap|sp|sys`.
			 *   Defaults to ``$seeking_type``.
			 *
			 * @return bool This function always returns true.
			 */
			public static function wp_redirect_w_mop_vars($seeking_type = FALSE, $seeking_type_value = FALSE, $req_type = FALSE, $req_type_value = FALSE, $seeking_uri = FALSE, $res_type = FALSE)
				{
					do_action("ws_plugin__s2member_before_wp_redirect_w_mop_vars", get_defined_vars());

					foreach(array("seeking_type", "seeking_type_value", "req_type", "req_type_value", "seeking_uri", "res_type") as $_param)
						{
							if($_param === "seeking_uri" || ($_param === "seeking_type_value" && $seeking_type === "ruri"))
								${$_param} = base64_encode((string)${$_param});
							else ${$_param} = str_replace("..", "--", (string)${$_param});
						}
					unset($_param); // Housekeeping.

					if(!$res_type) $res_type = $seeking_type;

					$vars = $res_type."..".$req_type."..".$req_type_value."..";
					$vars .= $seeking_type."..".$seeking_type_value."..".$seeking_uri;
					$vars = array("_s2member_vars" => $vars);

					$status = apply_filters("ws_plugin__s2member_content_redirect_status", 301, get_defined_vars());
					$status = apply_filters("ws_plugin__s2member_wp_redirect_w_mop_vars_status", $status, get_defined_vars());

					$mop_url = get_page_link($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_options_page"]);
					if($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_options_page_vars_enable"])
						{
							$mop_url = add_query_arg(urlencode_deep($vars), $mop_url);
							$mop_url = c_ws_plugin__s2member_utils_urls::add_s2member_sig($mop_url);
						}
					if(!empty($_GET) && $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_options_page_ga_vars_enable"])
						{
							$ga_vars = array(); // Initialize.

							foreach(stripslashes_deep($_GET) as $_key => $_value)
									if(strpos($_key, 'utm_') === 0) $ga_vars[$_key] = $_value;
							unset($_key, $_value); // Housekeeping.

							$mop_url = add_query_arg(urlencode_deep($ga_vars), $mop_url);
						}
					wp_redirect($mop_url, $status); // NOTE: we do not exit here (on purpose).

					do_action("ws_plugin__s2member_after_wp_redirect_w_mop_vars", get_defined_vars());

					return TRUE; // Always returns true here.
				}

			/*
			 * s2Member's MOP Vars are now a double-dot (`..`) delimited list of six values.
			 *
			 * e.g., .../membership-options-page/
			 *    ?_s2member_vars=[restriction type]..[requirement type]..[requirement type value]..
			 *       [seeking type]..[seeking type value]..[seeking URI base 64 encoded]
			 */
			public static function back_compat_mop_vars()
				{
					if(empty($_REQUEST["_s2member_vars"])
					   || !is_string($_REQUEST["_s2member_vars"])
					) return;

					$v = explode("..", $_REQUEST["_s2member_vars"]);
					if(count($v) !== 6) return;

					/*
					 * Back compat. Deprecated since v1404xx.
					 */
					$ov["_s2member_seeking"]     = array(
						"type" => $v[3],
						$v[3]  => $v[4],
						"_uri" => $v[5]
					);
					$ov["_s2member_req"]         = array(
						"type" => $v[1],
						$v[1]  => $v[2],
					);
					$ov["_s2member_res"]["type"] = $v[0];

					/*
					 * Back compat. Deprecated since v1104xx.
					 */
					$ov["s2member_seeking"]       = $v[3]."-".$v[4];
					$ov["s2member_".$v[1]."_req"] = $v[2];

					/*
					 * Fill both $_GET and $_REQUEST vars.
					 */
					foreach($ov as $_k => $_v)
						$_GET[$_k] = $_REQUEST[$_k] = $_v;
					unset($_k, $_v);
				}
		}
	}
