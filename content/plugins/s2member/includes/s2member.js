/**
 * Core JavaScript file for the s2Member plugin.
 *
 * This is the development version of the code.
 * Which ultimately produces s2member-min.js.
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
 * @since 3.0
 */
jQuery(document)
	.ready(function($)
	       {
		       window.ws_plugin__s2member_skip_all_file_confirmations = window.ws_plugin__s2member_skip_all_file_confirmations || false;
		       var runningBuddyPress = '<?php echo c_ws_plugin__s2member_utils_conds::bp_is_installed("query-active-plugins") ? "1" : ""; ?>',
			       filesBaseDir = '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])); ?>',
			       skipAllFileConfirmations = ws_plugin__s2member_skip_all_file_confirmations ? true : false,
			       uniqueFilesDownloadedInPage = [/* Real-time counts in a single page/instance. */];

		       window.ws_plugin__s2member_passwordMinLength = function()
		       {
			       return parseInt('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(c_ws_plugin__s2member_user_securities::min_password_length()); ?>');
		       };
		       window.ws_plugin__s2member_passwordMinStrengthCode = function()
		       {
			       return '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(c_ws_plugin__s2member_user_securities::min_password_strength_code()); ?>';
		       };
		       window.ws_plugin__s2member_passwordMinStrengthLabel = function()
		       {
			       return '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(c_ws_plugin__s2member_user_securities::min_password_strength_label()); ?>';
		       };
		       window.ws_plugin__s2member_passwordMinStrengthScore = function()
		       {
			       return parseInt('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(c_ws_plugin__s2member_user_securities::min_password_strength_score()); ?>');
		       };
		       window.ws_plugin__s2member_passwordStrengthMeter = function(password1, password2, scoreOnly)
		       {
			       var score = 0, // Initialize score.
						 	minLength = ws_plugin__s2member_passwordMinLength();

						 password1 = String(password1);
						 password2 = String(password2);

						 if(password1 != password2 && password2.length > 0)
				       return 'mismatch';

			       else if(password1.length < 1)
				       return 'empty';

			       else if(password1.length < minLength)
				       return 'short';

			       if(password1.match(/[0-9]/))
				       score += 10;

			       if(password1.match(/[a-z]/))
				       score += 10;

			       if(password1.match(/[A-Z]/))
				       score += 10;

			       if(password1.match(/[^0-9a-zA-Z]/))
				       score = score === 30 ? score + 20 : score + 10;

						 if(scoreOnly) return score;
			       if(score < 30) return 'weak';
			       if(score < 50) return 'good';
			       return 'strong'; // Default return value.
		       };
		       window.ws_plugin__s2member_passwordStrength = function($username, $pass1, $pass2, $result)
		       {
			       if($username instanceof jQuery && $pass1 instanceof jQuery && $pass2 instanceof jQuery && $result instanceof jQuery)
			       {
				       var pwsL10n = { // Password strength meter translations.
					       'empty'   : '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Strength indicator", "s2member-front", "s2member")); ?>',
					       'short'   : '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Very weak", "s2member-front", "s2member")); ?>',
					       'weak'    : '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Weak", "s2member-front", "s2member")); ?>',
					       'good'    : '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Good", "s2member-front", "s2member")); ?>',
					       'strong'  : '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Strong", "s2member-front", "s2member")); ?>',
					       'mismatch': '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Mismatch", "s2member-front", "s2member")); ?>'
				       };
				       $result.removeClass('ws-plugin--s2member-password-strength-short');
				       $result.removeClass('ws-plugin--s2member-password-strength-weak');
				       $result.removeClass('ws-plugin--s2member-password-strength-good');
				       $result.removeClass('ws-plugin--s2member-password-strength-strong');
				       $result.removeClass('ws-plugin--s2member-password-strength-mismatch');
				       $result.removeClass('ws-plugin--s2member-password-strength-empty');

				       var meterSays = ws_plugin__s2member_passwordStrengthMeter($pass1.val(), $pass2.val());
				       $result.addClass('ws-plugin--s2member-password-strength-' + meterSays).html(pwsL10n[meterSays]);
			       }
		       };
		       window.ws_plugin__s2member_validationErrors = function(label, field, context, required, expected)
		       {
			       if(typeof label === 'string' && label && typeof field === 'object' && typeof context === 'object')
				       if(typeof field.tagName === 'string' && /^(input|textarea|select)$/i.test(field.tagName) && !field.disabled)
				       {
					       var tag = field.tagName.toLowerCase(), $field = $(field), type = $.trim($field.attr('type')).toLowerCase(), name = $.trim($field.attr('name')), value = $field.val();
					       required = ( typeof required === 'boolean') ? required : ($field.attr('aria-required') === 'true'), expected = ( typeof expected === 'string') ? expected : $.trim($field.attr('data-expected'));

					       var forcePersonalEmails = ('<?php echo strlen($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_force_personal_emails"]); ?>' > 0);
					       var nonPersonalEmailUsers = new RegExp('^(<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq (implode ("|", preg_split ("/[\r\n\t ;,]+/", preg_quote ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_force_personal_emails"], "/")))); ?>)@', 'i');

					       if(tag === 'input' && type === 'checkbox' && /\[\]$/.test(name))
					       {
						       if(typeof field.id === 'string' && /-0$/.test(field.id))
							       if(required && !$('input[name="' + ws_plugin__s2member_escjQAttr(name) + '"]:checked', context).length)
								       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Please check at least one of the boxes.", "s2member-front", "s2member")); ?>';
					       }
					       else if(tag === 'input' && type === 'checkbox')
					       {
						       if(required && !field.checked)
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Required. This box must be checked.", "s2member-front", "s2member")); ?>';
					       }
					       else if(tag === 'input' && type === 'radio')
					       {
						       if(typeof field.id === 'string' && /-0$/.test(field.id))
							       if(required && !$('input[name="' + ws_plugin__s2member_escjQAttr(name) + '"]:checked', context).length)
								       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Please select one of the options.", "s2member-front", "s2member")); ?>';
					       }
					       else if(tag === 'select' && $field.attr('multiple'))
					       {
						       if(required && (!(value instanceof Array) || !value.length))
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Please select at least one of the options.", "s2member-front", "s2member")); ?>';
					       }
					       else if(typeof value !== 'string' || (required && !(value = $.trim(value)).length))
					       {
						       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("This is a required field, please try again.", "s2member-front", "s2member")); ?>';
					       }
					       else if((value = $.trim(value)).length && ((tag === 'input' && /^(text|password)$/i.test(type)) || tag === 'textarea') && typeof expected === 'string' && expected.length)
					       {
						       if(expected === 'numeric-wp-commas' && (!/^[0-9\.,]+$/.test(value) || isNaN(value.replace(/,/g, ''))))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Must be numeric (with or without decimals, commas allowed).", "s2member-front", "s2member")); ?>';
						       }
						       else if(expected === 'numeric' && (!/^[0-9\.]+$/.test(value) || isNaN(value)))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Must be numeric (with or without decimals, no commas).", "s2member-front", "s2member")); ?>';
						       }
						       else if(expected === 'integer' && (!/^[0-9]+$/.test(value) || isNaN(value)))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Must be an integer (a whole number, without any decimals).", "s2member-front", "s2member")); ?>';
						       }
						       else if(expected === 'integer-gt-0' && (!/^[0-9]+$/.test(value) || isNaN(value) || value <= 0))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Must be an integer > 0 (whole number, no decimals, greater than 0).", "s2member-front", "s2member")); ?>';
						       }
						       else if(expected === 'float' && (!/^[0-9\.]+$/.test(value) || !/[0-9]/.test(value) || !/\./.test(value) || isNaN(value)))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Must be a float (floating point number, decimals required).", "s2member-front", "s2member")); ?>';
						       }
						       else if(expected === 'float-gt-0' && (!/^[0-9\.]+$/.test(value) || !/[0-9]/.test(value) || !/\./.test(value) || isNaN(value) || value <= 0))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Must be a float > 0 (floating point number, decimals required, greater than 0).", "s2member-front", "s2member")); ?>';
						       }
						       else if(expected === 'date' && !/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/.test(value))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Must be a date (required date format: dd/mm/yyyy).", "s2member-front", "s2member")); ?>';
						       }
						       else if(expected === 'email' && !/^[a-zA-Z0-9_!#$%&*+=?`{}~|\/\^\'\-]+(?:\.?[a-zA-Z0-9_!#$%&*+=?`{}~|\/\^\'\-]+)*@[a-zA-Z0-9]+(?:\-*[a-zA-Z0-9]+)*(?:\.[a-zA-Z0-9]+(?:\-*[a-zA-Z0-9]+)*)*(?:\.[a-zA-Z][a-zA-Z0-9]+)?$/.test(value))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Must be a valid email address.", "s2member-front", "s2member")); ?>';
						       }
						       else if(expected === 'email' && forcePersonalEmails && nonPersonalEmailUsers.test(value))
						       {
							       return label + '\n' + $.sprintf('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Please use a personal email address.\nAddresses like <%s@> are problematic.", "s2member-front", "s2member")); ?>', value.split('@')[0]);
						       }
						       else if(expected === 'url' && !/^https?\:\/\/.+$/i.test(value))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Must be a full URL (starting with http or https).", "s2member-front", "s2member")); ?>';
						       }
						       else if(expected === 'domain' && !/^[a-zA-Z0-9]+(?:\-*[a-zA-Z0-9]+)*(?:\.[a-zA-Z0-9]+(?:\-*[a-zA-Z0-9]+)*)*(?:\.[a-zA-Z][a-zA-Z0-9]+)?$/.test(value))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Must be a domain name (domain name only, without http).", "s2member-front", "s2member")); ?>';
						       }
						       else if(expected === 'phone' && (!/^[0-9 ()\-]+$/.test(value) || value.replace(/[^0-9]+/g, '').length !== 10))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Must be a phone # (10 digits w/possible hyphens, spaces, brackets).", "s2member-front", "s2member")); ?>';
						       }
						       else if(expected === 'uszip' && !/^[0-9]{5}(?:\-[0-9]{4})?$/.test(value))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Must be a US zipcode (5-9 digits w/ possible hyphen).", "s2member-front", "s2member")); ?>';
						       }
						       else if(expected === 'cazip' && !/^[0-9A-Z]{3} ?[0-9A-Z]{3}$/i.test(value))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Must be a Canadian zipcode (6 alpha-numerics w/possible space).", "s2member-front", "s2member")); ?>';
						       }
						       else if(expected === 'uczip' && !/^[0-9]{5}(?:\-[0-9]{4})?$/.test(value) && !/^[0-9A-Z]{3} ?[0-9A-Z]{3}$/i.test(value))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Must be a zipcode (either a US or Canadian zipcode).", "s2member-front", "s2member")); ?>';
						       }
						       else if(/^alphanumerics\-spaces\-punctuation\-[0-9]+(?:\-e)?$/.test(expected) && !/^[a-z 0-9\/\\\\,.?:;"\'{}[\]\^|+=_()*&%$#@!`~\-]+$/i.test(value))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Please use alphanumerics, spaces & punctuation only.", "s2member-front", "s2member")); ?>';
						       }
						       else if(/^alphanumerics\-spaces\-[0-9]+(?:\-e)?$/.test(expected) && !/^[a-z 0-9]+$/i.test(value))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Please use alphanumerics & spaces only.", "s2member-front", "s2member")); ?>';
						       }
						       else if(/^alphanumerics\-punctuation\-[0-9]+(?:\-e)?$/.test(expected) && !/^[a-z0-9\/\\\\,.?:;"\'{}[\]\^|+=_()*&%$#@!`~\-]+$/i.test(value))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Please use alphanumerics & punctuation only (no spaces).", "s2member-front", "s2member")); ?>';
						       }
						       else if(/^alphanumerics\-[0-9]+(?:\-e)?$/.test(expected) && !/^[a-z0-9]+$/i.test(value))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Please use alphanumerics only (no spaces/punctuation).", "s2member-front", "s2member")); ?>';
						       }
						       else if(/^alphabetics\-[0-9]+(?:\-e)?$/.test(expected) && !/^[a-z]+$/i.test(value))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Please use alphabetics only (no digits/spaces/punctuation).", "s2member-front", "s2member")); ?>';
						       }
						       else if(/^numerics\-[0-9]+(?:\-e)?$/.test(expected) && !/^[0-9]+$/i.test(value))
						       {
							       return label + '\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Please use numeric digits only.", "s2member-front", "s2member")); ?>';
						       }
						       else if(/^(?:any|alphanumerics\-spaces\-punctuation|alphanumerics\-spaces|alphanumerics\-punctuation|alphanumerics|alphabetics|numerics)\-[0-9]+(?:\-e)?$/.test(expected))
						       {
							       var split = expected.split('-'), length = Number(split[1]), exactLength = (split.length > 2 && split[2] === 'e');

							       if(exactLength && value.length !== length/* An exact length is required? */)
								       return label + '\n' + $.sprintf('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Must be exactly %s %s.", "s2member-front", "s2member")); ?>', length, ((split[0] === 'numerics') ? ((length === 1) ? '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("digit", "s2member-front", "s2member")); ?>' : '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("digits", "s2member-front", "s2member")); ?>') : ((length === 1) ? '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("character", "s2member-front", "s2member")); ?>' : '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("characters", "s2member-front", "s2member")); ?>')));

							       else if(value.length < length/* Otherwise, we interpret as the minimum length. */)
								       return label + '\n' + $.sprintf('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Must be at least %s %s.", "s2member-front", "s2member")); ?>', length, ((split[0] === 'numerics') ? ((length === 1) ? '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("digit", "s2member-front", "s2member")); ?>' : '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("digits", "s2member-front", "s2member")); ?>') : ((length === 1) ? '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("character", "s2member-front", "s2member")); ?>' : '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("characters", "s2member-front", "s2member")); ?>')));
						       }
					       }
				       }
			       return ''; // No errors in this case.
		       };
		       window.ws_plugin__s2member_animateProcessing = function($obj, reset)
		       {
			       if(reset)
				       $($obj).removeClass('ws-plugin--s2member-animate-processing');
			       else $($obj).addClass('ws-plugin--s2member-animate-processing');
		       };
		       window.ws_plugin__s2member_escAttr = window.ws_plugin__s2member_escHtml = function(string)
		       {
			       if(/[&\<\>"']/.test(string = String(string)))
				       string = string.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'),
					       string = string.replace(/"/g, '&quot;').replace(/'/g, '&#039;');
			       return string;
		       };
		       window.ws_plugin__s2member_escjQAttr = function(string)
		       {
			       return String(string).replace(/([.:\[\]])/g, '\\$1');
		       };
		       if(!skipAllFileConfirmations && S2MEMBER_CURRENT_USER_IS_LOGGED_IN && S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY < S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED)
		       {
			       $('a[href*="s2member_file_download="], a[href*="/s2member-files/"], a[href^="s2member-files/"], a[href*="/' + filesBaseDir.replace(/([\:\.\[\]])/g, '\\$1') + '/"], a[href^="' + filesBaseDir.replace(/([\:\.\[\]])/g, '\\$1') + '/"]')
				       .click(function()
				              {
					              if(!/s2member[_\-]file[_\-]download[_\-]key[\=\-].+/i.test(this.href)/* Do NOT prompt on downloads issued with a Key. */)
					              {
						              var c = '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("— Confirm File Download —", "s2member-front", "s2member")); ?>' + '\n\n';
						              c += $.sprintf('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("You`ve downloaded %s protected %s in the last %s.", "s2member-front", "s2member")); ?>', S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY, ((S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY === 1) ? '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("file", "s2member-front", "s2member")); ?>' : '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("files", "s2member-front", "s2member")); ?>'), ((S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS === 1) ? '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("24 hours", "s2member-front", "s2member")); ?>' : $.sprintf('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("%s days", "s2member-front", "s2member")); ?>', S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS))) + '\n\n';
						              c += (S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED) ? '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("You`re entitled to UNLIMITED downloads though (so, no worries).", "s2member-front", "s2member")); ?>' : $.sprintf('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("You`re entitled to %s unique %s %s.", "s2member-front", "s2member")); ?>', S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED, ((S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED === 1) ? '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("download", "s2member-front", "s2member")); ?>' : '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("downloads", "s2member-front", "s2member")); ?>'), ((S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS === 1) ? '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("each day", "s2member-front", "s2member")); ?>' : $.sprintf('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("every %s-day period", "s2member-front", "s2member")); ?>', S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS)));

						              if((/s2member[_\-]skip[_\-]confirmation/i.test(this.href) && !/s2member[_\-]skip[_\-]confirmation[\=\-](0|no|false)/i.test(this.href)) || confirm(c))
						              {
							              if($.inArray(this.href, uniqueFilesDownloadedInPage) === -1)
								              S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY++, uniqueFilesDownloadedInPage.push(this.href);
							              return true;
						              }
						              return false;
					              }
					              return true;
				              });
		       }
		       if(!/\/wp-admin([\/?#]|$)/i.test(location.href))
		       {
			       $('input#ws-plugin--s2member-profile-password1, input#ws-plugin--s2member-profile-password2')
				       .on('keyup initialize.s2', function()
				              {
					              ws_plugin__s2member_passwordStrength(
						              $('input#ws-plugin--s2member-profile-login'),
						              $('input#ws-plugin--s2member-profile-password1'),
						              $('input#ws-plugin--s2member-profile-password2'),
						              $('div#ws-plugin--s2member-profile-password-strength')
					              );
				              }).trigger('initialize.s2');
			       $('form#ws-plugin--s2member-profile')
				       .submit(function(/* Validate Profile. */)
				               {
					               var context = this, label = '', error = '', errors = '',
						               $password1 = $('input#ws-plugin--s2member-profile-password1', context),
						               $password2 = $('input#ws-plugin--s2member-profile-password2', context);

												 var $submissionButton = $('input#ws-plugin--s2member-profile-submit', context);

					               $(':input', context)
						               .each(function(/* Go through them all together. */)
						                     {
							                     var id = /* Remove numeric suffixes. */ $.trim($(this).attr('id')).replace(/---[0-9]+$/g, '');

							                     if(id && (label = $.trim($('label[for="' + id + '"]', context).first().children('strong').first().text().replace(/[\r\n\t]+/g, ' '))))
							                     {
								                     if(error = ws_plugin__s2member_validationErrors(label, this, context))
									                     errors += /* Collect errors. */ error + '\n\n';
							                     }
						                     });
					               if(errors = $.trim(errors))
					               {
						               alert('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + errors);
						               return false;
					               }
					               else if($.trim($password1.val()) && $.trim($password1.val()) !== $.trim($password2.val()))
					               {
						               alert('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Passwords do not match up. Please try again.", "s2member-front", "s2member")); ?>');
						               return false;
					               }
					               else if($.trim($password1.val()) && $.trim($password1.val()).length < ws_plugin__s2member_passwordMinLength())
					               {
						               alert('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(sprintf(_x("Password MUST be at least %s characters. Please try again.", "s2member-front", "s2member"), c_ws_plugin__s2member_user_securities::min_password_length())); ?>');
						               return false;
					               }
					               else if($.trim($password1.val()) && ws_plugin__s2member_passwordStrengthMeter($.trim($password1.val()), $.trim($password2.val()), true) < ws_plugin__s2member_passwordMinStrengthScore())
					               {
						               alert('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(sprintf(_x("Password strength MUST be %s. Please try again.", "s2member-front", "s2member"), c_ws_plugin__s2member_user_securities::min_password_strength_label())); ?>');
						               return false;
					               }
					               ws_plugin__s2member_animateProcessing($submissionButton);
					               return true;
				               });
		       }
		       if(/\/wp-signup\.php/i.test(location.href))
		       {
			       $('div#content > div.mu_register > form#setupform')
				       .submit(function()
				               {
					               var context = this, label = '', error = '', errors = '',
						               $submissionButton = $('p.submit input[type="submit"]', context);

					               $('input#user_email', context).attr('data-expected', 'email');
					               $('input#user_name, input#user_email, input#blogname, input#blog_title, input#captcha_code', context).attr({'aria-required': 'true'});

					               $(':input', context)
						               .each(function(/* Go through them all together. */)
						                     {
							                     var id = $.trim($(this).attr('id')).replace(/---[0-9]+$/g, ''/* Remove numeric suffixes. */);

							                     if(id && (label = $.trim($('label[for="' + id + '"]', context).first().text().replace(/[\r\n\t]+/g, ' '))))
							                     {
								                     if(error = ws_plugin__s2member_validationErrors(label, this, context))
									                     errors += error + '\n\n'/* Collect errors. */;
							                     }
						                     });
					               if(errors = $.trim(errors))
					               {
						               alert('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + errors);
						               return false;
					               }
					               ws_plugin__s2member_animateProcessing($submissionButton);
					               return true;
				               });
		       }
		       if(/\/wp-login\.php/i.test(location.href))
		       {
			       $('div#login > form#registerform input#user_login').attr('tabindex', '10');
			       $('div#login > form#registerform input#user_email').attr('tabindex', '20');
			       $('div#login > form#registerform input#wp-submit').attr('tabindex', '1000');

			       $('input#ws-plugin--s2member-custom-reg-field-user-pass1, input#ws-plugin--s2member-custom-reg-field-user-pass2')
				       .on('keyup initialize.s2', function()
				              {
					              ws_plugin__s2member_passwordStrength(
						              $('input#user_login'),
						              $('input#ws-plugin--s2member-custom-reg-field-user-pass1'),
						              $('input#ws-plugin--s2member-custom-reg-field-user-pass2'),
						              $('div#ws-plugin--s2member-custom-reg-field-user-pass-strength')
					              );
				              }).trigger('initialize.s2');
			       $('div#login > form#registerform')
				       .submit(function()
				               {
					               var context = this, label = '', error = '', errors = '',
						               $pass1 = $('input#ws-plugin--s2member-custom-reg-field-user-pass1[aria-required="true"]', context),
						               $pass2 = $('input#ws-plugin--s2member-custom-reg-field-user-pass2', context),
						               $submissionButton = $('input#wp-submit', context/* Registration submission button. */);

					               $('input#user_email', context).attr('data-expected', 'email');
					               $('input#user_login, input#user_email, input#captcha_code', context).attr({'aria-required': 'true'});

					               $(':input', context)
						               .each(function(/* Go through them all together. */)
						                     {
							                     var id = $.trim($(this).attr('id')).replace(/---[0-9]+$/g, ''/* Remove numeric suffixes. */);

							                     if($.inArray(id, ['user_login', 'user_email', 'captcha_code']) !== -1/* No for="" attribute on these fields. */)
							                     {
								                     if((label = $.trim($(this).parent('label').text().replace(/[\r\n\t]+/g, ' '))))
								                     {
									                     if(error = ws_plugin__s2member_validationErrors(label, this, context))
										                     errors += error + '\n\n'/* Collect errors. */;
								                     }
							                     }
							                     else if(id && (label = $.trim($('label[for="' + id + '"]', context).first().children('span').first().text().replace(/[\r\n\t]+/g, ' '))))
							                     {
								                     if(error = ws_plugin__s2member_validationErrors(label, this, context))
									                     errors += error + '\n\n'/* Collect errors. */;
							                     }
						                     });
					               if(errors = $.trim(errors))
					               {
						               alert('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + errors);
						               return false;
					               }
					               else if($pass1.length && $.trim($pass1.val()) !== $.trim($pass2.val()))
					               {
						               alert('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("Passwords do not match up. Please try again.", "s2member-front", "s2member")); ?>');
						               return false;
					               }
					               else if($pass1.length && $.trim($pass1.val()).length < ws_plugin__s2member_passwordMinLength())
					               {
						               alert('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(sprintf(_x("Password MUST be at least %s characters. Please try again.", "s2member-front", "s2member"), c_ws_plugin__s2member_user_securities::min_password_length())); ?>');
						               return false;
					               }
					               else if($pass1.length && ws_plugin__s2member_passwordStrengthMeter($.trim($pass1.val()), $.trim($pass2.val()), true) < ws_plugin__s2member_passwordMinStrengthScore())
					               {
						               alert('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + '<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(sprintf(_x("Password strength MUST be %s. Please try again.", "s2member-front", "s2member"), c_ws_plugin__s2member_user_securities::min_password_strength_label())); ?>');
						               return false;
					               }
					               ws_plugin__s2member_animateProcessing($submissionButton);
					               return true;
				               });
		       }
		       if(/\/wp-admin\/(?:user\/)?profile\.php/i.test(location.href))
		       {
			       $('form#your-profile')
				       .submit(function(/* Validation. */)
				               {
					               var context = this, label = '', error = '', errors = '';

					               $('input#email', context).attr('data-expected', 'email');

					               $(':input[id^="ws-plugin--s2member-profile-"]', context)
						               .each(function(/* Go through them all together. */)
						                     {
							                     var id = /* Remove numeric suffixes. */ $.trim($(this).attr('id')).replace(/---[0-9]+$/g, '');

							                     if(id && (label = $.trim($('label[for="' + id + '"]', context).first().text().replace(/[\r\n\t]+/g, ' '))))
							                     {
								                     if(error = ws_plugin__s2member_validationErrors(label, this, context))
									                     errors += error + '\n\n'/* Collect errors. */;
							                     }
						                     });
					               if(errors = $.trim(errors))
					               {
						               alert('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + errors);
						               return false;
					               }
					               return true;
				               });
		       }
		       if(runningBuddyPress/* Attach form submission handler to `/register` for BuddyPress. */)
		       {
			       $('body.registration form div#ws-plugin--s2member-custom-reg-fields-4bp-section').closest('form')
				       .submit(function()
				               {
					               var context = this, label = '', error = '', errors = '';

					               $('input#signup_email', context).attr('data-expected', 'email');
					               $('input#signup_username, input#signup_email, input#signup_password, input#field_1', context).attr({'aria-required': 'true'});

					               $(':input', context)
						               .each(function(/* Go through them all together. */)
						                     {
							                     var id = $.trim($(this).attr('id')).replace(/---[0-9]+$/g, ''/* Remove numeric suffixes. */);

							                     if(id && (label = $.trim($('label[for="' + id + '"]', context).first().text().replace(/[\r\n\t]+/g, ' '))))
							                     {
								                     if(error = ws_plugin__s2member_validationErrors(label, this, context))
									                     errors += error + '\n\n'/* Collect errors. */;
							                     }
						                     });
					               if(errors = $.trim(errors))
					               {
						               alert('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + errors);
						               return false;
					               }
					               return true;
				               });
			       $('body.logged-in.profile.profile-edit :input.ws-plugin--s2member-profile-field-4bp').closest('form')
				       .submit(function()
				               {
					               var context = this, label = '', error = '', errors = '';

					               $('input#field_1', context).attr({'aria-required': 'true'});

					               $(':input', context)
						               .each(function(/* Go through them all together. */)
						                     {
							                     var id = $.trim($(this).attr('id')).replace(/---[0-9]+$/g, ''/* Remove numeric suffixes. */);

							                     if(id && (label = $.trim($('label[for="' + id + '"]', context).first().text().replace(/[\r\n\t]+/g, ' '))))
							                     {
								                     if(error = ws_plugin__s2member_validationErrors(label, this, context))
									                     errors += error + '\n\n'/* Collect errors. */;
							                     }
						                     });
					               if(errors = $.trim(errors))
					               {
						               alert('<?php echo c_ws_plugin__s2member_utils_strings::esc_js_sq(_x("— Oops, you missed something: —", "s2member-front", "s2member")); ?>' + '\n\n' + errors);
						               return false;
					               }
					               return true;
				               });
		       }
	       });
