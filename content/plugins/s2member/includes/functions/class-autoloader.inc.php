<?php
/**
 * s2Member class autoloader.
 *
 * Defines the __autoload function for s2Member classes.
 * This highly optimizes s2Member. Giving it a much smaller footprint.
 * See: {@link http://www.php.net/manual/en/function.spl-autoload-register.php}
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
 * @package s2Member
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!function_exists('ws_plugin__s2member_classes'))
{
	/**
	 * s2Member class autoloader.
	 *
	 * The __autoload function for s2Member classes.
	 * This highly optimizes s2Member. Giving it a much smaller footprint.
	 * See: {@link http://www.php.net/manual/en/function.spl-autoload-register.php}
	 *
	 * @package s2Member
	 * @since 3.5
	 *
	 * @param string $class The class that needs to be loaded. Passed in by PHP itself.
	 */
	function ws_plugin__s2member_classes($class = '')
	{
		static $c; // Holds the classes directory location (location is optimized with a static var).
		static $c_class_dirs; // All possible dir & sub-directory locations (with a static var).

		if(strpos($class, 'c_ws_plugin__s2member_') === 0 && strpos($class, 'c_ws_plugin__s2member_pro_') === FALSE)
		{
			$c            = (!isset ($c)) ? dirname(dirname(__FILE__)).'/classes' : $c; // Configures location of classes.
			$c_class_dirs = (!isset ($c_class_dirs)) ? array_merge(array($c), _ws_plugin__s2member_classes_scan_dirs_r($c)) : $c_class_dirs;

			$class = str_replace('_', '-', str_replace('c_ws_plugin__s2member_', '', $class));

			foreach($c_class_dirs as $class_dir) // Start looking for the class.
				if($class_dir === $c || strpos($class, basename($class_dir)) === 0)
					if(file_exists($class_dir.'/'.$class.'.inc.php'))
					{
						include_once $class_dir.'/'.$class.'.inc.php';
						break; // Now stop looking.
					}
		}
	}

	/**
	 * Scans recursively for class sub-directories.
	 *
	 * Used by the s2Member autoloader.
	 *
	 * @package s2Member
	 * @since 3.5
	 *
	 * @param string $starting_dir The directory to start scanning from.
	 *
	 * @return string[] An array of class directories.
	 */
	function _ws_plugin__s2member_classes_scan_dirs_r($starting_dir = '')
	{
		$dirs = array(); // Initialize dirs array.

		foreach(func_get_args() as $starting_dir)
			if($starting_dir && is_dir($starting_dir))
				foreach(scandir($starting_dir) as $dir) // Scan this directory.
					if($dir !== '.' && $dir !== '..' && is_dir($dir = $starting_dir.'/'.$dir))
						$dirs = array_merge($dirs, array($dir), _ws_plugin__s2member_classes_scan_dirs_r($dir));

		return $dirs; // Return all directories.
	}

	spl_autoload_register('ws_plugin__s2member_classes');
}