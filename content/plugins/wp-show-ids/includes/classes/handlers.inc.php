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
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__wp_show_ids_handlers"))
	{
		class c_ws_plugin__wp_show_ids_handlers
			{
				/*
				Add the ID column.
				*/
				public static function return_column ($cols = FALSE)
					{
						return apply_filters ("ws_plugin__wp_show_ids_return_column", array_merge ($cols, array ("ws_plugin__wp_show_ids" => "ID")), get_defined_vars ());
					}
				/*
				Return ID value.
				*/
				public static function return_value ($value = FALSE, $column_name = FALSE, $id = FALSE)
					{
						return apply_filters ("ws_plugin__wp_show_ids_return_value", (($column_name === "ws_plugin__wp_show_ids") ? $id : $value), get_defined_vars ());
					}
				/*
				Echo ID value.
				*/
				public static function echo_value ($column_name = FALSE, $id = FALSE)
					{
						echo apply_filters ("ws_plugin__wp_show_ids_echo_value", (($column_name === "ws_plugin__wp_show_ids") ? $id : ""), get_defined_vars ());
					}
				/*
				Custom CSS for columns.
				*/
				public static function echo_css () /* Includes column headers too. */
					{
						$css = '<style type="text/css">';
						$css .= 'th.column-ws_plugin__wp_show_ids, td.column-ws_plugin__wp_show_ids { width:45px; text-align:center; }';
						$css .= '</style>';
						/**/
						echo apply_filters ("ws_plugin__wp_show_ids_echo_css", $css, get_defined_vars ());
					}
			}
	}
?>