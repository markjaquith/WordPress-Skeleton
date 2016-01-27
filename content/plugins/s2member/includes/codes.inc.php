<?php
/**
 * Shortcodes for the s2Member plugin.
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
	exit ('Do not access this file directly.');
/*
Add WordPress Editor Shortcodes.
*/
add_shortcode('s2Key', 'c_ws_plugin__s2member_sc_keys::sc_get_key');
add_shortcode('s2Get', 'c_ws_plugin__s2member_sc_gets::sc_get_details');
add_shortcode('s2Eot', 'c_ws_plugin__s2member_sc_eots::sc_eot_details');
add_shortcode('s2File', 'c_ws_plugin__s2member_sc_files::sc_get_file');
add_shortcode('s2Stream', 'c_ws_plugin__s2member_sc_files::sc_get_stream');

add_shortcode('s2If', 'c_ws_plugin__s2member_sc_if_conds::sc_if_conditionals');
add_shortcode('_s2If', 'c_ws_plugin__s2member_sc_if_conds::sc_if_conditionals');
add_shortcode('__s2If', 'c_ws_plugin__s2member_sc_if_conds::sc_if_conditionals');
add_shortcode('___s2If', 'c_ws_plugin__s2member_sc_if_conds::sc_if_conditionals');
add_shortcode('____s2If', 'c_ws_plugin__s2member_sc_if_conds::sc_if_conditionals');
add_shortcode('_____s2If', 'c_ws_plugin__s2member_sc_if_conds::sc_if_conditionals');
add_shortcode('______s2If', 'c_ws_plugin__s2member_sc_if_conds::sc_if_conditionals');
add_shortcode('_______s2If', 'c_ws_plugin__s2member_sc_if_conds::sc_if_conditionals');
add_shortcode('________s2If', 'c_ws_plugin__s2member_sc_if_conds::sc_if_conditionals');
add_shortcode('_________s2If', 'c_ws_plugin__s2member_sc_if_conds::sc_if_conditionals');
add_shortcode('__________s2If', 'c_ws_plugin__s2member_sc_if_conds::sc_if_conditionals');

add_shortcode('s2Member-Profile', 'c_ws_plugin__s2member_sc_profile::sc_profile');
add_shortcode('s2Member-PayPal-Button', 'c_ws_plugin__s2member_sc_paypal_button::sc_paypal_button');
add_shortcode('s2Member-Security-Badge', 'c_ws_plugin__s2member_sc_s_badge::sc_s_badge');
