<?php
/**
 * SSL routines (inner processing routines).
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
 * @package s2Member\SSL
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_ssl_in'))
{
	/**
	 * SSL routines (inner processing routines).
	 *
	 * @package s2Member\SSL
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_ssl_in
	{
		/**
		 * Forces SSL on specific Posts/Pages, or any page for that matter.
		 *
		 * Triggered by Custom Field: `s2member_force_ssl = yes|port#`
		 *
		 * Triggered by: `?s2-ssl` or `?s2-ssl=yes|port#`.
		 *
		 * @package s2Member\SSL
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('init');``
		 * @also-attaches-to ``add_action('wp');``
		 *
		 * @param array $vars From: ``c_ws_plugin__s2member_ssl::check_force_ssl()``.
		 *
		 * @return null Possibly exiting script execution after redirection to SSL variation.
		 *
		 * @todo Cleanup this routine and convert callback functions to static class methods?
		 */
		public static function force_ssl($vars = array()) // Phase 2 of ``c_ws_plugin__s2member_ssl::check_force_ssl()``.
		{
			/**
			 * @var string               $s2_ssl_gv Extracted variable.
			 * @var string|integer|mixed $force_ssl Extracted variable.
			 */
			extract($vars); // From: ``c_ws_plugin__s2member_ssl::check_force_ssl()``.

			$force_ssl = !is_string($force_ssl) ? (string)(int)$force_ssl : $force_ssl;
			$force_ssl = is_numeric($force_ssl) && $force_ssl > 1 ? $force_ssl : 'yes';

			$ssl_host      = preg_replace('/\:[0-9]+$/', '', $_SERVER['HTTP_HOST']);
			$ssl_port      = (is_numeric($force_ssl) && $force_ssl > 1) ? $force_ssl : FALSE;
			$ssl_host_port = $ssl_host.(($ssl_port) ? ':'.$ssl_port : '');

			if(!is_ssl() || !isset($_GET[$s2_ssl_gv]))
			{
				$https                = 'https://'.$ssl_host_port.$_SERVER['REQUEST_URI'];
				$https_with_s2_ssl_gv = add_query_arg($s2_ssl_gv, urlencode($force_ssl), $https);
				wp_redirect($https_with_s2_ssl_gv).exit();
			}
			else // Otherwise, we buffer all output, and switch all content over to `https`.
				// Assume here that other links on the site should NOT be converted to `https`.
			{
				add_filter('redirect_canonical', '__return_false');

				define('_ws_plugin__s2member_force_ssl_host', $ssl_host);
				define('_ws_plugin__s2member_force_ssl_port', $ssl_port);
				define('_ws_plugin__s2member_force_ssl_host_port', $ssl_host_port);

				// Filter these. Do NOT create a sitewide conversion to `https`.
				add_filter('home_url', '_ws_plugin__s2member_maybe_force_non_ssl_scheme', 10, 3);
				add_filter('network_home_url', '_ws_plugin__s2member_maybe_force_non_ssl_scheme', 10, 3);

				// Filter these. Do NOT create a sitewide conversion to `https`.
				add_filter('site_url', '_ws_plugin__s2member_maybe_force_non_ssl_scheme', 10, 3);
				add_filter('network_site_url', '_ws_plugin__s2member_maybe_force_non_ssl_scheme', 10, 3);

				// Filter these. Do NOT create a sitewide conversion to `https`.
				// Note: these are necessary because these underlying functions create URLs in bits and pieces.
				// 	Thus, in order to properly detect static file extensions we need to look at these values also.
				add_filter('plugins_url', '_ws_plugin__s2member_maybe_force_non_ssl_scheme', 10, 2);
				add_filter('content_url', '_ws_plugin__s2member_maybe_force_non_ssl_scheme', 10, 2);
				add_filter('includes_url', '_ws_plugin__s2member_maybe_force_non_ssl_scheme', 10, 2);

				// Now we create various callback functions associated with SSL and non-SSL buffering.
				if(!function_exists('_ws_plugin__s2member_force_ssl_buffer_callback'))
				{
					function _ws_plugin__s2member_force_ssl_buffer_callback($m = array())
					{
						$s = preg_replace('/http\:\/\//i', 'https://', $m[0]);

						if(_ws_plugin__s2member_force_ssl_host && _ws_plugin__s2member_force_ssl_port && _ws_plugin__s2member_force_ssl_host_port)
							$s = preg_replace('/(?:https?\:)?\/\/'.preg_quote(_ws_plugin__s2member_force_ssl_host, '/').'(?:\:[0-9]+)?/i', 'https://'._ws_plugin__s2member_force_ssl_host_port, $s);

						$s = strtolower($m[1]) === 'link' && preg_match('/(["\'])(?:alternate|profile|pingback|EditURI|wlwmanifest|prev|next)\\1/i', $m[0]) ? $m[0] : $s;

						return $s; // Return string with conversions.
					}
				}
				if(!function_exists('_ws_plugin__s2member_force_non_ssl_buffer_callback'))
				{
					function _ws_plugin__s2member_force_non_ssl_buffer_callback($m = array())
					{
						$s = $m[0]; // Initialize the `$s` variable.

						if(stripos($s, 's2member_file_download') !== false || stripos($s, 's2member-files') !== false)
							return $s; // See: <https://github.com/websharks/s2member/issues/702>

						$s = preg_replace('/(?:https?\:)?\/\/'.preg_quote(_ws_plugin__s2member_force_ssl_host_port, '/').'/i', 'http://'._ws_plugin__s2member_force_ssl_host, $s);
						$s = preg_replace('/(?:https?\:)?\/\/'.preg_quote(_ws_plugin__s2member_force_ssl_host, '/').'/i', 'http://'._ws_plugin__s2member_force_ssl_host, $s);

						return $s; // Return string with conversions.
					}
				}
				if(!function_exists('_ws_plugin__s2member_maybe_force_non_ssl_scheme'))
				{
					function _ws_plugin__s2member_maybe_force_non_ssl_scheme($url = '', $path = '', $scheme = null)
					{
						static $static_file_extensions; // Cache of static file extensions.
						if(!isset($static_file_extensions)) // Cached this yet?
						{
							$wp_media_library_extensions = array_keys(wp_get_mime_types());
        					$wp_media_library_extensions = explode('|', strtolower(implode('|', $wp_media_library_extensions)));
        					$static_file_extensions      = array_unique(array_merge($wp_media_library_extensions, array('eot', 'ttf', 'otf', 'woff')));
						}
						if($scheme === 'relative') // e.g. `/root/relative/path.ext`
							return $url; // Nothing to do in this case.

						if(!in_array($scheme, array('http', 'https'), TRUE)) // If NOT explicit.
						{
							if(($scheme === 'login_post' || $scheme === 'rpc') && (force_ssl_login() || force_ssl_admin()))
								$scheme = 'https'; // Use an SSL scheme in this case.

							else if(($scheme === 'login' || $scheme === 'admin') && force_ssl_admin())
								$scheme = 'https'; // Use an SSL scheme in this case.

							else if($url && ($url_path = @parse_url($url, PHP_URL_PATH)) && $url_path !== '/'
							 	&& ($url_ext = strtolower(ltrim((string) strrchr(basename($url_path), '.'), '.')))
								&& in_array($url_ext, $static_file_extensions, true) // Static resource?
							) $scheme = 'https'; // Use an SSL scheme in this case.

							else $scheme = 'http'; // Default to non-SSL: `http`.
						}
						return preg_replace('/^(?:https?\:)?\/\//i', $scheme.'://', $url);
					}
				}
				if(!function_exists('_ws_plugin__s2member_force_ssl_buffer'))
				{
					function _ws_plugin__s2member_force_ssl_buffer($buffer = '')
					{
						$o_pcre = @ini_get('pcre.backtrack_limit'); // Record existing backtrack limit.
						@ini_set('pcre.backtrack_limit', 10000000); // Increase PCRE backtrack limit for this routine.

						$ssl_entire_tags     = array_unique(array_map('strtolower', apply_filters('_ws_plugin__s2member_force_ssl_buffer_entire_tags', array('script', 'style', 'iframe', 'object', 'embed', 'video'), get_defined_vars())));
						$non_ssl_entire_tags = array_unique(array_map('strtolower', apply_filters('_ws_plugin__s2member_force_non_ssl_buffer_entire_tags', array(), get_defined_vars())));

						$ssl_attr_only_tags     = array_unique(array_diff(array_map('strtolower', apply_filters('_ws_plugin__s2member_force_ssl_buffer_attr_only_tags', array('link', 'img', 'form', 'input'), get_defined_vars())), $ssl_entire_tags));
						$non_ssl_attr_only_tags = array_unique(array_diff(array_map('strtolower', apply_filters('_ws_plugin__s2member_force_non_ssl_buffer_attr_only_tags', array('a'), get_defined_vars())), $non_ssl_entire_tags));

						$buffer = $ssl_entire_tags ? preg_replace_callback('/\<('.implode('|', c_ws_plugin__s2member_utils_strings::preg_quote_deep($ssl_entire_tags, '/')).')(?![a-z_0-9\-])[^\>]*?\>.*?\<\/\\1\>/is', '_ws_plugin__s2member_force_ssl_buffer_callback', $buffer) : $buffer;
						$buffer = $ssl_attr_only_tags ? preg_replace_callback('/\<('.implode('|', c_ws_plugin__s2member_utils_strings::preg_quote_deep($ssl_attr_only_tags, '/')).')(?![a-z_0-9\-])[^\>]+?\>/i', '_ws_plugin__s2member_force_ssl_buffer_callback', $buffer) : $buffer;

						$buffer = $non_ssl_entire_tags ? preg_replace_callback('/\<('.implode('|', c_ws_plugin__s2member_utils_strings::preg_quote_deep($non_ssl_entire_tags, '/')).')(?![a-z_0-9\-])[^\>]*?\>.*?\<\/\\1\>/is', '_ws_plugin__s2member_force_non_ssl_buffer_callback', $buffer) : $buffer;
						$buffer = $non_ssl_attr_only_tags ? preg_replace_callback('/\<('.implode('|', c_ws_plugin__s2member_utils_strings::preg_quote_deep($non_ssl_attr_only_tags, '/')).')(?![a-z_0-9\-])[^\>]+?\>/i', '_ws_plugin__s2member_force_non_ssl_buffer_callback', $buffer) : $buffer;

						@ini_set('pcre.backtrack_limit', $o_pcre); // Restore original PCRE backtrack limit. This just keeps things tidy; probably NOT necessary.

						return apply_filters('_ws_plugin__s2member_force_ssl_buffer', $buffer, get_defined_vars());
					}
				}
				ob_start('_ws_plugin__s2member_force_ssl_buffer');
			}
		}
	}
}
