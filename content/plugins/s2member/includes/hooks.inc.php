<?php
/**
 * Primary Hooks/Filters used by the s2Member plugin.
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
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');
/*
Add the plugin Actions/Filters here.
*/
add_action('set_current_user', 'c_ws_plugin__s2member_user_securities::set_current_user', 2);

add_action('init', 'c_ws_plugin__s2member_translations::load', 2);

add_action('init', 'c_ws_plugin__s2member_ssl::check_force_ssl', 3);
add_action('init', 'c_ws_plugin__s2member_user_securities::initialize', 3);
add_action('init', 'c_ws_plugin__s2member_database::wait_timeout', 3);
add_action('init', 'c_ws_plugin__s2member_no_cache::no_cache', 3);

add_action('init', 'c_ws_plugin__s2member_register::register', 4);
add_action('init', 'c_ws_plugin__s2member_paypal_notify::paypal_notify', 4);
add_action('init', 'c_ws_plugin__s2member_files::check_file_download_access', 4);
add_action('init', 'c_ws_plugin__s2member_profile_mods::handle_profile_modifications', 4);
add_action('init', 'c_ws_plugin__s2member_tracking_cookies::delete_sp_tracking_cookie', 4);
add_action('init', 'c_ws_plugin__s2member_tracking_cookies::delete_tracking_cookie', 4);
add_action('init', 'c_ws_plugin__s2member_cron_jobs::auto_eot_system_via_cron', 4);
add_action('init', 'c_ws_plugin__s2member_mo_page::membership_options_page', 4);
add_action('init', 'c_ws_plugin__s2member_s_badge_status::s_badge_status', 4);

add_action('init', 'c_ws_plugin__s2member_admin_css_js::menu_pages_css', 5);
add_action('init', 'c_ws_plugin__s2member_admin_css_js::menu_pages_js', 5);
add_action('init', 'c_ws_plugin__s2member_css_js::css', 5);

add_action('init', 'c_ws_plugin__s2member_constants::constants', 6);

add_action('init', 'c_ws_plugin__s2member_css_js::js_w_globals', 7);
add_action('init', 'c_ws_plugin__s2member_paypal_return::paypal_return', 7);
add_action('init', 'c_ws_plugin__s2member_profile::profile', 7);

add_action('init', 'c_ws_plugin__s2member_labels::config_label_translations', 10);

add_action('init', 'c_ws_plugin__s2member_login_redirects_r::remove_login_redirect_filters', 11);

add_action('init', 'c_ws_plugin__s2member_login_checks::monitor_simultaneous_logins', 10);

add_action('admin_init', 'c_ws_plugin__s2member_menu_pages::log_file_downloader');
add_action('admin_init', 'c_ws_plugin__s2member_menu_pages::logs_zip_downloader');

add_filter('bbp_has_replies_query', 'c_ws_plugin__s2member_querys::_bbp_flag_has_replies');
add_action('pre_get_posts', 'c_ws_plugin__s2member_security::security_gate_query', 100);
add_filter('wp_list_pages_excludes', 'c_ws_plugin__s2member_querys::_query_level_access_list_pages', 100);

add_action('wp', 'c_ws_plugin__s2member_ssl::check_force_ssl', 1);
add_action('wp', 'c_ws_plugin__s2member_security::security_gate', 1);

add_filter('wp_mail', 'c_ws_plugin__s2member_email_configs::email_filter');

add_filter('widget_text', 'do_shortcode'); // Shortcodes in widgets.

add_action('wp_print_styles', 'c_ws_plugin__s2member_css_js_themes::add_css');
add_action('wp_print_scripts', 'c_ws_plugin__s2member_css_js_themes::add_js_w_globals');

add_action('wp_login_failed', 'c_ws_plugin__s2member_brute_force::track_failed_logins');
add_filter('authenticate', 'c_ws_plugin__s2member_brute_force::stop_brute_force_logins', 100);

add_filter('wp_authenticate_user', 'c_ws_plugin__s2member_login_checks::ms_wp_authenticate_user', 100);
add_filter('wp_authenticate_user', 'c_ws_plugin__s2member_login_checks::stop_simultaneous_logins', 100);
add_action('wp_login', 'c_ws_plugin__s2member_login_checks::update_simultaneous_logins', 1);
add_action('clear_auth_cookie', 'c_ws_plugin__s2member_login_checks::simultaneous_logout', 1);

