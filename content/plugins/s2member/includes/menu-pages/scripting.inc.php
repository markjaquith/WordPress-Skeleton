<?php
/**
* Menu page for the s2Member plugin (API Scripting page).
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
 *
 * @TODO Shortcode equivalents using [else] syntax.
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_menu_page_scripting"))
	{
		/**
		* Menu page for the s2Member plugin (API Scripting page).
		*
		* @package s2Member\Menu_Pages
		* @since 110531
		*/
		class c_ws_plugin__s2member_menu_page_scripting
			{
				public function __construct ()
					{
						echo '<div class="wrap ws-menu-page">' . "\n";

						echo '<div class="ws-menu-page-toolbox">'."\n";
						c_ws_plugin__s2member_menu_pages_tb::display ();
						echo '</div>'."\n";

						echo '<h2>API / Scripting</h2>' . "\n";

						echo '<table class="ws-menu-page-table">' . "\n";
						echo '<tbody class="ws-menu-page-table-tbody">' . "\n";
						echo '<tr class="ws-menu-page-table-tr">' . "\n";
						echo '<td class="ws-menu-page-table-l">' . "\n";

						do_action("ws_plugin__s2member_during_scripting_page_before_left_sections", get_defined_vars ());

						if (apply_filters("ws_plugin__s2member_during_scripting_page_during_left_sections_display_api_easy_way", true, get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_before_api_easy_way", get_defined_vars ());

								echo '<div class="ws-menu-page-group" title="The Extremely Easy Way">' . "\n";

								echo '<div class="ws-menu-page-section ws-plugin--s2member-api-easy-way-section">' . "\n";
								echo '<h3>The Extremely Easy Way (no scripting required)</h3>' . "\n";
								echo '<p>From your s2Member Restriction Options panel, you may restrict access to certain Posts, Pages, Tags, Categories, and/or URIs based on a Member\'s Level. The s2Member Restriction Options panel makes it easy for you. All you do is type in the basics of what you want to restrict access to, and those sections of your site will be off limits to non-Members. That being said, there are times when you might need to have greater control over which portions of your site can be viewed by non-Members, or Members at different Levels; with different Capabilities. This is where API Scripting with Conditionals comes in.</p>' . "\n";
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_during_api_easy_way", get_defined_vars ());
								echo '</div>' . "\n";

								echo '</div>' . "\n";

								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_after_api_easy_way", get_defined_vars ());
							}

						if (apply_filters("ws_plugin__s2member_during_scripting_page_during_left_sections_display_api_simple_way", true, get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_before_api_simple_way", get_defined_vars ());

								if (is_multisite () && c_ws_plugin__s2member_utils_conds::is_multisite_farm () && !is_main_site ())
									{
										echo '<div class="ws-menu-page-group" title="Simple/Shortcode Conditionals">' . "\n";

										echo '<div class="ws-menu-page-section ws-plugin--s2member-api-simple-way-section">' . "\n";
										echo '<h3>Simple Conditionals (via WordPress Shortcodes)</h3>' . "\n";
										echo '<p>In an effort to give you even more control over access restrictions, s2Member makes Simple Conditionals available to you from within WordPress, using Shortcodes that are fully compatible with both the Visual Editor, and also the HTML Tab in WordPress. In this section, we\'ll demonstrate several functions that are possible using Shortcodes: <strong><code>is_user_logged_in()</code></strong>, <strong><code>is_user_not_logged_in()</code></strong>, <strong><code>user_is(user_id, role)</code></strong>, <strong><code>user_is_not(user_id, role)</code></strong>, <strong><code>user_can(user_id, capability)</code></strong>, <strong><code>user_cannot(user_id, capability)</code></strong>, <strong><code>current_user_is(role)</code></strong>, <strong><code>current_user_is_not(role)</code></strong>, <strong><code>current_user_can(capability)</code></strong>, <strong><code>current_user_cannot(capability)</code></strong>. To make use of these functions, please follow our code samples below. Using Shortcodes, it\'s easy to build Simple Conditionals within your content; based on a Member\'s Level, or even based on Custom Capabilities. s2Member\'s Shortcodes can be used inside a Post/Page, and also inside Text Widgets.</p>' . "\n";
										echo '<p><em>There are <strong>two different Shortcodes</strong> being demonstrated here:<br /><strong>1. <code>s2If</code></strong> (for testing simple conditional expressions).<br /><strong>2. <code>s2Get</code></strong> (to get an API Constant value, a Custom Field, or meta key).</em></p>' . "\n";
										do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_during_api_simple_way_farm", get_defined_vars ());

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Example #1:</strong> Full access for anyone that is logged in.</strong></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-is-user-logged-in-farm.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Example #2:</strong> The same as example #1, but this uses <code>[else]</code> syntax.</strong></p>' . "\n";
										if(!c_ws_plugin__s2member_utils_conds::pro_is_installed()) echo '<p><em><strong class="ws-menu-page-hilite">NOTE: the use of <code>[else]</code> requires s2Member Pro.</strong></em></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-is-user-logged-in-else-farm.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Example #3:</strong> Full access for any Member with a Level >= 1; also using <code>[else]</code> syntax.</strong></p>' . "\n";
										if(!c_ws_plugin__s2member_utils_conds::pro_is_installed()) echo '<p><em><strong class="ws-menu-page-hilite">NOTE: the use of <code>[else]</code> requires s2Member Pro.</strong></em></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-current-user-can-full-access-farm.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Example #4:</strong> Specific content for each different Member Level.</strong></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-current-user-is-specific-content-farm.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Example #5:</strong> The same as example #4, but this uses <code>[else]</code> syntax &amp; nesting.</strong></p>' . "\n";
										if(!c_ws_plugin__s2member_utils_conds::pro_is_installed()) echo '<p><em><strong class="ws-menu-page-hilite">NOTE: the use of <code>[else]</code> requires s2Member Pro.</strong></em></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-current-user-is-specific-content-else-farm.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Example #6:</strong> Simple Conditionals w/ integrated use of [s2Get /].</strong></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-s2-conditional-supplements-1-farm.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Example #7:</strong> Using multiple Conditionals together; also nesting other Shortcodes.</strong></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-s2-conditional-supplements-2-farm.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Example #8:</strong> Using multiple Conditionals together; also nesting Conditionals.</strong></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-s2-conditional-supplements-3-farm.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Membership Levels provide incremental access:</strong></p>' . "\n";
										echo '<p>* A Member with Level 4 access, will also be able to access Levels 0, 1, 2 &amp; 3.<br />* A Member with Level 3 access, will also be able to access Levels 0, 1 &amp; 2.<br />* A Member with Level 2 access, will also be able to access Levels 0 &amp; 1.<br />* A Member with Level 1 access, will also be able to access Level 0.<br />* A Subscriber with Level 0 access, will ONLY be able to access Level 0.<br />* A public Visitor will have NO access to protected content.</p>' . "\n";
										echo '<p><em>* WordPress Subscribers are at Membership Level 0. If you\'re allowing Open Registration, Subscribers will be at Level 0 (a Free Subscriber). WordPress Administrators, Editors, Authors, and Contributors have Level 4 access, with respect to s2Member. All of their other Roles/Capabilities are left untouched.</em></p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><em><strong>s2Member supports many <a href="http://s2member.com/r/wordpress-conditional-tags/" target="_blank" rel="external">Conditional Tags</a> in WordPress.</strong> Including: <strong><code>is_user_logged_in()</code></strong>, <strong><code>is_user_not_logged_in()</code></strong>, <strong><code>user_is(user_id, role)</code></strong>, <strong><code>user_is_not(user_id, role)</code></strong>, <strong><code>user_can(user_id, capability)</code></strong>, <strong><code>user_cannot(user_id, capability)</code></strong>, <strong><code>current_user_is(role)</code></strong>, <strong><code>current_user_is_not(role)</code></strong>, <strong><code>current_user_can(capability)</code></strong>, <strong><code>current_user_cannot(capability)</code></strong>, <strong><code>is_admin()</code></strong>, <strong><code>is_blog_admin()</code></strong>, <strong><code>is_user_admin()</code></strong>, <strong><code>is_network_admin()</code></strong>, <strong><code>is_404()</code></strong>, <strong><code>is_home()</code></strong>, <strong><code>is_front_page()</code></strong>, <strong><code>is_singular(ID|slug|{slug,ID})"</code></strong>, <strong><code>is_single(ID|slug|{slug,ID})</code></strong>, <strong><code>is_page(ID|slug|{slug,ID})</code></strong>, <strong><code>is_page_template(file.php)</code></strong>, <strong><code>is_attachment()</code></strong>, <strong><code>is_feed()</code></strong>, <strong><code>is_archive()</code></strong>, <strong><code>is_search()</code></strong>, <strong><code>is_category(ID|slug|{slug,ID})</code></strong>, <strong><code>is_tax(taxonomy,term)</code></strong>, <strong><code>is_tag(slug|{slug,slug})"</code></strong>, <strong><code>has_tag(slug|{slug,slug})"</code></strong>, <strong><code>is_author(ID|slug|{slug,ID})</code></strong>, <strong><code>is_date()</code></strong>, <strong><code>is_day()</code></strong>, <strong><code>is_month()</code></strong>, <strong><code>is_time()</code></strong>, <strong><code>is_year()</code></strong>, <strong><code>is_sticky(ID)</code></strong>, <strong><code>is_paged()</code></strong>, <strong><code>is_preview()</code></strong>, <strong><code>is_comments_popup()</code></strong>, <strong><code>in_the_loop()</code></strong>, <strong><code>comments_open()</code></strong>, <strong><code>pings_open()</code></strong>, <strong><code>has_excerpt(ID)</code></strong>, <strong><code>has_post_thumbnail(ID)</code></strong>.</em></p>' . "\n";

										echo '<p><em><strong>Passing arguments into a Simple Conditional:</strong></em></p>' . "\n";
										echo '<p><em>1. True/false → ex: <code>current_user_can()</code> / <code>!current_user_can()</code><br />2. False explicitly → ex: <code>current_user_cannot()</code><br />3. Passing an ID → ex: <code>is_page(24)</code><br />4. Passing a Slug → ex: <code>is_page(my-cool-page)</code><br />5. Passing an Array → ex: <code>is_page({my-cool-page,24,about,contact-form})</code></em></p>' . "\n";
										echo '<p><em>*Tip: do NOT use spaces inside Conditionals.<br /> <strong class="ws-menu-page-error-hilite">BAD</strong> <code>is_page(My Membership Options Page)</code><br />- use slugs or IDs instead, no spaces.</em></p>' . "\n";

										echo '<p><em><strong>Implementing AND/OR Conditional expressions:</strong></em></p>' . "\n";
										echo '<p><em>*Tip: do NOT mix AND/OR expressions.<br /> <strong class="ws-menu-page-error-hilite">BAD</strong> <code>is_user_logged_in() AND is_page(1) OR is_page(2)</code><br />- use one or the other; do NOT mix AND/OR together.</em></p>' . "\n";
										echo '<p><em><strong class="ws-menu-page-hilite">If you need to have both types of logic, use nesting...</strong></em></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-s2-conditional-nesting-farm.x-php")) . '</p>' . "\n";
										echo '<p><em><strong class="ws-menu-page-hilite">Another example, if you use <code>[else]</code> when nesting...</strong></em></p>' . "\n";
										if(!c_ws_plugin__s2member_utils_conds::pro_is_installed()) echo '<p><em><strong class="ws-menu-page-hilite">NOTE: the use of <code>[else]</code> requires s2Member Pro.</strong></em></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-s2-conditional-nesting-else-farm.x-php")) . '</p>' . "\n";
										echo '</div>' . "\n";

										echo '</div>' . "\n";
									}
								else // Otherwise, we can display the standardized version of this information.
									{
										echo '<div class="ws-menu-page-group" title="Simple/Shortcode Conditionals">' . "\n";

										echo '<div class="ws-menu-page-section ws-plugin--s2member-api-simple-way-section">' . "\n";
										echo '<h3>Simple Conditionals (via WordPress Shortcodes)</h3>' . "\n";
										echo '<p>In an effort to give you even more control over access restrictions, s2Member makes Simple Conditionals available to you from within WordPress, using Shortcodes that are fully compatible with both the Visual Editor, and also the HTML Tab in WordPress. In this section, we\'ll demonstrate several functions that are possible using Shortcodes: <strong><code>is_user_logged_in()</code></strong>, <strong><code>is_user_not_logged_in()</code></strong>, <strong><code>user_is(user_id, role)</code></strong>, <strong><code>user_is_not(user_id, role)</code></strong>, <strong><code>user_can(user_id, capability)</code></strong>, <strong><code>user_cannot(user_id, capability)</code></strong>, <strong><code>current_user_is(role)</code></strong>, <strong><code>current_user_is_not(role)</code></strong>, <strong><code>current_user_can(capability)</code></strong>, <strong><code>current_user_cannot(capability)</code></strong>, <strong><code>current_user_is_for_blog(blog_id,role)</code></strong>, <strong><code>current_user_is_not_for_blog(blog_id,role)</code></strong>, <strong><code>current_user_can_for_blog(blog_id,capability)</code></strong>, <strong><code>current_user_cannot_for_blog(blog_id,capability)</code></strong>. To make use of these functions, please follow our code samples below. Using Shortcodes, it\'s easy to build Simple Conditionals within your content; based on a Member\'s Level, or even based on Custom Capabilities. s2Member\'s Shortcodes can be used inside a Post/Page, and also inside Text Widgets.</p>' . "\n";
										echo '<p><em>There are <strong>two different Shortcodes</strong> being demonstrated here:<br /><strong>1. <code>s2If</code></strong> (for testing simple conditional expressions).<br /><strong>2. <code>s2Get</code></strong> (to get an API Constant value, a Custom Field, or meta key).</em></p>' . "\n";
										echo '<p>Please see this KB article to learn more: <a href="http://www.s2member.com/kb/simple-shortcode-conditionals/" target="_blank" rel="external">s2Member Simple Shortcode Conditionals</a></p>' . "\n";
										do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_during_api_simple_way", get_defined_vars ());

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>TIP:</strong> In addition to this documentation, you may also want to have a look at the <a href="http://www.s2member.com/codex/" target="_blank" rel="external">s2Member Codex</a>.<br />' . "\n";
										echo '<strong>See Also:</strong> <a href="http://www.s2member.com/codex/stable/s2member/api_constants/package-summary/" target="_blank" rel="external">s2Member Codex → API Constants</a>, and <a href="http://www.s2member.com/codex/stable/s2member/api_functions/package-summary/" target="_blank" rel="external">s2Member Codex → API Functions</a>.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Example #1:</strong> Full access for anyone that is logged in.</strong></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-is-user-logged-in.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Example #2:</strong> The same as example #1, but this uses <code>[else]</code> syntax.</strong></p>' . "\n";
										if(!c_ws_plugin__s2member_utils_conds::pro_is_installed()) echo '<p><em><strong class="ws-menu-page-hilite">NOTE: the use of <code>[else]</code> requires s2Member Pro.</strong></em></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-is-user-logged-in-else.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Example #3:</strong> Full access for any Member with a Level >= 1; also using <code>[else]</code> syntax.</strong></p>' . "\n";
										if(!c_ws_plugin__s2member_utils_conds::pro_is_installed()) echo '<p><em><strong class="ws-menu-page-hilite">NOTE: the use of <code>[else]</code> requires s2Member Pro.</strong></em></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-current-user-can-full-access.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Example #4:</strong> Specific content for each different Member Level.</strong></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-current-user-is-specific-content.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Example #5:</strong> The same as example #4, but this uses <code>[else]</code> syntax &amp; nesting.</strong></p>' . "\n";
										if(!c_ws_plugin__s2member_utils_conds::pro_is_installed()) echo '<p><em><strong class="ws-menu-page-hilite">NOTE: the use of <code>[else]</code> requires s2Member Pro.</strong></em></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-current-user-is-specific-content-else.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Example #6:</strong> Simple Conditionals w/ integrated use of [s2Get /].</strong></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-s2-conditional-supplements-1.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Example #7:</strong> Using multiple Conditionals together; also nesting other Shortcodes.</strong></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-s2-conditional-supplements-2.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Example #8:</strong> Using multiple Conditionals together; also nesting Conditionals.</strong></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-s2-conditional-supplements-3.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>Membership Levels provide incremental access:</strong></p>' . "\n";
										echo '<p>* A Member with Level 4 access, will also be able to access Levels 0, 1, 2 &amp; 3.<br />* A Member with Level 3 access, will also be able to access Levels 0, 1 &amp; 2.<br />* A Member with Level 2 access, will also be able to access Levels 0 &amp; 1.<br />* A Member with Level 1 access, will also be able to access Level 0.<br />* A Subscriber with Level 0 access, will ONLY be able to access Level 0.<br />* A public Visitor will have NO access to protected content.</p>' . "\n";
										echo '<p><em>* WordPress Subscribers are at Membership Level 0. If you\'re allowing Open Registration, Subscribers will be at Level 0 (a Free Subscriber). WordPress Administrators, Editors, Authors, and Contributors have Level 4 access, with respect to s2Member. All of their other Roles/Capabilities are left untouched.</em></p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><em><strong>s2Member supports ALL <a href="http://s2member.com/r/wordpress-conditional-tags/" target="_blank" rel="external">Conditional Tags</a> in WordPress.</strong> Including, but not limited to: <strong><code>is_user_logged_in()</code></strong>, <strong><code>is_user_not_logged_in()</code></strong>, <strong><code>user_is(user_id, role)</code></strong>, <strong><code>user_is_not(user_id, role)</code></strong>, <strong><code>user_can(user_id, capability)</code></strong>, <strong><code>user_cannot(user_id, capability)</code></strong>, <strong><code>current_user_is(role)</code></strong>, <strong><code>current_user_is_not(role)</code></strong>, <strong><code>current_user_can(capability)</code></strong>, <strong><code>current_user_cannot(capability)</code></strong>, <strong><code>current_user_is_for_blog(blog_id,role)</code></strong>, <strong><code>current_user_is_not_for_blog(blog_id,role)</code></strong>, <strong><code>current_user_can_for_blog(blog_id,capability)</code></strong>, <strong><code>current_user_cannot_for_blog(blog_id,capability)</code></strong>, <strong><code>is_multisite()</code></strong>, <strong><code>is_main_site()</code></strong>, <strong><code>is_super_admin()</code></strong>, <strong><code>is_admin()</code></strong>, <strong><code>is_blog_admin()</code></strong>, <strong><code>is_user_admin()</code></strong>, <strong><code>is_network_admin()</code></strong>, <strong><code>is_404()</code></strong>, <strong><code>is_home()</code></strong>, <strong><code>is_front_page()</code></strong>, <strong><code>is_comments_popup()</code></strong>, <strong><code>is_singular(ID|slug|{slug,ID})"</code></strong>, <strong><code>is_single(ID|slug|{slug,ID})</code></strong>, <strong><code>is_page(ID|slug|{slug,ID})</code></strong>, <strong><code>is_page_template(file.php)</code></strong>, <strong><code>is_attachment()</code></strong>, <strong><code>is_feed()</code></strong>, <strong><code>is_trackback()</code></strong>, <strong><code>is_archive()</code></strong>, <strong><code>is_search()</code></strong>, <strong><code>is_category(ID|slug|{slug,ID})</code></strong>, <strong><code>is_tax(taxonomy,term)</code></strong>, <strong><code>is_tag(slug|{slug,slug})"</code></strong>, <strong><code>has_tag(slug|{slug,slug})"</code></strong>, <strong><code>is_author(ID|slug|{slug,ID})</code></strong>, <strong><code>is_date()</code></strong>, <strong><code>is_day()</code></strong>, <strong><code>is_month()</code></strong>, <strong><code>is_time()</code></strong>, <strong><code>is_year()</code></strong>, <strong><code>is_sticky(ID)</code></strong>, <strong><code>is_paged()</code></strong>, <strong><code>is_preview()</code></strong>, <strong><code>is_comments_popup()</code></strong>, <strong><code>in_the_loop()</code></strong>, <strong><code>comments_open()</code></strong>, <strong><code>pings_open()</code></strong>, <strong><code>has_excerpt(ID)</code></strong>, <strong><code>has_post_thumbnail(ID)</code></strong>, <strong><code>is_active_sidebar(ID|number)</code></strong>.</em></p>' . "\n";

										echo '<p><em><strong>Passing arguments into a Simple Conditional:</strong></em></p>' . "\n";
										echo '<p><em>1. True/false → ex: <code>current_user_can()</code> / <code>!current_user_can()</code><br />2. False explicitly → ex: <code>current_user_cannot()</code><br />3. Passing an ID → ex: <code>is_page(24)</code><br />4. Passing a Slug → ex: <code>is_page(my-cool-page)</code><br />5. Passing an Array → ex: <code>is_page({my-cool-page,24,about,contact-form})</code></em></p>' . "\n";
										echo '<p><em>*Tip: do NOT use spaces inside Conditionals.<br /> <strong class="ws-menu-page-error-hilite">BAD</strong> <code>is_page(My Membership Options Page)</code><br />- use slugs or IDs instead, no spaces.</em></p>' . "\n";

										echo '<p><em><strong>Implementing AND/OR Conditional expressions:</strong></em></p>' . "\n";
										echo '<p><em>*Tip: do NOT mix AND/OR expressions.<br /> <strong class="ws-menu-page-error-hilite">BAD</strong> <code>is_user_logged_in() AND is_page(1) OR is_page(2)</code><br />- use one or the other; do NOT mix AND/OR together.</em></p>' . "\n";
										echo '<p><em><strong class="ws-menu-page-hilite">If you need to have both types of logic, use nesting...</strong></em></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-s2-conditional-nesting.x-php")) . '</p>' . "\n";
										echo '<p><em><strong class="ws-menu-page-hilite">Another example, if you use <code>[else]</code> when nesting...</strong></em></p>' . "\n";
										if(!c_ws_plugin__s2member_utils_conds::pro_is_installed()) echo '<p><em><strong class="ws-menu-page-hilite">NOTE: the use of <code>[else]</code> requires s2Member Pro.</strong></em></p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/sc-s2-conditional-nesting-else.x-php")) . '</p>' . "\n";
										echo '</div>' . "\n";

										echo '</div>' . "\n";
									}

								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_after_api_simple_way", get_defined_vars ());
							}

						if (apply_filters("ws_plugin__s2member_during_scripting_page_during_left_sections_display_api_advanced_way", (!is_multisite () || !c_ws_plugin__s2member_utils_conds::is_multisite_farm () || is_main_site ()), get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_before_api_advanced_way", get_defined_vars ());

								echo '<div class="ws-menu-page-group" title="Advanced/PHP Conditionals">' . "\n";

								echo '<div class="ws-menu-page-section ws-plugin--s2member-api-advanced-way-section">' . "\n";
								echo '<h3>The Advanced Way (some PHP scripting required)</h3>' . "\n";
								echo '<p>In an effort to give you even more control over access restrictions, s2Member makes some PHP functions, and also some PHP Constants, available to you from within WordPress. In this section, we\'ll demonstrate several functions: <strong><code>is_user_logged_in()</code></strong>, <strong><code>is_user_not_logged_in()</code></strong>, <strong><code>user_is(user_id, role)</code></strong>, <strong><code>user_is_not(user_id, role)</code></strong>, <strong><code>user_can(user_id, capability)</code></strong>, <strong><code>user_cannot(user_id, capability)</code></strong>, <strong><code>current_user_is("role")</code></strong>, <strong><code>current_user_is_not("role")</code></strong>, <strong><code>current_user_can("capability")</code></strong>, <strong><code>current_user_cannot("capability")</code></strong>, <strong><code>current_user_is_for_blog($blog_id,"role")</code></strong>, <strong><code>current_user_is_not_for_blog($blog_id,"role")</code></strong>, <strong><code>current_user_can_for_blog($blog_id,"capability")</code></strong>, &amp; <strong><code>current_user_cannot_for_blog($blog_id,"capability")</code></strong>. To make use of these functions, please follow our PHP code samples below. Using PHP, is a very powerful way to build Advanced Conditionals within your content; based on a Member\'s Level, Custom Capabilities, and/or other factors. In order to use PHP scripting inside your Posts/Pages, you\'ll need to install this handy plugin (<a href="http://s2member.com/r/ezphp/" target="_blank" rel="external">ezPHP</a>).</p>' . "\n";
								echo '<p>See also this related KB article: <a href="http://www.s2member.com/kb/simple-shortcode-conditionals/" target="_blank" rel="external">s2Member Simple Shortcode Conditionals</a></p>' . "\n";
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_during_api_advanced_way", get_defined_vars ());

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>TIP:</strong> In addition to this documentation, you may also want to have a look at the <a href="http://www.s2member.com/codex/" target="_blank" rel="external">s2Member Codex</a>.<br />' . "\n";
								echo '<strong>See Also:</strong> <a href="http://www.s2member.com/codex/stable/s2member/api_constants/package-summary/" target="_blank" rel="external">s2Member Codex → API Constants</a>, and <a href="http://www.s2member.com/codex/stable/s2member/api_functions/package-summary/" target="_blank" rel="external">s2Member Codex → API Functions</a>.</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Example #1:</strong> Full access for anyone that is logged in.</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/is-user-logged-in.x-php")) . '</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Example #2:</strong> Full access for any Member with a Level >= 1.</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-can-full-access.x-php")) . '</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Example #3:</strong> Specific content for each different Member Level.</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-is-specific-content.x-php")) . '</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Example #4:</strong> Using s2Member API Conditionals, supplementing WordPress core functions.</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/s2-conditional-supplements-1.x-php")) . '</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Example #5:</strong> Using s2Member API Conditionals, supplementing WordPress core functions.</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/s2-conditional-supplements-2.x-php")) . '</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Example #6:</strong> Using multiple Conditionals together, and even nesting Conditionals.</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/s2-conditional-supplements-3.x-php")) . '</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Example #7:</strong> Using s2Member API Constants, instead of conditional functions.</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-can-constants-1.x-php")) . '</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Example #8:</strong> Using s2Member API Constants, instead of conditional functions.</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-can-constants-2.x-php")) . '</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Membership Levels provide incremental access:</strong></p>' . "\n";
								echo '<p>* A Member with Level 4 access, will also be able to access Levels 0, 1, 2 &amp; 3.<br />* A Member with Level 3 access, will also be able to access Levels 0, 1 &amp; 2.<br />* A Member with Level 2 access, will also be able to access Levels 0 &amp; 1.<br />* A Member with Level 1 access, will also be able to access Level 0.<br />* A Subscriber with Level 0 access, will ONLY be able to access Level 0.<br />* A public Visitor will have NO access to protected content.</p>' . "\n";
								echo '<p><em>* WordPress Subscribers are at Membership Level 0. If you\'re allowing Open Registration, Subscribers will be at Level 0 (a Free Subscriber). WordPress Administrators, Editors, Authors, and Contributors have Level 4 access, with respect to s2Member. All of their other Roles/Capabilities are left untouched.</em></p>' . "\n";
								echo '</div>' . "\n";

								echo '</div>' . "\n";

								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_after_api_advanced_way", get_defined_vars ());
							}

						if (apply_filters("ws_plugin__s2member_during_scripting_page_during_left_sections_display_api_queries", (!is_multisite () || !c_ws_plugin__s2member_utils_conds::is_multisite_farm () || is_main_site ()), get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_before_api_queries", get_defined_vars ());

								echo '<div class="ws-menu-page-group" title="Advanced/PHP Query Conditionals">' . "\n";

								echo '<div class="ws-menu-page-section ws-plugin--s2member-api-advanced-way-section">' . "\n";
								echo '<h3>Advanced Query Conditionals (some PHP scripting required)</h3>' . "\n";
								echo '<p>s2Member provides several built-in API Functions that are tailored to meet the needs of developers integrating s2Member into their themes. Such as: <strong><code>is_protected_by_s2member($id, "[category,tag,post,page,singular,uri]")</code></strong>, <strong><code>is_permitted_by_s2member($id, "[category,tag,post,page,singular,uri]")</code></strong>, <strong><code>is_category_protected_by_s2member($cat_id)</code></strong>, <strong><code>is_category_permitted_by_s2member($cat_id)</code></strong>, <strong><code>is_tag_protected_by_s2member($tag_id [slug or tag name])</code></strong>, <strong><code>is_tag_permitted_by_s2member($tag_id [slug or tag name])</code></strong>, <strong><code>is_post_protected_by_s2member($post_id)</code></strong>, <strong><code>is_post_permitted_by_s2member($post_id)</code></strong>, <strong><code>is_page_protected_by_s2member($page_id)</code></strong>, <strong><code>is_page_permitted_by_s2member($page_id)</code></strong>, <strong><code>is_uri_protected_by_s2member($uri [or full url])</code></strong>, <strong><code>is_uri_permitted_by_s2member($uri [ or full url])</code></strong>.</p>' . "\n";
								echo '<p>In addition, there are two special functions that can be applied by theme authors before making custom queries: <strong><code>attach_s2member_query_filters()</code></strong>, <strong><code>detach_s2member_query_filters()</code></strong>. These can be used before and after a call to <strong><code>query_posts()</code></strong> for example. s2Member will automatically filter all protected content (not available to the current User/Member).</p>' . "\n";
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_during_api_queries", get_defined_vars ());

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>TIP:</strong> In addition to this documentation, you may also want to have a look at the <a href="http://www.s2member.com/codex/" target="_blank" rel="external">s2Member Codex</a>.<br />' . "\n";
								echo '<strong>See Also:</strong> <a href="http://www.s2member.com/codex/stable/s2member/api_constants/package-summary/" target="_blank" rel="external">s2Member Codex → API Constants</a>, and <a href="http://www.s2member.com/codex/stable/s2member/api_functions/package-summary/" target="_blank" rel="external">s2Member Codex → API Functions</a>.</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Example #1:</strong> Pre-filtering custom queries in WordPress.</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/custom-queries.x-php")) . '</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Example #2:</strong> OR, instead of pre-filtering; check Access Restrictions in The Loop.</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/custom-queries-loop.x-php")) . '</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Example #3:</strong> Checking Tag Restrictions.</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/specific-tag-restrictions.x-php")) . '</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Example #4:</strong> Checking Category Restrictions.</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/specific-category-restrictions.x-php")) . '</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Example #5:</strong> Checking Page Restrictions.</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/specific-page-restrictions.x-php")) . '</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Example #6:</strong> Checking Post Restrictions, including Custom Post Types.</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/specific-post-restrictions.x-php")) . '</p>' . "\n";
								echo '</div>' . "\n";

								echo '</div>' . "\n";

								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_after_api_queries", get_defined_vars ());
							}

						if (apply_filters("ws_plugin__s2member_during_scripting_page_during_left_sections_display_custom_capabilities", (!is_multisite () || !c_ws_plugin__s2member_utils_conds::is_multisite_farm () || is_main_site ()), get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_before_custom_capabilities", get_defined_vars ());

								echo '<div class="ws-menu-page-group" title="Custom Capabilities (Packages)">' . "\n";

								echo '<div class="ws-menu-page-section ws-plugin--s2member-custom-capabilities-section">' . "\n";
								echo '<h3>Packaging Together Custom Capabilities w/ Membership</h3>' . "\n";
								echo '<iframe width="560" height="315" src="https://www.youtube.com/embed/_F91xzrmq-Q" frameborder="0" allowscriptaccess="always" allowfullscreen="true" style="float:right; margin:0 0 20px 20px; width:300px; height:200px;"></iframe>' . "\n";
								echo '<p>Using one of s2Member\'s Payment Button and/or Form Generators, you can add Custom Capabilities in comma-delimited format. s2Member builds upon existing functionality offered by <a href="http://s2member.com/r/wordpress-rolescaps/" target="_blank" rel="external">WordPress Roles/Capabilities</a>. s2Member supports Free Subscribers <em>(at Level #0)</em>, and several Primary Roles created by the s2Member plugin <em>(i.e., s2Member Levels 1-4, or up to the number of configured Levels)</em>. Each s2Member Level <em>(aka: s2Member Role)</em> provides the Capability <code>current_user_can("access_s2member_level0"), 1, 2, 3, 4</code>, where <code>access_s2member_level[0-4]</code> is the Capability associated with each Role; and Membership Levels provide incremental access <em>(i.e., Level #4 Members can also access content at Levels 0, 1, 2, and 3 beneath them)</em>. In short, these Level-based permissions are the default Capabilities that come with each Membership Level being sold on your site.</p>' . "\n";
								echo '<p>Now, if you\'d like to package together some variations of each Membership Level that you\'re selling, you can! All you do is add <strong>Custom Capabilities</strong> whenever you create your Payment Button and/or Form Shortcode (<em>there is a field in the Button &amp; Form Generators where you can enter Custom Capabilities</em>). You can sell Membership Packages that come with Custom Capabilities, and even with custom prices.</p>' . "\n";
								echo '<p>Custom Capabilities are an extension to a feature that already exists in WordPress. The <code>current_user_can()</code> function, can be used to test for these additional Capabilities that you allow. Whenever a Member completes the checkout process, after having purchased a Membership from you (one that included Custom Capabilities), s2Member will add those Custom Capabilities to the account for that specific Member.</p>' . "\n";
								echo '<p>Custom Capabilities are always prepended with <code>access_s2member_ccap_</code>. You fill in the last part, with ONLY lowercase alpha-numerics and/or underscores. For example, let\'s say you want to sell Membership Level #1, as is. But, you also want to sell a slight variation of Membership Level #1, that includes the ability to access the Music &amp; Video sections of your site. So, instead of selling this additional access under a whole new Membership Level, you could just sell a modified version of Membership Level #1. Add the the Custom Capabilities: <code>music,videos</code>. Once a Member has these Capabilities, you can test for these Capabilities using <code>current_user_can("access_s2member_ccap_music")</code> and <code>current_user_can("access_s2member_ccap_videos")</code>.</p>' . "\n";
								echo '<p>The important thing to realize, is that Custom Capabilities, are just that. They\'re custom. s2Member only deals with the default Capabilities that it uses. If you start using Custom Capabilities, you MUST use Simple or Advanced Conditionals (<em>i.e., <a href="http://codex.wordpress.org/Function_Reference/current_user_can" target="_blank" rel="external"><code>current_user_can()</code></a> logic</em>) to test for them. Either in your theme files with PHP, or in Posts/Pages using <a href="http://www.s2member.com/kb/simple-shortcode-conditionals/" target="_blank" rel="external">Simple Conditionals</a> <em>(powered by Shortcodes)</em>.</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>See also:</strong> This KB article: <a href="http://www.s2member.com/kb/roles-caps/" target="_blank" rel="external">s2Member Roles/Capabilities (Including bbPress Support)</a>.</p>'."\n";
								echo '<p><strong class="ws-menu-page-hilite">See also:</strong> This VIDEO tutorial: <a href="http://www.s2member.com/videos/A2C07377CF60025E/" target="_blank" rel="external">Using Custom Capabilities with s2Member</a> (by Lead Developer Jason Caldwell).</p>'."\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>New:</strong> In the latest versions of s2Member, you can automatically require certain Custom Capabilities on a per Post/Page basis. So now, s2Member <em>(if you prefer)</em> CAN handle Custom Capabilities for you automatically! Whenever you edit a Post/Page <em>(i.e., there is a Meta Box for s2Member in your Post/Page editing station)</em>... you can tell s2Member to require certain Custom Capabilities that you type in, using comma-delimited format. In other words, you will need to type in some of the trigger words that you used whenever you created your Payment Buttons/Forms. This way paying Members will have the Custom Capabilities to view different kinds of content that you offer.</p>' . "\n";
								echo '<p><strong>New:</strong> By default, a Checkout Button or Form generated by s2Member is designed to (Add) Custom Capabilities to any that may or may not already exist for a particular User/Member. However, starting with s2Member v110815+, you can tell s2Member to (Remove All) Custom Capabilities, and then (Add) only the new ones that you specify. This is accomplished on a per Form/Button basis by preceding your comma-delimited list of Custom Capabilities with `-all`. For further details on this topic, click the <a href="#" onclick="alert(\'*ADVANCED TIP: You can specifiy a list of Custom Capabilities that will be (Added) with this purchase. Or, you could tell s2Member to (Remove All) Custom Capabilities that may or may not already exist for a particular Member, and (Add) only the new ones that you specify. To do this, just start your list of Custom Capabilities with `-all`.\\n\\nSo instead of just (Adding) Custom Capabilities:\\nmusic,videos,archives,gifts\\n\\nYou could (Remove All) that may already exist, and then (Add) new ones:\\n-all,calendar,forums,tools\\n\\nOr to just (Remove All) and (Add) nothing:\\n-all\'); return false;" tabindex="-1">[?]</a> icon next to the Custom Capabilities field in any Button/Form Generator supplied by s2Member.</p>' . "\n";
								echo '<p><strong>New:</strong> Independent Custom Capabilities. You can now sell one or more Custom Capabilities using Buy Now functionality, to "existing" Users/Members, regardless of which Membership Level they have on your site <em>(i.e., you could even sell Independent Custom Capabilities to Users at Membership Level #0, normally referred to as Free Subscribers, if you like)</em>. So this is quite flexible. For further details, please check your Dashboard, under: <strong>s2Member → PayPal Buttons → Capability (Buy Now) Buttons</strong>.</p>' . "\n";
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_during_custom_capabilities", get_defined_vars ());

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>TIP:</strong> In addition to this documentation, you may also want to have a look at the <a href="http://www.s2member.com/codex/" target="_blank" rel="external">s2Member Codex</a>.<br />' . "\n";
								echo '<strong>See Also:</strong> <a href="http://www.s2member.com/codex/stable/s2member/api_constants/package-summary/" target="_blank" rel="external">s2Member Codex → API Constants</a>, and <a href="http://www.s2member.com/codex/stable/s2member/api_functions/package-summary/" target="_blank" rel="external">s2Member Codex → API Functions</a>.<br />' . "\n";
								echo '<strong>See Also:</strong> <a href="http://s2member.com/r/enhanced-capability-manager/" target="_blank" rel="external">Plugins → Enhanced Capability Manager</a> <em>(may come in handy for some)</em>.</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Custom Capabilities:</strong> (music,videos):</p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-can-ccaps-1.x-php")) . '</p>' . "\n";

								echo '<p><strong>Custom Capabilities:</strong> (ebooks,reports,tips):</p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-can-ccaps-2.x-php")) . '</p>' . "\n";
								echo '</div>' . "\n";

								echo '</div>' . "\n";

								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_after_custom_capabilities", get_defined_vars ());
							}

						if (apply_filters("ws_plugin__s2member_during_scripting_page_during_left_sections_display_custom_capability_files", (!is_multisite () || !c_ws_plugin__s2member_utils_conds::is_multisite_farm () || is_main_site ()), get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_before_custom_capability_files", get_defined_vars ());

								echo '<div class="ws-menu-page-group" title="Custom Capability &amp; Member Level Files">' . "\n";

								echo '<div class="ws-menu-page-section ws-plugin--s2member-custom-capability-files-section">' . "\n";
								echo '<h3>Restricting Files, Based On Custom Capabilities</h3>' . "\n";
								echo '<p>If you\'re NOT familiar with Custom Capabilities yet, please read the section above, titled: <code>Custom Capability Packages</code>, and also see: <strong>s2Member → Download Options</strong>, both as primers; BEFORE you read this section. Once you understand the basic concept of Custom Capabilities &amp; Protected File Downloads, you\'ll see that (by default) s2Member does NOT handle File Download Protection with respect to Custom Capabilities. That\'s where Custom Capability Sub-directories come in.</p>' . "\n";
								echo '<p>You can create Custom Capability Sub-directories under: <code>' . esc_html (c_ws_plugin__s2member_utils_dirs::doc_root_path ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])) . '</code>. For instance, if you have a Custom Capability <code>music</code>, you can place protected files that should ONLY be accessible to Members with <code>access_s2member_ccap_music</code>, inside: <code>/' . esc_html (c_ws_plugin__s2member_utils_dirs::basename_dir_app_data ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])) . '/access-s2member-ccap-music/</code>. Some examples are provided below.</p>' . "\n";
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_during_custom_capability_files", get_defined_vars ());

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>TIP:</strong> In addition to this documentation, you may also want to have a look at the <a href="http://www.s2member.com/codex/" target="_blank" rel="external">s2Member Codex</a>.<br />' . "\n";
								echo '<strong>See Also:</strong> <a href="http://www.s2member.com/codex/stable/s2member/api_constants/package-summary/" target="_blank" rel="external">s2Member Codex → API Constants</a>, and <a href="http://www.s2member.com/codex/stable/s2member/api_functions/package-summary/" target="_blank" rel="external">s2Member Codex → API Functions</a>.</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Custom Capabilities:</strong> (music,videos)</p>' . "\n";
								echo '<p>Sub-Directory: <code>/' . esc_html (c_ws_plugin__s2member_utils_dirs::basename_dir_app_data ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])) . '/access-s2member-ccap-music</code><br />Sub-Directory: <code>/' . esc_html (c_ws_plugin__s2member_utils_dirs::basename_dir_app_data ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])) . '/access-s2member-ccap-videos</code></p>' . "\n";
								echo '<p>Protected File: <code>/' . esc_html (c_ws_plugin__s2member_utils_dirs::basename_dir_app_data ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])) . '/access-s2member-ccap-music/file.mp3</code><br />Protected File: <code>/' . esc_html (c_ws_plugin__s2member_utils_dirs::basename_dir_app_data ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])) . '/access-s2member-ccap-videos/file.avi</code></p>' . "\n";
								echo '<p>Now, here are some link examples, using Custom Capability Sub-directories:</p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/ccap-file-downloads.x-php")) . '</p>' . "\n";
								echo '<p><em>These links will ONLY work for Members who are logged-in, with the proper Capabilities.</em></p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Membership Levels:</strong> (this also works fine)</p>' . "\n";
								echo '<p>Sub-Directory: <code>/' . esc_html (c_ws_plugin__s2member_utils_dirs::basename_dir_app_data ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])) . '/access-s2member-level0</code><br />Sub-Directory: <code>/' . esc_html (c_ws_plugin__s2member_utils_dirs::basename_dir_app_data ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])) . '/access-s2member-level1</code><br />Sub-Directory: <code>/' . esc_html (c_ws_plugin__s2member_utils_dirs::basename_dir_app_data ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])) . '/access-s2member-level2</code><br />Sub-Directory: <code>/' . esc_html (c_ws_plugin__s2member_utils_dirs::basename_dir_app_data ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])) . '/access-s2member-level3</code><br />Sub-Directory: <code>/' . esc_html (c_ws_plugin__s2member_utils_dirs::basename_dir_app_data ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])) . '/access-s2member-level4</code></p>' . "\n";
								echo '<p>Protected File: <code>/' . esc_html (c_ws_plugin__s2member_utils_dirs::basename_dir_app_data ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])) . '/access-s2member-level0/tiger.doc</code><br />Protected File: <code>/' . esc_html (c_ws_plugin__s2member_utils_dirs::basename_dir_app_data ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])) . '/access-s2member-level1/zebra.pdf</code><br />Protected File: <code>/' . esc_html (c_ws_plugin__s2member_utils_dirs::basename_dir_app_data ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])) . '/access-s2member-level2/elephant.doc</code><br />Protected File: <code>/' . esc_html (c_ws_plugin__s2member_utils_dirs::basename_dir_app_data ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])) . '/access-s2member-level3/rhino.pdf</code><br />Protected File: <code>/' . esc_html (c_ws_plugin__s2member_utils_dirs::basename_dir_app_data ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])) . '/access-s2member-level4/lion.doc</code></p>' . "\n";
								echo '<p>Now, here are some link examples, using Member Level Sub-directories:</p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/level-file-downloads.x-php")) . '</p>' . "\n";
								echo '<p><em>These links will ONLY work for Members who are logged-in, with an adequate Membership Level.</em></p>' . "\n";
								echo '</div>' . "\n";

								echo '</div>' . "\n";

								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_after_custom_capability_files", get_defined_vars ());
							}

						if (apply_filters("ws_plugin__s2member_during_scripting_page_during_left_sections_display_api_advanced_dripping", (!is_multisite () || !c_ws_plugin__s2member_utils_conds::is_multisite_farm () || is_main_site ()), get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_before_api_advanced_dripping", get_defined_vars ());

								echo '<div class="ws-menu-page-group" title="s2Member Content Dripping">' . "\n";

								echo '<div class="ws-menu-page-section ws-plugin--s2member-api-advanced-dripping-section">' . "\n";
								echo '<h3>Dripping Content (some PHP scripting may be required)</h3>' . "\n";
								echo '<p>Content Dripping is the gradual, pre-scheduled release of premium website content to paying Members. This has become increasingly popular, because it allows older Members; those who have paid you more, due to recurring charges; to acquire access to more content progressively; based on their original paid registration time. It also gives you (as the site owner), the ability to launch multiple membership site portals, operating on autopilot, without any direct day-to-day involvement in a content release process. <strong>The <a href="http://www.s2member.com/kb/s2drip-shortcode/" target="_blank" rel="external"><code>[s2Drip]</code> shortcode</a> is the easiest way to drip content.</strong> The other methods (shown below) require some PHP scripting. In order to use PHP scripting inside your Posts/Pages, you\'ll need to install this handy plugin (<a href="http://s2member.com/r/ezphp/" target="_blank" rel="external">ezPHP</a>).</p>' . "\n";
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_during_api_advanced_dripping", get_defined_vars ());

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>TIP:</strong> In addition to this documentation, you may also want to have a look at the <a href="http://www.s2member.com/codex/" target="_blank" rel="external">s2Member Codex</a>.<br />' . "\n";
								echo '<strong>See Also:</strong> <a href="http://www.s2member.com/codex/stable/s2member/api_constants/package-summary/" target="_blank" rel="external">s2Member Codex → API Constants</a>, and <a href="http://www.s2member.com/codex/stable/s2member/api_functions/package-summary/" target="_blank" rel="external">s2Member Codex → API Functions</a>.</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>A very simple way to drip content; via the <a href="http://www.s2member.com/kb/s2drip-shortcode/" target="_blank" rel="external"><code>[s2Drip]</code></a> Shortcode:</strong></p>' . "\n";
								if(!c_ws_plugin__s2member_utils_conds::pro_is_installed()) echo '<p><em><strong class="ws-menu-page-hilite">NOTE: the use of <code>[s2Drip]</code> requires s2Member Pro.</strong></em></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/s2drip-example1.x-php")) . '</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>To drip content using <code>S2MEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS</code>:</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-paid-registration-days-dripping.x-php")) . '</p>' . "\n";

								echo '<p><em>There are more examples on this page, under the sub-section "s2Member PHP/API Constants". You\'ll see that s2Member provides you with access to several PHP/API Constants, which will assist you in dripping content. Some of the most relevant API Constants include: <code>S2MEMBER_CURRENT_USER_PAID_REGISTRATION_TIME</code>, <code>S2MEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS</code>, <code>S2MEMBER_CURRENT_USER_REGISTRATION_TIME</code>, <code>S2MEMBER_CURRENT_USER_REGISTRATION_DAYS</code>; and there are many others.</em></p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<h3>Very Advanced Content Dripping (some PHP required)</h3>' . "\n";
								echo '<p>If you plan on dripping content in VERY advanced ways, you can tap into s2Member\'s recorded history of all Paid Registration Times. (i.e., <code>' . esc_html ('<?php $time = s2member_paid_registration_time("level1"); ?>') . '</code>) will give you a timestamp at which a Member first paid for Level#1 access. If they\'ve never paid for Level#1 access, the function will return 0. s2Member keeps a recorded history of timestamps associated with each Level that a Member gains access to, throughout the lifetime of their account.</p>' . "\n";
								echo '<p><strong>Here is the function documentation for PHP/WordPress developers:</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/s2member-paid-registration-time.x-php")) . '</p>' . "\n";
								echo '<p><strong>Here are some actual examples that should give you some ideas:</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/s2member-paid-registration-time-examples.x-php")) . '</p>' . "\n";
								echo '</div>' . "\n";

								echo '</div>' . "\n";

								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_after_api_advanced_dripping", get_defined_vars ());
							}

						if (apply_filters("ws_plugin__s2member_during_scripting_page_during_left_sections_display_api_constants", true, get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_before_api_constants", get_defined_vars ());

								if (is_multisite () && c_ws_plugin__s2member_utils_conds::is_multisite_farm () && !is_main_site ())
									{
										echo '<div class="ws-menu-page-group" title="s2Get / s2Member API Constants">' . "\n";

										echo '<div class="ws-menu-page-section ws-plugin--s2member-api-constants-section">' . "\n";
										echo '<h3>Using s2Get w/ s2Member API Constants</h3>' . "\n";
										echo '<p>A Constant, is an identifier (a name) for a simple value. Below is a comprehensive list that includes all of the defined Constants available to you. We recommend using some of these Constants in the creation of your Login Welcome Page; which is described in the s2Member General Options Panel. These are NOT required, but you can get pretty creative with your Login Welcome Page if you know how to use the <code>[s2Get constant="" /]</code> Shortcode for WordPress.</p>' . "\n";
										echo '<p>For example, you might use <code>[s2Get constant="S2MEMBER_CURRENT_USER_ACCESS_LABEL" /]</code> to display the type of Membership a Customer has.</em></p>' . "\n";
										do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_during_api_constants_farm", get_defined_vars ());

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_VERSION</strong><br />This will always be a (string) with the current s2Member version. Available since s2Member 3.0. Dated versions began with s2Member v110604.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_LOGIN_COUNTER</strong><br />This will always be (int) <code>-1</code> or higher <em>(representing the number of times a User/Member has logged into your site)</em>. <code>-1</code> if no User is logged in. <code>0</code> if the current User has NEVER logged in.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_IS_LOGGED_IN</strong><br />This will always be (bool) true or false. True if a User/Member is currently logged in with an Access Level >= 0.</p>' . "\n";
										echo '<p><em>See: <code>S2MEMBER_CURRENT_USER_ACCESS_LEVEL</code> below for a full explanation.</em></p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_IS_LOGGED_IN_AS_MEMBER</strong><br />This will always be (bool) true or false. True if a Member is currently logged in with an Access Level >= 1.</p>' . "\n";
										echo '<p><em>See: <code>S2MEMBER_CURRENT_USER_ACCESS_LEVEL</code> below for a full explanation.</em></p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_ACCESS_LEVEL</strong><br />This will always be (int) <code>-1</code> thru <code>4</code> <em>(or, up to the total number Membership Levels you\'ve configured)</em>. <code>-1</code> if not logged in. <code>0</code> if logged in as a Free Subscriber.</p>' . "\n";
										echo '<p><strong>Membership Levels provide incremental access:</strong></p>' . "\n";
										echo '<p>* A Member with Level 4 access, will also be able to access Levels 0, 1, 2 &amp; 3.<br />* A Member with Level 3 access, will also be able to access Levels 0, 1 &amp; 2.<br />* A Member with Level 2 access, will also be able to access Levels 0 &amp; 1.<br />* A Member with Level 1 access, will also be able to access Level 0.<br />* A Subscriber with Level 0 access, will ONLY be able to access Level 0.<br />* A public Visitor will have NO access to protected content.</p>' . "\n";
										echo '<p><em>* WordPress Subscribers are at Membership Level 0. If you\'re allowing Open Registration, Subscribers will be at Level 0 (a Free Subscriber). WordPress Administrators, Editors, Authors, and Contributors have Level 4 access, with respect to s2Member. All of their other Roles/Capabilities are left untouched.</em></p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_ACCESS_LABEL</strong><br />This will always be a (string) containing the Membership Label associated with the current User\'s account. Empty if not logged in.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_SUBSCR_ID</strong><br />This will always be a (string) containing the current User\'s Paid Subscr. ID. If they\'ve NOT paid yet, this will be an empty string. Also empty if not logged in.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID</strong><br />This will always be a (string) containing the current User\'s Paid Subscr. ID. If they\'ve NOT paid yet, this will be their WordPress User ID#. Empty if not logged in.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_SUBSCR_GATEWAY</strong><br />This will always be a (string) containing the current User\'s Paid Subscr. Gateway. If they\'ve NOT paid yet, this will be empty. Empty if not logged in.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_CUSTOM</strong><br />This will always be a (string) containing the current User\'s Custom String; associated with their s2Member Profile. Empty if not logged in.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_REGISTRATION_TIME</strong><br />This will always be an (int); in the form of a Unix timestamp. 0 if not logged in. This holds the recorded time at which the User originally registered their Username for access to your site; for free or otherwise. <strong>* Note:</strong> this is NOT the same as <code>S2MEMBER_CURRENT_USER_PAID_REGISTRATION_TIME</code>, which could be used as an alternative, depending on your intended usage.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_PAID_REGISTRATION_TIME</strong><br />This will always be an (int); in the form of a Unix timestamp. However, this will be 0 if they\'re not logged in; or if they\'ve never paid you at all <em>(i.e., if they\'re still a Free Subscriber)</em>. This holds the recorded time at which the Member originally registered their Username (or upgraded for) any type of "paid" access to your site. This value is preserved for the lifetime of their account, even if they upgrade, and even if they\'re demoted at some point. Once this value is recorded, it never changes under any circumstance. <strong>* Note:</strong> this is NOT the same as <code>S2MEMBER_CURRENT_USER_REGISTRATION_TIME</code>, which could be used as an alternative, depending on your intended usage.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS</strong><br />This will always be an (int); in the form of a Unix timestamp. However, this will be 0 if they\'re not logged in; or if they\'ve never paid you at all <em>(i.e., if they\'re still a Free Subscriber)</em>. This is the number of days that have passed since the Member originally registered their Username (or upgraded for) any type of "paid" access to your site. The underlying timestamp behind this value is preserved for the lifetime of their account, even if they upgrade, and even if they\'re demoted at some point. Once the underlying timestamp behind this value is recorded, it never changes under any circumstance. <strong>* Note:</strong> this is NOT the same as <code>S2MEMBER_CURRENT_USER_REGISTRATION_DAYS</code>, which could be used as an alternative, depending on your intended usage.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_REGISTRATION_DAYS</strong><br />This will always be an (int). 0 if not logged in. This is the number of days that have passed since the User originally registered their Username for access to your site; for free or otherwise. <strong>* Note:</strong> this is NOT the same as <code>S2MEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS</code>, which could be used as an alternative, depending on your intended usage.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_DISPLAY_NAME</strong><br />This will always be a (string) containing the current User\'s Display Name. Empty if not logged in.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_FIRST_NAME</strong><br />This will always be a (string) containing the current User\'s First Name. Empty if not logged in.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_LAST_NAME</strong><br />This will always be a (string) containing the current User\'s Last Name. Empty if not logged in.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_LOGIN</strong><br />This will always be a (string) containing the current User\'s Username. Empty if not logged in.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_EMAIL</strong><br />This will always be a (string) containing the current User\'s Email Address. Empty if not logged in.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_IP</strong><br />This will always be a (string) containing the current User\'s IP Address, even when/if NOT logged-in. Taken from <code>$_SERVER["REMOTE_ADDR"]</code>. Empty if browsing anonymously.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_REGISTRATION_IP</strong><br />This is a (string) containing the IP Address the current User had at the time they registered. Taken from <code>$_SERVER["REMOTE_ADDR"]</code>. Empty if not logged in.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_ID</strong><br />This will always be an (int) containing the current User\'s ID# in WordPress. However, it will be 0 if not logged in.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										#echo '<p><strong>S2MEMBER_CURRENT_USER_FIELDS</strong><br />This will always be a JSON encoded array, in (string) format. An empty JSON encoded array, in (string) format, if not logged in. This JSON encoded array will contain the following fields: <code>id, ip, reg_ip, email, login, first_name, last_name, display_name, subscr_id, subscr_or_wp_id, subscr_gateway, custom</code>. If you\'ve configured additional Custom Fields, those Custom Fields will also be added to this array. You can do <code>print_r(json_decode(S2MEMBER_CURRENT_USER_FIELDS, true));</code> to get a full list for testing.</p>' . "\n";

										#echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED</strong><br />This will always be an (int) value >= 0. This indicates how many unique files they\'re allowed to download. 0 means no access.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED</strong><br />This will always be (bool) true or false. A value of true means their allowed downloads are >= 999999999, and false means it is not. This is useful if you are allowing unlimited (999999999) downloads on some Membership Levels. You can display `Unlimited` instead of a number.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY</strong><br />This will always be an (int) value >= 0. This indicates how many unique files they\'ve downloaded in the current period.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS</strong><br />This will always be an (int) value >= 0. This indicates how many total days make up the current period. 0 means no access.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL</strong><br />This is a Stand-Alone URL where a User can modify their Profile. In addition to this Stand-Alone version, s2Member also makes a Shortcode available which produces an Inline Profile Editing Form. Use <code>[s2Member-Profile /]</code> in any Post/Page, or even in a Text Widget if you like.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL</strong><br />This is the full URL to the Limit Exceeded Page (informational).</p>' . "\n";
										echo '<p><strong>S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID</strong><br />This is the Page ID that was used to generate the full URL.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_URL</strong><br />This is the full URL to the Membership Options Page (the signup page).</p>' . "\n";
										echo '<p><strong>S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_ID</strong><br />This is the Page ID that was used to generate the full URL.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_LOGIN_WELCOME_PAGE_URL</strong><br />This is the full URL to the Login Welcome Page (the User\'s account page). * This could also be the full URL to a Special Redirection URL (if you configured one). See <strong>s2Member → General Options → Login Welcome Page</strong>.</p>' . "\n";
										echo '<p><strong>S2MEMBER_LOGIN_WELCOME_PAGE_ID</strong><br />This is the Page ID that was used to generate the full URL. * In the case of a Special Redirection URL, this ID is not really applicable.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_LOGIN_PAGE_URL</strong><br />This is the full URL to the Membership Login Page (the WordPress login page).</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_LOGOUT_PAGE_URL</strong><br />This is the full URL to the Membership Logout Page (the WordPress logout page).</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_LEVELn_LABEL</strong><br />This is the (string) Label that you configured for a particular Membership Level #. Replace <code>n</code> with a numeric Membership Level #.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED</strong><br />This is the (int) allowed downloads for a particular Membership Level #. Replace <code>n</code> with a numeric Membership Level #.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS</strong><br />This is the (int) allowed download days for a particular Membership Level #. Replace <code>n</code> with a numeric Membership Level #.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS</strong><br />This is the (string) list of extensions to display inline.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_REG_EMAIL_FROM_NAME</strong><br />This is the Name that outgoing email messages are sent by.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_REG_EMAIL_FROM_EMAIL</strong><br />This is the Email Address that outgoing messages are sent by.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_NOTIFY_URL</strong><br />This is the URL on your system that receives PayPal IPN responses.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_RETURN_URL</strong><br />This is the URL on your system that receives PayPal return variables.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_BUSINESS</strong><br />This is the Email Address that identifies your PayPal Business.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_ENDPOINT</strong><br />PayPal Endpoint Domain <em>(changes when Sandbox Mode is enabled)</em>.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_API_ENDPOINT</strong><br />PayPal API Endpoint Domain <em>(changes when Sandbox Mode is enabled)</em>.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_API_USERNAME</strong><br />This is the API Username associated with your PayPal Business.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_API_PASSWORD</strong><br />This is the API Password associated with your PayPal Business.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_API_SIGNATURE</strong><br />This is the API Signature associated with your PayPal Business.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_PDT_IDENTITY_TOKEN</strong><br />This is the PDT Identity Token associated with your PayPal Business.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_during_list_of_api_constants_farm", get_defined_vars ());

										echo '<p><strong>S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0</strong> ... This auto-fills the <code>on0</code> value in PayPal Button Codes. If a Button Code is presented to a logged-in Member, this will auto-fill the value for the <code>on0</code> input variable, with the string: <code>"Referencing Customer ID"</code>. Otherwise, it will be set to a default value of: <code>"Originating Domain"</code>.</p>' . "\n";
										echo '<p><strong>S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0</strong> ... This auto-fills the <code>os0</code> value in PayPal Button Codes. If a Button Code is presented to a logged-in Member, this will auto-fill the value for the <code>os0</code> input variable, with the value of <code>S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID</code>. Otherwise, it will be set to a default value of <code>$_SERVER["HTTP_HOST"]</code> <em>(the originating domain name)</em>.</p>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1</strong> ... This auto-fills the <code>on1</code> value in PayPal Button Codes. This always contains the string: <code>"Customer IP Address"</code>.</p>' . "\n";
										echo '<p><strong>S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1</strong> ... This auto-fills the <code>os1</code> value in PayPal Button Codes. This always contains the value of <code>$_SERVER["REMOTE_ADDR"]</code> <em>(the Customer\'s IP address)</em>.</p>' . "\n";

										echo '<p><em>These four Constants above are special. They\'re used by the PayPal Button Generator for s2Member. This is how s2Member identifies an existing Member (and/or a Free Subscriber), who is already logged in when they click a PayPal Modification Button that was generated for you by s2Member. Instead of forcing a Member (and/or a Free Subscriber) to re-register for a new account, s2Member can identify their existing account, and update it, according to the modified terms in your Button Code. Specifically, these three Button Code parameters: <code>on0, os0, modify</code>, work together in harmony. If you\'re using the Shortcode Format for PayPal Buttons, you won\'t even see these, because they\'re added internally by the Shortcode processor. Anyway, they\'re just documented here for clarity; you probably won\'t use these directly; the Button Generator pops them in.</em></p>' . "\n";

										echo '</div>' . "\n";

										echo '</div>' . "\n";
									}
								else // Otherwise, we can display the standardized version of this information.
									{
										echo '<div class="ws-menu-page-group" title="s2Member PHP/API Constants">' . "\n";

										echo '<div class="ws-menu-page-section ws-plugin--s2member-api-constants-section">' . "\n";
										echo '<h3>You Have Access To PHP Constants (some PHP scripting required)</h3>' . "\n";
										echo '<p>A Constant, is an identifier <em>(i.e., a name)</em> for a simple value in PHP scripting. Below is a comprehensive list that includes all of the PHP defined Constants available to you. All of these Constants are also available through JavaScript as Global Variables. Example code has been provided in the documentation below. If you\'re a web developer, we suggest using some of these Constants in the creation of your Login Welcome Page; which is described in the s2Member General Options Panel. These are NOT required, but you can get pretty creative with the Login Welcome Page, if you know a little PHP.</p>' . "\n";
										echo '<p>If you don\'t know any PHP, you can use the <code>[s2Get constant="NAME_OF_CONSTANT" /]</code> Shortcode for WordPress. For example, you might use <code>[s2Get constant="S2MEMBER_CURRENT_USER_ACCESS_LABEL" /]</code> to display the type of Membership a Customer has. The <code>[s2Get constant="" /]</code> Shortcode will work for any of the API Constants documented below.</p>' . "\n";
										do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_during_api_constants", get_defined_vars ());

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p>Before you read any further, you should install this handy plugin: <a href="http://s2member.com/r/ezphp/" target="_blank" rel="external">ezPHP</a>.<br />' . "\n";
										echo 'You\'ll need to have this plugin installed to use PHP code in Posts/Pages.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>TIP:</strong> In addition to this documentation, you may also want to have a look at the <a href="http://www.s2member.com/codex/" target="_blank" rel="external">s2Member Codex</a>.<br />' . "\n";
										echo '<strong>See Also:</strong> <a href="http://www.s2member.com/codex/stable/s2member/api_constants/package-summary/" target="_blank" rel="external">s2Member Codex → API Constants</a>, and <a href="http://www.s2member.com/codex/stable/s2member/api_functions/package-summary/" target="_blank" rel="external">s2Member Codex → API Functions</a>.</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_VERSION</strong><br />This will always be a (string) with the current s2Member version. Available since s2Member 3.0. Dated versions began with s2Member v110604.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/version.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_LOGIN_COUNTER</strong><br />This will always be (int) <code>-1</code> or higher <em>(representing the number of times a User/Member has logged into your site)</em>. <code>-1</code> if no User is logged in. <code>0</code> if the current User has NEVER logged in.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-login-counter.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_IS_LOGGED_IN</strong><br />This will always be (bool) true or false. True if a User/Member is currently logged in with an Access Level >= 0.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-is-logged-in.x-php")) . '</p>' . "\n";
										echo '<p><em>See: <code>S2MEMBER_CURRENT_USER_ACCESS_LEVEL</code> below for a full explanation.</em></p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_IS_LOGGED_IN_AS_MEMBER</strong><br />This will always be (bool) true or false. True if a Member is currently logged in with an Access Level >= 1.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-is-logged-in-as-member.x-php")) . '</p>' . "\n";
										echo '<p><em>See: <code>S2MEMBER_CURRENT_USER_ACCESS_LEVEL</code> below for a full explanation.</em></p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_ACCESS_LEVEL</strong><br />This will always be (int) <code>-1</code> thru <code>4</code> <em>(or, up to the total number Membership Levels you\'ve configured)</em>. <code>-1</code> if not logged in. <code>0</code> if logged in as a Free Subscriber.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-access-level.x-php")) . '</p>' . "\n";
										echo '<p><strong>Membership Levels provide incremental access:</strong></p>' . "\n";
										echo '<p>* A Member with Level 4 access, will also be able to access Levels 0, 1, 2 &amp; 3.<br />* A Member with Level 3 access, will also be able to access Levels 0, 1 &amp; 2.<br />* A Member with Level 2 access, will also be able to access Levels 0 &amp; 1.<br />* A Member with Level 1 access, will also be able to access Level 0.<br />* A Subscriber with Level 0 access, will ONLY be able to access Level 0.<br />* A public Visitor will have NO access to protected content.</p>' . "\n";
										echo '<p><em>* WordPress Subscribers are at Membership Level 0. If you\'re allowing Open Registration, Subscribers will be at Level 0 (a Free Subscriber). WordPress Administrators, Editors, Authors, and Contributors have Level 4 access, with respect to s2Member. All of their other Roles/Capabilities are left untouched.</em></p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_ACCESS_LABEL</strong><br />This will always be a (string) containing the Membership Label associated with the current User\'s account. Empty if not logged in.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-access-label.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_SUBSCR_ID</strong><br />This will always be a (string) containing the current User\'s Paid Subscr. ID. If they\'ve NOT paid yet, this will be an empty string. Also empty if not logged in.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-subscr-id.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID</strong><br />This will always be a (string) containing the current User\'s Paid Subscr. ID. If they\'ve NOT paid yet, this will be their WordPress User ID#. Empty if not logged in.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-subscr-or-wp-id.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_SUBSCR_GATEWAY</strong><br />This will always be a (string) containing the current User\'s Paid Subscr. Gateway. If they\'ve NOT paid yet, this will be empty. Empty if not logged in.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-subscr-gateway.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_CUSTOM</strong><br />This will always be a (string) containing the current User\'s Custom String; associated with their s2Member Profile. Empty if not logged in.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-custom.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_REGISTRATION_TIME</strong><br />This will always be an (int); in the form of a Unix timestamp. 0 if not logged in. This holds the recorded time at which the User originally registered their Username for access to your site; for free or otherwise. This is useful if you want to drip content over an extended period of time, based on how long someone has been registered (period); regardless of whether they are/were paying you. <strong>* Note:</strong> this is NOT the same as <code>S2MEMBER_CURRENT_USER_PAID_REGISTRATION_TIME</code>, which could be used as an alternative, depending on your intended usage.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-registration-time.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_REGISTRATION_DAYS</strong><br />This will always be an (int). 0 if not logged in. This is the number of days that have passed since the User originally registered their Username for access to your site; for free or otherwise. This is useful if you want to drip content over an extended period of time, based on how long someone has been registered (period); regardless of whether they are/were paying you. <strong>* Note:</strong> this is NOT the same as <code>S2MEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS</code>, which could be used as an alternative, depending on your intended usage.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-registration-days.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_PAID_REGISTRATION_TIME</strong><br />This will always be an (int); in the form of a Unix timestamp. However, this will be 0 if they\'re not logged in; or if they\'ve never paid you at all <em>(i.e., if they\'re still a Free Subscriber)</em>. This holds the recorded time at which the Member originally registered their Username (or upgraded for) any type of "paid" access to your site. This value is preserved for the lifetime of their account, even if they upgrade, and even if they\'re demoted at some point. Once this value is recorded, it never changes under any circumstance. This is useful if you want to drip content over an extended period of time, based on how long someone has been a "paying" Member (period); regardless of their original or existing Membership Level. <strong>* Note:</strong> this is NOT the same as <code>S2MEMBER_CURRENT_USER_REGISTRATION_TIME</code>, which could be used as an alternative, depending on your intended usage.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-paid-registration-time.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS</strong><br />This will always be an (int); in the form of a Unix timestamp. However, this will be 0 if they\'re not logged in; or if they\'ve never paid you at all <em>(i.e., if they\'re still a Free Subscriber)</em>. This is the number of days that have passed since the Member originally registered their Username (or upgraded for) any type of "paid" access to your site. The underlying timestamp behind this value is preserved for the lifetime of their account, even if they upgrade, and even if they\'re demoted at some point. Once the underlying timestamp behind this value is recorded, it never changes under any circumstance. This is useful if you want to drip content over an extended period of time, based on how long someone has been a "paying" Member (period); regardless of their original or existing Membership Level. <strong>* Note:</strong> this is NOT the same as <code>S2MEMBER_CURRENT_USER_REGISTRATION_DAYS</code>, which could be used as an alternative, depending on your intended usage.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-paid-registration-days.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_DISPLAY_NAME</strong><br />This will always be a (string) containing the current User\'s Display Name. Empty if not logged in.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-display-name.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_FIRST_NAME</strong><br />This will always be a (string) containing the current User\'s First Name. Empty if not logged in.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-first-name.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_LAST_NAME</strong><br />This will always be a (string) containing the current User\'s Last Name. Empty if not logged in.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-last-name.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_LOGIN</strong><br />This will always be a (string) containing the current User\'s Username. Empty if not logged in.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-login.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_EMAIL</strong><br />This will always be a (string) containing the current User\'s Email Address. Empty if not logged in.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-email.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_IP</strong><br />This will always be a (string) containing the current User\'s IP Address, even when/if NOT logged in. Taken from <code>$_SERVER["REMOTE_ADDR"]</code>. Empty if browsing anonymously.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-ip.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_REGISTRATION_IP</strong><br />This will always be a (string) containing the current User\'s original IP Address during registration. Taken from <code>$_SERVER["REMOTE_ADDR"]</code>. Empty if not logged in.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-registration-ip.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_ID</strong><br />This will always be an (int) containing the current User\'s ID# in WordPress. However, it will be 0 if not logged in.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-id.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_FIELDS</strong><br />This will always be a JSON encoded array, in (string) format. An empty JSON encoded array, in (string) format, if not logged in. This JSON encoded array will contain the following fields: <code>id, ip, reg_ip, email, login, first_name, last_name, display_name, subscr_id, subscr_or_wp_id, subscr_gateway, custom</code>. If you\'ve configured additional Custom Fields, those Custom Fields will also be added to this array. You can do <code>print_r(json_decode(S2MEMBER_CURRENT_USER_FIELDS, true));</code> to get a full list for testing.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-fields.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED</strong><br />This will always be an (int) value >= 0. This indicates how many unique files they\'re allowed to download. 0 means no access.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-downloads-allowed.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED</strong><br />This will always be (bool) true or false. A value of true means their allowed downloads are >= 999999999, and false means it is not. This is useful if you are allowing unlimited (999999999) downloads on some Membership Levels. You can display `Unlimited` instead of a number.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-downloads-allowed-is-unlimited.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY</strong><br />This will always be an (int) value >= 0. This indicates how many unique files they\'ve downloaded in the current period.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-downloads-currently.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS</strong><br />This will always be an (int) value >= 0. This indicates how many total days make up the current period. 0 means no access.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-downloads-allowed-days.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL</strong><br />This is a Stand-Alone URL where a User can modify their Profile. In addition to this Stand-Alone version, s2Member also makes a Shortcode available which produces an Inline Profile Editing Form. Use <code>[s2Member-Profile /]</code> in any Post/Page, or even in a Text Widget if you like.</p>' . "\n";
										echo '<p><strong>Code Sample #1</strong> (standard link):</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-profile-modification-page-url-1.x-php")) . '</p>' . "\n";
										echo '<p><strong>Code Sample #2</strong> (open the link in a popup window):</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-profile-modification-page-url-2.x-php")) . '</p>' . "\n";
										echo '<p><strong>Code Sample #3</strong> (embed the form into a Post/Page using an IFRAME tag):</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-profile-modification-page-url-3.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL</strong><br />This is the full URL to the Limit Exceeded Page (informational).</p>' . "\n";
										echo '<p><strong>S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID</strong><br />This is the Page ID that was used to generate the full URL.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/file-download-limit-exceeded-page-url.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_URL</strong><br />This is the full URL to the Membership Options Page (the signup page).</p>' . "\n";
										echo '<p><strong>S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_ID</strong><br />This is the Page ID that was used to generate the full URL.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/membership-options-page-url.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_LOGIN_WELCOME_PAGE_URL</strong><br />This is the full URL to the Login Welcome Page (the User\'s account page). * This could also be the full URL to a Special Redirection URL (if you configured one). See <strong>s2Member → General Options → Login Welcome Page</strong>.</p>' . "\n";
										echo '<p><strong>S2MEMBER_LOGIN_WELCOME_PAGE_ID</strong><br />This is the Page ID that was used to generate the full URL. * In the case of a Special Redirection URL, this ID is not really applicable.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/login-welcome-page-url.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_LOGIN_PAGE_URL</strong><br />This is the full URL to the Membership Login Page (the WordPress login page).</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/login-page-url.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_LOGOUT_PAGE_URL</strong><br />This is the full URL to the Membership Logout Page (the WordPress logout page).</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/logout-page-url.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_LEVELn_LABEL</strong><br />This is the (string) Label that you created for a particular Membership Level #. Replace <code>n</code> with a numeric Membership Level #..</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/leveln-label.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED</strong><br />This is the (int) allowed downloads for a particular Membership Level #. Replace <code>n</code> with a numeric Membership Level #.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/leveln-file-downloads-allowed.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS</strong><br />This is the (int) allowed download days for a particular Membership Level #. Replace <code>n</code> with a numeric Membership Level #.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/leveln-file-downloads-allowed-days.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS</strong><br />This is the (string) list of extensions to display inline.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/file-download-inline-extensions.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_REG_EMAIL_FROM_NAME</strong><br />This is the Name that outgoing email messages are sent by.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/reg-email-from-name.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_REG_EMAIL_FROM_EMAIL</strong><br />This is the Email Address that outgoing messages are sent by.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/reg-email-from-email.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_NOTIFY_URL</strong><br />This is the URL on your system that receives PayPal IPN responses.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/paypal-notify-url.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_RETURN_URL</strong><br />This is the URL on your system that receives PayPal return variables.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/paypal-return-url.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_ENDPOINT</strong><br />This is the Endpoint Domain to the PayPal server <em>(changes when Sandbox Mode is enabled)</em>.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/paypal-endpoint.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_API_ENDPOINT</strong><br />This is the Endpoint Domain to the PayPal API server <em>(changes when Sandbox Mode is enabled)</em>.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/paypal-api-endpoint.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_BUSINESS</strong><br />This is the Email Address that identifies your PayPal Business.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/paypal-business.x-php")) . '</p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_API_USERNAME</strong><br />This is the API Username associated with your PayPal Business.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/paypal-api-username.x-php")) . '</p>' . "\n";
										echo '<p><em>* For security purposes, this is NOT included in the JS/API (JavaSript API).</em></p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_API_PASSWORD</strong><br />This is the API Password associated with your PayPal Business.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/paypal-api-password.x-php")) . '</p>' . "\n";
										echo '<p><em>* For security purposes, this is NOT included in the JS/API (JavaSript API).</em></p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_API_SIGNATURE</strong><br />This is the API Signature associated with your PayPal Business.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/paypal-api-signature.x-php")) . '</p>' . "\n";
										echo '<p><em>* For security purposes, this is NOT included in the JS/API (JavaSript API).</em></p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										echo '<p><strong>S2MEMBER_PAYPAL_PDT_IDENTITY_TOKEN</strong><br />This is the PDT Identity Token associated with your PayPal Business.</p>' . "\n";
										echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/paypal-pdt-identity-token.x-php")) . '</p>' . "\n";
										echo '<p><em>* For security purposes, this is NOT included in the JS/API (JavaSript API).</em></p>' . "\n";

										echo '<div class="ws-menu-page-hr"></div>' . "\n";

										do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_during_list_of_api_constants", get_defined_vars ());

										echo '<p><strong>S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0</strong> ... This auto-fills the <code>on0</code> value in PayPal Button Codes. If a Button Code is presented to a logged-in Member, this will auto-fill the value for the <code>on0</code> input variable, with the string: <code>"Referencing Customer ID"</code>. Otherwise, it will be set to a default value of: <code>"Originating Domain"</code>.</p>' . "\n";
										echo '<p><strong>S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0</strong> ... This auto-fills the <code>os0</code> value in PayPal Button Codes. If a Button Code is presented to a logged-in Member, this will auto-fill the value for the <code>os0</code> input variable, with the value of <code>S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID</code>. Otherwise, it will be set to a default value of <code>$_SERVER["HTTP_HOST"]</code> <em>(the originating domain name)</em>.</p>' . "\n";

										echo '<p><strong>S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1</strong> ... This auto-fills the <code>on1</code> value in PayPal Button Codes. This always contains the string: <code>"Customer IP Address"</code>.</p>' . "\n";
										echo '<p><strong>S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1</strong> ... This auto-fills the <code>os1</code> value in PayPal Button Codes. This always contains the value of <code>$_SERVER["REMOTE_ADDR"]</code> <em>(the Customer\'s IP address)</em>.</p>' . "\n";

										echo '<p><em>These four Constants are special. They are used by the PayPal Button Generator for s2Member. This is how s2Member identifies an existing Member (and/or a Free Subscriber), who is already logged in when they click a PayPal Modification Button that was generated for you by s2Member. Instead of forcing a Member (and/or a Free Subscriber) to re-register for a new account, s2Member can identify their existing account, and update it, according to the modified terms in your Button Code. Specifically, these three Button Code parameters: <code>on0, os0, modify</code>, work together in harmony. If you\'re using the Shortcode Format for PayPal Buttons, you won\'t even see these, because they\'re added internally by the Shortcode processor. Anyway, they\'re just documented here for clarity; you probably won\'t use these directly; the Button Generator pops them in.</em></p>' . "\n";

										echo '<p><em>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/current-user-value-for-pp-on0-os0-on1-os1.x-php")) . '</em></p>' . "\n";

										echo '</div>' . "\n";

										echo '</div>' . "\n";
									}

								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_after_api_constants", get_defined_vars ());
							}

						if (apply_filters("ws_plugin__s2member_during_scripting_page_during_left_sections_display_api_js_globals", true, get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_before_api_js_globals", get_defined_vars ());

								echo '<div class="ws-menu-page-group" title="s2Member JS/API Globals">' . "\n";

								echo '<div class="ws-menu-page-section ws-plugin--s2member-api-js-globals-section">' . "\n";
								echo '<h3>You Also Have Access To JS Globals (some JavaScript knowledge required)</h3>' . "\n";
								echo '<p>Unless noted otherwise, all of the PHP Constants, are also available through JavaScript, as Global Variables <em>(with the exact same names/types as their PHP counterparts)</em>. s2Member automatically loads it\'s compressed JavaScript API into your theme for WordPress. s2Member is very intelligent about the way it loads <em>(and maintains)</em> it\'s JavaScript API. You can rely on the JavaScript Globals, the same way you rely on PHP Constants. The only exceptions are related to security. Variables that include private server-side details, like Identity Tokens and other API service credentials, will be excluded automatically.</p>' . "\n";
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_during_api_js_globals", get_defined_vars ());
								echo '</div>' . "\n";

								echo '</div>' . "\n";

								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_after_api_js_globals", get_defined_vars ());
							}

						if (apply_filters("ws_plugin__s2member_during_scripting_page_during_left_sections_display_api_mop_vars", (!is_multisite () || !c_ws_plugin__s2member_utils_conds::is_multisite_farm () || is_main_site ()), get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_before_api_mop_vars", get_defined_vars ());

								echo '<div class="ws-menu-page-group" title="Membership Options Page / Variables">' . "\n";

								echo '<div class="ws-menu-page-section ws-plugin--s2member-api-mop-vars-section">' . "\n";
								echo '<h3>Membership Options Page Variables (some scripting required)</h3>' . "\n";
								echo '<p>At the core of s2Member, is it\'s ability to protect content <em>(i.e., Posts, Pages, Tags, Categories, URI word fragments, etc)</em>. Whenever a public User, or even an existing Member attempts to access an area of your site that is unavailable to them; either because they are not logged-in, not a paying Member at all; or maybe they are logged-in, but they don\'t have access to content you\'ve protected at a higher Membership Level; s2Member will always redirect these unauthenticated requests to your Membership Options Page.</p>' . "\n";
								echo '<p>So your Membership Options Page is a key element of your site. It serves as the focal point of your s2Member installation. Understanding this, you can see it becomes important for s2Member to provide information about what the User/Member was attempting to access <em>(before they were redirected to the Membership Options Page)</em>. This is where s2Member\'s MOP Vars come in <em>(i.e., Membership Options Page Variables)</em>. Whenever s2Member redirects a User/Member to your Membership Options Page, it will include these important MOP Variables in the query string of the URL. These Variables can be used to provide more informative messages; or even to provide a different set of Membership Options <em>(e.g., Payment Buttons)</em>, based on what a User/Member was attempting to access.</p>' . "\n";
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_during_api_mop_vars", get_defined_vars ());

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>TIP:</strong> If you\'re running s2Member Pro, there is a NEW simplified way to deal with MOP Vars using the <a href="http://www.s2member.com/kb/s2mop-shortcode/" target="_blank" rel="external"><code>[s2MOP]</code> Shortcode</a>.<br />' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>PHP Code Sample:</strong> This may give you some other ideas [<a href="#" onclick="jQuery(\'p#ws-plugin--s2member-api-mop-vars-code-samples\').toggle(); return false;" class="ws-dotted-link">click here</a>].</p>' . "\n";
								echo '<p id="ws-plugin--s2member-api-mop-vars-code-samples" style="display:none;">' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/api-mop-vars-e.x-php")) . '</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>s2Member MOP Vars (Explained) ...</strong></p>' . "\n";
								echo '<p>' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/api-mop-vars.x-php")) . '</p>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Backward compatibility (the old way):</strong> The structure of s2Member\'s MOP Vars changed again in Apr, 2014. However, s2Member still provides the same MOP Vars that it used in previous versions, for backward compatibility. These <a href="#" onclick="jQuery(\'p#ws-plugin--s2member-old-api-mop-vars-details\').toggle(); return false;" class="ws-dotted-link">old MOP Variables</a> were more difficult to use; they are now deprecated <em>(i.e., they WILL eventually be removed)</em>. Going foward, please go by the new documentation above.</p>' . "\n";
								echo '<p id="ws-plugin--s2member-old-api-mop-vars-details" style="display:none;">' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/api-mop-vars-o.x-php")) . '</p>' . "\n";
								echo '</div>' . "\n";

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>Backward compatibility (the really old way):</strong> The structure of s2Member\'s MOP Vars changed a bit in Nov, 2011. However, s2Member still provides the same MOP Vars that it used in previous versions, for backward compatibility. These <a href="#" onclick="jQuery(\'p#ws-plugin--s2member-really-old-api-mop-vars-details\').toggle(); return false;" class="ws-dotted-link">old MOP Variables</a> were more difficult to use; they are now deprecated <em>(i.e., they WILL eventually be removed)</em>. Going foward, please go by the new documentation above.</p>' . "\n";
								echo '<p id="ws-plugin--s2member-really-old-api-mop-vars-details" style="display:none;">' . c_ws_plugin__s2member_utils_strings::highlight_php (file_get_contents (dirname (__FILE__) . "/code-samples/api-mop-vars-ro.x-php")) . '</p>' . "\n";
								echo '</div>' . "\n";

								echo '</div>' . "\n";

								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_after_api_mop_vars", get_defined_vars ());
							}

						if (apply_filters("ws_plugin__s2member_during_scripting_page_during_left_sections_display_api_hooks", (!is_multisite () || !c_ws_plugin__s2member_utils_conds::is_multisite_farm () || is_main_site ()), get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_before_api_hooks", get_defined_vars ());

								echo '<div class="ws-menu-page-group" title="Hooks/Filters (For Developers)">' . "\n";

								echo '<div class="ws-menu-page-section ws-plugin--s2member-api-hooks-section">' . "\n";
								echo '<h3>WP Hooks For Theme/Plugin Developers (scripting required)</h3>' . "\n";
								echo '<p>In addition to its API Constants, s2Member also makes several Hooks/Filters available throughout its framework. This makes it possible to build onto <em>(or even modify)</em> s2Member in lots of different ways. If you need to add custom processing routines, modify the behavior of existing processing routines, or tinker with things otherwise; you should use API Hooks/Filters. API Hooks &amp; Filters, give you the ability to "hook into", and/or "filter" processing routines, with files/functions of your own; instead of editing the s2Member plugin files directly. If you don\'t use a Hook/Filter, and instead, you edit the plugin files for s2Member, you\'ll have to merge all of your changes every time a new version of s2Member is released. If you create custom processing routines, you could place those routines into a PHP file here: <code>/wp-content/mu-plugins/s2-hacks.php</code>. If you don\'t have an <code>/mu-plugins/</code> directory, please create one. These are <em>(mu)</em> <a href="http://s2member.com/r/mu-plugins/" target="_blank" rel="external">MUST USE plugins</a>, which are loaded into WordPress automatically; that\'s what you want!</p>' . "\n";
								echo '<p><strong>Attn Developers:</strong> There are simply too many Hooks/Filters spread throughout s2Member\'s framework <em>(over 1000 total)</em>. Rather than documenting each Hook/Filter, it is easier to browse through the files inside: <code>/s2member/includes/classes/</code>. Inspecting Hooks/Filters in this way, also leads you to a better understanding of how they work. One way to save time, is to run a search for <code>do_action</code> and/or <code>apply_filters</code>. If you\'re new to the concept of Hooks/Filters for WordPress/s2Member, we suggest <a href="http://www.s2member.com/codex/#src_doc_overview_description" target="_blank" rel="external">this article</a> as a primer. The <a href="http://www.s2member.com/codex/" target="_blank" rel="external">s2Member Codex</a> also contains information about all Hooks/Filters that come with s2Member.</p>' . "\n";
								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_during_api_hooks", get_defined_vars ());

								echo '<div class="ws-menu-page-hr"></div>' . "\n";

								echo '<p><strong>TIP:</strong> In addition to this documentation, you may also want to have a look at the <a href="http://www.s2member.com/codex/" target="_blank" rel="external">s2Member Codex</a>.<br />' . "\n";
								echo '<strong>See Also:</strong> <a href="http://www.s2member.com/codex/stable/s2member/api_constants/package-summary/" target="_blank" rel="external">s2Member Codex → API Constants</a>, and <a href="http://www.s2member.com/codex/stable/s2member/api_functions/package-summary/" target="_blank" rel="external">s2Member Codex → API Functions</a>.</p>' . "\n";
								echo '</div>' . "\n";

								echo '</div>' . "\n";

								do_action("ws_plugin__s2member_during_scripting_page_during_left_sections_after_api_hooks", get_defined_vars ());
							}

						do_action("ws_plugin__s2member_during_scripting_page_after_left_sections", get_defined_vars ());

						echo '</td>' . "\n";

						echo '<td class="ws-menu-page-table-r">' . "\n";
						c_ws_plugin__s2member_menu_pages_rs::display ();
						echo '</td>' . "\n";

						echo '</tr>' . "\n";
						echo '</tbody>' . "\n";
						echo '</table>' . "\n";

						echo '</div>' . "\n";
					}
			}
	}

new c_ws_plugin__s2member_menu_page_scripting ();
