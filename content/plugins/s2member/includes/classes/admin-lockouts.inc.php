<?php
/**
 * Locks Users/Members out of admin panels.
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
 * @package s2Member\Admin_Lockouts
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_admin_lockouts'))
{
	/**
	 * Locks Users/Members out of admin panels.
	 *
	 * @package s2Member\Admin_Lockouts
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_admin_lockouts
	{
		/**
		 * Locks Users/Members out of admin panels.
		 *
		 * @package s2Member\Admin_Lockouts
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('admin_init');``
		 *
		 * @return null Or exits script execution after redirection.
		 */
		public static function admin_lockout()
		{
			do_action('ws_plugin__s2member_before_admin_lockouts', get_defined_vars());

			if(is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX) && !current_user_can('edit_posts') /* Give Filters a chance here too. */)
				if(apply_filters('ws_plugin__s2member_admin_lockout', $GLOBALS['WS_PLUGIN__']['s2member']['o']['force_admin_lockouts'], get_defined_vars()))
				{
					if(($redirection_url = c_ws_plugin__s2member_login_redirects::login_redirection_url()))
						wp_redirect($redirection_url).exit ( /* Special Redirection. */);

					else // Else we use the Login Welcome Page configured for s2Member.
						wp_redirect(get_page_link($GLOBALS['WS_PLUGIN__']['s2member']['o']['login_welcome_page'])).exit ();
				}
			do_action('ws_plugin__s2member_after_admin_lockouts', get_defined_vars());
		}

		/**
		 * Filters administrative menu bar for Users/Members.
		 *
		 * @package s2Member\Admin_Lockouts
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('admin_bar_menu');``
		 *
		 * @param WP_Admin_Bar $wp_admin_bar Expects the ``$wp_admin_bar``, by reference; passed in by the Action Hook.
		 *
		 * @return null After modifying ``$wp_admin_var``.
		 */
		public static function filter_admin_menu_bar(&$wp_admin_bar)
		{
			do_action('ws_plugin__s2member_before_filter_admin_menu_bar', get_defined_vars());

			$uses_nodes = (version_compare(get_bloginfo('version'), '3.3-RC1', '>=')) ? TRUE : FALSE;

			if(is_object($wp_admin_bar) && !current_user_can('edit_posts') /* Always for Users/Members. */)
			{
				if($uses_nodes && $wp_admin_bar->get_node('site-name'))
				{
					$id    = 's2-site-name'; // Give this a special/unique ID.
					$title = wp_html_excerpt(get_bloginfo('name'), 42); // A brief excerpt.
					$title = ($title !== get_bloginfo('name')) ? trim($title).'&hellip;' : $title;
					$href  = home_url('/'); // Change to front page.

					$wp_admin_bar->add_node(array('id' => $id, 'title' => $title, 'href' => $href));
					$wp_admin_bar->remove_node('site-name');

					unset($id, $title, $href); // Housekeeping.
				}
				if($uses_nodes && $wp_admin_bar->get_node('wp-logo'))
					$wp_admin_bar->remove_node('wp-logo');
				// -------
				if(!$uses_nodes && isset  ($wp_admin_bar->menu->{'dashboard'}))
					unset  ($wp_admin_bar->menu->{'dashboard'});

				if(!$uses_nodes && isset  ($wp_admin_bar->menu->{'my-blogs'}))
					unset  ($wp_admin_bar->menu->{'my-blogs'});
				// -------
				if($uses_nodes && $wp_admin_bar->get_node('my-sites'))
					$wp_admin_bar->remove_node('my-sites');
			}
			if(is_object($wp_admin_bar) && !current_user_can('edit_posts') /* If locking Users/Members out of `/wp-admin/` areas. */)
				if(apply_filters('ws_plugin__s2member_admin_lockout', $GLOBALS['WS_PLUGIN__']['s2member']['o']['force_admin_lockouts'], get_defined_vars()))
				{
					$lwp = c_ws_plugin__s2member_login_redirects::login_redirection_url();
					$lwp = (!$lwp) ? get_page_link($GLOBALS['WS_PLUGIN__']['s2member']['o']['login_welcome_page']) : $lwp;

					if($uses_nodes && $wp_admin_bar->get_node('my-account'))
						$wp_admin_bar->add_node(array('id' => 'my-account', 'href' => $lwp));

					if($uses_nodes && $wp_admin_bar->get_node('user-info'))
						$wp_admin_bar->add_node(array('id' => 'user-info', 'href' => $lwp));

					if($uses_nodes && $wp_admin_bar->get_node('edit-profile'))
						$wp_admin_bar->add_node(array('id' => 'edit-profile', 'href' => $lwp));
					// -------
					if(!$uses_nodes && isset  ($wp_admin_bar->menu->{'my-account'}['href']))
						$wp_admin_bar->menu->{'my-account'}['href'] = $lwp;

					if(!$uses_nodes && isset  ($wp_admin_bar->menu->{'my-account'}['children']->{'edit-profile'}['href']))
						$wp_admin_bar->menu->{'my-account'}['children']->{'edit-profile'}['href'] = $lwp;

					if(!$uses_nodes && isset  ($wp_admin_bar->menu->{'my-account'}['children']->{'user-info'}['href']))
						$wp_admin_bar->menu->{'my-account'}['children']->{'user-info'}['href'] = $lwp;
					// -------
					if(!$uses_nodes && isset  ($wp_admin_bar->menu->{'my-account-with-avatar'}['href']))
						$wp_admin_bar->menu->{'my-account-with-avatar'}['href'] = $lwp;

					if(!$uses_nodes && isset  ($wp_admin_bar->menu->{'my-account-with-avatar'}['children']->{'user-info'}['href']))
						$wp_admin_bar->menu->{'my-account-with-avatar'}['children']->{'user-info'}['href'] = $lwp;

					if(!$uses_nodes && isset  ($wp_admin_bar->menu->{'my-account-with-avatar'}['children']->{'edit-profile'}['href']))
						$wp_admin_bar->menu->{'my-account-with-avatar'}['children']->{'edit-profile'}['href'] = $lwp;
				}
			do_action('ws_plugin__s2member_after_filter_admin_menu_bar', get_defined_vars());
		}
	}
}