add_action('delete_user', 'c_ws_plugin__s2member_user_deletions::handle_user_deletions');
add_action('wpmu_delete_user', 'c_ws_plugin__s2member_user_deletions::handle_ms_user_deletions');
add_action('remove_user_from_blog', 'c_ws_plugin__s2member_user_deletions::handle_ms_user_deletions', 10, 2);

add_filter('enable_edit_any_user_configuration', 'c_ws_plugin__s2member_user_securities::ms_allow_edits');

add_filter('pre_option_default_role', 'c_ws_plugin__s2member_option_forces::force_default_role');
add_filter('pre_site_option_default_user_role', 'c_ws_plugin__s2member_option_forces::force_mms_default_role');
add_filter('pre_site_option_add_new_users', 'c_ws_plugin__s2member_option_forces::mms_allow_new_users');
add_filter('pre_site_option_dashboard_blog', 'c_ws_plugin__s2member_option_forces::mms_dashboard_blog');
add_filter('pre_option_users_can_register', 'c_ws_plugin__s2member_option_forces::check_register_access');
add_filter('pre_site_option_registration', 'c_ws_plugin__s2member_option_forces::check_mms_register_access');
add_filter('bp_core_get_root_options', 'c_ws_plugin__s2member_option_forces::check_bp_mms_register_access');
add_filter('bp_core_get_site_options', 'c_ws_plugin__s2member_option_forces::check_bp_mms_register_access');

add_filter('random_password', 'c_ws_plugin__s2member_registrations::generate_password');
add_action('user_register', 'c_ws_plugin__s2member_registrations::configure_user_registration');
add_action('register_form', 'c_ws_plugin__s2member_custom_reg_fields::custom_registration_fields');
add_filter('registration_errors', 'c_ws_plugin__s2member_registrations::custom_registration_field_errors', 10, 3);
add_filter('send_password_change_email', '__return_false'); // Turn this off in favor of s2Member.

add_filter('add_signup_meta', 'c_ws_plugin__s2member_registrations::ms_process_signup_meta');
add_filter('bp_signup_usermeta', 'c_ws_plugin__s2member_registrations::ms_process_signup_meta');
add_filter('bp_core_activated_user', 'c_ws_plugin__s2member_registrations::bp_user_activation');
add_filter('wpmu_validate_user_signup', 'c_ws_plugin__s2member_registrations::ms_validate_user_signup');
add_action('signup_hidden_fields', 'c_ws_plugin__s2member_registrations::ms_process_signup_hidden_fields');
add_filter('registration_errors', 'c_ws_plugin__s2member_registrations::ms_register_existing_user', 11, 3);
add_filter('wpmu_signup_user_notification_email', 'c_ws_plugin__s2member_email_configs::ms_nice_email_roles', 11);
add_filter('_wpmu_activate_existing_error_', 'c_ws_plugin__s2member_registrations::ms_activate_existing_user', 10, 2);
add_action('wpmu_activate_user', 'c_ws_plugin__s2member_registrations::configure_user_on_ms_user_activation', 10, 3);
add_action('wpmu_activate_blog', 'c_ws_plugin__s2member_registrations::configure_user_on_ms_blog_activation', 10, 5);
add_action('signup_extra_fields', 'c_ws_plugin__s2member_custom_reg_fields::ms_custom_registration_fields');

add_action('plugins_loaded', 'c_ws_plugin__s2member_custom_reg_fields::add_filters_get_user_option', 1);

add_action('bp_after_signup_profile_fields', 'c_ws_plugin__s2member_custom_reg_fields_4bp::custom_registration_fields_4bp');
add_action('bp_signup_validate', 'c_ws_plugin__s2member_registrations::custom_registration_field_errors_4bp');
add_action('bp_after_profile_field_content', 'c_ws_plugin__s2member_custom_reg_fields_4bp::custom_profile_fields_4bp');
add_action('bp_profile_field_item', 'c_ws_plugin__s2member_custom_reg_fields_4bp::custom_profile_field_items_4bp');
add_action('xprofile_updated_profile', 'c_ws_plugin__s2member_profile_mods_4bp::handle_profile_modifications_4bp', 1000);

