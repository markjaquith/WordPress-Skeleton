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
Determine the full URL to the directory this plugin resides in.
*/
$GLOBALS["WS_PLUGIN__"]["wp_show_ids"]["c"]["dir_url"] = (stripos (__FILE__, WP_CONTENT_DIR) !== 0) ? /* Have to assume plugins dir? */
plugins_url ("/" . basename (dirname (dirname (__FILE__)))) : /* Otherwise, this gives it a chance to live anywhere in the content dir. */
content_url (preg_replace ("/^(.*?)\/" . preg_quote (basename (WP_CONTENT_DIR), "/") . "/", "", str_replace (DIRECTORY_SEPARATOR, "/", dirname (dirname (__FILE__)))));
/*
Configure checksum time for the syscon.inc.php file.
*/
$GLOBALS["WS_PLUGIN__"]["wp_show_ids"]["c"]["checksum"] = filemtime (__FILE__); /* File modification time. */
?>