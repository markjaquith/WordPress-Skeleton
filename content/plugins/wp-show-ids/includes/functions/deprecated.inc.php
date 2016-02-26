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
As of v1.2+, these two functions are deprecated ( i.e. do NOT use ).
All older PriMoThemes called upon the activate/deactivate functions.
*/
function ws_plugin__wp_show_ids_activate () /* Call with classes. */
	{
		return c_ws_plugin__wp_show_ids_installation::activate ();
	}
/**/
function ws_plugin__wp_show_ids_deactivate () /* Call class. */
	{
		return c_ws_plugin__wp_show_ids_installation::deactivate ();
	}
?>