add_action('wp_login', 'c_ws_plugin__s2member_login_redirects::login_redirect', 10, 2);
add_action('login_head', 'c_ws_plugin__s2member_login_customizations::login_header_styles');
add_filter('login_headerurl', 'c_ws_plugin__s2member_login_customizations::login_header_url');
add_filter('login_headertitle', 'c_ws_plugin__s2member_login_customizations::login_header_title');
add_action('login_footer', 'c_ws_plugin__s2member_login_customizations::login_footer_design');
add_filter("lostpassword_url", "c_ws_plugin__s2member_login_customizations::lost_password_url", 10, 2);

add_action('login_footer', 'c_ws_plugin__s2member_tracking_codes::display_signup_tracking_codes');
add_action('wp_footer', 'c_ws_plugin__s2member_tracking_codes::display_signup_tracking_codes');

add_action('login_footer', 'c_ws_plugin__s2member_tracking_codes::display_modification_tracking_codes');
add_action('wp_footer', 'c_ws_plugin__s2member_tracking_codes::display_modification_tracking_codes');

add_action('login_footer', 'c_ws_plugin__s2member_tracking_codes::display_ccap_tracking_codes');
add_action('wp_footer', 'c_ws_plugin__s2member_tracking_codes::display_ccap_tracking_codes');

add_action('login_footer', 'c_ws_plugin__s2member_tracking_codes::display_sp_tracking_codes');
add_action('wp_footer', 'c_ws_plugin__s2member_tracking_codes::display_sp_tracking_codes');

add_action('wp_footer', 'c_ws_plugin__s2member_wp_footer::wp_footer_code');

add_action('admin_init', 'c_ws_plugin__s2member_admin_lockouts::admin_lockout', 1);
add_action('admin_init', 'c_ws_plugin__s2member_check_activation::check');

add_action('load-settings.php', 'c_ws_plugin__s2member_op_notices::multisite_ops_notice');
add_action('load-options-general.php', 'c_ws_plugin__s2member_op_notices::general_ops_notice');
add_action('load-options-reading.php', 'c_ws_plugin__s2member_op_notices::reading_ops_notice');
add_action('load-user-new.php', 'c_ws_plugin__s2member_user_new::admin_user_new_fields');

add_action('add_meta_boxes', 'c_ws_plugin__s2member_meta_boxes::add_meta_boxes');
add_action('save_post', 'c_ws_plugin__s2member_meta_box_saves::save_meta_boxes');
add_action('admin_menu', 'c_ws_plugin__s2member_menu_pages::add_admin_options');
add_action('network_admin_menu', 'c_ws_plugin__s2member_menu_pages::add_network_admin_options');
add_action('admin_bar_menu', 'c_ws_plugin__s2member_admin_lockouts::filter_admin_menu_bar', 100);
add_action('admin_print_scripts', 'c_ws_plugin__s2member_menu_pages::add_admin_scripts');
add_action('admin_print_styles', 'c_ws_plugin__s2member_menu_pages::add_admin_styles');

add_action('admin_notices', 'c_ws_plugin__s2member_admin_notices::admin_notices');
add_action('user_admin_notices', 'c_ws_plugin__s2member_admin_notices::admin_notices');
add_action('network_admin_notices', 'c_ws_plugin__s2member_admin_notices::admin_notices');

add_action('pre_user_query', 'c_ws_plugin__s2member_users_list::users_list_query');
add_filter('manage_users_columns', 'c_ws_plugin__s2member_users_list::users_list_cols');
add_filter('manage_users_custom_column', 'c_ws_plugin__s2member_users_list::users_list_display_cols', 10, 3);
add_filter('manage_users_sortable_columns', 'c_ws_plugin__s2member_users_list::users_list_add_sortable');
add_filter('pre_user_query', 'c_ws_plugin__s2member_users_list::users_list_make_sortable');
add_action('edit_user_profile', 'c_ws_plugin__s2member_users_list::users_list_edit_cols');
add_action('show_user_profile', 'c_ws_plugin__s2member_users_list::users_list_edit_cols');
add_action('edit_user_profile_update', 'c_ws_plugin__s2member_users_list::users_list_update_cols');
add_action('personal_options_update', 'c_ws_plugin__s2member_users_list::users_list_update_cols');
add_action('set_user_role', 'c_ws_plugin__s2member_registration_times::synchronize_paid_reg_times', 10, 2);
add_filter('show_password_fields', 'c_ws_plugin__s2member_user_securities::hide_password_fields', 10, 2);

