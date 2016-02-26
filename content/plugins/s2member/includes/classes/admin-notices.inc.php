<?php
/**
 * Enqueues/displays administrative notices.
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
 * @package s2Member\Admin_Notices
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_admin_notices'))
{
	/**
	 * Enqueues/displays administrative notices.
	 *
	 * @package s2Member\Admin_Notices
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_admin_notices
	{
		/**
		 * Enqueues administrative notices.
		 *
		 * @package s2Member\Admin_Notices
		 * @since 3.5
		 *
		 * @param string       $notice String value of actual notice *(i.e., the message)*.
		 * @param string|array $on_pages Optional. Defaults to any page. String or array of pages to display this notice on.
		 * @param bool         $error Optional. True if this notice is regarding an error. Defaults to false.
		 * @param int          $time Optional. Unix timestamp indicating when this notice will be displayed.
		 * @param bool         $dismiss Optional. If true, the notice will remain persistent, until dismissed. Defaults to false.
		 */
		public static function enqueue_admin_notice($notice = '', $on_pages = array(), $error = FALSE, $time = 0, $dismiss = FALSE)
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_enqueue_admin_notice', get_defined_vars());
			unset($__refs, $__v); // Allow variables to be modified by reference.

			if($notice && is_string($notice))// Have a valid string.
			{
				$notices = (array)get_option('ws_plugin__s2member_notices');
				array_push($notices, array('notice' => $notice, 'on_pages' => $on_pages, 'error' => $error, 'time' => $time, 'dismiss' => $dismiss));

				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_enqueue_admin_notice', get_defined_vars());
				unset($__refs, $__v); // Allow variables to be modified by reference.

				update_option('ws_plugin__s2member_notices', c_ws_plugin__s2member_utils_arrays::array_unique($notices));
			}
			do_action('ws_plugin__s2member_after_enqueue_admin_notice', get_defined_vars());
		}

		/**
		 * Displays an administrative notice.
		 *
		 * @package s2Member\Admin_Notices
		 * @since 3.5
		 *
		 * @param string $notice String value of actual notice *(i.e., the message)*.
		 * @param bool   $error Optional. True if this notice is regarding an error. Defaults to false.
		 * @param bool   $dismiss Optional. If true, the notice will be displayed with a dismissal link. Defaults to false.
		 */
		public static function display_admin_notice($notice = '', $error = FALSE, $dismiss = FALSE)
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_display_admin_notice', get_defined_vars());
			unset($__refs, $__v); // Allow variables to be modified by reference.

			if($dismiss) $dismissal_link = '<div style="float:right; margin:0 0 0 1em; font-weight:bold;">'.
				'[ <a href="'.esc_attr(add_query_arg('ws-plugin--s2member-dismiss-admin-notice', urlencode(md5($notice)), $_SERVER['REQUEST_URI'])).'">dismiss</a> ]'.
				'</div>';
			if($notice && is_string($notice) && $error)
			{
				if($dismiss && !empty($dismissal_link))
					$notice = $dismissal_link.$notice;
				echo '<div class="error fade"><p>'.$notice.'</p></div>';
			}
			else if($notice && is_string($notice))
			{
				if($dismiss && !empty($dismissal_link))
					$notice = $dismissal_link.$notice;
				echo '<div class="updated fade"><p>'.$notice.'</p></div>';
			}
			do_action('ws_plugin__s2member_after_display_admin_notice', get_defined_vars());
		}

		/**
		 * Processes all administrative notices.
		 *
		 * @package s2Member\Admin_Notices
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('admin_notices');``
		 * @attaches-to ``add_action('user_admin_notices');``
		 * @attaches-to ``add_action('network_admin_notices');``
		 * @todo Update to ``add_action('all_admin_notices');``.
		 */
		public static function admin_notices()
		{
			global $pagenow; // This holds the current page filename.

			do_action('ws_plugin__s2member_before_admin_notices', get_defined_vars());

			if(is_admin() && is_array($notices = get_option('ws_plugin__s2member_notices')) && !empty($notices))
			{
				$a = (is_blog_admin()) ? 'blog' : '';
				$a = (is_user_admin()) ? 'user' : $a;
				$a = (is_network_admin()) ? 'network' : $a;
				$a = (!$a) ? 'blog' : $a; // Default blog admin.

				foreach($notices as $i => $notice) // Check several things about each notice.
					foreach(!$notice['on_pages'] ? array('*') : (array)$notice['on_pages'] as $page)
					{
						if(!preg_match('/^(.+?)\:/', $page)) // NO prefix?
							$page = 'blog:'.ltrim($page, ':'); // `blog:`

						$adms = preg_split('/\|/', preg_replace('/\:(.*)$/i', '', $page));
						$page = preg_replace('/^([^\:]*)\:/i', '', $page);

						if(empty($adms) || in_array('*', $adms) || in_array($a, $adms))
							if(!$page || '*' === $page || $pagenow === $page || @$_GET['page'] === $page)
							{
								if(strtotime('now') >= (int)$notice['time']) // Time to show it?
								{
									foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
									do_action('ws_plugin__s2member_during_admin_notices_before_display', get_defined_vars());
									unset($__refs, $__v); // Allow variables to be modified by reference.

									if(!$notice['dismiss'] || (!empty($_GET['ws-plugin--s2member-dismiss-admin-notice']) && $_GET['ws-plugin--s2member-dismiss-admin-notice'] === md5($notice['notice'])))
										unset($notices[$i]); // Clear this administrative notice now?

									if(!$notice['dismiss'] || empty($_GET['ws-plugin--s2member-dismiss-admin-notice']) || $_GET['ws-plugin--s2member-dismiss-admin-notice'] !== md5($notice['notice']))
										c_ws_plugin__s2member_admin_notices::display_admin_notice($notice['notice'], $notice['error'], $notice['dismiss']);

									do_action('ws_plugin__s2member_during_admin_notices_after_display', get_defined_vars());
								}
								continue 2; // This notice processed; continue.
							}
					}
				$notices = array_merge($notices); // Re-index array.

				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_admin_notices', get_defined_vars());
				unset($__refs, $__v); // Allow variables to be modified by reference.

				update_option('ws_plugin__s2member_notices', $notices);
			}
			do_action('ws_plugin__s2member_after_admin_notices', get_defined_vars());
		}
	}
}
