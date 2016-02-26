<?php
/**
* Deprecated functions from previous versions of s2Member.
*
* See: {@link https://en.wikipedia.org/wiki/Deprecation}
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
* @package s2Member
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");
/**
* Deprecated in s2Member v3.5+.
*
* The s2Clean theme; prior to s2Clean v1.2.5 looked for the existence of this function.
* In fact, all older PriMoThemes called upon the activate/deactivate functions.
*
* @package s2Member
* @since 1.0
*
* @deprecated Starting with s2Member v3.5+, please use:
* 	``c_ws_plugin__s2member_installation::activate()``
*
* @see s2Member\Installation\c_ws_plugin__s2member_installation::activate()
*/
function ws_plugin__s2member_activate ()
	{
		return c_ws_plugin__s2member_installation::activate ();
	}
/**
* Deprecated in s2Member v3.5+.
*
* The s2Clean theme; prior to s2Clean v1.2.5 looked for the existence of this function.
* In fact, all older PriMoThemes called upon the activate/deactivate functions.
*
* @package s2Member
* @since 1.0
*
* @deprecated Starting with s2Member v3.5+, please use:
* 	``c_ws_plugin__s2member_installation::deactivate()``
*
* @see s2Member\Installation\c_ws_plugin__s2member_installation::deactivate()
*/
function ws_plugin__s2member_deactivate ()
	{
		return c_ws_plugin__s2member_installation::deactivate ();
	}
/**
* Deprecated in s2Member v3.5+.
*
* Needed by the s2Member Pro upgrader prior to s2Member Pro v1.5+.
*
* @package s2Member
* @since 3.0
*
* @deprecated Starting with s2Member v3.5+, please use:
* 	``c_ws_plugin__s2member_utils_strings::trim_deep()``
*
* @see s2Member\Utilities\c_ws_plugin__s2member_utils_strings::trim_deep()
*/
function ws_plugin__s2member_trim_deep ($data = FALSE)
	{
		return c_ws_plugin__s2member_utils_strings::trim_deep ($data);
	}
/**
* Deprecated in s2Member v3.5+.
*
* Needed by the s2Member Pro upgrader prior to s2Member Pro v1.5+.
*
* @package s2Member
* @since 3.0
*
* @deprecated Starting with s2Member v3.5+, please use:
* 	``c_ws_plugin__s2member_utils_urls::remote()``
*
* @see s2Member\Utilities\c_ws_plugin__s2member_utils_urls::remote()
*/
function ws_plugin__s2member_remote ($url = FALSE, $post_vars = FALSE, $args = array())
	{
		return c_ws_plugin__s2member_utils_urls::remote ($url, $post_vars, $args);
	}
/**
* Deprecated in s2Member v3.5+.
*
* Needed by the s2Member Pro upgrader prior to s2Member Pro v1.5+.
*
* @package s2Member
* @since 3.0
*
* @deprecated Starting with s2Member v3.5+, please use:
* 	``c_ws_plugin__s2member_admin_notices::enqueue_admin_notice()``
*
* @see s2Member\Admin_Notices\c_ws_plugin__s2member_admin_notices::enqueue_admin_notice()
*/
function ws_plugin__s2member_enqueue_admin_notice ($notice = FALSE, $on_pages = FALSE, $error = FALSE, $time = FALSE, $dismiss = FALSE)
	{
		return c_ws_plugin__s2member_admin_notices::enqueue_admin_notice ($notice, $on_pages, $error, $time, $dismiss);
	}
