<?php
/**
* Menu page for the s2Member plugin (Main Multisite Options page).
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
* @package s2Member\Menu_Pages
* @since 3.0
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");

if(!class_exists("c_ws_plugin__s2member_menu_page_mms_ops"))
	{
		/**
		* Menu page for the s2Member plugin (Main Multisite Options page).
		*
		* @package s2Member\Menu_Pages
		* @since 110531
		*/
		class c_ws_plugin__s2member_menu_page_mms_ops
			{
				public function __construct()
					{
						if(c_ws_plugin__s2member_utils_conds::pro_is_installed()) {
							c_ws_plugin__s2member_pro_menu_pages::mms_ops_page_display();
							return; // Stop here.
						}
						echo '<div class="wrap ws-menu-page">'."\n";

						echo '<div class="ws-menu-page-toolbox">'."\n";
						c_ws_plugin__s2member_menu_pages_tb::display ();
						echo '</div>'."\n";

						echo '<h2>Multisite Config</h2>'."\n";

						echo '<table class="ws-menu-page-table">'."\n";
						echo '<tbody class="ws-menu-page-table-tbody">'."\n";
						echo '<tr class="ws-menu-page-table-tr">'."\n";
						echo '<td class="ws-menu-page-table-l">'."\n";

						if(is_multisite() && is_main_site()) // These panels will ONLY be available on the Main Site.
							{
								echo '<img src="'.esc_attr($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"]).'/images/large-icon.png" title="s2Member (a Membership management system for WordPress)" alt="" style="float:right; margin:25px 0 0 25px; border:0;" />'."\n";

								if(file_exists($ws_plugin__s2member_temp = dirname(dirname(dirname(__FILE__)))."/readme-ms.txt"))
									{
										echo '<div class="ws-menu-page-hr"></div>'."\n";

										if(!function_exists("NC_Markdown"))
											include_once dirname(dirname(__FILE__))."/externals/markdown/nc-markdown.inc.php";

										$ws_plugin__s2member_temp = file_get_contents($ws_plugin__s2member_temp);
										$ws_plugin__s2member_temp = preg_replace("/(\=)( )(.+?)( )(\=)/", "<h3>$3</h3>", $ws_plugin__s2member_temp);
										$ws_plugin__s2member_temp = NC_Markdown($ws_plugin__s2member_temp);

										echo '<div style="max-width:1024px;">';
										echo preg_replace("/(\<a)( href)/i", "$1".' target="_blank" rel="nofollow external"'."$2", $ws_plugin__s2member_temp);
										echo '</div>';
									}
							}
						else // Otherwise, we can display a simple notation; leading into Multisite Networking.
							{
								echo '<p style="margin-top:0;">Your WordPress installation does not have Multisite Networking enabled.<br />Which is perfectly OK :-) Multisite Networking is 100% completely optional.</p>'."\n";
								echo '<img src="'.esc_attr($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"]).'/images/large-icon.png" title="s2Member (a Membership management system for WordPress)" alt="" style="float:right; margin:25px 0 0 25px; border:0;" />'."\n";

								if(file_exists($ws_plugin__s2member_temp = dirname(dirname(dirname(__FILE__)))."/readme-ms.txt"))
									{
										echo '<div class="ws-menu-page-hr"></div>'."\n";

										if(!function_exists("NC_Markdown"))
											include_once dirname(dirname(__FILE__))."/externals/markdown/nc-markdown.inc.php";

										$ws_plugin__s2member_temp = file_get_contents($ws_plugin__s2member_temp);
										$ws_plugin__s2member_temp = preg_replace("/(\=)( )(.+?)( )(\=)/", "<h3>$3</h3>", $ws_plugin__s2member_temp);
										$ws_plugin__s2member_temp = NC_Markdown($ws_plugin__s2member_temp);

										echo '<div style="max-width:1024px;">';
										echo preg_replace("/(\<a)( href)/i", "$1".' target="_blank" rel="nofollow external"'."$2", $ws_plugin__s2member_temp);
										echo '</div>';
									}
							}

						echo '</td>'."\n";

						echo '<td class="ws-menu-page-table-r">'."\n";
						c_ws_plugin__s2member_menu_pages_rs::display();
						echo '</td>'."\n";

						echo '</tr>'."\n";
						echo '</tbody>'."\n";
						echo '</table>'."\n";

						echo '</div>'."\n";
					}
			}
	}

new c_ws_plugin__s2member_menu_page_mms_ops();
