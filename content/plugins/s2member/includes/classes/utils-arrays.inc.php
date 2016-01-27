<?php
/**
* Array utilities.
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
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_utils_arrays"))
	{
		/**
		* Array utilities.
		*
		* @package s2Member\Utilities
		* @since 3.5
		*/
		class c_ws_plugin__s2member_utils_arrays
			{
				/**
				* Extends ``array_unique()`` to support multi-dimensional arrays.
				*
				* @package s2Member\Utilities
				* @since 3.5
				*
				* @param array $array Expects an incoming array.
				* @return array Returns the ``$array`` after having reduced it to a unique set of values.
				*/
				public static function array_unique ($array = FALSE)
					{
						$array = (array)$array;

						foreach ($array as &$value)
							$value = serialize ($value);

						$array = array_unique ($array);

						foreach ($array as &$value)
							$value = unserialize ($value);

						return $array;
					}
				/**
				* Searches an array *(or even a multi-dimensional array)* using a regular expression match against array values.
				*
				* @package s2Member\Utilities
				* @since 3.5
				*
				* @param string $regex A regular expression to look for inside the array.
				* @return bool True if the regular expression matched at least one value in the array, else false.
				*/
				public static function regex_in_array($regex = FALSE, $array = FALSE)
					{
						if (is_string ($regex) && strlen ($regex) && is_array($array))
							{
								foreach ($array as $value)
									{
										if (is_array($value) /* Recursive function call? */)
											{
												if (c_ws_plugin__s2member_utils_arrays::regex_in_array($regex, $value))
													return true;
											}
										else if (is_string ($value) /* Must be a string. */)
											{
												if (@preg_match ($regex, $value))
													return true;
											}
									}
								return false;
							}
						else // False.
							return false;
					}
				/**
				* Searches an array *(or even a multi-dimensional array)* of regular expressions, to match against a string value.
				*
				* @package s2Member\Utilities
				* @since 3.5
				*
				* @param string $string A string to test against.
				* @param array $array An array of regex patterns to match against ``$string``.
				* @return bool True if at least one regular expression in the ``$array`` matched ``$string``, else false.
				*/
				public static function in_regex_array($string = FALSE, $array = FALSE)
					{
						if (is_string ($string) && strlen ($string) && is_array($array))
							{
								foreach ($array as $value)
									{
										if (is_array($value) /* Recursive function call. */)
											{
												if (c_ws_plugin__s2member_utils_arrays::in_regex_array($string, $value))
													return true;
											}
										else if (is_string ($value) /* Must be a string. */)
											{
												if (@preg_match ($value, $string))
													return true;
											}
									}
								return false;
							}
						else // False.
							return false;
					}
				/**
				* Removes all null values from an array *(or even a multi-dimensional array)*.
				*
				* @package s2Member\Utilities
				* @since 111101
				*
				* @param array $array An input array.
				* @return array Returns the ``$array`` after having reduced its set of values.
				*/
				public static function remove_nulls ($array = FALSE)
					{
						$array = (array)$array;

						foreach ($array as $key => &$value)
							{
								if (is_array($value) /* Recursive function call here. */)
									$value = c_ws_plugin__s2member_utils_arrays::remove_nulls ($value);

								else if (is_null /* Is it null? */ ($value))
									unset($array[$key]);
							}
						return $array;
					}
				/**
				* Removes all 0-byte strings from an array *(or even a multi-dimensional array)*.
				*
				* @package s2Member\Utilities
				* @since 111216
				*
				* @param array $array An input array.
				* @return array Returns the ``$array`` after having reduced its set of values.
				*/
				public static function remove_0b_strings ($array = FALSE)
					{
						$array = (array)$array;

						foreach ($array as $key => &$value)
							{
								if (is_array($value) /* Recursive function call here. */)
									$value = c_ws_plugin__s2member_utils_arrays::remove_0b_strings ($value);

								else if (is_string ($value) && !strlen ($value))
									unset($array[$key]);
							}
						return $array;
					}
				/**
				* Forces string values on each array value *(also supports multi-dimensional arrays)*.
				*
				* @package s2Member\Utilities
				* @since 111101
				*
				* @param array $array An input array.
				* @return array Returns the ``$array`` after having forced it to set of string values.
				*/
				public static function force_strings ($array = FALSE)
					{
						$array = (array)$array;

						foreach ($array as &$value)
							{
								if (is_array($value) /* Recursive function call here. */)
									$value = c_ws_plugin__s2member_utils_arrays::force_strings ($value);

								else if (!is_string ($value) /* String? */)
									$value = (string)$value;
							}
						return $array;
					}
				/**
				* Forces integer values on each array value *(also supports multi-dimensional arrays)*.
				*
				* @package s2Member\Utilities
				* @since 111101
				*
				* @param array $array An input array.
				* @return array Returns the ``$array`` after having forced it to set of integer values.
				*/
				public static function force_integers ($array = array())
					{
						$array = (array)$array;

						foreach ($array as &$value)
							{
								if (is_array($value) /* Recursive function call here. */)
									$value = c_ws_plugin__s2member_utils_arrays::force_integers ($value);

								else if (!is_integer ($value) /* Integer? */)
									$value = (int)$value;
							}
						return $array;
					}
				/**
				* Sorts arrays *(also supports multi-dimensional arrays)* by key, low to high.
				*
				* @package s2Member\Utilities
				* @since 111205
				*
				* @param array $array An input array.
				* @param int $flags Optional. Can be used to modify the sorting behavior.
				* 	See: {@link http://www.php.net/manual/en/function.ksort.php}
				* @return Unlike PHP's ``ksort()``, this function returns the array, and does NOT work on a reference.
				*/
				public static function ksort_deep ($array = FALSE, $flags = SORT_REGULAR)
					{
						$array = (array)$array;
						ksort /* Sort by key. */ ($array, $flags);

						foreach ($array as &$value)
							if (is_array($value) /* Recursive function call here. */)
								$value = c_ws_plugin__s2member_utils_arrays::ksort_deep ($value, $flags);

						return /* Now return the array. */ $array;
					}
			}
	}
