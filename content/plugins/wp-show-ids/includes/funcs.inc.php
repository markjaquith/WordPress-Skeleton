<?php
/*
Copyright: © 2009 WebSharks, Inc. ( coded in the USA )
<mailto:support@websharks-inc.com> <http://www.websharks-inc.com/>

Released under the terms of the GNU General Public License.
You should have received a copy of the GNU General Public License,
along with this software. In the main directory, see: /licensing/
If not, see: <http://www.gnu.org/licenses/>.
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit ("Do not access this file directly.");
/*
Include all of the functions that came with this plugin.
*/
if (is_dir ($ws_plugin__wp_show_ids_temp_dir = dirname (__FILE__) . "/functions"))
	foreach (scandir ($ws_plugin__wp_show_ids_temp_dir) as $ws_plugin__wp_show_ids_temp_s)
		if (preg_match ("/\.php$/", $ws_plugin__wp_show_ids_temp_s) && $ws_plugin__wp_show_ids_temp_s !== "index.php")
			include_once $ws_plugin__wp_show_ids_temp_dir . "/" . $ws_plugin__wp_show_ids_temp_s;
/**/
unset ($ws_plugin__wp_show_ids_temp_dir, $ws_plugin__wp_show_ids_temp_s);
?>