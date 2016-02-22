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
/**/
if (!class_exists ("c_ws_plugin__wp_show_ids_installation"))
	{
		class c_ws_plugin__wp_show_ids_installation
			{
				/*
				Handles activation routines.
				*/
				public static function activate ()
					{
						do_action ("ws_plugin__wp_show_ids_before_activation", get_defined_vars ());
						/**/
						do_action ("ws_plugin__wp_show_ids_after_activation", get_defined_vars ());
						/**/
						return; /* Return for uniformity. */
					}
				/*
				Handles de-activation / cleanup routines.
				*/
				public static function deactivate ()
					{
						do_action ("ws_plugin__wp_show_ids_before_deactivation", get_defined_vars ());
						/**/
						do_action ("ws_plugin__wp_show_ids_after_deactivation", get_defined_vars ());
						/**/
						return; /* Return for uniformity. */
					}
			}
	}
?>