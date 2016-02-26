<?php
/**
* Registration Links.
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
* @package s2Member\Registrations
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_register"))
	{
		/**
		* Registration Links.
		*
		* @package s2Member\Registrations
		* @since 3.5
		*/
		class c_ws_plugin__s2member_register
			{
				/**
				* Handles Registration Links.
				*
				* @package s2Member\Registrations
				* @since 3.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null|inner Return-value of inner routine.
				*/
				public static function register ()
					{
						if (!empty($_GET["s2member_register"]))
							{
								return c_ws_plugin__s2member_register_in::register ();
							}
					}
			}
	}