add_action('add_user_meta', 'c_ws_plugin__s2member_access_cap_times::get_user_caps_before_update_on_add', 10, 3);
add_action('update_user_meta', 'c_ws_plugin__s2member_access_cap_times::get_user_caps_before_update', 10, 4);

add_action('added_user_meta', 'c_ws_plugin__s2member_access_cap_times::log_access_cap_times', 10, 4);
add_action('updated_user_meta', 'c_ws_plugin__s2member_access_cap_times::log_access_cap_times', 10, 4);
add_action('deleted_user_meta', 'c_ws_plugin__s2member_access_cap_times::log_access_cap_times_on_delete', 10, 3);

add_filter('cron_schedules', 'c_ws_plugin__s2member_cron_jobs::extend_cron_schedules');
add_action('ws_plugin__s2member_auto_eot_system__schedule', 'c_ws_plugin__s2member_auto_eots::auto_eot_system');

add_action('wp_ajax_ws_plugin__s2member_update_roles_via_ajax', 'c_ws_plugin__s2member_roles_caps::update_roles_via_ajax');

add_action('wp_ajax_ws_plugin__s2member_sp_access_link_via_ajax', 'c_ws_plugin__s2member_sp_access::sp_access_link_via_ajax');
add_action('wp_ajax_ws_plugin__s2member_reg_access_link_via_ajax', 'c_ws_plugin__s2member_register_access::reg_access_link_via_ajax');

add_action('wp_ajax_ws_plugin__s2member_delete_reset_all_ip_restrictions_via_ajax', 'c_ws_plugin__s2member_ip_restrictions::delete_reset_all_ip_restrictions_via_ajax');
add_action('wp_ajax_ws_plugin__s2member_delete_reset_specific_ip_restrictions_via_ajax', 'c_ws_plugin__s2member_ip_restrictions::delete_reset_specific_ip_restrictions_via_ajax');

add_action('ws_plugin__s2member_during_collective_mods', 'c_ws_plugin__s2member_list_servers::auto_process_list_server_removals', 10, 7);
add_action('ws_plugin__s2member_during_collective_eots', 'c_ws_plugin__s2member_list_servers::auto_process_list_server_removals', 10, 4);

add_filter('ws_plugin__s2member_content_redirect_status', 'c_ws_plugin__s2member_utils_urls::redirect_browsers_using_302_status');

add_filter('bbp_get_caps_for_role', 'c_ws_plugin__s2member_roles_caps::bbp_dynamic_role_caps', 10, 2);
add_action('bbp_activation', 'c_ws_plugin__s2member_roles_caps::config_roles', 11);

add_action('http_api_debug', 'c_ws_plugin__s2member_utils_logs::http_api_debug', 1000, 5);

add_action('plugins_loaded', 'c_ws_plugin__s2member_mo_page::back_compat_mop_vars', -(PHP_INT_MAX - 10));

remove_filter('the_content', 'wptexturize'); // Bug fix; see: <https://github.com/websharks/s2member/issues/349>
// See also, this WP core bug report for further details: <https://core.trac.wordpress.org/ticket/29608>
/*
Register the activation | de-activation routines.
*/
register_activation_hook($GLOBALS['WS_PLUGIN__']['s2member']['c']['plugin_basename'], 'c_ws_plugin__s2member_installation::activate');
register_deactivation_hook($GLOBALS['WS_PLUGIN__']['s2member']['c']['plugin_basename'], 'c_ws_plugin__s2member_installation::deactivate');
register_uninstall_hook($GLOBALS['WS_PLUGIN__']['s2member']['c']['plugin_basename'], 'c_ws_plugin__s2member_installation::uninstall');
add_filter('auto_update_plugin', 'c_ws_plugin__s2member_installation::auto_update_filter', PHP_INT_MAX-10, 2); // Disallow s2Member update.
