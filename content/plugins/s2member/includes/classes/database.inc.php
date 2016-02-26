<?php
/**
* Database routines.
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
* @package s2Member\Database
* @since 130625
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_database"))
	{
		/**
		* Database routines.
		*
		* @package s2Member\Database
		* @since 130625
		*/
		class c_ws_plugin__s2member_database
			{
				/**
				* @package s2Member\Database
				* @since 130625
				*
				* @attaches-to ``add_action("init");``
				*/
				public static function wait_timeout ()
					{
						global $wpdb; // Global database object reference.

						if(c_ws_plugin__s2member_systematics::is_s2_systematic_use_page ()
							|| (!empty($_SERVER["QUERY_STRING"]) && preg_match ("/[\?&]s2member/", $_SERVER["QUERY_STRING"])))
							$increase_wait_timeout = TRUE;

						if(empty($increase_wait_timeout) && !empty($_POST)) foreach(array_keys($_POST) as $post_key)
							if(strpos($post_key, "s2member") === 0) { $increase_wait_timeout = TRUE; break; }

						if(!empty($increase_wait_timeout)) $wpdb->query("SET SESSION `wait_timeout` = 300");
					}
			}
	}
