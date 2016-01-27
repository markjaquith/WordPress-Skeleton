<?php
/**
* Form utilities.
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

if (!class_exists ("c_ws_plugin__s2member_utils_forms"))
	{
		/**
		* Form utilities.
		*
		* @package s2Member\Utilities
		* @since 3.5
		*/
		class c_ws_plugin__s2member_utils_forms
			{
				/**
				* Converts a form with hidden inputs into a URL w/ query string.
				*
				* @package s2Member\Utilities
				* @since 3.5
				*
				* @param string $form A form tag with hidden input fields.
				* @return string A URL with query string equivalents.
				*/
				public static function form_whips_2_url ($form = FALSE)
					{
						if (preg_match ("/\<form(.+?)\>/is", $form, $form_attr_m)) // Is this a form?
							{
								if (preg_match ("/(\s)(action)( ?)(\=)( ?)(['\"])(.+?)(['\"])/is", $form_attr_m[1], $form_action_m))
									{
										if (($url = trim ($form_action_m[7]))) // Set URL value dynamically. Now we add values.
											{
												foreach ((array)c_ws_plugin__s2member_utils_forms::form_whips_2_array($form) as $name => $value)
													{
														if (strlen ($name) && strlen ($value)) // Check $name → $value lengths.

															if (strlen ($value = (preg_match ("/^http(s)?\:\/\//i", $value)) ? rawurlencode ($value) : urlencode ($value)))
																{
																	$url = add_query_arg ($name, $value, $url);
																}
													}

												return $url;
											}
									}
							}

						return false;
					}
				/**
				* Converts a form with hidden inputs into an associative array.
				*
				* @package s2Member\Utilities
				* @since 3.5
				*
				* @param string $form A form tag with hidden input fields.
				* @return array An associative array of all hidden input fields.
				*/
				public static function form_whips_2_array($form = FALSE)
					{
						if (preg_match ("/\<form(.+?)\>/is", $form)) // Is this a form?
							{
								if (preg_match_all ("/(?<!\<\!--)\<input(.+?)\>/is", $form, $input_attr_ms, PREG_SET_ORDER))
									{
										foreach ($input_attr_ms as $input_attr_m) // Go through each hidden input variable.
											{
												if (preg_match ("/(\s)(type)( ?)(\=)( ?)(['\"])(hidden)(['\"])/is", $input_attr_m[1]))
													{
														if (preg_match ("/(\s)(name)( ?)(\=)( ?)(['\"])(.+?)(['\"])/is", $input_attr_m[1], $input_name_m))
															{
																if (preg_match ("/(\s)(value)( ?)(\=)( ?)(['\"])(.+?)(['\"])/is", $input_attr_m[1], $input_value_m))
																	{
																		$array[trim ($input_name_m[7])] = trim (wp_specialchars_decode ($input_value_m[7], ENT_QUOTES));
																	}
															}
													}
											}
									}
							}
						return (array)$array;
					}
			}
	}
