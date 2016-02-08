<?php
/**
 * Shortcode `[s2If /]` (inner processing routines).
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
 * @package s2Member\s2If
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_sc_if_conds_in'))
{
	/**
	 * Shortcode `[s2If /]` (inner processing routines).
	 *
	 * @package s2Member\s2If
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_sc_if_conds_in
	{
		/**
		 * Handles the Shortcode for: `[s2If /]`.
		 *
		 * These Shortcodes are also safe to use on a Multisite Blog Farm.
		 *
		 * Is Multisite Networking enabled? Please keep the following in mind.
		 * ``current_user_can()``, will ALWAYS return true for a Super Admin!
		 *   *(this can be confusing when testing conditionals)*.
		 *
		 * If you're running a Multisite Blog Farm, you can Filter this array:
		 *   `ws_plugin__s2member_sc_if_conditionals_blog_farm_safe`
		 *   ``$blog_farm_safe``
		 *
		 * @package s2Member\s2If
		 * @since 3.5
		 *
		 * @attaches-to ``add_shortcode('s2If')`` + _s2If, __s2If, ___s2If for nesting.
		 *
		 * @param array  $attr An array of Attributes.
		 * @param string $content Content inside the Shortcode.
		 * @param string $shortcode The actual Shortcode name itself.
		 *
		 * @return string The ``$content`` if true, else an empty string.
		 *
		 * @todo Add support for nested AND/OR conditionals inside the ONE Shortcode.
		 * @todo Address possible security issue on sites with multiple editors, some of which should not have access to this feature.
		 */
		public static function sc_if_conditionals($attr = array(), $content = '', $shortcode = '')
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_sc_if_conditionals', get_defined_vars());
			unset($__refs, $__v); // Allows variables to be modified by reference.

			c_ws_plugin__s2member_no_cache::no_cache_constants(true);

			$blog_farm_safe = apply_filters('ws_plugin__s2member_sc_if_conditionals_blog_farm_safe',
			                                array('is_user_logged_in', 'is_user_not_logged_in',
			                                      'user_is', 'user_is_not', 'user_can', 'user_cannot',
			                                      'current_user_is', 'current_user_is_not', 'current_user_can', 'current_user_cannot',
			                                      'is_admin', 'is_blog_admin', 'is_user_admin', 'is_network_admin',
			                                      'is_404', 'is_home', 'is_front_page', 'is_singular', 'is_single', 'is_page',
			                                      'is_page_template', 'is_attachment', 'is_feed', 'is_archive', 'is_search',
			                                      'is_category', 'is_tax', 'is_tag', 'has_tag', 'is_author', 'is_date',
			                                      'is_day', 'is_month', 'is_time', 'is_year', 'is_sticky', 'is_paged',
			                                      'is_preview', 'is_comments_popup', 'in_the_loop', 'comments_open',
			                                      'pings_open', 'has_excerpt', 'has_post_thumbnail'), get_defined_vars());

			$pro_is_installed = c_ws_plugin__s2member_utils_conds::pro_is_installed(); // Has pro version?

			$sc_conds_allow_arbitrary_php = $GLOBALS['WS_PLUGIN__']['s2member']['o']['sc_conds_allow_arbitrary_php'];
			if(!$pro_is_installed || (is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()))
				$sc_conds_allow_arbitrary_php = FALSE; // Always disallow on child blogs of a blog farm.

			$attr =  // Trim quote entities to prevent issues in messy editors.
				c_ws_plugin__s2member_utils_strings::trim_qts_deep((array)$attr);

			$content_if      = $content_else = NULL; // Initialize.
			$shortcode_depth = strspn($shortcode, '_'); // Based on a zero index.
			$else_tag        = '['.str_repeat('_', $shortcode_depth).'else]'; // e.g., [else], [_else], [__else]

			if(strpos($content, $else_tag) !== FALSE && $pro_is_installed)
				list($content_if, $content_else) = explode($else_tag, $content, 2);

			# Arbitrary PHP code via the `php` attribute...

			if($sc_conds_allow_arbitrary_php && isset($attr['php']))
			{
				if(($condition_succeeded = c_ws_plugin__s2member_sc_if_conds_in::evl($attr['php'])))
					$condition_content = isset($content_if) ? $content_if : $content;
				else $condition_content = isset($content_else) ? $content_else : '';

				if($condition_content) $condition_content = c_ws_plugin__s2member_utils_strings::trim_html($condition_content);

				return do_shortcode(apply_filters('ws_plugin__s2member_sc_if_conditionals', $condition_content, get_defined_vars()));
			}
			else if(isset($attr['php'])) // Site owner is trying to use `php`, but it's NOT allowed on this installation.
			{
				trigger_error('s2If syntax error. Simple Conditionals are not currently configured to allow arbitrary PHP code evaluation.', E_USER_ERROR);
				return ''; // Return now; empty string in this case.
			}
			# Default behavior otherwise...

			foreach($attr as $attr_key => $attr_value) // Detects and removes logical attributes.
				// It's NOT possible to mix logic. You MUST stick to one type of logic or another.
				// If both types of logic are needed, you MUST use two different Shortcodes.
				if(preg_match('/^(&&|&amp;&amp;|&#038;&#038;|AND|\|\||OR|[\!\=\<\>]+)$/i', $attr_value))
				{ // Stick with AND/OR. Ampersands are corrupted by the Visual Editor.

					$logicals[] = strtolower($attr_value); // Place all logicals into an array here.
					unset($attr[$attr_key]); // ^ Detect logic here. We'll use the first key #0.

					if(preg_match('/^[\!\=\<\>]+$/i', $attr_value)) // Error on these operators.
					{
						trigger_error('s2If, invalid operator [ '.$attr_value.' ]. Simple Conditionals cannot process operators like ( == != <> ). Please use Advanced (PHP) Conditionals instead.', E_USER_ERROR);
						return ''; // Return now; empty string in this case.
					}
				}
			if(!empty($logicals) && is_array($logicals) && count(array_unique($logicals)) > 1)
			{
				trigger_error('s2If, AND/OR malformed conditional logic. It\'s NOT possible to mix logic using AND/OR combinations. You MUST stick to one type of logic or another. If both types of logic are needed, you MUST use two different Shortcode expressions. Or, use Advanced (PHP) Conditionals instead.', E_USER_ERROR);
				return ''; // Return now; empty string in this case.
			}
			$conditional_logic = (!empty($logicals) && is_array($logicals) && preg_match('/^(\|\||OR)$/i', $logicals[0])) ? 'OR' : 'AND';

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_sc_if_conditionals_after_conditional_logic', get_defined_vars());
			unset($__refs, $__v); // Allows variables to be modified by reference.

			if($conditional_logic === 'AND') // This is the AND variation. This routine analyzes conditionals using AND logic (the default behavior).
			{
				foreach($attr as $attr_value) // This is the AND variation. This routine analyzes conditionals using AND logic (the default behavior).
				{
					if(preg_match('/^(\!?)(.+?)(\()(.*?)(\))$/', $attr_value, $m) && ($exclamation = $m[1]) !== 'nill' && ($conditional = $m[2]) && ($attr_args = preg_replace('/['."\r\n\t".'\s]/', '', $m[4])) !== 'nill')
					{
						if(!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site() || !(preg_match('/[\$\(\)]/', $attr_args) || preg_match('/new['."\r\n\t".'\s]/i', $attr_args)))
						{
							if(is_array($args = preg_split('/[;,]+/', $attr_args, 0, PREG_SPLIT_NO_EMPTY))) // Convert all arguments into an array. And take note; possibly into an empty array.
							{
								if(!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site() || in_array(strtolower($conditional), $blog_farm_safe))
								{
									$test = ($exclamation) ? FALSE : TRUE; // If !exclamation (false) otherwise this defaults to true.

									if(preg_match('/^\{(.*?)\}$/', $attr_args)) // Single argument passed as an array.
									{
										if($test === TRUE && !call_user_func($conditional, $args))
										{
											$condition_failed = TRUE;
											break;
										}
										else if($test === FALSE && call_user_func($conditional, $args))
										{
											$condition_failed = TRUE;
											break;
										}
									}
									else if(empty($args)) // No arguments at all.
									{
										if($test === TRUE && !call_user_func($conditional))
										{
											$condition_failed = TRUE;
											break;
										}
										else if($test === FALSE && call_user_func($conditional))
										{
											$condition_failed = TRUE;
											break;
										}
									}
									else if($test === TRUE && !call_user_func_array($conditional, $args))
									{
										$condition_failed = TRUE;
										break;
									}
									else if($test === FALSE && call_user_func_array($conditional, $args))
									{
										$condition_failed = TRUE;
										break;
									}
								}
								else
								{
									trigger_error('s2If, unsafe conditional function [ '.$attr_value.' ]', E_USER_ERROR);
									return ''; // Return now; empty string in this case.
								}
							}
							else
							{
								trigger_error('s2If, conditional args are NOT an array [ '.$attr_value.' ]', E_USER_ERROR);
								return ''; // Return now; empty string in this case.
							}
						}
						else
						{
							trigger_error('s2If, unsafe conditional args [ '.$attr_value.' ]', E_USER_ERROR);
							return ''; // Return now; empty string in this case.
						}
					}
					else
					{
						trigger_error('s2If, malformed conditional [ '.$attr_value.' ]', E_USER_ERROR);
						return ''; // Return now; empty string in this case.
					}
				}
				if(!empty($condition_failed))
					$condition_content = isset($content_else) ? $content_else : '';
				else $condition_content = isset($content_if) ? $content_if : $content;

				if($condition_content) $condition_content = c_ws_plugin__s2member_utils_strings::trim_html($condition_content);

				return do_shortcode(apply_filters('ws_plugin__s2member_sc_if_conditionals', $condition_content, get_defined_vars()));
			}
			else if($conditional_logic === 'OR') // This is the OR variation. This routine analyzes conditionals using OR logic, instead of AND logic.
			{
				foreach($attr as $attr_value) // This is the OR variation. This routine analyzes conditionals using OR logic, instead of AND logic.
				{
					if(preg_match('/^(\!?)(.+?)(\()(.*?)(\))$/', $attr_value, $m) && ($exclamation = $m[1]) !== 'nill' && ($conditional = $m[2]) && ($attr_args = preg_replace('/['."\r\n\t".'\s]/', '', $m[4])) !== 'nill')
					{
						if(!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site() || !(preg_match('/[\$\(\)]/', $attr_args) || preg_match('/new['."\r\n\t".'\s]/i', $attr_args)))
						{
							if(is_array($args = preg_split('/[;,]+/', $attr_args, 0, PREG_SPLIT_NO_EMPTY))) // Convert all arguments into an array. And take note; possibly into an empty array.
							{
								if(!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site() || in_array(strtolower($conditional), $blog_farm_safe))
								{
									$test = ($exclamation) ? FALSE : TRUE; // If !exclamation (false) otherwise this defaults to true.

									if(preg_match('/^\{(.*?)\}$/', $attr_args)) // Single argument passed as an array.
									{
										if($test === TRUE && call_user_func($conditional, $args))
										{
											$condition_succeeded = TRUE;
											break;
										}
										else if($test === FALSE && !call_user_func($conditional, $args))
										{
											$condition_succeeded = TRUE;
											break;
										}
									}
									else if(empty($args)) // No arguments at all.
									{
										if($test === TRUE && call_user_func($conditional))
										{
											$condition_succeeded = TRUE;
											break;
										}
										else if($test === FALSE && !call_user_func($conditional))
										{
											$condition_succeeded = TRUE;
											break;
										}
									}
									else if($test === TRUE && call_user_func_array($conditional, $args))
									{
										$condition_succeeded = TRUE;
										break;
									}
									else if($test === FALSE && !call_user_func_array($conditional, $args))
									{
										$condition_succeeded = TRUE;
										break;
									}
								}
								else
								{
									trigger_error('s2If, unsafe conditional function [ '.$attr_value.' ]', E_USER_ERROR);
									return ''; // Return now; empty string in this case.
								}
							}
							else
							{
								trigger_error('s2If, conditional args are NOT an array [ '.$attr_value.' ]', E_USER_ERROR);
								return ''; // Return now; empty string in this case.
							}
						}
						else
						{
							trigger_error('s2If, unsafe conditional args [ '.$attr_value.' ]', E_USER_ERROR);
							return ''; // Return now; empty string in this case.
						}
					}
					else
					{
						trigger_error('s2If, malformed conditional [ '.$attr_value.' ]', E_USER_ERROR);
						return ''; // Return now; empty string in this case.
					}
				}
				if(!empty($condition_succeeded))
					$condition_content = isset($content_if) ? $content_if : $content;
				else $condition_content = isset($content_else) ? $content_else : '';

				if($condition_content) $condition_content = c_ws_plugin__s2member_utils_strings::trim_html($condition_content);

				return do_shortcode(apply_filters('ws_plugin__s2member_sc_if_conditionals', $condition_content, get_defined_vars()));
			}
			return ''; // Default return value.
		}

		/**
		 * Sandbox for arbitrary PHP code evaluation in `[s2If/]` shortcodes.
		 *
		 * @package s2Member\s2If
		 * @since 140326
		 *
		 * @param string $expression PHP expression.
		 *
		 * @return bool TRUE if condition succeed; else FALSE.
		 */
		public static function evl($expression)
		{
			return eval('return ('.(string)$expression.');');
		}
	}
}
