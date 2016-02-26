<?php
/**
 * Custom Registration/Profile Fields for BuddyPress integration.
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
 * @package s2Member\Custom_Reg_Fields
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_custom_reg_fields_4bp'))
{
	/**
	 * Custom Registration/Profile Fields for BuddyPress integration.
	 *
	 * @package s2Member\Custom_Reg_Fields
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_custom_reg_fields_4bp
	{
		/**
		 * Adds Custom Fields to BuddyPress Registration Form.
		 *
		 * @package s2Member\Custom_Reg_Fields
		 * @since 110524RC
		 *
		 * @attaches-to ``add_action('bp_after_signup_profile_fields');``
		 */
		public static function custom_registration_fields_4bp()
		{
			global $bp; // Global reference to the BuddyPress object.
			static $processed = FALSE; // Process this routine only one time.

			do_action('ws_plugin__s2member_before_custom_registration_fields_4bp', get_defined_vars());

			if(!$processed && in_array('registration', $GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields_4bp']))
				if(apply_filters('ws_plugin__s2member_custom_registration_fields_4bp_display', TRUE, get_defined_vars()))
					if(bp_is_register_page() && ($processed = TRUE))
					{
						$_p = (!empty($_POST)) ? c_ws_plugin__s2member_utils_strings::trim_deep(stripslashes_deep($_POST)) : array();

						if(($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields'] && ($fields_applicable = c_ws_plugin__s2member_custom_reg_fields::custom_fields_configured_at_level('auto-detection', 'registration'))) || ($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_opt_in'] && c_ws_plugin__s2member_list_servers::list_servers_integrated()))
							if(($close_section_container = TRUE))
							{
								echo '<div id="ws-plugin--s2member-custom-reg-fields-4bp-section" class="ws-plugin--s2member-custom-reg-fields-4bp-section register-section">'."\n";
								echo '<div id="ws-plugin--s2member-custom-reg-fields-4bp-container" class="ws-plugin--s2member-custom-reg-fields-4bp-container">'."\n";
								echo '<input type="hidden" name="ws_plugin__s2member_registration" value="'.esc_attr(wp_create_nonce('ws-plugin--s2member-registration')).'" />'."\n";
							}
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_custom_registration_fields_4bp_before', get_defined_vars());
						unset($__refs, $__v);

						if($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields'])
							if($fields_applicable = c_ws_plugin__s2member_custom_reg_fields::custom_fields_configured_at_level('auto-detection', 'registration'))
							{
								foreach(json_decode($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields'], TRUE) as $field)
								{
									foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
									do_action('ws_plugin__s2member_during_custom_registration_fields_4bp_before_custom_fields', get_defined_vars());
									unset($__refs, $__v);

									if(in_array($field['id'], $fields_applicable))
									{
										$field_var      = preg_replace('/[^a-z0-9]/i', '_', strtolower($field['id']));
										$field_id_class = preg_replace('/_/', '-', $field_var);

										foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
										if(apply_filters('ws_plugin__s2member_during_custom_registration_fields_4bp_during_custom_fields_display', TRUE, get_defined_vars()))
										{
											if(!empty($field['section']) && $field['section'] === 'yes')
												echo '<div class="ws-plugin--s2member-custom-reg-field-4bp-divider-section'.((!empty($field['sectitle'])) ? '-title' : '').'">'.((!empty($field['sectitle'])) ? $field['sectitle'] : '').'</div>';

											echo '<div class="ws-plugin--s2member-custom-reg-field-4bp ws-plugin--s2member-custom-reg-field-4bp-'.esc_attr($field_id_class).' field_'.esc_attr($field_var).' editfield">'."\n";
											echo '<label for="ws-plugin--s2member-custom-reg-field-4bp-'.esc_attr($field_id_class).'">'."\n";
											echo '<span'.((preg_match('/^(checkbox|pre_checkbox)$/', $field['type'])) ? ' style="display:none;"' : '').'>'.$field['label'].(($field['required'] === 'yes') ? ' *' : '').'</span></label>'."\n";
											echo c_ws_plugin__s2member_custom_reg_fields::custom_field_gen(__FUNCTION__, $field, 'ws_plugin__s2member_custom_reg_field_', 'ws-plugin--s2member-custom-reg-field-4bp-', 'ws-plugin--s2member-custom-reg-field-4bp', '', '', '', $_p, @$_p['ws_plugin__s2member_custom_reg_field_'.$field_var], 'registration');
											echo '</div>'."\n";
										}
										unset($__refs, $__v);
									}
									foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
									do_action('ws_plugin__s2member_during_custom_registration_fields_4bp_after_custom_fields', get_defined_vars());
									unset($__refs, $__v);
								}
							}
						if($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_opt_in'] && c_ws_plugin__s2member_list_servers::list_servers_integrated())
						{
							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action('ws_plugin__s2member_during_custom_registration_fields_4bp_before_opt_in', get_defined_vars());
							unset($__refs, $__v);

							echo '<div class="ws-plugin--s2member-custom-reg-field-4bp field_opt_in editfield">'."\n";
							echo '<label for="ws-plugin--s2member-custom-reg-field-4bp-opt-in">'."\n";
							echo '<input type="checkbox" name="ws_plugin__s2member_custom_reg_field_opt_in" id="ws-plugin--s2member-custom-reg-field-4bp-opt-in" class="ws-plugin--s2member-custom-reg-field-4bp" value="1"'.(((empty($_p) && $GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_opt_in'] == 1) || $_p['ws_plugin__s2member_custom_reg_field_opt_in']) ? ' checked="checked"' : '').' />'."\n";
							echo $GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_opt_in_label']."\n";
							echo '</label>'."\n";
							echo '</div>'."\n";

							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action('ws_plugin__s2member_during_custom_registration_fields_4bp_after_opt_in', get_defined_vars());
							unset($__refs, $__v);
						}
						if(isset ($close_section_container) && $close_section_container)
							echo '</div>'."\n".'</div>'."\n";

						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_custom_registration_fields_4bp_after', get_defined_vars());
						unset($__refs, $__v);
					}
			do_action('ws_plugin__s2member_after_custom_registration_fields_4bp', get_defined_vars());
		}

		/**
		 * Adds Custom Fields to BuddyPress Profiles.
		 *
		 * @package s2Member\Custom_Reg_Fields
		 * @since 110524RC
		 *
		 * @attaches-to ``add_action('bp_after_profile_field_content');``
		 */
		public static function custom_profile_fields_4bp()
		{
			global $bp; // Global reference to the BuddyPress object.
			static $processed = FALSE; // Process this routine only one time.

			do_action('ws_plugin__s2member_before_custom_profile_fields_4bp', get_defined_vars());

			if(!$processed && in_array('profile', $GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields_4bp']))
				if(apply_filters('ws_plugin__s2member_custom_profile_fields_4bp_display', TRUE, get_defined_vars()))
					if(bp_is_user_profile() && bp_is_user_profile_edit() && (integer)bp_get_the_profile_group_id() === 1)
						if(isset($bp->displayed_user->id) && ($user_id = $bp->displayed_user->id) && ($processed = TRUE))
						{
							echo '<input type="hidden" name="ws_plugin__s2member_profile_4bp_save" id="ws-plugin--s2member-profile-4bp-save" value="'.esc_attr(wp_create_nonce('ws-plugin--s2member-profile-4bp-save')).'" />'."\n";

							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action('ws_plugin__s2member_during_custom_profile_fields_4bp_before', get_defined_vars());
							unset($__refs, $__v);

							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields'])
								if(($level = c_ws_plugin__s2member_user_access::user_access_level(new WP_User($user_id))) >= 0)
									if($fields_applicable = c_ws_plugin__s2member_custom_reg_fields::custom_fields_configured_at_level($level, 'profile'))
									{
										$fields = get_user_option('s2member_custom_fields', $user_id);

										foreach(json_decode($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields'], TRUE) as $field)
										{
											foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
											do_action('ws_plugin__s2member_during_custom_profile_fields_4bp_before_custom_fields', get_defined_vars());
											unset($__refs, $__v);

											if(in_array($field['id'], $fields_applicable))
											{
												$field_var      = preg_replace('/[^a-z0-9]/i', '_', strtolower($field['id']));
												$field_id_class = preg_replace('/_/', '-', $field_var);

												foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
												if(apply_filters('ws_plugin__s2member_during_custom_profile_fields_4bp_during_custom_fields_display', TRUE, get_defined_vars()))
												{
													if(!empty($field['section']) && $field['section'] === 'yes')
														echo '<div class="ws-plugin--s2member-profile-field-4bp-divider-section'.((!empty($field['sectitle'])) ? '-title' : '').'">'.((!empty($field['sectitle'])) ? $field['sectitle'] : '').'</div>';

													echo '<div class="ws-plugin--s2member-profile-field-4bp ws-plugin--s2member-profile-4bp-'.esc_attr($field_id_class).' field_'.esc_attr($field_var).' editfield">'."\n";
													echo '<label for="ws-plugin--s2member-profile-4bp-'.esc_attr($field_id_class).'">'."\n";
													echo '<span'.((preg_match('/^(checkbox|pre_checkbox)$/', $field['type'])) ? ' style="display:none;"' : '').'>'.$field['label'].(($field['required'] === 'yes') ? ' *' : '').'</span></label>'."\n";
													echo c_ws_plugin__s2member_custom_reg_fields::custom_field_gen(__FUNCTION__, $field, 'ws_plugin__s2member_profile_4bp_', 'ws-plugin--s2member-profile-4bp-', 'ws-plugin--s2member-profile-field-4bp', '', '', '', $fields, @$fields[$field_var], 'profile');
													echo '</div>'."\n";
												}
												unset($__refs, $__v);
											}
											foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
											do_action('ws_plugin__s2member_during_custom_profile_fields_4bp_after_custom_fields', get_defined_vars());
											unset($__refs, $__v);
										}
									}
							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action('ws_plugin__s2member_during_custom_profile_fields_4bp_after', get_defined_vars());
							unset($__refs, $__v);

							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_opt_in'] && c_ws_plugin__s2member_list_servers::list_servers_integrated())
							{
								foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
								do_action('ws_plugin__s2member_during_custom_profile_fields_4bp_before_opt_in', get_defined_vars());
								unset($__refs, $__v);

								echo '<div class="ws-plugin--s2member-profile-field-4bp ws-plugin--s2member-profile-4bp-opt-in field_opt_in editfield">'."\n";
								echo '<label for="ws-plugin--s2member-profile-4bp-opt-in">'."\n";
								echo '<input type="checkbox" name="ws_plugin__s2member_profile_4bp_opt_in" id="ws-plugin--s2member-profile-4bp-opt-in" class="ws-plugin--s2member-profile-field-4bp" value="1"'.((get_user_option('s2member_opt_in', $user_id)) ? ' checked="checked"' : '').' />'."\n";
								echo $GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_opt_in_label']."\n";
								echo '</label>'."\n";
								echo '</div>'."\n";

								foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
								do_action('ws_plugin__s2member_during_custom_profile_fields_4bp_after_opt_in', get_defined_vars());
								unset($__refs, $__v);
							}
						}
			do_action('ws_plugin__s2member_after_custom_profile_fields_4bp', get_defined_vars());
		}

		/**
		 * Adds Custom Fields to BuddyPress Profiles in public view.
		 *
		 * @package s2Member\Custom_Reg_Fields
		 * @since 110524RC
		 *
		 * @attaches-to ``add_action('bp_profile_field_item');``
		 */
		public static function custom_profile_field_items_4bp()
		{
			global $bp; // Global reference to the BuddyPress object.
			static $processed = FALSE; // Process this routine only one time.

			do_action('ws_plugin__s2member_before_custom_profile_field_items_4bp', get_defined_vars());

			if(!$processed && in_array('profile-view', $GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields_4bp']))
				if(apply_filters('ws_plugin__s2member_custom_profile_field_items_4bp_display', TRUE, get_defined_vars()))
					if(bp_is_user_profile() && !bp_is_user_profile_edit() && (integer)bp_get_the_profile_group_id() === 1)
						if(isset ($bp->displayed_user->id) && ($user_id = $bp->displayed_user->id) && ($processed = TRUE))
						{
							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action('ws_plugin__s2member_during_custom_profile_field_items_4bp_before', get_defined_vars());
							unset($__refs, $__v);

							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields'])
								if(($level = c_ws_plugin__s2member_user_access::user_access_level(new WP_User ($user_id))) >= 0)
									if($fields_applicable = c_ws_plugin__s2member_custom_reg_fields::custom_fields_configured_at_level($level, 'profile-view'))
									{
										$fields = get_user_option('s2member_custom_fields', $user_id);

										foreach(json_decode($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_fields'], TRUE) as $field)
										{
											foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
											do_action('ws_plugin__s2member_during_custom_profile_field_items_4bp_before_custom_fields', get_defined_vars());
											unset($__refs, $__v);

											if(in_array($field['id'], $fields_applicable))
											{
												$field_var      = preg_replace('/[^a-z0-9]/i', '_', strtolower($field['id']));
												$field_id_class = preg_replace('/_/', '-', $field_var);

												foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
												if(apply_filters('ws_plugin__s2member_during_custom_profile_field_items_4bp_during_custom_fields_display', TRUE, get_defined_vars()))
												{
													if(!empty($field['section']) && $field['section'] === 'yes')
													{
														echo '<tr class="ws-plugin--s2member-profile-field-4bp-divider-section">'."\n";
														echo '<td colspan="2"><div class="ws-plugin--s2member-profile-field-4bp-divider-section'.((!empty($field['sectitle'])) ? '-title' : '').'">'.((!empty($field['sectitle'])) ? $field['sectitle'] : '').'</div></td>'."\n";
														echo '</tr>'."\n";
													}
													echo '<tr class="ws-plugin--s2member-profile-field-4bp ws-plugin--s2member-profile-4bp-'.esc_attr($field_id_class).' field_'.esc_attr($field_var).'">'."\n";
													echo '<td class="ws-plugin--s2member-profile-field-4bp ws-plugin--s2member-profile-4bp-'.esc_attr($field_id_class).' field_'.esc_attr($field_var).' label"><span>'.$field['label'].'</span></td>'."\n";
													echo '<td class="ws-plugin--s2member-profile-field-4bp ws-plugin--s2member-profile-4bp-'.esc_attr($field_id_class).' field_'.esc_attr($field_var).' data">'.c_ws_plugin__s2member_custom_reg_fields::custom_field_gen(__FUNCTION__, $field, 'ws_plugin__s2member_profile_4bp_', 'ws-plugin--s2member-profile-4bp-', 'ws-plugin--s2member-profile-field-4bp', '', '', '', $fields, @$fields[$field_var], 'profile-view').'</td>'."\n";
													echo '</tr>'."\n";
												}
												unset($__refs, $__v);
											}
											foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
											do_action('ws_plugin__s2member_during_custom_profile_field_items_4bp_after_custom_fields', get_defined_vars());
											unset($__refs, $__v);
										}
									}
							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action('ws_plugin__s2member_during_custom_profile_field_items_4bp_after', get_defined_vars());
							unset($__refs, $__v);
						}
			do_action('ws_plugin__s2member_after_custom_profile_field_items_4bp', get_defined_vars());
		}
	}
}