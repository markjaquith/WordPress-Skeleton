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
if (!class_exists ("c_ws_plugin__wp_show_ids_utilities"))
	{
		class c_ws_plugin__wp_show_ids_utilities
			{
				/*
				Function builds a version checksum for this installation.
				*/
				public static function ver_checksum ()
					{
						$checksum = WS_PLUGIN__WP_SHOW_IDS_VERSION; /* Software version string. */
						$checksum .= "-" . abs (crc32 ($GLOBALS["WS_PLUGIN__"]["wp_show_ids"]["c"]["checksum"]));
						/**/
						return $checksum; /* ( i.e. version-checksum ) */
					}
			}
	}
?>