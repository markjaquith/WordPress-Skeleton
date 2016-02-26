<?php
/**
* Security Badge Status API.
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
* @package s2Member\Security_Badges
* @since 110524RC
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_s_badge_status"))
	{
		/**
		* Security Badge Status API.
		*
		* @package s2Member\Security_Badges
		* @since 110524RC
		*/
		class c_ws_plugin__s2member_s_badge_status
			{
				/**
				* Handles Security Badge Status API.
				*
				* @package s2Member\Security_Badges
				* @since 110524RC
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null|inner Return-value of inner routine.
				*/
				public static function s_badge_status ()
					{
						if (!empty($_GET["s2member_s_badge_status"]))
							{
								c_ws_plugin__s2member_s_badge_status_in::s_badge_status ();
							}
					}
			}
	}
