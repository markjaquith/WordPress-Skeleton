<?php
/**
 * s2Member's Return Page template handler.
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
 * @package s2Member\Return_Templates
 * @since 110720
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_return_templates'))
{
	/**
	 * s2Member's Return Page template handler.
	 *
	 * @package s2Member\Return_Templates
	 * @since 110720
	 */
	class c_ws_plugin__s2member_return_templates
	{
		/**
		 * Handles Return templates w/ response message.
		 *
		 * @package s2Member\Return_Templates
		 * @since 110720
		 *
		 * @param string $template Optional A Subscr. Gateway code should be used as the template name, or `default` is a multipurpose template. Defaults to `default`. Used in template selection.
		 * @param string $response Optional. Response message to fill template with, using the Replacement Code `%%response%%` inside the template file. Defaults to: `Thank you. Please click the link below.`.
		 * @param string $continue_html Optional. The HTML value of the continuation link presented within the template using Replacement Code `%%continue%%`. Defaults to: `Continue`.
		 * @param string $continue_link Optional. The HREF value for the continuation link presented within the template using Replacement Code `%%continue%%`. Defaults to: ``home_url ('/', 'http')``.
		 *
		 * @return string The full HTML code of the template. All Replacement Codes inside the template file will have already been filled by this routine.
		 */
		public static function return_template($template = '', $response = '', $continue_html = '', $continue_link = '')
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_return_template', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			$template      = ($template) ? $template : 'default';
			$continue_link = ($continue_link) ? $continue_link : home_url('/', 'http');
			$continue_html = ($continue_html) ? $continue_html : _x('Continue', 's2member-front', 's2member');
			$response      = ($response) ? $response : _x('Thank you. Please click the link below.', 's2member-front', 's2member');

			$custom_template = (is_file(TEMPLATEPATH.'/'.$template.'-return.php')) ? TEMPLATEPATH.'/'.$template.'-return.php' : '';
			$custom_template = (is_file(get_stylesheet_directory().'/'.$template.'-return.php')) ? get_stylesheet_directory().'/'.$template.'-return.php' : $custom_template;
			$custom_template = (is_file(WP_CONTENT_DIR.'/'.$template.'-return.php')) ? WP_CONTENT_DIR.'/'.$template.'-return.php' : $custom_template;

			$custom_template = (!$custom_template && is_file(TEMPLATEPATH.'/default-return.php')) ? TEMPLATEPATH.'/default-return.php' : $custom_template;
			$custom_template = (!$custom_template && is_file(get_stylesheet_directory().'/default-return.php')) ? get_stylesheet_directory().'/default-return.php' : $custom_template;
			$custom_template = (!$custom_template && is_file(WP_CONTENT_DIR.'/default-return.php')) ? WP_CONTENT_DIR.'/default-return.php' : $custom_template;

			$specific_template = ($custom_template) ? $custom_template : ((is_file($_default_specific_template = dirname(dirname(__FILE__)).'/templates/returns/'.$template.'-return.php')) ? $_default_specific_template : '');

			$code = trim(file_get_contents((($specific_template) ? $specific_template : ($_default_template = dirname(dirname(__FILE__)).'/templates/returns/default-return.php'))));
			$code = trim(((!$custom_template || !is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()) ? c_ws_plugin__s2member_utilities::evl($code) : $code));

			$doctype_html_head = c_ws_plugin__s2member_utils_html::doctype_html_head(get_bloginfo('name'), 'ws_plugin__s2member_during_return_template_head_'.(($specific_template) ? basename($specific_template) : 'default-return.php'));
			$code              = preg_replace('/%%doctype_html_head%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(apply_filters('ws_plugin__s2member_return_template_doctype_html_head', $doctype_html_head, get_defined_vars())), $code);

			$code = preg_replace('/%%header%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(apply_filters('ws_plugin__s2member_return_template_header', sprintf(_x('[ %s ] <strong><em>says&hellip;</em></strong>', 's2member-front', 's2member'), esc_html($_SERVER['HTTP_HOST'])), get_defined_vars())), $code);

			$code = preg_replace('/%%response%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(apply_filters('ws_plugin__s2member_return_template_response', $response, get_defined_vars())), $code);
			$code = preg_replace('/%%continue%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(apply_filters('ws_plugin__s2member_return_template_continue', '<a href="'.esc_attr($continue_link).'">'.$continue_html.'</a>', get_defined_vars())), $code);
			$code = preg_replace('/%%support%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(apply_filters('ws_plugin__s2member_return_template_support', sprintf(_x('If you need assistance, please <a href="%s" target="_blank">contact support</a>.', 's2member-front', 's2member'), esc_attr($GLOBALS['WS_PLUGIN__']['s2member']['o']['reg_email_support_link'])), get_defined_vars())), $code);
			$code = preg_replace('/%%tracking%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(apply_filters('ws_plugin__s2member_return_template_tracking', c_ws_plugin__s2member_tracking_codes::generate_all_tracking_codes(), get_defined_vars())), $code);

			return apply_filters('ws_plugin__s2member_return_template', $code, get_defined_vars());
		}
	}
}