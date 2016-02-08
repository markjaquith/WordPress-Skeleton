<?php
/**
 * Loads functions created by the s2Member plugin.
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
Include all of the functions that came with this plugin.
*/
if(is_dir($ws_plugin__s2member_temp_dir = dirname(__FILE__).'/functions'))
	foreach(scandir($ws_plugin__s2member_temp_dir) as $ws_plugin__s2member_temp_s)
		if(preg_match('/\.php$/', $ws_plugin__s2member_temp_s) && $ws_plugin__s2member_temp_s !== 'index.php')
			include_once $ws_plugin__s2member_temp_dir.'/'.$ws_plugin__s2member_temp_s;
unset($ws_plugin__s2member_temp_dir, $ws_plugin__s2member_temp_s);