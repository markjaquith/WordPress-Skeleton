<?php
/**
 * Shortcode `[s2Get /]` (inner processing routines).
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
 * @package s2Member\s2Get
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_sc_gets_in'))
{
	/**
	 * Shortcode `[s2Get /]` (inner processing routines).
	 *
	 * @package s2Member\s2Get
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_sc_gets_in
	{
		/**
		 * Handles the Shortcode for: `[s2Get /]`.
		 *
		 * @package s2Member\s2Get
		 * @since 3.5
		 *
		 * @attaches-to ``add_shortcode('s2Get');``
		 *
		 * @param array  $attr An array of Attributes.
		 * @param string $content Content inside the Shortcode.
		 * @param string $shortcode The actual Shortcode name itself.
		 *
		 * @return mixed Value of the requested data.
		 */
		public static function sc_get_details($attr = array(), $content = '', $shortcode = '')
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_sc_get_details', get_defined_vars());
			unset($__refs, $__v); // Allow variables to be modified by reference.

			c_ws_plugin__s2member_no_cache::no_cache_constants(true);

			$attr = shortcode_atts( // Attributes.
				array(
					// One of these.
					'constant'      => '',
					'user_field'    => '',
					'user_option'   => '',

					// Options.
					'user_id'        => '',
					'date_format'    => '',
					'size'           => '',
				),
				c_ws_plugin__s2member_utils_strings::trim_qts_deep((array)$attr)
			);
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_sc_get_details_after_shortcode_atts', get_defined_vars());
			unset($__refs, $__v); // Allow variables to be modified by reference.

			if($attr['constant'] && defined($attr['constant'])) // Security check here. It must start with S2MEMBER_ on a Blog Farm.
			{
				if(!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site() || preg_match('/^S2MEMBER_/i', $attr['constant']))
					$get = constant($attr['constant']);
			}
			else if($attr['user_field'] && (is_user_logged_in() || $attr['user_id']))
				{
					$user_field_args = array('size' => $attr['size']);
					$get = c_ws_plugin__s2member_utils_users::get_user_field($attr['user_field'], (int)$attr['user_id'], $user_field_args);

					if(preg_match('/time$/i', $attr['user_field']) && $attr['date_format'])
					 	if(is_numeric($get) && strlen($get) === 10) // Timestamp?
							{
								if($attr['date_format'] === 'timestamp')
									$get = (string)$get; // No change.

								else if($attr['date_format'] === 'default')
									$get = date(get_option('date_format'), (integer)$get);

								else $get = date($attr['date_format'], (integer)$get);
							}
				}
			else if($attr['user_option'] && (is_user_logged_in() || $attr['user_id']))
				{
					$get = get_user_option($attr['user_option'], (int)$attr['user_id']);

					if(preg_match('/time$/i', $attr['user_option']) && $attr['date_format'])
						if(is_numeric($get) && strlen($get) === 10) // Timestamp?
							{
								if($attr['date_format'] === 'timestamp')
									$get = (string)$get; // No change.

								else if($attr['date_format'] === 'default')
									$get = date(get_option('date_format'), (integer)$get);

								else $get = date($attr['date_format'], (integer)$get);
							}
				}
			if(isset($get) && (is_array($get) || is_object($get)))
			{
				$_get_array = $get; // Temporary variable.
				$get        = array(); // New empty array.

				foreach($_get_array as $_key_prop => $_value)
				{
					if(is_scalar($_value)) // One dimension only.
						$get[$_key_prop] = (string)$_value;
				}
				unset($_get_array, $_key_prop, $_value); // Housekeeping.

				$get = implode(', ', $get); // Convert to a string now.
			}
			if(isset($get) && !is_scalar($get))
				$get = ''; // Do not allow non-scalar values to be returned by a shortcode.
			else if(isset($get)) $get = (string)$get; // Convert to a string.

			return apply_filters('ws_plugin__s2member_sc_get_details', isset($get) ? $get : '', get_defined_vars());
		}
	}
}
