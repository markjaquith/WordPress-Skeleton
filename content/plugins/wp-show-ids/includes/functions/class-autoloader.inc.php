<?php
/*
Copyright: © 2009 WebSharks, Inc. ( coded in the USA )
<mailto:support@websharks-inc.com> <http://www.websharks-inc.com/>

Released under the terms of the GNU General Public License.
You should have received a copy of the GNU General Public License,
along with this software. In the main directory, see: /licensing/
If not, see: <http://www.gnu.org/licenses/>.
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/*
The __autoload function for all WP Show IDs plugin classes.
This highly optimizes the plugin. Giving it a much smaller footprint.
See: http://www.php.net/manual/en/function.spl-autoload-register.php
*/
if (!function_exists ("ws_plugin__wp_show_ids_classes")) /* Already exists? */
	{
		function ws_plugin__wp_show_ids_classes ($class = FALSE) /* Build dynamic __autoload function. */
			{
				static $c; /* Holds the classes directory location ( location is optimized with a static var ). */
				static $c_class_dirs; /* All possible dir & sub-directory locations ( with a static var ). */
				/**/
				if (strpos ($class, "c_ws_plugin__wp_show_ids_") === 0) /* Quick check. Is this a class for the plugin? */
					{
						$c = (!isset ($c)) ? dirname (dirname (__FILE__)) . "/classes" : $c; /* Configures location of classes. */
						$c_class_dirs = (!isset ($c_class_dirs)) ? array_merge (array ($c), _ws_plugin__wp_show_ids_classes_scan_dirs_r ($c)) : $c_class_dirs;
						/**/
						$class = str_replace ("_", "-", str_replace ("c_ws_plugin__wp_show_ids_", "", $class));
						/**/
						foreach ($c_class_dirs as $class_dir) /* Start looking for the class. */
							if ($class_dir === $c || strpos ($class, basename ($class_dir)) === 0)
								if (file_exists ($class_dir . "/" . $class . ".inc.php"))
									{
										include_once $class_dir . "/" . $class . ".inc.php";
										/**/
										break; /* Now stop looking. */
									}
					}
			}
		function _ws_plugin__wp_show_ids_classes_scan_dirs_r ($starting_dir = FALSE)
			{
				$dirs = array (); /* Initialize dirs array. */
				/**/
				foreach (func_get_args () as $starting_dir)
					if (is_dir ($starting_dir)) /* Does this directory exist? */
						foreach (scandir ($starting_dir) as $dir) /* Scan this directory. */
							if ($dir !== "." && $dir !== ".." && is_dir ($dir = $starting_dir . "/" . $dir))
								$dirs = array_merge ($dirs, array ($dir), _ws_plugin__wp_show_ids_classes_scan_dirs_r ($dir));
				/**/
				return $dirs; /* Return array of all directories. */
			}
		/**/
		spl_autoload_register ("ws_plugin__wp_show_ids_classes"); /* Register __autoload. */
	}
?>