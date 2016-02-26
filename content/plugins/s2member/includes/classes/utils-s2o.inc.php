<?php
/**
 * s2Member-only utilities.
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
 * @since 110912
 */
if(!class_exists('c_ws_plugin__s2member_utils_s2o'))
{
	/**
	 * s2Member-only utilities.
	 *
	 * @package s2Member\Utilities
	 * @since 110912
	 */
	class c_ws_plugin__s2member_utils_s2o
	{
		/**
		 * WordPress directory.
		 *
		 * @package s2Member\Utilities
		 * @since 110912
		 *
		 * @param string $starting_dir A directory to start searching from.
		 * @param string $alt_starting_dir An alternate directory to search from.
		 *
		 * @return string|null WordPress directory, else exits script execution on failure.
		 */
		public static function wp_dir($starting_dir = '', $alt_starting_dir = '')
		{
			if(!empty($_SERVER['WP_DIR']))
				return (string)$_SERVER['WP_DIR'];

			foreach(array($starting_dir, $alt_starting_dir, $_SERVER['DOCUMENT_ROOT']) as $_directory)
				if($_directory && is_string($_directory) && is_dir($_directory))
					for($_i = 0, $_dir = $_directory; $_i <= 20; $_i++, $_dir = dirname($_dir))
						if(file_exists($_dir.'/wp-settings.php'))
							return ($wp_dir = $_dir);

			header('HTTP/1.0 500 Error');
			header('Content-Type: text/plain; charset=UTF-8');

			while(@ob_end_clean()) ; // Clean any existing output buffers.
			exit ('ERROR: s2Member unable to locate WordPress directory.');
		}

		/**
		 * WordPress settings, after ``SHORTINIT`` section.
		 *
		 * @package s2Member\Utilities
		 * @since 110912
		 *
		 * @param string $wp_dir WordPress directory path.
		 * @param string $o_file Location of calling `*-o.php` file.
		 *
		 * @return string|bool WordPress settings, else false on failure.
		 */
		public static function wp_settings_as($wp_dir = '', $o_file = '')
		{
			if($wp_dir && is_dir($wp_dir) && is_readable($wp_settings = $wp_dir.'/wp-settings.php') && $o_file && is_file($o_file) && ($_wp_settings = trim(file_get_contents($wp_settings))))
			{
				$wp_shortinit_section = '/if\s*\(\s*SHORTINIT\s*\)['."\r\n\t".'\s]*\{?['."\r\n\t".'\s]*return\s+false;['."\r\n\t".'\s]*\}?['."\r\n\t".'\s]*/'; // Run ``preg_match()`` to confirm existence.
				if(preg_match($wp_shortinit_section, $_wp_settings) && ($_wp_settings_parts = preg_split($wp_shortinit_section, $_wp_settings, 2)) && ($_wp_settings = trim($_wp_settings_parts[1])) && ($_wp_settings = '<?php'."\n".$_wp_settings))
				{
					if(($_wp_settings = str_replace('__FILE__', "'".str_replace("'", "'", $wp_settings)."'", $_wp_settings))) // Eval compatible. Hard-code the ``__FILE__`` location here.
					{
						$mu_plugins_section = '/['."\r\n\t".'\s]+foreach\s*\(\s*wp_get_mu_plugins\s*\(\s*\)\s*as\s*\$mu_plugin\s*\)['."\r\n\t".'\s]*\{?['."\r\n\t".'\s]*include_once\s*\(\s*\$mu_plugin\s*\)\s*;['."\r\n\t".'\s]*\}?['."\r\n\t".'\s]*unset\s*\(\s*\$mu_plugin\s*\)\s*;/';
						$mu_plugins_replace = "\n\n".c_ws_plugin__s2member_utils_s2o::esc_ds(trim(c_ws_plugin__s2member_utils_s2o::evl(file_get_contents(dirname(dirname(__FILE__)).'/templates/cfg-files/s2o-mu-plugins.php'))))."\n";
						if(($_wp_settings = preg_replace($mu_plugins_section, $mu_plugins_replace, $_wp_settings, 1, $mu_plugins_replaced)) && $mu_plugins_replaced)
						{
							$nw_plugins_section = '/['."\r\n\t".'\s]+foreach\s*\(\s*wp_get_active_network_plugins\s*\(\s*\)\s*as\s*\$network_plugin\s*\)['."\r\n\t".'\s]*\{?['."\r\n\t".'\s]*wp_register_plugin_realpath\s*\(\s*\$network_plugin\s*\)\s*;['."\r\n\t".'\s]*include_once\s*\(\s*\$network_plugin\s*\)\s*;['."\r\n\t".'\s]*\}?['."\r\n\t".'\s]*unset\s*\(\s*\$network_plugin\s*\)\s*;/';
							$nw_plugins_replace = "\n\n".c_ws_plugin__s2member_utils_s2o::esc_ds(trim(c_ws_plugin__s2member_utils_s2o::evl(file_get_contents(dirname(dirname(__FILE__)).'/templates/cfg-files/s2o-nw-plugins.php'))))."\n";
							if(($_wp_settings = preg_replace($nw_plugins_section, $nw_plugins_replace, $_wp_settings, 1, $nw_plugins_replaced)) && $nw_plugins_replaced)
							{
								$st_plugins_section = '/['."\r\n\t".'\s]+foreach\s*\(\s*wp_get_active_and_valid_plugins\s*\(\s*\)\s*as\s*\$plugin\s*\)['."\r\n\t".'\s]*\{?['."\r\n\t".'\s]*wp_register_plugin_realpath\s*\(\s*\$plugin\s*\)\s*;['."\r\n\t".'\s]*include_once\s*\(\s*\$plugin\s*\)\s*;['."\r\n\t".'\s]*\}?['."\r\n\t".'\s ]*unset\s*\(\s*\$plugin\s*\)\s*;/';
								$st_plugins_replace = "\n\n".c_ws_plugin__s2member_utils_s2o::esc_ds(trim(c_ws_plugin__s2member_utils_s2o::evl(file_get_contents(dirname(dirname(__FILE__)).'/templates/cfg-files/s2o-st-plugins.php'))))."\n";
								if(($_wp_settings = preg_replace($st_plugins_section, $st_plugins_replace, $_wp_settings, 1, $st_plugins_replaced)) && $st_plugins_replaced)
								{
									$th_funcs_section = '/['."\r\n\t".'\s]+if\s*\(\s*\!\s*defined\s*\(\s*[\'"]WP_INSTALLING[\'"]\s*\)\s*\|\|\s*[\'"]wp\-activate\.php[\'"]\s*\=\=\=\s*\$pagenow\s*\)['."\r\n\t".'\s]*\{['."\r\n\t".'\s]*if\s*\(\s*TEMPLATEPATH\s*\!\=\=\s*STYLESHEETPATH\s*&&\s*file_exists\s*\(\s*STYLESHEETPATH\s*\.\s*[\'"]\/functions\.php[\'"]\s*\)\s*\)['."\r\n\t".'\s]*\{?['."\r\n\t".'\s]*include\s*\(\s*STYLESHEETPATH\s*\.\s*[\'"]\/functions\.php[\'"]\s*\);['."\r\n\t".'\s]*\}?['."\r\n\t".'\s]*if\s*\(\s*file_exists\s*\(\s*TEMPLATEPATH\s*\.\s*[\'"]\/functions\.php[\'"]\s*\)\s*\)['."\r\n\t".'\s]*\{?['."\r\n\t".'\s]*include\s*\(\s*TEMPLATEPATH\s*\.\s*[\'"]\/functions\.php[\'"]\s*\);['."\r\n\t".'\s]*\}?['."\r\n\t".'\s]*\}/';
									$th_funcs_replace = "\n\n".c_ws_plugin__s2member_utils_s2o::esc_ds(trim(c_ws_plugin__s2member_utils_s2o::evl(file_get_contents(dirname(dirname(__FILE__)).'/templates/cfg-files/s2o-th-funcs.php'))))."\n";
									if(($_wp_settings = preg_replace($th_funcs_section, $th_funcs_replace, $_wp_settings, 1, $th_funcs_replaced)) && $th_funcs_replaced)
									{
										if(($_wp_settings = str_replace('__FILE__', '"'.str_replace('"', '\"', $o_file).'"', $_wp_settings))) // Eval compatible.
										{
											if(($_wp_settings = trim($_wp_settings))) // WordPress, with s2Member only.
												return ($wp_settings_as = $_wp_settings); // After ``SHORTINIT``.
										}
									}
								}
							}
						}
					}
				}
			}
			return FALSE;
		}

		/**
		 * Escapes dollars signs (for regex patterns).
		 *
		 * @package s2Member\Utilities
		 * @since 110917
		 *
		 * @param string $string Input string.
		 * @param int    $times Mumber of escapes. Defaults to 1.
		 *
		 * @return string Output string after dollar signs are escaped.
		 */
		public static function esc_ds($string = '', $times = 0)
		{
			$times = is_numeric($times) && $times >= 0 ? (int)$times : 1;
			return str_replace('$', str_repeat('\\', $times).'$', (string)$string);
		}

		/**
		 * Evaluates PHP code, and 'returns' output.
		 *
		 * @package s2Member\Utilities
		 * @since 110917
		 *
		 * @param string $code A string of data, possibly with embedded PHP code.
		 *
		 * @return string Output after PHP evaluation.
		 */
		public static function evl($code = '')
		{
			ob_start(); // Output buffer.

			eval ('?>'.trim($code));

			return ob_get_clean();
		}
	}
}
