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
Add the plugin Actions/Filters here.
*/
add_action ("admin_init", "c_ws_plugin__wp_show_ids_columns::configure");
/*
Register the activation | de-activation routines.
*/
register_activation_hook ($GLOBALS["WS_PLUGIN__"]["wp_show_ids"]["l"], "c_ws_plugin__wp_show_ids_installation::activate");
register_deactivation_hook ($GLOBALS["WS_PLUGIN__"]["wp_show_ids"]["l"], "c_ws_plugin__wp_show_ids_installation::deactivate");
?>