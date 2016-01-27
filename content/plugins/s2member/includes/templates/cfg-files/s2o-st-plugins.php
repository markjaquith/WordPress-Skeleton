<?php
if(!defined('_WS_PLUGIN__S2MEMBER_ONLY'))
	exit('Do not access this file directly.');
?>

// s2Member-only mode. Only load the s2Member plugin, exclude all others.

$o_ws_plugin__s2member                   = preg_replace('/-o\.php$/', '.php', __FILE__);
$o_ws_plugin__s2member_is_loaded_already = defined('WS_PLUGIN__S2MEMBER_VERSION') ? TRUE : FALSE;
$o_ws_plugin__plugins_s2member           = WP_PLUGIN_DIR.'/'.basename(dirname($o_ws_plugin__s2member)).'/'.basename($o_ws_plugin__s2member);

if((!is_file($o_ws_plugin__plugins_s2member) || @is_link($o_ws_plugin__plugins_s2member)) && is_file($o_ws_plugin__s2member) && !$o_ws_plugin__s2member_is_loaded_already)
	include_once $o_ws_plugin__s2member; // s2Member in a strange location?

else if(in_array($o_ws_plugin__plugins_s2member, wp_get_active_and_valid_plugins()) && is_file($o_ws_plugin__plugins_s2member) && !$o_ws_plugin__s2member_is_loaded_already)
	include_once $o_ws_plugin__plugins_s2member;

else if(apply_filters('ws_plugin_s2member_o_force', FALSE) && !$o_ws_plugin__s2member_is_loaded_already) // Off by default. Force s2Member to load?
	include_once $o_ws_plugin__s2member;

unset($o_ws_plugin__plugins_s2member, $o_ws_plugin__s2member_is_loaded_already, $o_ws_plugin__s2member);
