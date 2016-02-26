<?php
/**
* Captcha utilities.
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
	exit ('Do not access this file directly.');

if (!class_exists ('c_ws_plugin__s2member_utils_captchas'))
	{
		/**
		* Captcha utilities.
		*
		* @package s2Member\Utilities
		* @since 3.5
		*/
		class c_ws_plugin__s2member_utils_captchas
			{
				/**
				* Which reCAPTCHA™ version.
				*
				* @package s2Member\Utilities
				* @since 150717
				*
				* @return string The version number.
				*/
				public static function recaptcha_version()
					{
						return apply_filters('ws_plugin__s2member_recaptcha_version', '1', get_defined_vars());
					}

				/**
				* Public/private keys to use for reCAPTCHA™.
				*
				* @package s2Member\Utilities
				* @since 111203
				*
				* @return array An array with with two elements: `public` and `private`.
				*/
				public static function recaptcha_keys()
					{
						// NOTE: Version 2 keys are only possible w/ s2Member Pro filters.

						$public  = $GLOBALS['WS_PLUGIN__']['s2member']['c']['recaptcha']['public_key'];
						$private = $GLOBALS['WS_PLUGIN__']['s2member']['c']['recaptcha']['private_key'];

						return apply_filters('ws_plugin__s2member_recaptcha_keys', array('public' => $public, 'private' => $private), get_defined_vars ());
					}

				/**
				* reCAPTCHA™ post vars.
				*
				* @package s2Member\Utilities
				* @since 150717
				*
				* @param array $post_vars Existing post vars array.
				*
				* @return array Post vars array, with reCAPTCHA™ challenge/response.
				*/
				public static function recaptcha_post_vars($post_vars = array())
					{
						$post_vars = (array)$post_vars; // Force array.

						if(self::recaptcha_version() === '2')
						{
							$post_vars['g-recaptcha-response']      = isset($_POST['g-recaptcha-response']) ? trim(stripslashes((string)$_POST['g-recaptcha-response'])) : '';
							$post_vars['recaptcha_challenge_field'] = $post_vars['recaptcha_response_field'] = $post_vars['g-recaptcha-response']; // Compatibility.

							return apply_filters('ws_plugin__s2member_recaptcha_post_vars', $post_vars, get_defined_vars());
						}
						$post_vars['recaptcha_challenge_field']     = isset($_POST['recaptcha_challenge_field']) ? trim(stripslashes((string)$_POST['recaptcha_challenge_field'])) : '';
						$post_vars['recaptcha_response_field']      = isset($_POST['recaptcha_response_field']) ? trim(stripslashes((string)$_POST['recaptcha_response_field'])) : '';

						return apply_filters('ws_plugin__s2member_recaptcha_post_vars', $post_vars, get_defined_vars());
					}

				/**
				* Verifies a reCAPTCHA™ code via Google.
				*
				* @package s2Member\Utilities
				* @since 3.5
				*
				* @param string $challenge The value of `recaptcha_challenge_field` during form submisson.
				* @param string $response The value of `recaptcha_response_field` during form submission.
				* @return bool True if ``$response`` is valid, else false.
				*/
				public static function recaptcha_code_validates($challenge = '', $response = '')
					{
						$keys = c_ws_plugin__s2member_utils_captchas::recaptcha_keys();

						if(self::recaptcha_version() === '2') // New API verifier.
							{
								$api_post_vars = array('secret' => $keys['private'], 'response' => $response, 'remoteip' => $_SERVER['REMOTE_ADDR']);
								$api_response  = c_ws_plugin__s2member_utils_urls::remote('https://www.google.com/recaptcha/api/siteverify', $api_post_vars);
								$api_response  = json_decode($api_response);

								return is_object($api_response) && !empty($api_response->success);
							}
						else // Old API call; note that this is NOT over SSL for some reason.
							{
								$api_post_vars = array('privatekey' => $keys['private'], 'challenge' => $challenge, 'response' => $response, 'remoteip' => $_SERVER['REMOTE_ADDR']);
								$api_response  = c_ws_plugin__s2member_utils_urls::remote ('http://www.google.com/recaptcha/api/verify', $api_post_vars);

								return preg_match('/^true/i', trim($api_response));
							}
					}

				/**
				* Builds a reCAPTCHA™ JavaScript `script` tag for display.
				*
				* @package s2Member\Utilities
				* @since 3.5
				*
				* @param string $theme Optional. The theme used in display. Defaults to `clean`.
				* @param string $tabindex Optional. Value of `tabindex=""` attribute. Defaults to `-1`.
				* @param string $error Optional. An error message to display.
				* @return string HTML markup for JavaScript tag.
				*/
				public static function recaptcha_script_tag($theme = '', $tabindex = '', $error = '')
					{
						$theme             = $theme ? $theme : 'clean';
						$tabindex          = strlen($tabindex) ? (int)$tabindex : -1;
						$keys              = c_ws_plugin__s2member_utils_captchas::recaptcha_keys();

						if(self::recaptcha_version() === '2') // New API verifier.
						{
							$theme = !$theme || in_array($theme, array('red', 'white', 'clean', 'blackglass'), TRUE) ? 'light' : $theme;
							return '<div class="g-recaptcha" data-sitekey="'.esc_attr($keys['public']).'" data-size="normal" data-theme="'.esc_attr($theme).'" data-tabindex="'.esc_attr($tabindex).'"></div>'.
									'<script src="https://www.google.com/recaptcha/api.js"></script>';
						}
						$options           = '<script type="text/javascript">'."if(typeof RecaptchaOptions !== 'object'){ var RecaptchaOptions = {theme: '".c_ws_plugin__s2member_utils_strings::esc_js_sq($theme)."', lang: '".c_ws_plugin__s2member_utils_strings::esc_js_sq($GLOBALS['WS_PLUGIN__']['s2member']['c']['recaptcha']['lang'])."', tabindex: ".$tabindex." }; }".'</script>'."\n";
						$no_tabindex_icons = '<script type="text/javascript">'."if(typeof jQuery === 'function'){ jQuery('td a[id^=\"recaptcha\"]').removeAttr('tabindex'); }".'</script>';
						$adjustments       = !apply_filters('c_ws_plugin__s2member_utils_tabindex_recaptcha_icons', false, get_defined_vars ()) ? $no_tabindex_icons : '';

						return $options.'<script type="text/javascript" src="'.esc_attr('https://www.google.com/recaptcha/api/challenge?k='.urlencode($keys['public'])).($error ? '&amp;error='.urlencode($error) : '').'"></script>'.$adjustments;
					}
			}
	}
