<?php
/**
 * s2Member Stand-Alone Profile page (inner processing routines).
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
 * @package s2Member\Profiles
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_profile_in'))
{
	/**
	 * s2Member Stand-Alone Profile page (inner processing routines).
	 *
	 * @package s2Member\Profiles
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_profile_in
	{
		/**
		 * Displays a Stand-Alone Profile Modification Form.
		 *
		 * @package s2Member\Profiles
		 * @since 3.5
		 *
		 * @attaches-to ``add_action("init");``
		 */
		public static function profile()
		{
			do_action('ws_plugin__s2member_before_profile', get_defined_vars());

			if(!empty($_GET['s2member_profile'])) // Requesting Profile?
			{
				c_ws_plugin__s2member_no_cache::no_cache_constants(TRUE); // No caching.

				$tabindex = apply_filters('ws_plugin__s2member_sc_profile_tabindex', 0, get_defined_vars());

				if(($user = (is_user_logged_in()) ? wp_get_current_user() : FALSE) && ($user_id = $user->ID))
				{
					echo c_ws_plugin__s2member_utils_html::doctype_html_head('My Profile', 'ws_plugin__s2member_during_profile_head');

					echo '<body style="'.esc_attr(apply_filters('ws_plugin__s2member_profile_body_styles', "background:#FFFFFF; color:#333333; font-family:'Verdana', sans-serif; font-size:13px;", get_defined_vars())).'">'."\n";

					echo '<form method="post" name="ws_plugin__s2member_profile" id="ws-plugin--s2member-profile" action="'.esc_attr(home_url('/')).'" autocomplete="off">'."\n";

					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action('ws_plugin__s2member_during_profile_before_table', get_defined_vars());
					unset($__refs, $__v);

					echo '<table cellpadding="0" cellspacing="0">'."\n";
					echo '<tbody>'."\n";

					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action('ws_plugin__s2member_during_profile_before_fields', get_defined_vars());
					unset($__refs, $__v);

					if(apply_filters('ws_plugin__s2member_during_profile_during_fields_display_username', TRUE, get_defined_vars()))
					{
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_profile_during_fields_before_username', get_defined_vars());
						unset($__refs, $__v);

						echo '<tr>'."\n";
						echo '<td>'."\n";
						echo '<label for="ws-plugin--s2member-profile-login">'."\n";
						echo '<strong>'._x('Username', 's2member-front', 's2member').' *</strong> <small>'._x('(cannot be changed)', 's2member-front', 's2member').'</small><br />'."\n";
						echo '<input type="text" aria-required="true" maxlength="60" autocomplete="off" name="ws_plugin__s2member_profile_login" id="ws-plugin--s2member-profile-login" class="ws-plugin--s2member-profile-field form-control" value="'.format_to_edit($user->user_login).'" disabled="disabled" />'."\n";
						echo '</label>'."\n";
						echo '</td>'."\n";
						echo '</tr>'."\n";

						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_profile_during_fields_after_username', get_defined_vars());
						unset($__refs, $__v);
					}
					if(apply_filters('ws_plugin__s2member_during_profile_during_fields_display_email', TRUE, get_defined_vars()))
					{
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_profile_during_fields_before_email', get_defined_vars());
						unset($__refs, $__v);

						echo '<tr>'."\n";
						echo '<td>'."\n";
						echo '<label for="ws-plugin--s2member-profile-email">'."\n";
						echo '<strong>'._x('Email Address', 's2member-front', 's2member').' *</strong><br />'."\n";
						echo '<input type="email" aria-required="true" data-expected="email" maxlength="100" autocomplete="off" name="ws_plugin__s2member_profile_email" id="ws-plugin--s2member-profile-email" class="ws-plugin--s2member-profile-field form-control" value="'.format_to_edit($user->user_email).'" tabindex="'.esc_attr(($tabindex = $tabindex + 10)).'" />'."\n";
						echo '</label>'."\n";
						echo '</td>'."\n";
						echo '</tr>'."\n";

						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_profile_during_fields_after_email', get_defined_vars());
						unset($__refs, $__v);
					}
					if($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_names'])
					{
						if(apply_filters('ws_plugin__s2member_during_profile_during_fields_display_first_name', TRUE, get_defined_vars()))
						{
							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action('ws_plugin__s2member_during_profile_during_fields_before_first_name', get_defined_vars());
							unset($__refs, $__v);

							echo '<tr>'."\n";
							echo '<td>'."\n";
							echo '<label for="ws-plugin--s2member-profile-first-name">'."\n";
							echo '<strong>'._x('First Name', 's2member-front', 's2member').' *</strong><br />'."\n";
							echo '<input type="text" aria-required="true" maxlength="100" autocomplete="off" name="ws_plugin__s2member_profile_first_name" id="ws-plugin--s2member-profile-first-name" class="ws-plugin--s2member-profile-field form-control" value="'.esc_attr($user->first_name).'" tabindex="'.esc_attr(($tabindex = $tabindex + 10)).'" />'."\n";
							echo '</label>'."\n";
							echo '</td>'."\n";
							echo '</tr>'."\n";

							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action('ws_plugin__s2member_during_profile_during_fields_after_first_name', get_defined_vars());
							unset($__refs, $__v);
						}
						if(apply_filters('ws_plugin__s2member_during_profile_during_fields_display_last_name', TRUE, get_defined_vars()))
						{
							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action('ws_plugin__s2member_during_profile_during_fields_before_last_name', get_defined_vars());
							unset($__refs, $__v);

							echo '<tr>'."\n";
							echo '<td>'."\n";
							echo '<label for="ws-plugin--s2member-profile-last-name">'."\n";
							echo '<strong>'._x('Last Name', 's2member-front', 's2member').' *</strong><br />'."\n";
							echo '<input type="text" aria-required="true" maxlength="100" autocomplete="off" name="ws_plugin__s2member_profile_last_name" id="ws-plugin--s2member-profile-last-name" class="ws-plugin--s2member-profile-field form-control" value="'.esc_attr($user->last_name).'" tabindex="'.esc_attr(($tabindex = $tabindex + 10)).'" />'."\n";
							echo '</label>'."\n";
							echo '</td>'."\n";
							echo '</tr>'."\n";

							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action('ws_plugin__s2member_during_profile_during_fields_after_last_name', get_defined_vars());
							unset($__refs, $__v);
						}
						if(apply_filters('ws_plugin__s2member_during_profile_during_fields_display_display_name', TRUE, get_defined_vars()))
						{
							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action('ws_plugin__s2member_during_profile_during_fields_before_display_name', get_defined_vars());
							unset($__refs, $__v);

							echo '<tr>'."\n";
							echo '<td>'."\n";
							echo '<label for="ws-plugin--s2member-profile-display-name">'."\n";
							echo '<strong>'._x('Display Name', 's2member-front', 's2member').' *</strong><br />'."\n";
							echo '<input type="text" aria-required="true" maxlength="100" autocomplete="off" name="ws_plugin__s2member_profile_display_name" id="ws-plugin--s2member-profile-display-name" class="ws-plugin--s2member-profile-field form-control" value="'.esc_attr($user->display_name).'" tabindex="'.esc_attr(($tabindex = $tabindex + 10)).'" />'."\n";
							echo '</label>'."\n";
							echo '</td>'."\n";
							echo '</tr>'."\n";

							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action('ws_plugin__s2member_during_profile_during_fields_after_display_name', get_defined_vars());
							unset($__refs, $__v);
						}
					}
					if(apply_filters('ws_plugin__s2member_during_profile_during_fields_display_custom_fields', TRUE, get_defined_vars()))
					{
						if($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields']) // Now, do we have Custom Fields?
							if($fields_applicable = c_ws_plugin__s2member_custom_reg_fields::custom_fields_configured_at_level('auto-detection', 'profile'))
							{
								$fields = get_user_option('s2member_custom_fields', $user_id);

								$tabindex = $tabindex + 9; // Start tabindex at +9 ( +1 below ).

								foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
								do_action('ws_plugin__s2member_during_profile_during_fields_before_custom_fields', get_defined_vars());
								unset($__refs, $__v);

								foreach(json_decode($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields'], TRUE) as $field)
								{
									foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
									do_action('ws_plugin__s2member_during_profile_during_fields_during_custom_fields_before', get_defined_vars());
									unset($__refs, $__v);

									if(in_array($field['id'], $fields_applicable)) // Field applicable?
									{
										$field_var      = preg_replace('/[^a-z0-9]/i', '_', strtolower($field['id']));
										$field_id_class = preg_replace('/_/', '-', $field_var);

										foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
										if(apply_filters('ws_plugin__s2member_during_profile_during_fields_during_custom_fields_display', TRUE, get_defined_vars()))
										{
											if(!empty($field['section']) && $field['section'] === 'yes') // Starts a new section?
												echo '<tr><td><div class="ws-plugin--s2member-profile-field-divider-section'.((!empty($field['sectitle'])) ? '-title' : '').'">'.((!empty($field['sectitle'])) ? $field['sectitle'] : '').'</div></td></tr>';

											echo '<tr>'."\n";
											echo '<td>'."\n";
											echo '<label for="ws-plugin--s2member-profile-'.esc_attr($field_id_class).'">'."\n";
											echo '<strong'.((preg_match('/^(checkbox|pre_checkbox)$/', $field['type'])) ? ' style="display:none;"' : '').'>'.$field['label'].(($field['required'] === 'yes') ? ' *' : '').'</strong></label>'.((preg_match('/^(checkbox|pre_checkbox)$/', $field['type'])) ? '' : '<br />')."\n";
											echo c_ws_plugin__s2member_custom_reg_fields::custom_field_gen(__FUNCTION__, $field, 'ws_plugin__s2member_profile_', 'ws-plugin--s2member-profile-', 'ws-plugin--s2member-profile-field', '', ($tabindex = $tabindex + 1), '', $fields, $fields[$field_var], 'profile');
											echo '</td>'."\n";
											echo '</tr>'."\n";
										}
										unset($__refs, $__v);
									}
									foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
									do_action('ws_plugin__s2member_during_profile_during_fields_during_custom_fields_after', get_defined_vars());
									unset($__refs, $__v);
								}
								foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
								do_action('ws_plugin__s2member_during_profile_during_fields_after_custom_fields', get_defined_vars());
								unset($__refs, $__v);
							}
					}
					if(apply_filters('ws_plugin__s2member_during_profile_during_fields_display_password', TRUE, get_defined_vars()))
					{
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_profile_during_fields_before_password', get_defined_vars());
						unset($__refs, $__v);

						echo '<tr><td><div class="ws-plugin--s2member-profile-field-divider-section"></div></td></tr>';

						echo '<tr>'."\n";
						echo '<td>'."\n";

						echo '<label for="ws-plugin--s2member-profile-password1" title="'.esc_attr(_x('Please type your Password twice to confirm.', 's2member-front', 's2member')).'">'."\n";
						echo '<strong>'._x('New Password?', 's2member-front', 's2member').'</strong> <em>'._x('(please type it twice)', 's2member-front', 's2member').'</em><br />'."\n";
						echo '<em>'._x('Only if changing password, otherwise leave blank.', 's2member-front', 's2member').'</em><br />'."\n";
						echo '<input type="password" maxlength="100" autocomplete="off" name="ws_plugin__s2member_profile_password1" id="ws-plugin--s2member-profile-password1" class="ws-plugin--s2member-profile-field form-control" value="" tabindex="'.esc_attr(($tabindex = $tabindex + 10)).'"'.(($user->user_login === 'demo') ? ' disabled="disabled"' : '').' />'."\n";
						echo '</label>'."\n";

						echo '<label for="ws-plugin--s2member-profile-password2" title="'.esc_attr(_x('Please type your Password twice to confirm.', 's2member-front', 's2member')).'">'."\n";
						echo '<input type="password" maxlength="100" autocomplete="off" name="ws_plugin__s2member_profile_password2" id="ws-plugin--s2member-profile-password2" class="ws-plugin--s2member-profile-field form-control" value="" tabindex="'.esc_attr(($tabindex = $tabindex + 10)).'"'.(($user->user_login === 'demo') ? ' disabled="disabled"' : '').' />'."\n";
						echo '</label>'."\n";

						echo '<div id="ws-plugin--s2member-profile-password-strength" class="ws-plugin--s2member-password-strength"><em>'._x('password strength indicator', 's2member-front', 's2member').'</em></div>'."\n";

						echo '</td>'."\n";
						echo '</tr>'."\n";

						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_profile_during_fields_after_password', get_defined_vars());
						unset($__refs, $__v);
					}
					if($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_opt_in'] && c_ws_plugin__s2member_list_servers::list_servers_integrated())
					{
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_profile_during_fields_before_opt_in', get_defined_vars());
						unset($__refs, $__v);

						echo '<tr><td><div class="ws-plugin--s2member-profile-field-divider-section"></div></td></tr>';

						echo '<tr>'."\n";
						echo '<td>'."\n";
						echo '<label for="ws-plugin--s2member-profile-opt-in">'."\n";
						echo '<input type="checkbox" name="ws_plugin__s2member_profile_opt_in" id="ws-plugin--s2member-profile-opt-in" class="ws-plugin--s2member-profile-field" value="1"'.((get_user_option('s2member_opt_in', $user_id)) ? ' checked="checked"' : '').' tabindex="'.esc_attr(($tabindex = $tabindex + 10)).'" />'."\n";
						echo $GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_opt_in_label']."\n";
						echo '</label>'."\n";
						echo '</td>'."\n";
						echo '</tr>'."\n";

						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_profile_during_fields_after_opt_in', get_defined_vars());
						unset($__refs, $__v);
					}
					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action('ws_plugin__s2member_during_profile_after_fields', get_defined_vars());
					unset($__refs, $__v);

					echo '<tr>'."\n";
					echo '<td>'."\n";
					echo '<input type="hidden" name="ws_plugin__s2member_profile_save" id="ws-plugin--s2member-profile-save" value="'.esc_attr(wp_create_nonce('ws-plugin--s2member-profile-save')).'" />'."\n";
					echo '<input type="submit" id="ws-plugin--s2member-profile-submit" class="btn btn-primary" value="'.esc_attr(_x('Save All Changes', 's2member-front', 's2member')).'" tabindex="'.esc_attr(($tabindex = $tabindex + 10)).'" />'."\n";
					echo '</td>'."\n";
					echo '</tr>'."\n";

					echo '</tbody>'."\n";
					echo '</table>'."\n";

					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action('ws_plugin__s2member_during_profile_after_table', get_defined_vars());
					unset($__refs, $__v);

					echo '</form>'."\n";

					echo '</body>'."\n";
					echo '</html>';
				}
				exit();
			}
			do_action('ws_plugin__s2member_after_profile', get_defined_vars());
		}
	}
}