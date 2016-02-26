<?php
/**
 * Menu page for the s2Member plugin (File Download Options page).
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

if(!class_exists("c_ws_plugin__s2member_menu_page_down_ops"))
{
	/**
	 * Menu page for the s2Member plugin (File Download Options page).
	 *
	 * @package s2Member\Menu_Pages
	 * @since 110531
	 */
	class c_ws_plugin__s2member_menu_page_down_ops
	{
		public function __construct()
		{
			echo '<div class="wrap ws-menu-page">'."\n";

			echo '<div class="ws-menu-page-toolbox">'."\n";
			c_ws_plugin__s2member_menu_pages_tb::display();
			echo '</div>'."\n";

			echo '<h2>Download Options</h2>'."\n";

			echo '<table class="ws-menu-page-table">'."\n";
			echo '<tbody class="ws-menu-page-table-tbody">'."\n";
			echo '<tr class="ws-menu-page-table-tr">'."\n";
			echo '<td class="ws-menu-page-table-l">'."\n";

			echo '<form method="post" name="ws_plugin__s2member_options_form" id="ws-plugin--s2member-options-form" action="'.esc_attr(remove_query_arg('ws_plugin__s2member_cf_options_reset')).'" autocomplete="off">'."\n";
			echo '<input type="hidden" name="ws_plugin__s2member_options_save" id="ws-plugin--s2member-options-save" value="'.esc_attr(wp_create_nonce("ws-plugin--s2member-options-save")).'" />'."\n";
			echo '<input type="hidden" name="ws_plugin__s2member_amazon_cf_files_distros_auto_config_status" id="ws-plugin--s2member-amazon-cf-files-distros-auto-config-status" value="'.esc_attr($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_distros_auto_config_status"]).'" />'."\n";
			echo '<input type="hidden" name="ws_plugin__s2member_configured" id="ws-plugin--s2member-configured" value="1" />'."\n";

			do_action("ws_plugin__s2member_during_down_ops_page_before_left_sections", get_defined_vars());

			if(apply_filters("ws_plugin__s2member_during_down_ops_page_during_left_sections_display_restrictions", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_before_restrictions", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Basic Download Restrictions">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-restrictions-section">'."\n";
				echo '<h3>File Download Restrictions (required, if providing access to protected files)</h3>'."\n";
				echo '<p>If your Membership offering allows access to restricted files, you\'ll want to configure these options.</p>'."\n";
				echo '<p class="info"><strong>NOTE:</strong> If you intend to offer File Downloads in one way or another, you must configure at least one of the options below. For security purposes, s2Member\'s File Download functionality is disabled unless &amp; until at least one of the options below have been configured; i.e., s2Member expects you to configure Basic Downloads for at least one Membership Level before any sort of download-related functionality will work. This includes functionality associated with the <code>[s2File /]</code> and <code>[s2Stream /]</code> Shortcodes also.</p>'."\n";
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_during_restrictions", get_defined_vars());

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<p><strong>Upload restricted files to this security-enabled directory:</strong><br /><code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'</code></p>'."\n";
				echo '<p>- Now, you can link to any protected file, using this special format:<br />&nbsp;&nbsp;<code>'.esc_html(home_url("/?s2member_file_download=example-file.zip")).'</code><br />&nbsp;&nbsp;<small><em><strong>s2member_file_download</strong> = file, relative to the /'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/ directory. In other words, just the file name.</em></small></p>'."\n";
				echo '<p>- Or, use: <code>[s2File download="example-file.zip" /]</code> <em>(easier Shortcode if you prefer)</em><br />&nbsp;&nbsp;<small><em><strong>Shortcode equivalent:</strong> <code>[s2File /]</code> produces the entire URL for you, easier.</em></small></p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<p>s2Member will allow access to these protected files, based on the configuration you specify below. Repeated downloads of the same exact file are NOT tabulated against the totals below. Once a file has been downloaded, future downloads of the same exact file, by the same exact Member will not be counted against them. In other words, if a Member downloads the same file three times, the system only counts that as one unique download. In addition, multiple variations of popular media formats are only counted once. This is because many site owners provide multiple download options to their Users/Members, for compatibility purposes. Files that have the same exact name, with one of these extensions, will only be counted ONE time: <code>'.esc_html(implode(",", $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["streaming_file_extns"])).'</code>.</p>'."\n";
				echo '<p>s2Member will automatically detect links, anywhere in your content, and/or anywhere in your theme files, that contain <code>s2member_file_download</code> or <code>s2member-files</code>. Whenever a logged-in Member clicks a link that contains <code>s2member_file_download</code> or <code>s2member-files</code>, the system will politely ask the user to confirm the download using a very intuitive JavaScript confirmation prompt, which contains specific details about your configured download limitations. This way your Members will be aware of how many files they\'ve downloaded in the current period; and they\'ll be able to make a conscious decision about whether to proceed with a specific download or not. If you want to suppress this JavaScript confirmation prompt, you can add this to the end of your links: <code>&amp;s2member_skip_confirmation</code>. Shortcode alternative: <code>[s2File skip_confirmation="yes" /]</code>.</p>'."\n";
				echo '<p><em>* The above only applies to Users who are logged in as Members. For all other visitors in the general public, the <code>?s2member_file_download</code> links will redirect them your Membership Options Page, so that new visitors can signup, in order to gain access, by becoming a Member. You may also want to have a look down below at s2Member\'s "Advanced Download Restrictions", which provides a greater degree of flexibility.</em></p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";

				for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
				{
					echo '<tr>'."\n";

					echo '<th style="padding-top:0;">'."\n";
					echo '<label for="ws-plugin--s2member-level'.$n.'-file-downloads-allowed">'."\n";
					echo ($n === $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]) ? 'File Downloads (Highest Level #'.$n.'):'."\n" : 'File Downloads (Level #'.$n.' Or Higher):'."\n";
					echo '</label>'."\n";
					echo '</th>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<td>'."\n";
					echo '<input type="text" maxlength="9" autocomplete="off" name="ws_plugin__s2member_level'.$n.'_file_downloads_allowed" id="ws-plugin--s2member-level'.$n.'-file-downloads-allowed" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level".$n."_file_downloads_allowed"]).'" style="width:200px;" /> every <input type="text" maxlength="3" autocomplete="off" name="ws_plugin__s2member_level'.$n.'_file_downloads_allowed_days" id="ws-plugin--s2member-level'.$n.'-file-downloads-allowed-days" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level".$n."_file_downloads_allowed_days"]).'" style="width:200px;" onkeyup="if(this.value > 365){ alert(\'(365 days is the maximum).\\nThis keeps the logs optimized.\'); this.value = 365; }" /> day(s).<br />'."\n";
					echo 'Only this many unique downloads will be permitted every X day(s), at '.(($n === $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]) ? 'highest Level #'.$n : 'Level #'.$n.' or higher').'.<br />'."\n";
					echo '<em>* To allow UNLIMITED downloads, use: <code>999999999</code> (i.e., <code>999999999</code> = unlimited).</em>'."\n";
					echo '</td>'."\n";

					echo '</tr>'."\n";

					echo ($n < $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]) ? '<tr><td><div class="ws-menu-page-hr" style="margin:10px 0 10px 0;"></div></td></tr>' : '';
				}
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_after_restrictions", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_down_ops_page_during_left_sections_display_limit_exceeded_page", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_before_limit_exceeded_page", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Download Limit Exceeded Page">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-limit-exceeded-page-section">'."\n";
				echo '<h3>Download Limit Exceeded Page (required, if providing access to protected files)</h3>'."\n";
				echo '<p>This Page will be shown when/if a Member reaches their download limit, based on your configuration of <strong>Basic Download Restrictions</strong> above. This Page should be created by you, in WordPress. This Page should provide an informative message to the Member, describing your file access restrictions. Just tell them a little bit about your policy on file downloads, and why they might have reached this Page.</p>'."\n";
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_during_limit_exceeded_page", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-file-download-limit-exceeded-page">'."\n";
				echo 'Download Limit Exceeded Page:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_file_download_limit_exceeded_page" id="ws-plugin--s2member-file-download-limit-exceeded-page">'."\n";
				echo '<option value="">&mdash; Select &mdash;</option>'."\n";
				foreach(($ws_plugin__s2member_temp_a = array_merge((array)get_pages())) as $ws_plugin__s2member_temp_o)
					echo '<option value="'.esc_attr($ws_plugin__s2member_temp_o->ID).'"'.(($ws_plugin__s2member_temp_o->ID == $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["file_download_limit_exceeded_page"]) ? ' selected="selected"' : '').'>'.esc_html($ws_plugin__s2member_temp_o->post_title).'</option>'."\n";
				echo '</select><br />'."\n";
				echo 'We recommend the following title: <code>Download Limit Exceeded</code>.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_after_limit_exceeded_page", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_down_ops_page_during_left_sections_display_advanced_restrictions", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_before_advanced_restrictions", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Advanced Download Restrictions">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-restrictions-section">'."\n";
				echo '<h3>Advanced Download Restrictions (optional, for greater flexibility)</h3>'."\n";
				echo '<p>By default, s2Member uses your Basic Download Restrictions, as configured above. However, you can force s2Member to allow File Downloads, using an extra query string parameter: <code>&amp;s2member_file_download_key=[Key]</code>. A File Download `Key` is passed through this parameter; it tells s2Member to allow the download of this particular file, regardless of Membership Level; and WITHOUT checking any Basic Restrictions, that you may or may not have configured above. The creation of a File Download `Key`, requires a small PHP code snippet. In order to use PHP scripting inside your Posts/Pages, you\'ll need to install this handy plugin (<a href="http://s2member.com/r/ezphp/" target="_blank" rel="external">ezPHP</a>). There is also a Shortcode equivalent, which does NOT require PHP at all, as seen below.</p>'."\n";
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_during_advanced_restrictions", get_defined_vars());

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<p>'.esc_html(home_url("/?s2member_file_download=example-file.zip")).'<code>&amp;s2member_file_download_key=&lt;?php echo s2member_file_download_key("example-file.zip"); ?&gt;</code><br />&nbsp;&nbsp;<small><em><strong>s2member_file_download_key</strong> = &lt;?php echo s2member_file_download_key("file, relative to the /'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/ directory"); ?&gt;</em></small></p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<p>'.esc_html(home_url("/?s2member_file_download=example-file.zip")).'<code>&amp;s2member_file_download_key=[s2Key file_download="example-file.zip" /]</code><br />&nbsp;&nbsp;<small><em><strong>Shortcode equivalent:</strong> <code>[s2Key file_download="example-file.zip" /]</code></em></small></p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<p><code>[s2File download="example-file.zip" download_key="true" /]</code> <em>(Key is auto-generated in this case)</em><br />&nbsp;&nbsp;<small><em><strong>Shortcode equivalent:</strong> <code>[s2File /]</code> produces the entire URL, no need to generate a Key yourself.</em></small></p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<p>The function <a href="http://www.s2member.com/codex/stable/s2member/api_functions/package-functions/#src_doc_s2member_file_download_key()" target="_blank" rel="external">s2member_file_download_key()</a>, is part of the s2Member API. It produces a time-sensitive File Download Key that is unique to each and every visitor. Each Key it produces <em>(at the time it is produced)</em>, will be valid for the current day, and only for a specific IP address and User-Agent string; as detected by s2Member. This makes it possible for you to create links on your site, which provide access to protected file downloads; and without having to worry about one visitor sharing their link with another. So let\'s take a quick look at what <code>s2member_file_download_key()</code> actually produces.</p>'."\n";
				echo '<p><code>s2member_file_download_key("example-file.zip")</code> = a site-specific hash of: <code>date("Y-m-d").$_SERVER["REMOTE_ADDR"].$_SERVER["HTTP_USER_AGENT"].$file</code></p>'."\n";
				echo '<p>When <code>s2member_file_download_key = <em>a valid Key</em></code>, it works independently from Member Level Access. That is, a visitor does NOT have to be logged in to receive access; they just need a valid Key. Using this advanced technique, you could extend s2Member\'s file protection routines, or even combine them with Specific Post/Page Access, and more. The possibilities are limitless really.</p>'."\n";

				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_after_advanced_restrictions", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_down_ops_page_during_left_sections_display_inline_extensions", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_before_inline_extensions", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Inline File Extensions">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-inline-extensions-section">'."\n";
				echo '<h3>Inline File Extensions (optional, for content-disposition)</h3>'."\n";
				echo '<p>There are two ways to serve files. Inline, or as an Attachment. By default, s2Member will serve all of your protected files, as downloadable attachments. Meaning, visitors will be given a file download prompt. Otherwise known as <code>Content-Disposition: attachment</code>. In some cases though, you may wish to serve files inline. For example, PDF files and images should usually be served inline. When you serve a file inline, it is displayed in your browser immediately, rather than your browser prompting you to download the file as an attachment.</p>'."\n";
				echo '<p>Using the field below, you can list all of the extensions that you want s2Member to serve inline (ex: <code>htm,html,pdf,jpg,jpeg,jpe,gif,png,mp3,mp4,flv,ogg,webm</code>). Please understand, some files just cannot be displayed inline. For instance, there is no way to display an <code>exe</code> file inline. So only specify extensions that can, and should be displayed inline by a web browser. Alternatively, if you would rather handle this on a case-by-case basis, you can simply add the following to the end of your download links: <code>&amp;s2member_file_inline=yes</code>. Shortcode alternative: <code>[s2File download="example-file.zip" inline="yes" /]</code>.</p>'."\n";
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_during_inline_extensions", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-file-download-inline-extensions">'."\n";
				echo 'Default Inline File Extensions (comma-delimited):'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_file_download_inline_extensions" id="ws-plugin--s2member-file-download-inline-extensions" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["file_download_inline_extensions"]).'" /><br />'."\n";
				echo 'Inline extensions, comma-delimited. Ex: <code>htm,html,pdf,jpg,jpeg,jpe,gif,png,mp3,mp4,flv,ogg,webm</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_after_inline_extensions", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_down_ops_page_during_left_sections_display_remote_authorization", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_before_remote_authorization", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Remote Auth / Podcasting">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-remote-authorization-section">'."\n";
				echo '<h3>Remote Header Authorization (optional)</h3>'."\n";
				echo '<p>This can be enabled on a case-by-case basis. Just add this to the end of your download links: <code>&amp;s2member_file_remote=yes</code></p>'."\n";
				echo '<p>Shortcode alternative: <code>[s2File download="example-file.zip" remote="yes" /]</code></p>'."\n";
				echo '<p>Remote Header Authorization allows access to file downloads through an entirely different approach. Instead of asking the Member to log into your site through a browser, a Member will be prompted automatically, to log in through HTTP Header Authorization prompts; which is the same technique used in more traditional security systems via .htaccess files. In other words, Remote Header Authorization makes it possible for your Members to access files through remote applications that may NOT use a browser. This is often the case when a Member needs to access protected files through a software client like iTunes; typical with podcasts. See <a href="http://www.s2member.com/videos/71F49478D6983A9C/" target="_blank" rel="external">tutorial video here</a> for details about how to setup a Podcast for iTunes.</p>'."\n";
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_during_remote_authorization", get_defined_vars());
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_after_remote_authorization", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_down_ops_page_during_left_sections_display_amazon_s3", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_before_amazon_s3", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Amazon S3/CDN Storage Option"'.((!empty(c_ws_plugin__s2member_menu_pages::$pre_display_errors["cf_files_auto_configure_distros"])) ? ' default-state="open"' : '').'>'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-amazon-s3-section">'."\n";
				echo '<h3>Amazon S3/CDN Storage &amp; Delivery (optional)</h3>'."\n";
				echo '<iframe width="560" height="315" src="https://www.youtube.com/embed/Gr87ZBJQE0I" frameborder="0" allowscriptaccess="always" allowfullscreen="true" style="float:right; margin:0 0 20px 20px; width:300px; height:200px;"></iframe>'."\n";
				echo '<a href="http://s2member.com/r/amazon-s3/" target="_blank"><img src="'.esc_attr($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"]).'/images/amazon-logo.png" class="ws-menu-page-right" style="width:250px; height:100px; border:0;" alt="." /></a>'."\n";
				echo '<p>Please note, all of this is optional. s2Member can be configured here to ONLY use Amazon S3 <em>(i.e., without Amazon CloudFront)</em>. Or, s2Member can be configured to use BOTH Amazon S3 and Amazon CloudFront together. If you want to use Amazon S3 Storage, but you don\'t care about Amazon CloudFront, feel free to leave the entire Amazon CloudFront section empty. The configuration options in the Amazon CloudFront section are ONLY required if you are planning to use both Amazon S3 and Amazon CloudFront together.</p>'."\n";
				echo '<p>Amazon Simple Storage Service (<a href="http://s2member.com/r/amazon-s3/" target="_blank" rel="external">Amazon S3</a>). Amazon S3 is storage for the Internet. It is designed to make web-scale computing easier for developers. Amazon S3 provides a simple web services interface that can be used to store and retrieve any amount of data, at any time, from anywhere on the web. It gives developers access to the same highly scalable, reliable, secure, fast, inexpensive infrastructure that Amazon uses to run its own global network of web sites. s2Member has been integrated with Amazon S3, so that <em>(if you wish)</em>, instead of using the <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/</code> directory, you can store all of your protected files inside an Amazon S3 Bucket.</p>'."\n";
				echo '<p>If you configure the options below, s2Member will assume all protected files are inside your Amazon S3 Bucket; and the <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/</code> directory is no longer used at all. That being said, all other aspects of s2Member\'s File Download protection remain the same. The only thing that changes, is the location of your protected files. In other words, Basic Download Restrictions, Download Keys, Inline Extensions, Custom Capability and/or Membership Level Files will all continue to work just as before. The only difference is that s2Member will use your Amazon S3 Bucket as a CDN <em>(i.e., Content Delivery Network)</em> instead of the local <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/</code> directory.</p>'."\n";
				echo '<p>s2Member assumes that you\'re creating a new Amazon S3 Bucket, specifically for this installation; and that your Bucket is NOT available publicly. In other words, if you type this URL into your browser <em>(i.e., <code>http://your-bucket-name.s3.amazonaws.com/</code>)</em>, you should get an error that says: <code>Access Denied</code>. That\'s good, that\'s exactly what you want. You can create your Amazon S3 Bucket using the <a href="http://s2member.com/r/amazon-s3-console/" target="_blank" rel="external">Amazon interface</a>. Or, some people prefer to use this popular Firefox extension (<a href="http://s2member.com/r/s3-fox-organizer/" target="_blank" rel="external">S3 Fox Organizer</a>).</p>'."\n";
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_during_amazon_s3", get_defined_vars());

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<p><em><strong>Dev Note w/Technical Details:</strong> s2Member uses "Digitally Signed URLs", authenticated by the Amazon S3 API. Documented for developers <a href="http://s2member.com/r/amazon-s3-digital-signing-process/" target="_blank" rel="external">here</a>. To put it simply, s2Member will generate Amazon S3 URLs (internally); which allow Customers temporary access to specific files inside your S3 Bucket. s2Member\'s Digitally Signed URLs leading to Amazon S3, give a Customer 24 hours to connect to the file inside your S3 Bucket. This connection period of 24 hours is largely irrelevant when used in combination with s2Member, because access is renewed for another 24 hours each time you make a file available to a User/Member, and they are authenticated by your configuration of s2Member. This connection period of 24 hours is just a secondary line of defense to further prevent the possibility of link sharing. If you need to change this connection timeout of <code>24 hours</code> for some reason (not likely), you can use this WordPress Filter: <code>ws_plugin__s2member_amazon_s3_file_expires_time</code>.</em></p>'."\n";
				echo '<p><em><strong>Linking To Protected Files:</strong> Nothing changes. s2Member\'s integration with Amazon S3 serves protected files through the same links that all s2Member site owners use. For example, you might use: <code>'.esc_html(home_url("/?s2member_file_download=example-file.zip")).'</code>, where <strong>s2member_file_download</strong> = the file, relative to the root of your Amazon S3 Bucket. In other words, just the file name in most cases. s2Member will redirect Users/Members to a digitally signed Amazon S3 URL, which allows them access to a particular file via Amazon S3. For further details, please review this section of your Dashboard: <strong>s2Member → Download Options → Basic Download Restrictions</strong>. Also see: <strong>s2Member → Download Options → Advanced Mod-Rewrite Linkage</strong>.</em></p>'."\n";
				echo '<p><em><strong>Content Type, Disposition &amp; Inline Files:</strong> The query string parameter <code>&amp;s2member_file_inline=yes</code> DOES work for files served directly through Amazon S3. s2Member DOES have control over the <code>Content-Type</code> and <code>Content-Disposition</code> headers for files being served through Amazon S3. However, Amazon CloudFront servers do NOT automatically determine the MIME type for the objects they serve. If you integrate both Amazon S3 and CloudFront, s2Member will NOT have control over headers. Therefore, when you upload a file to your Amazon S3 Bucket, you should set its Content-Type header. Again, with the Amazon S3/CloudFront combination, you MUST configure headers yourself (such as <code>Content-Type: video/webm</code>, or <code>Content-Disposition: inline|attachment</code>) that you want Amazon CloudFront to send for a particular file. It\'s quite easy. You do this by setting <strong>Properties → Metadata (i.e., headers)</strong> on a per-file basis, from inside your Amazon S3 Management Console. In short, when you upload a file to your Amazon S3 Bucket, if you want that file to be served a certain way, be sure to configure its <strong>Properties → Metadata</strong> accordingly.</em></p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th style="padding-top:0;">'."\n";
				echo '<label for="ws-plugin--s2member-amazon-s3-files-bucket-region">'."\n";
				echo 'Amazon S3 File Bucket Region (please choose):'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_amazon_s3_files_bucket_region" id="ws-plugin--s2member-amazon-s3-files-bucket-region">'."\n";
				echo '<option value="us-east-1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_s3_files_bucket_region"] === 'us-east-1') ? ' selected="selected"' : '').'>us-east-1</option>'."\n";
				echo '<option value="us-west-1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_s3_files_bucket_region"] === 'us-west-1') ? ' selected="selected"' : '').'>us-west-1</option>'."\n";
				echo '<option value="us-west-2"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_s3_files_bucket_region"] === 'us-west-2') ? ' selected="selected"' : '').'>us-west-2</option>'."\n";
				echo '<option value="eu-west-1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_s3_files_bucket_region"] === 'eu-west-1') ? ' selected="selected"' : '').'>eu-west-1</option>'."\n";
				echo '<option value="eu-central-1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_s3_files_bucket_region"] === 'eu-central-1') ? ' selected="selected"' : '').'>eu-central-1</option>'."\n";
				echo '<option value="ap-southeast-1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_s3_files_bucket_region"] === 'ap-southeast-1') ? ' selected="selected"' : '').'>ap-southeast-1</option>'."\n";
				echo '<option value="ap-southeast-2"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_s3_files_bucket_region"] === 'ap-southeast-2') ? ' selected="selected"' : '').'>ap-southeast-2</option>'."\n";
				echo '<option value="ap-northeast-1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_s3_files_bucket_region"] === 'ap-northeast-1') ? ' selected="selected"' : '').'>ap-northeast-1</option>'."\n";
				echo '<option value="sa-east-1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_s3_files_bucket_region"] === 'sa-east-1') ? ' selected="selected"' : '').'>sa-east-1</option>'."\n";
				echo '<option value="us-gov-west-1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_s3_files_bucket_region"] === 'us-gov-west-1') ? ' selected="selected"' : '').'>us-gov-west-1</option>'."\n";
				echo '</select><br />'."\n";
				echo 'See: <a href="http://s2member.com/r/aws-s3-region-codes/" target="_blank">http://s2member.com/r/aws-s3-region-codes/</a>'."\n";
				echo '</td>'."\n";
				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th style="padding-top:0;">'."\n";
				echo '<label for="ws-plugin--s2member-amazon-s3-files-bucket">'."\n";
				echo 'Amazon S3 File Bucket Name (where protected files are):'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_amazon_s3_files_bucket" id="ws-plugin--s2member-amazon-s3-files-bucket" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_s3_files_bucket"]).'" /><br />'."\n";
				echo 'Your Amazon S3 Bucket will be used, instead of the <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/</code> directory.<br />'."\n";
				echo 'Please type the name of your Bucket. Ex: <code>mys3bucket</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-amazon-s3-files-access-key">'."\n";
				echo 'Amazon Access Key (Access Key ID):'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="password" autocomplete="off" name="ws_plugin__s2member_amazon_s3_files_access_key" id="ws-plugin--s2member-amazon-s3-files-access-key" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_s3_files_access_key"]).'" /><br />'."\n";
				echo 'See: <strong>Amazon Web Services Account → Security Credentials → Access Keys</strong><br />'."\n";
				echo '<em><small>Amazon suggests creating a new IAM user. Use the Keys for that IAM user here.</small></em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-amazon-s3-files-secret-key">'."\n";
				echo 'Amazon Secret Key (Secret Access Key):'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="password" autocomplete="off" name="ws_plugin__s2member_amazon_s3_files_secret_key" id="ws-plugin--s2member-amazon-s3-files-secret-key" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_s3_files_secret_key"]).'" /><br />'."\n";
				echo 'See: <strong>Amazon Web Services Account → Security Credentials → Access Keys</strong><br />'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_after_amazon_s3", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_down_ops_page_during_left_sections_display_amazon_cf", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_before_amazon_cf", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Amazon S3/CloudFront CDN Storage Option"'.((!empty(c_ws_plugin__s2member_menu_pages::$pre_display_errors["cf_files_auto_configure_distros"])) ? ' default-state="open"' : '').'>'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-amazon-cf-section">'."\n";
				echo '<h3>Amazon S3/CloudFront CDN Storage &amp; Delivery (optional)</h3>'."\n";
				echo '<iframe width="560" height="315" src="https://www.youtube.com/embed/Gr87ZBJQE0I" frameborder="0" allowscriptaccess="always" allowfullscreen="true" style="float:right; margin:0 0 20px 20px; width:300px; height:200px;"></iframe>'."\n";
				echo '<a href="http://s2member.com/r/amazon-cloudfront/" target="_blank"><img src="'.esc_attr($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"]).'/images/amazon-logo.png" class="ws-menu-page-right" style="width:250px; height:100px; border:0;" alt="." /></a>'."\n";
				echo '<p>Please note, all of this is optional. s2Member can be configured to ONLY use Amazon S3 <em>(i.e., without Amazon CloudFront)</em>. Or, s2Member can be configured to use BOTH Amazon S3 and Amazon CloudFront together. If you don\'t want to use Amazon CloudFront, please leave this entire section empty. The configuration options in this section are ONLY required if you are planning to use both Amazon S3 and Amazon CloudFront together.</p>'."\n";
				echo '<p>Amazon Simple Storage Service (<a href="http://s2member.com/r/amazon-s3/" target="_blank" rel="external">Amazon S3</a>) combined with <a href="http://s2member.com/r/amazon-cloudfront/" target="_blank" rel="external">Amazon CloudFront</a>. Amazon CloudFront is a web service for content delivery. It integrates with other Amazon Web Services <em>(i.e., Amazon S3 Storage)</em> to give developers and businesses an easy way to distribute content to end users with low latency, and with high data transfer speeds. Amazon CloudFront delivers your static and streaming content using a global network of edge locations. Requests for your Amazon S3 Bucket Objects <em>(i.e., your protected files)</em> are automatically routed to the nearest edge location, so content is delivered with the best possible performance. s2Member has been integrated with both Amazon S3 and with Amazon CloudFront. So <em>(if you wish)</em>, instead of using the <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/</code> directory, you can store all of your protected files inside an Amazon S3 Bucket and serve them via Amazon CloudFront. But again, please understand, the configuration options in this section are ONLY required if you\'re going to use both Amazon S3 &amp; CloudFront together.</p>'."\n";
				echo '<p><strong>One of the great things about Amazon CloudFront</strong>, is its ability to <strong>stream/seek media files</strong> in the truest sense of the word. For sites delivering protected <em>FLV/MP4/OGG/WEBM</em> and other streaming audio/video file types over the <em>RTMP</em> protocol, Amazon CloudFront is our recommendation. Once you\'ve successfully configured s2Member to use both Amazon S3 and Amazon CloudFront together, please review the section below regarding <code>JW Player &amp; RTMP Protocol Examples</code>. s2Member will automatically serve your protected files over the <em>RTMP</em> protocol using an Amazon CloudFront Streaming Distribution.</p>'."\n";
				echo '<p>If you configure the options below, s2Member will assume all protected files are inside your Amazon S3 Bucket; and the <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/</code> directory is no longer used at all. That being said, all other aspects of s2Member\'s File Download protection remain the same. The only thing that changes, is the location of your protected files. In other words, Basic Download Restrictions, Download Keys, Custom Capability and/or Membership Level Files will all continue to work just as before. The only difference is that s2Member will use your Amazon S3 Bucket, automatically connecting it to both of the Amazon CloudFront Distributions, which s2Member auto-configures for you <em>(see below)</em>. In this way, s2Member uses Amazon CloudFront as a CDN <em>(i.e., Content Delivery Network)</em> for your protected files.</p>'."\n";
				echo '<p>s2Member assumes that you\'re creating a new Amazon S3 Bucket, specifically for this installation; and that your Bucket is NOT available publicly. In other words, if you type this URL into your browser <em>(i.e., <code>http://your-bucket-name.s3.amazonaws.com/</code>)</em>, you should get an error that says: <code>Access Denied</code>. That\'s good, that\'s exactly what you want. You can create your Amazon S3 Bucket using the <a href="http://s2member.com/r/amazon-s3-console/" target="_blank" rel="external">Amazon interface</a>. Or, some people prefer to use this popular Firefox extension (<a href="http://s2member.com/r/s3-fox-organizer/" target="_blank" rel="external">S3 Fox Organizer</a>). You will also need to enable CloudFront inside your Web Services account at Amazon. Don\'t worry about creating or configuring any CloudFront Distributions, s2Member will auto-create and auto-configure those for you, allowing you to serve protected files.</p>'."\n";
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_during_amazon_cf", get_defined_vars());

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<p><em><strong>Dev Note w/Technical Details:</strong> s2Member\'s auto-configuration routines for Amazon CloudFront (below), are designed to create &amp; configure various components on your Amazon Web Services account, which are all requirements for you to <a href="http://s2member.com/r/amazon-cloudfront-digitally-signed-urls/" target="_blank" rel="external">serve protected files through the Amazon S3/CloudFront combination</a>. These components include: an Origin Access Identity, read permissions for the Origin Access Identity, and two private content Distributions. One private content Distribution for file downloads, and another private content Distribution for streaming media files; both connected to and sourced by your Amazon S3 Bucket. In addition, s2Member will automatically configure an ACL &amp; Policy (i.e., permissions) on your Amazon S3 Bucket to make sure your protected object/files are NOT available to the public.</em></p>'."\n";
				echo '<p><em><strong>Dev Note w/Technical Details:</strong> s2Member uses "Digitally Signed URLs", authenticated by the Amazon CloudFront API. Documented for developers <a href="http://s2member.com/r/amazon-cloudfront-protected-files-for-developers/" target="_blank" rel="external">here</a>. To put it simply, s2Member will generate Amazon CloudFront URLs (internally); which allow Customers temporary access to specific files inside your S3 Bucket—via CloudFront Distributions. s2Member\'s Digitally Signed URLs leading to Amazon S3/CloudFront, give a Customer 24 hours to connect to the file inside your S3 Bucket. This connection period of 24 hours is largely irrelevant when used in combination with s2Member, because access is renewed for another 24 hours each time you make a file available to a User/Member, and they are authenticated by your configuration of s2Member. This connection period of 24 hours is just a secondary line of defense to further prevent the possibility of link sharing. If you need to change this connection timeout of <code>24 hours</code> for some reason (not likely), you can use this WordPress Filter: <code>ws_plugin__s2member_amazon_cf_file_expires_time</code>.</em></p>'."\n";
				echo '<p><em><strong>Linking To Protected Files:</strong> RTMP streams are special, but nothing else changes. s2Member\'s integration with Amazon S3/CloudFront serves protected files through the same links that all s2Member site owners use. For example, you might use: <code>'.esc_html(home_url("/?s2member_file_download=example-file.zip")).'</code>, where <strong>s2member_file_download</strong> = the file, relative to the root of your Amazon S3 Bucket. In other words, just the file name in most cases. s2Member will redirect Users/Members to a digitally signed Amazon CloudFront URL, which allows them access to a particular file via Amazon CloudFront. For further details, please review this section of your Dashboard: <strong>s2Member → Download Options → Basic Download Restrictions</strong>. Also see: <strong>s2Member → Download Options → Advanced Mod-Rewrite Linkage</strong>. If you\'re streaming audio/video files over the RTMP protocol, please review the section below: <code>JW Player &amp; RTMP Protocol Examples</code>.</em></p>'."\n";
				echo '<p><em><strong>Content Type, Disposition &amp; Inline Files:</strong> An IMPORTANT issue. The query string parameter <code>&amp;s2member_file_inline=yes</code> does NOTHING for files served via Amazon CloudFront. s2Member has NO control over the <code>Content-Type</code> and/or <code>Content-Disposition</code> headers for a file being served through Amazon CloudFront, and CloudFront servers do NOT automatically determine the MIME type for the objects they serve. Therefore, when you upload a file to your Amazon S3 Bucket, you should set its Content-Type header. That is, you MUST configure headers yourself (such as <code>Content-Type: video/webm</code>, or <code>Content-Disposition: inline|attachment</code>) that you want Amazon CloudFront to send for a particular file. It\'s quite easy. You do this by setting <strong>Properties → Metadata (i.e., headers)</strong> on a per-file basis, from inside your Amazon S3 Management Console. In short, when you upload a file to your Amazon S3 Bucket, if you want that file to be served a certain way, be sure to configure its <strong>Properties → Metadata</strong> accordingly.</em></p>'."\n";
				echo (stripos(PHP_OS, "win") === 0 && c_ws_plugin__s2member_utils_conds::is_localhost()) ? '<p><em><strong>Localhost Developers:</strong> s2Member\'s Amazon CloudFront integration requires the <a href="http://php.net/manual/en/function.openssl-sign.php" target="_blank" rel="external">openssl_sign()</a> function in PHP so it can digitially sign CloudFront URLs. This function is sometimes problematic on localhost servers such as WAMP &amp; EasyPHP. We recommend installing <a href="http://www.slproweb.com/products/Win32OpenSSL.html" target="_blank" rel="external">this lightweight alternative for Windows</a> while you\'re developing. s2Member will automatically find it here: <code>C:\OpenSSL-Win[32/64]\bin\openssl.exe</code>.'.((file_exists("c:\openssl-win32\bin\openssl.exe") || file_exists("c:\openssl-win64\bin\openssl.exe")) ? ' <strong class="ws-menu-page-hilite">( s2Member has detected that OpenSSL-Win[32/64] IS installed in the correct location, thank you! )</strong>' : ' <strong class="ws-menu-page-hilite">(s2Member has detected that OpenSSL-Win[32/64] is NOT currently available)</strong>').'</em></p>'."\n" : '';

				if($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_distros_auto_config_status"] === "configured")
					echo '<p><em class="ws-menu-page-hilite"><strong>Your Amazon CloudFront Distributions are: ( ALREADY configured! )</strong></em>'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_distro_downloads_cname"]) ? '<br /><em class="ws-menu-page-hilite">Downloads Distribution CNAME:</em> <em><code>'.esc_html($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_distro_downloads_cname"]).' &mdash;&raquo; '.esc_html($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_distro_downloads_dname"]).'</code></em>' : '').(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_distro_streaming_cname"]) ? '<br /><em class="ws-menu-page-hilite">Streaming Distribution CNAME:</em> <em><code>'.esc_html($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_distro_streaming_cname"]).' &mdash;&raquo; '.esc_html($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_distro_streaming_dname"]).'</code></em>' : '').'</p>'."\n";

				else if(!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_distros_auto_config_status"])
					echo '<p><em class="ws-menu-page-hilite"><strong>Your Amazon CloudFront Distributions are: (NOT yet auto-configured).</strong></em></p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th style="padding-top:0;">'."\n";
				echo '<label for="ws-plugin--s2member-amazon-cf-files-private-key-id">'."\n";
				echo 'Amazon CloudFront Key Pair ID (your Key Pair ID):'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_amazon_cf_files_private_key_id" id="ws-plugin--s2member-amazon-cf-files-private-key-id" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_private_key_id"]).'" data-s-prev-config-value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_private_key_id"]).'" /><br />'."\n";
				echo 'See: <strong>Amazon Web Services Account → Security Credentials → CloudFront Key Pairs</strong>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-amazon-cf-files-private-key-entry">'."\n";
				echo 'Amazon CloudFront Private Key (contents of your <code>pk-[***].pem</code> file):'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="hidden" name="ws_plugin__s2member_amazon_cf_files_private_key" id="ws-plugin--s2member-amazon-cf-files-private-key" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_private_key"]).'" data-s-prev-config-value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_private_key"]).'" />'."\n";
				echo '<textarea name="ws_plugin__s2member_amazon_cf_files_private_key_entry" id="ws-plugin--s2member-amazon-cf-files-private-key-entry" rows="3" wrap="off" spellcheck="false">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_private_key"]).'</textarea><br />'."\n";
				echo 'See: <strong>Amazon Web Services Account → Security Credentials → CloudFront Key Pairs</strong><br />'."\n";
				echo '<em>* Note, s2Member needs your <strong>Private Key file</strong>, NOT your Public Key file.</em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-amazon-cf-files-auto-configure-distros">'."\n";
				echo 'Auto-Configure your Amazon S3/CloudFront combination?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="checkbox" name="ws_plugin__s2member_amazon_cf_files_auto_configure_distros" id="ws-plugin--s2member-amazon-cf-files-auto-configure-distros" value="'.esc_attr(wp_create_nonce("ws-plugin--s2member-amazon-cf-files-auto-configure-distros")).'"'.((!empty(c_ws_plugin__s2member_menu_pages::$pre_display_errors["cf_files_auto_configure_distros"])) ? ' checked="checked"' : '').' /> <label for="ws-plugin--s2member-amazon-cf-files-auto-configure-distros"><strong>Yes</strong>, automatically configure my Amazon CloudFront Distributions &amp; Amazon S3 ACLs for me.</label><br />'."\n";
				echo '<em>s2Member will auto-configure and/or delete &amp; re-configure your Amazon CloudFront Distributions for you.</em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="checkbox" name="ws_plugin__s2member_amazon_cf_files_auto_configure_distros_w_cnames" id="ws-plugin--s2member-amazon-cf-files-auto-configure-distros-w-cnames" value="'.esc_attr(wp_create_nonce("ws-plugin--s2member-amazon-cf-files-auto-configure-distros-w-cnames")).'"'.((!empty(c_ws_plugin__s2member_menu_pages::$pre_display_errors["cf_files_auto_configure_distros"]) && ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_distro_downloads_cname"] || $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_distro_streaming_cname"])) ? ' checked="checked"' : '').' /> <label for="ws-plugin--s2member-amazon-cf-files-auto-configure-distros-w-cnames"><strong>Yes</strong>, I want s2Member to auto-configure using custom CNAMES that I\'ll setup.</label><br />'."\n";
				echo '<em>* Optional, do NOT check this box unless you know what you\'re doing. This requires DNS changes.</em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div id="ws-plugin--s2member-amazon-cf-files-auto-configure-distro-cnames" style="display:none;">'."\n";
				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-amazon-cf-files-downloads-distro-cname">'."\n";
				echo 'Amazon CloudFront CNAME for File Downloads (optional):'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_amazon_cf_files_distro_downloads_cname" id="ws-plugin--s2member-amazon-cf-files-downloads-distro-cname" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_distro_downloads_cname"]).'" /><br />'."\n";
				echo 'Example: <code>s2-file-downloads.'.esc_html(c_ws_plugin__s2member_utils_urls::parse_url(home_url(), PHP_URL_HOST)).'</code>.<br />'."\n";
				echo '<em>* Optional, do NOT fill this in unless you know what you\'re doing. This requires DNS changes.</em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-amazon-cf-files-streaming-distro-cname">'."\n";
				echo 'Amazon CloudFront CNAME for Streaming Files (optional):'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_amazon_cf_files_distro_streaming_cname" id="ws-plugin--s2member-amazon-cf-files-streaming-distro-cname" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_cf_files_distro_streaming_cname"]).'" /><br />'."\n";
				echo 'Example: <code>s2-streaming-files.'.esc_html(c_ws_plugin__s2member_utils_urls::parse_url(home_url(), PHP_URL_HOST)).'</code>.<br />'."\n";
				echo '<em>* Optional, do NOT fill this in unless you know what you\'re doing. This requires DNS changes.</em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<h3>Completely Reset CloudFront Configuration?</h3>'."\n";
				echo '<div style="float:right; margin:0 0 0 25px;">'."\n";
				echo '   <button type="button" onclick="if(confirm(\'Are you sure?\')) location.href = \''.c_ws_plugin__s2member_utils_strings::esc_js_sq(add_query_arg(urlencode_deep(array('ws_plugin__s2member_cf_options_reset' => wp_create_nonce('ws-plugin--s2member-cf-options-reset'))))).'\';">Reset CloudFront Configuration</button>'."\n";
				echo '</div>'."\n";
				echo '<p>If you need to start all over again, you can click this button to reset your existing s2Member/CloudFront configuration. <em><strong>However, please note:</strong> you will still need to log into your AWS CloudFront Console (at some point) and remove any existing CloudFront Distributions and/or Origin Access Identities that were previously generated with s2Member; i.e., resetting your configuration here will allow you to start over with s2Member using a new set of CF Distros, but it does NOT delete anything on the AWS side.</em></p>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '</div>'."\n";
				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_after_amazon_cf", get_defined_vars());
			}
			/*			if(apply_filters("ws_plugin__s2member_during_down_ops_page_during_left_sections_display_amazon_s3_comp", TRUE, get_defined_vars()))
						{
							do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_before_amazon_s3_comp", get_defined_vars());

							echo '<div class="ws-menu-page-group" title="S3-Compatible Content Delivery (e.g., DreamObjects, etc.)">'."\n";

							echo '<div class="ws-menu-page-section ws-plugin--s2member-amazon-s3-comp-section">'."\n";
							echo '<h3>S3-Compatible Content Delivery (optional)</h3>'."\n";
							echo '<p>[Documentation goes here.]</p>'."\n";

							echo '<div class="ws-menu-page-hr"></div>'."\n";

							echo '<table class="form-table" style="margin-top:0;">'."\n";
							echo '<tbody>'."\n";
							echo '<tr>'."\n";

							echo '<th style="padding-top:0;">'."\n";
							echo '<label for="ws-plugin--s2member-amazon-s3-comp-files-bucket">'."\n";
							echo 'API Endpoint URL:'."\n";
							echo '</label>'."\n";
							echo '</th>'."\n";

							echo '</tr>'."\n";
							echo '<tr>'."\n";

							echo '<td>'."\n";
							echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_amazon_s3_comp_api_endpoint" id="ws-plugin--s2member-amazon-s3-comp-api-endpoint" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_s3_comp_api_endpoint"]).'" /><br />'."\n";
							echo 'Please type the S3-Compatible API Endpoint URL'."\n";
							echo '</td>'."\n";

							echo '</tr>'."\n";
							echo '<tr>'."\n";

							echo '<th style="padding-top:0;">'."\n";
							echo '<label for="ws-plugin--s2member-amazon-s3-comp-files-bucket">'."\n";
							echo 'S3-Compatible Bucket Name (where protected files are):'."\n";
							echo '</label>'."\n";
							echo '</th>'."\n";

							echo '</tr>'."\n";
							echo '<tr>'."\n";

							echo '<td>'."\n";
							echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_amazon_s3_comp_files_bucket" id="ws-plugin--s2member-amazon-s3-comp-files-bucket" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_s3_comp_files_bucket"]).'" /><br />'."\n";
							echo 'Your S3-Compatible Bucket will be used, instead of the <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/</code> directory.<br />'."\n";
							echo 'Please type the name of your Bucket. Ex: <code>mys3compbucket</code>'."\n";
							echo '</td>'."\n";

							echo '</tr>'."\n";
							echo '<tr>'."\n";

							echo '<th>'."\n";
							echo '<label for="ws-plugin--s2member-amazon-s3-comp-files-access-key">'."\n";
							echo 'Access Key (Access Key ID):'."\n";
							echo '</label>'."\n";
							echo '</th>'."\n";

							echo '</tr>'."\n";
							echo '<tr>'."\n";

							echo '<td>'."\n";
							echo '<input type="password" autocomplete="off" name="ws_plugin__s2member_amazon_s3_comp_files_access_key" id="ws-plugin--s2member-amazon-s3-comp-files-access-key" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_s3_comp_files_access_key"]).'" /><br />'."\n";
							echo 'Please type the Access Key.'."\n";
							echo '</td>'."\n";

							echo '</tr>'."\n";
							echo '<tr>'."\n";

							echo '<th>'."\n";
							echo '<label for="ws-plugin--s2member-amazon-s3-comp-files-secret-key">'."\n";
							echo 'Secret Key (Secret Access Key):'."\n";
							echo '</label>'."\n";
							echo '</th>'."\n";

							echo '</tr>'."\n";
							echo '<tr>'."\n";

							echo '<td>'."\n";
							echo '<input type="password" autocomplete="off" name="ws_plugin__s2member_amazon_s3_comp_files_secret_key" id="ws-plugin--s2member-amazon-s3-comp-files-secret-key" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["amazon_s3_comp_files_secret_key"]).'" /><br />'."\n";
							echo 'Please type the Secret Key.'."\n";
							echo '</td>'."\n";

							echo '</tr>'."\n";
							echo '</tbody>'."\n";
							echo '</table>'."\n";
							echo '</div>'."\n";

							echo '</div>'."\n";

							do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_after_amazon_s3_comp", get_defined_vars());
						}*/
			if(apply_filters("ws_plugin__s2member_during_down_ops_page_during_left_sections_display_rtmp_streaming", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_before_rtmp_streaming", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="JW Player v6 &amp; RTMP Protocol Examples">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-rtmp-streaming-section">'."\n";
				echo '<h3>JW Player v6 &amp; RTMP Protocol Examples</h3>'."\n";
				echo '<iframe width="560" height="315" src="https://www.youtube.com/embed/ZTopRQQAELw" frameborder="0" allowscriptaccess="always" allowfullscreen="true" style="float:right; margin:0 0 20px 20px; width:300px; height:200px;"></iframe>'."\n";
				echo '<p>While it is possible to serve audio/video files protected by s2Member, without needing to integrate Amazon S3 or CloudFront; we DO highly recommend that you integrate both Amazon S3 and Amazon CloudFront in order to maximize speed and compatibility across various viewing platforms. That being said, there are code samples below that will serve audio/video files both with and without Amazon S3/CloudFront. You can also check the <a href="'.esc_attr(c_ws_plugin__s2member_readmes::parse_readme_value("Forum URI")).'" target="_blank" rel="external">s2Member Support Forums</a> for tips/tricks if you like.</p>'."\n";
				echo '<p><strong>One of the great things about Amazon CloudFront</strong>, is its ability to <strong>stream/seek media files</strong> in the truest sense of the word. For sites delivering protected <em>FLV/MP4/OGG/WEBM</em> and other streaming audio/video file types over the <em>RTMP</em> protocol, Amazon CloudFront is our recommendation. Once you\'ve successfully configured s2Member to use both Amazon S3 and Amazon CloudFront together, please review the code samples below. s2Member can automatically serve your protected files over the <em>RTMP</em> protocol using an Amazon CloudFront Streaming Distribution.</p>'."\n";
				echo '<p><strong>See also:</strong> This KB article: <a href="http://www.s2member.com/kb/jwplayer-s2stream-shortcodes/" target="_blank" rel="external">JW Player w/ <code>[s2Stream /]</code> Shortcodes</a>.</p>'."\n";
				if(stripos(wp_get_theme(), 'infocus') !== FALSE)
					echo '<p><strong>Note:</strong> It appears that you\'re using the inFocus WordPress theme. If you experience trouble with the shortcodes below, try wrapping the shortcode in <code>[raw][/raw]</code> tags (e.g., <code>[raw][s2Stream ... /][/raw]</code>).</p>'."\n";
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_during_rtmp_streaming", get_defined_vars());

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<h3><code>[s2Stream /]</code> Video Shortcode Examples (recommended—it\'s the easiest way)</h3>'."\n";

				echo '<p style="font-size:110%;"><a href="#" onclick="jQuery(\'p#ws-plugin--s2member-rtmp-streaming-details-jwplayer-s2stream-mp4\').toggle(); return false;" class="ws-dotted-link">JW Player (MP4 file, via Rewrite URLs. Amazon S3/CloudFront NOT required)</a></p>'."\n";
				echo '<p id="ws-plugin--s2member-rtmp-streaming-details-jwplayer-s2stream-mp4" style="display:none;">Download <a href="http://www.s2member.com/r/jw-player-download/" target="_blank" rel="external">JW Player here</a>, and upload <code>/jwplayer/</code> to your website\'s root directory.<br />Works with any audio/video file. This does NOT require s2Member to be integrated with Amazon S3/CloudFront.<br /><br />'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/jwplayer-s2stream-mp4.x-php")).'</p>'."\n";

				echo '<p style="font-size:110%;"><a href="#" onclick="jQuery(\'p#ws-plugin--s2member-rtmp-streaming-details-jwplayer-s2stream-mp4-rtmp\').toggle(); return false;" class="ws-dotted-link">JW Player (RTMP streaming MP4, via s2Member\'s Amazon S3/CloudFront integration)</a></p>'."\n";
				echo '<p id="ws-plugin--s2member-rtmp-streaming-details-jwplayer-s2stream-mp4-rtmp" style="display:none;">Download <a href="http://www.s2member.com/r/jw-player-download/" target="_blank" rel="external">JW Player here</a>, and upload <code>/jwplayer/</code> to your website\'s root directory.<br />Streams with the RTMP protocol, plus there is a full download fallback of the MP4 source file if streaming is not possible on a particular device.<br />This requires s2Member to be integrated with Amazon S3/CloudFront.<br /><br />'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/jwplayer-s2stream-mp4-rtmp.x-php")).'</p>'."\n";

				echo '<p style="font-size:110%;"><a href="#" onclick="jQuery(\'p#ws-plugin--s2member-rtmp-streaming-details-jwplayer-s2stream-mp4-rtmp-only\').toggle(); return false;" class="ws-dotted-link">JW Player (RTMP streaming MP4 only, via s2Member\'s Amazon S3/CloudFront integration)</a></p>'."\n";
				echo '<p id="ws-plugin--s2member-rtmp-streaming-details-jwplayer-s2stream-mp4-rtmp-only" style="display:none;">Download <a href="http://www.s2member.com/r/jw-player-download/" target="_blank" rel="external">JW Player here</a>, and upload <code>/jwplayer/</code> to your website\'s root directory.<br />Streams with the RTMP protocol only, with no access to the source file, only to the RTMP stream.<br />This requires s2Member to be integrated with Amazon S3/CloudFront.<br /><br />'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/jwplayer-s2stream-mp4-rtmp-only.x-php")).'</p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<h3><code>[s2Stream /]</code> Audio Shortcode Examples (recommended—it\'s the easiest way)</h3>'."\n";

				echo '<p style="font-size:110%;"><a href="#" onclick="jQuery(\'p#ws-plugin--s2member-rtmp-streaming-details-jwplayer-s2stream-mp3\').toggle(); return false;" class="ws-dotted-link">JW Player (MP3 file, via Rewrite URLs. Amazon S3/CloudFront NOT required)</a></p>'."\n";
				echo '<p id="ws-plugin--s2member-rtmp-streaming-details-jwplayer-s2stream-mp3" style="display:none;">Download <a href="http://www.s2member.com/r/jw-player-download/" target="_blank" rel="external">JW Player here</a>, and upload <code>/jwplayer/</code> to your website\'s root directory.<br />Works with any audio/video file. This does NOT require s2Member to be integrated with Amazon S3/CloudFront.<br /><br />'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/jwplayer-s2stream-mp3.x-php")).'</p>'."\n";

				echo '<p style="font-size:110%;"><a href="#" onclick="jQuery(\'p#ws-plugin--s2member-rtmp-streaming-details-jwplayer-s2stream-mp3-rtmp\').toggle(); return false;" class="ws-dotted-link">JW Player (RTMP streaming MP3, via s2Member\'s Amazon S3/CloudFront integration)</a></p>'."\n";
				echo '<p id="ws-plugin--s2member-rtmp-streaming-details-jwplayer-s2stream-mp3-rtmp" style="display:none;">Download <a href="http://www.s2member.com/r/jw-player-download/" target="_blank" rel="external">JW Player here</a>, and upload <code>/jwplayer/</code> to your website\'s root directory.<br />Streams with the RTMP protocol, plus there is a full download fallback of the MP3 source file if streaming is not possible on a particular device.<br />This requires s2Member to be integrated with Amazon S3/CloudFront.<br /><br />'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/jwplayer-s2stream-mp3-rtmp.x-php")).'</p>'."\n";

				echo '<p style="font-size:110%;"><a href="#" onclick="jQuery(\'p#ws-plugin--s2member-rtmp-streaming-details-jwplayer-s2stream-mp3-rtmp-only\').toggle(); return false;" class="ws-dotted-link">JW Player (RTMP streaming MP3 only, via s2Member\'s Amazon S3/CloudFront integration)</a></p>'."\n";
				echo '<p id="ws-plugin--s2member-rtmp-streaming-details-jwplayer-s2stream-mp3-rtmp-only" style="display:none;">Download <a href="http://www.s2member.com/r/jw-player-download/" target="_blank" rel="external">JW Player here</a>, and upload <code>/jwplayer/</code> to your website\'s root directory.<br />Streams with the RTMP protocol only, with no access to the source file, only to the RTMP stream.<br />This requires s2Member to be integrated with Amazon S3/CloudFront.<br /><br />'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/jwplayer-s2stream-mp3-rtmp-only.x-php")).'</p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<h3>PHP Code Examples (for more advanced integrations via PHP—in WordPress themes)</h3>'."\n";

				echo '<p style="font-size:110%;"><a href="#" onclick="jQuery(\'p#ws-plugin--s2member-rtmp-streaming-details-jwplayer-standard-mp4\').toggle(); return false;" class="ws-dotted-link">JW Player (MP4 file, via Rewrite URLs. Amazon S3/CloudFront NOT required)</a></p>'."\n";
				echo '<p id="ws-plugin--s2member-rtmp-streaming-details-jwplayer-standard-mp4" style="display:none;">Download <a href="http://www.s2member.com/r/jw-player-download/" target="_blank" rel="external">JW Player here</a>, and upload <code>/jwplayer/</code> to your website\'s root directory.<br />This does NOT require s2Member to be integrated with Amazon S3/CloudFront.<br />Also see: <strong>s2Member → Download Options → Advanced Mod Rewrite Linkage</strong>.<br /><br />'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/jwplayer-standard-mp4.x-php")).'</p>'."\n";

				echo '<p style="font-size:110%;"><a href="#" onclick="jQuery(\'p#ws-plugin--s2member-rtmp-streaming-details-jwplayer-streaming-mp4\').toggle(); return false;" class="ws-dotted-link">JW Player (RTMP streaming MP4, via s2Member\'s Amazon S3/CloudFront integration)</a></p>'."\n";
				echo '<p id="ws-plugin--s2member-rtmp-streaming-details-jwplayer-streaming-mp4" style="display:none;">Download <a href="http://www.s2member.com/r/jw-player-download/" target="_blank" rel="external">JW Player here</a>, and upload <code>/jwplayer/</code> to your website\'s root directory.<br />This requires s2Member to be integrated with Amazon S3/CloudFront.<br />Also see: <a href="http://www.s2member.com/codex/stable/s2member/api_functions/package-summary/" target="_blank" rel="external">s2Member Codex → API Functions</a>.<br /><br />'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/jwplayer-streaming-mp4.x-php")).'</p>'."\n";

				echo '<p style="font-size:110%;"><a href="#" onclick="jQuery(\'p#ws-plugin--s2member-rtmp-streaming-details-jwplayer-streaming-mp4-sca\').toggle(); return false;" class="ws-dotted-link">JW Player (RTMP streaming MP4, via s2Member\'s JSON/Shortcode alternative)</a></p>'."\n";
				echo '<p id="ws-plugin--s2member-rtmp-streaming-details-jwplayer-streaming-mp4-sca" style="display:none;">Download <a href="http://www.s2member.com/r/jw-player-download/" target="_blank" rel="external">JW Player here</a>, and upload <code>/jwplayer/</code> to your website\'s root directory.<br />This requires s2Member to be integrated with Amazon S3/CloudFront.<br />Also see: <a href="http://www.s2member.com/codex/stable/s2member/api_functions/package-summary/" target="_blank" rel="external">s2Member Codex → API Functions</a>.<br /><br />'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/jwplayer-streaming-mp4-sca.x-php")).'</p>'."\n";

				echo '<p style="font-size:110%;"><a href="#" onclick="jQuery(\'p#ws-plugin--s2member-rtmp-streaming-details-jwplayer-streaming-mp4-webm\').toggle(); return false;" class="ws-dotted-link">JW Player (RTMP streaming MP4, advanced w/ multiple fallbacks)</a></p>'."\n";
				echo '<p id="ws-plugin--s2member-rtmp-streaming-details-jwplayer-streaming-mp4-webm" style="display:none;">Download <a href="http://www.s2member.com/r/jw-player-download/" target="_blank" rel="external">JW Player here</a>, and upload <code>/jwplayer/</code> to your website\'s root directory.<br />This requires s2Member to be integrated with Amazon S3/CloudFront.<br />Also see: <a href="http://www.s2member.com/codex/stable/s2member/api_functions/package-summary/" target="_blank" rel="external">s2Member Codex → API Functions</a>.<br /><br />'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/jwplayer-streaming-mp4-webm.x-php")).'</p>'."\n";

				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_after_rtmp_streaming", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_down_ops_page_during_left_sections_display_rewrite_linkage", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_before_rewrite_linkage", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Advanced Mod-Rewrite Linkage">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-rewrite-linkage-section">'."\n";
				echo '<h3>Advanced Mod-Rewrite Linkage</h3>'."\n";
				echo '<p>s2Member automatically creates <code>mod_rewrite</code> rules inside your <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/</code> directory, which provide additional flexibility in the way protected files can be served to your Customers. With s2Member\'s <code>mod_rewrite</code> rules, it is now possible to link directly to a protected file, avoiding the use of query string variables <em>(it\'s completely optional though, i.e., NOT required)</em>.</p>'."\n";
				echo '<p>This new flexibility may come in handy for site owners serving files through media playback devices that have issues with query string variables. For instance, it is now possible to link to an s2Member-protected file directly, like this: <code>... /wp-content/plugins/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/example-file.zip</code> instead of <code>... /?s2member_file_download=example-file.zip</code>. Either way works, but the direct link might be easier for some.</p>'."\n";
				echo '<p>It is also possible to pass query string parameters through a direct link:<br /><code>... /wp-content/plugins/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/example-file.zip?s2member_file_inline=yes&amp;s2member_file_download_key=[key]</code>.</p>'."\n";
				echo '<p>That being said, s2Member\'s <code>mod_rewrite</code> rules allow for more advanced control over s2Member-specific parameters.</p>'."\n";
				echo '<p>For example, you could just do this for inline files:<br /><code>... /wp-content/plugins/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'<strong class="ws-menu-page-hilite">/s2member-file-inline</strong>/example-file.zip</code></p>'."\n";
				echo '<p>Or, if you really want to get advanced, you could do something like this:<br /><code>... /wp-content/plugins/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'<strong class="ws-menu-page-hilite">/s2member-file-inline-[yes|no]/s2member-file-download-key-[key]</strong>/example-file.zip</code><br /><code>... /wp-content/plugins/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'<strong class="ws-menu-page-hilite">/s2member-file-inline-yes/s2member-file-download-key-xS54df5ER4d5x</strong>/example-file.zip</code><br /><code>... /wp-content/plugins/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'<strong class="ws-menu-page-hilite">/s2member-file-inline-yes/s2member-skip-confirmation</strong>/example-file.zip</code></p>'."\n";
				echo '<p>Or even this, if you\'re using Remote Header Authorization:<br /><code>... /wp-content/plugins/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'<strong class="ws-menu-page-hilite">/s2member-file-remote</strong>/example-file.zip</code></p>'."\n";
				echo '<p>Specifying storage location option dynamically:<br /><code>... /wp-content/plugins/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'<strong class="ws-menu-page-hilite">/s2member-file-storage-[local|s3|cf]</strong>/example-file.zip</code><br /><code>... /wp-content/plugins/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'<strong class="ws-menu-page-hilite">/s2member-file-storage-cf</strong>/example-cloudfront-file.zip</code><br /><code>... /wp-content/plugins/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'<strong class="ws-menu-page-hilite">/s2member-file-storage-s3/s2member-file-inline</strong>/example-s3-file.html</code></p>'."\n";
				echo '<p><em>* Note, the order of your s2Member-specific parameters with Advanced Mod-Rewrite Linkage is irrelevant. Feel free to add/remove, or even change the order. Everything discussed here is also Multisite compatible. Everything discussed here is also compatible when/if combined with Amazon S3/CDN Storage. However, NONE of this will work on servers that do NOT support <code>mod_rewrite</code>. Almost all web servers do though.</em></p>'."\n";
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_during_rewrite_linkage", get_defined_vars());
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_after_rewrite_linkage", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_down_ops_page_during_left_sections_display_shortcode_attrs", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_before_shortcode_attrs", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Shortcode Attributes &amp; API Functions (Explained)">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-shortcode-attrs-section">'."\n";
				echo '<h3>Shortcode Attributes &amp; API Functions (Explained In Full Detail)</h3>'."\n";
				echo '<p>s2Member makes <a href="http://s2member.com/r/shortcode-reference/" target="_blank" rel="external">Shortcodes</a> available to you, which allow you to generate File Download URLs and/or File Download Keys. Like most Shortcodes for WordPress, s2Member reads Attributes in your Shortcode. Many site owners like to know exactly how these Shortcode Attributes work. Below, is a brief overview of each possible Shortcode Attribute.</p>'."\n";
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_during_shortcode_attrs", get_defined_vars());

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<h4 style="margin:0;"><code>[s2File /]</code> &amp; <code>[s2Stream /]</code> Shortcode Attributes:</h4>'."\n";
				echo '<p style="margin:0;"><strong>See also:</strong> API Function <a href="http://www.s2member.com/codex/stable/s2member/api_functions/package-functions/#src_doc_s2member_file_download_url()" target="_blank" rel="external">s2member_file_download_url()</a> for PHP integration.</p>'."\n";
				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr style="padding-top:0;">'."\n";

				echo '<td style="padding-top:0;">'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>download="file.zip"</code> Location of the file, relative to the <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/</code> directory; or, relative to the root of your Amazon S3 Bucket, when applicable.</li>'."\n";
				echo '<li><code>download_key="no"</code> Defaults to <code>no</code>. If <code>download_key="1|on|yes|true|ip-forever|universal"</code>, s2Member will return a URL with an s2Member-generated File Download Key. You don\'t need to generate the File Download Key yourself, s2Member does it for you. If you set <code>download_key="ip-forever"</code>, the File Download Key that s2Member generates will last forever, for a specific IP Address; otherwise, by default, all File Download Keys expire after 24 hours automatically. If you set <code>download_key="universal"</code>, s2Member will generate a File Download Key that is good for anyone/everyone forever, with NO restrictions on who/where/when a file is accessed <em>(i.e., be careful with this one)</em>.</li>'."\n";
				echo '<li><code>stream="no"</code> Defaults to <code>no</code> with <code>[s2File /]</code> Shortcode. Defaults to <code>yes</code> with <code>[s2Stream /]</code> Shortcode. If <code>stream="1|on|yes|true"</code>, s2Member will return a URL containing a parameter/directive, which forces the File Download to take place over the RTMP protocol if at all possible. This ONLY works when/if s2Member is configured to run with both Amazon S3/CloudFront. Please note however, it\'s better to use the example code provided in the section above, regarding: <code>JW Player and the RTMP Protocol</code>. Also note, if <code>get_streamer_json="1|on|yes|true"</code>, s2Member will automatically force <code>stream="yes"</code> for you.</li>'."\n";
				echo '<li><code>inline=""</code> Defaults to <code>[empty]</code> with <code>[s2File /]</code> Shortcode. Defaults to <code>yes</code> with <code>[s2Stream /]</code> Shortcode. If <code>inline="1|on|yes|true"</code>, s2Member will serve the file inline, instead of as an actual File Download. If empty, s2Member will look at your <code>Inline File Extensions</code> configuration above, and serve the file inline; if, and only if, its extension matches one found in your configuration. By default, s2Member serves all files as attachments <em>(i.e., downloads)</em>, except in the case of the <code>[s2Stream /]</code> Shortcode where this defaults to <code>yes</code>. Please read the section above regarding <code>Inline File Extensions</code> for further details. Also note, this Shortcode Attribute does NOTHING for files served via Amazon CloudFront. See the tech-notes listed in the Amazon CloudFront section for further details and workarounds.</li>'."\n";
				echo '<li><code>storage=""</code> Defaults to <code>[empty]</code>. If <code>storage="local|s3|cf"</code>, s2Member will serve the file from a specific source location, based on the value of this Shortcode Attribute. For example, if you\'ve configured Amazon S3 and/or CloudFront; but, there are a few files that you want to upload locally to the <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/</code> directory; you can force s2Member to serve a file from local storage by setting <code>storage="local"</code> explicitly.</li>'."\n";
				echo '<li><code>remote="no"</code> Defaults to <code>no</code>. If <code>remote="1|on|yes|true"</code>, s2Member will authenticate access to the File Download via Remote Header Authorization, instead of through your web site. This is similar to <code>.htaccess</code> protection routines of yester-year</code>. Please check the <code>Remote Authorization and Podcasting</code> section for further details about how this works.</li>'."\n";
				echo '<li><code>ssl=""</code> Defaults to <code>[empty]</code>. If <code>ssl="1|on|yes|true"</code>, s2Member will generate a File Download URL with an SSL protocol <em>(i.e., the URL will start with <code>https://</code> or <code>rtmpe://</code>)</em>. If empty, s2Member will only generate a File Download URL with an SSL protocol, when/if the Post/Page/URL firing the Shortcode itself, is also being viewed over SSL. Otherwise, s2Member will use a non-SSL protocol by default.</li>'."\n";
				echo '<li><code>rewrite="no"</code> Defaults to <code>no</code> with <code>[s2File /]</code> Shortcode. Defaults to <code>yes</code> with <code>[s2Stream /]</code> Shortcode. If <code>rewrite="1|on|yes|true"</code>, s2Member will generate a File Download URL that takes full advantage of s2Member\'s Advanced Mod Rewrite functionality. If you\'re running an Apache web server, or another server that supports <code>mod_rewrite</code>, we highly recommend turning this on. s2Member\'s <code>mod_rewrite</code> URLs do NOT contain query string parameters, making them more portable/compatible with other software applications and/or plugins for WordPress. If you\'re integrating with JW Player, you MUST use <code>rewrite="yes"</code>.</li>'."\n";
				echo '<li><code>rewrite_base=""</code> Defaults to <code>[empty]</code>. If <code>rewrite_base="'.esc_attr(site_url("/")).'"</code>, s2Member will generate a File Download URL that takes full advantage of s2Member\'s Advanced Mod Rewrite functionality, and it will use the rewrite base URL as a prefix. This could be useful on some WordPress installations that use advanced directory structures. It could also be useful for site owners using virtual directories that point to <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/</code>. Note, if <code>rewrite_base</code> is set, s2Member will automatically force <code>rewrite="yes"</code> for you.</li>'."\n";
				echo '<li><code>skip_confirmation="no"</code> Defaults to <code>no</code>. If <code>skip_confirmation="1|on|yes|true"</code>, s2Member will generate a File Download URL which contains a directive, telling s2Member NOT to introduce any JavaScript confirmation prompts on your site, for this File Download URL. Please note, s2Member will automatically detect links, anywhere in your content, and/or anywhere in your theme files, that contain <code>s2member_file_download</code> or <code>s2member-files</code>. Whenever a logged-in Member clicks a link that contains <code>s2member_file_download</code> or <code>s2member-files</code>, the system will politely ask the User to confirm the download using a very intuitive JavaScript confirmation prompt, which contains specific details about your configured download limitations. This way your Members will be aware of how many files they\'ve downloaded in the current period; and they\'ll be able to make a conscious decision about whether to proceed with a specific download or not.</li>'."\n";
				echo '<li><code>url_to_storage_source="no"</code> Defaults to <code>no</code> with <code>[s2File /]</code> Shortcode. Defaults to <code>yes</code> with <code>[s2Stream /]</code> Shortcode. If <code>url_to_storage_source="1|on|yes|true"</code>, s2Member will generate a File Download URL which points directly to the storage source. This is only functional with Amazon S3 and/or CloudFront integrations. If you create a URL that points directly to the storage source <em>(i.e., points directly to Amazon S3 or CloudFront)</em>, s2Member will NOT be able to further authenticate the current User/Member; and, s2Member will NOT be able to count the File Download against the current User\'s account record, because the URL being generated does not pass back through s2Member at all, it points directly to the storage source. For this reason, if you set <code>url_to_storage_source="true"</code>, you should also set <code>check_user="true"</code> and <code>count_against_user="true"</code>, telling s2Member to authenticate the current User, and if authenticated, count this File Download URL against the current User\'s account record in real-time <em>(i.e., as the URL is being generated) </em>, while it still has a chance to do so. This Shortcode Attribute is useful when you stream files over the RTMP protocol; where an <code>http://</code> URL is not feasible. It also helps in situations where a 3rd-party software application will not work as intended, with s2Member\'s internal redirection to Amazon S3/CloudFront files. Important, when <code>check_user="true"</code> and/or <code>count_against_user="true"</code>, the Shortcode will return an empty and/or null object value in situations where the current User/Member does NOT have access to the file.</li>'."\n";
				echo '<li><code>count_against_user="no"</code> Defaults to <code>no</code> with <code>[s2File /]</code> Shortcode. Defaults to <code>yes</code> with <code>[s2Stream /]</code> Shortcode. If <code>count_against_user="1|on|yes|true"</code>, it will automatically force <code>check_user="true"</code> as well. In other words, s2Member will authenticate the current User, and if authenticated, count this File Download URL against the current User\'s account record in real-time <em>(i.e., as the URL is being generated) </em>. This is off by default with the <code>[s2File /]</code> Shortcode. By default, s2Member will simply generate a File Download URL, and upon a User/Member clicking the URL, s2Member will authenticate the User/Member at that time, count the File Download against their account record, and serve the File Download. In other words, under normal circumstances, there is no reason to set <code>check_user="true"</code> and/or <code>count_against_user="true"</code> when generating the URL itself. However, this is a useful Shortcode Attribute when <code>url_to_storage_source="true"</code>. Please note, when <code>check_user="true"</code> and/or <code>count_against_user="true"</code>, the Shortcode will return an empty and/or null object value in situations where the current User/Member does NOT have access to the file.</li>'."\n";
				echo '<li><code>check_user="no"</code> Defaults to <code>no</code> with <code>[s2File /]</code> Shortcode. Defaults to <code>yes</code> with <code>[s2Stream /]</code> Shortcode. If <code>check_user="1|on|yes|true"</code>, s2Member will authenticate the current User before allowing the File Download URL to be generated. This is off by default with the <code>[s2File /]</code> Shortcode. By default, s2Member will simply generate a File Download URL, and upon a User/Member clicking the URL, s2Member will authenticate the User/Member at that time, and serve the File Download to the User/Member. In other words, under normal circumstances, there is no reason to set <code>check_user="true"</code> and/or <code>count_against_user="true"</code> when generating the URL itself. However, this IS a useful Shortcode Attribute when <code>url_to_storage_source="true"</code>. Please note, when <code>check_user="true"</code> and/or <code>count_against_user="true"</code>, the Shortcode will return an empty and/or null object value in situations where the current User/Member does NOT have access to the file.</li>'."\n";
				echo '<li><code>get_streamer_json="no"</code> Defaults to <code>no</code>. N/A with <code>[s2Stream /]</code> Shortcode. If <code>get_streamer_json="1|on|yes|true"</code>, the <code>[s2File /]</code> Shortcode will return a JSON object for JavaScript notation, making it possible to integrate the <code>[s2File /]</code> Shortcode into JavaScript routines that configure streaming media players. For further details, please review the section above: <code>JW Player &amp; RTMP Protocol Examples</code>. Note, if you set <code>get_streamer_json="true"</code>, s2Member will automatically force <code>url_to_storage_source="true"</code> and <code>stream="true"</code>. For that reason, you should carefully review the details and warning above regarding <code>url_to_storage_source</code>. If you set <code>get_streamer_json="true"</code>, you should also set <code>check_user="true"</code> and <code>count_against_user="true"</code>.</li>'."\n";
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_during_shortcode_attrs_s2file_lis", get_defined_vars());
				echo '</ul>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<h4 style="margin:0;">Additional <code>[s2Stream /]</code> Shortcode Attributes:</h4>'."\n";
				echo '<p style="margin:0;"><strong>See also:</strong> This KB article: <a href="http://www.s2member.com/kb/jwplayer-s2stream-shortcodes/" target="_blank" rel="external">JW Player w/ <code>[s2Stream /]</code> Shortcodes</a>.</p>'."\n";
				echo '<p style="margin:0;"><strong>See also:</strong> API Function <a href="http://www.s2member.com/codex/stable/s2member/api_functions/package-functions/#src_doc_s2member_file_download_url()" target="_blank" rel="external">s2member_file_download_url()</a> for PHP integration.</p>'."\n";
				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr style="padding-top:0;">'."\n";

				echo '<td style="padding-top:0;">'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>file_download="video.mp4"</code> Location of the audio/video file, relative to the <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/</code> directory; or, relative to the root of your Amazon S3 Bucket, when applicable.</li>'."\n";
				echo '<li><code>player="jwplayer-v6-rtmp"</code> Required. Current supported players in this Shortcode include: <code>jwplayer-v6</code> (works with any audio/video file, and you do NOT need to have Amazon  S3 or CloudFront integrated for this to work), <code>jwplayer-v6-rtmp</code> (streams with the RTMP protocol, plus there is a full download fallback of the source file if streaming is not possible on a particular device; this requires both Amazon S3 and CloudFront integration), <code>jwplayer-v6-rtmp-only</code> (streams with the RTMP protocol only, with no access to the source file, only to the RTMP stream; this requires both Amazon S3 and CloudFront integration).</li>'."\n";
				echo '<li><code>player_id=""</code> Optional. HTML div ID for the audio/video player. Defaults to a unique ID generated by s2Member for each instance of your Shortcode.</li>'."\n";
				echo '<li><code>player_path="/jwplayer/jwplayer.js"</code> Required. Path to the player\'s JavaScript file (ex: <code>/jwplayer/jwplayer.js</code>—you should upload the <a href="http://www.s2member.com/r/jw-player-download/" target="_blank" rel="external">/jwplayer</a> folder to the root of your web directory).</li>'."\n";
				echo '<li><code>player_resolutions=""</code> Optional (requires s2Member Pro). This is a comma-delimited list of all available resolution options (should you decide to offer more than just one file download at a single resolution). Please review the full list of Shortcode Attributes (i.e., click the "Shortcode Attributes (Explained)" tab) in <a href="http://www.s2member.com/kb/jwplayer-s2stream-shortcodes/#using-s2stream-shortcodes" target="_blank" rel="external">this KB article</a> for further details, requirements, and an example of use.</li>'."\n";
				echo '<li><code>player_{setting}=""</code> Optional. Any additional configuration attributes supported by your audio/video player, prefixed with <code>player_</code>. For JW Player v6, see <a href="http://www.s2member.com/r/jw-player-config-options/" target="_blank" rel="external">this article please</a>. Examples: <code>player_width="480"</code>, <code>player_height="270"</code>, <code>player_title="My Video"</code>, <code>player_description="A video about something."</code>, <code>player_image="http://www.example.com/wp-content/uploads/video-preview.jpg"</code>, <code>player_mediaid="video_ei0wsx23"</code>, <code>player_autostart="true"</code>, <code>player_skin="/jwplayer/my-skin.xml"</code>, <code>player_key="my-license-key"</code>, <code>player_captions="{file:\'/assets/captions-en.vtt\',label:\'English\'}"</code> (<em>With <a href="http://www.s2member.com/r/jw-player-video-captions/" target="_blank" rel="external">Captions</a>, you can exclude the square array brackets to avoid Shortcode parsing issues. s2Member will automatically wrap your Caption objects with square array brackets.</em>). Please note that "Advanced Options Blocks" listed on <a href="http://www.s2member.com/r/jw-player-config-options/" target="_blank" rel="external">this page</a> are NOT supported here. For those, please use: <code>player_option_blocks=""</code> (see below).</li>'."\n";
				echo '<li><code>player_option_blocks=""</code> Optional. Any "Advanced Option Blocks" supported by your audio/video player. For JW Player v6, see <a href="http://www.s2member.com/r/jw-player-config-options/" target="_blank" rel="external">this article please</a>. Here are some examples: <code>player_option_blocks="sharing:{}"</code>, <code>player_option_blocks="sharing:{}, logo: {file: \'/logo.png\', link: \'http://example.com\'}"</code>. Or: <code>player_option_blocks="c2hhcmluZzoge30="</code> (base64 encoded version of <code>sharing:{}</code>). Please note that "Advanced Options Blocks" can be defined in plain text or with a <a href="http://s2member.com/r/base64-encoding/" target="_blank" rel="external">base64 encoded string</a>. Advanced Option Blocks are JavaScript objects with properties. If you have trouble defining JavaScript object properties inside a Shortcode Attribute, please use <a href="http://s2member.com/r/base64-encoding/" target="_blank" rel="external">this tool</a> to base64 encode your Advanced Option Blocks, so that you end up with a string that\'s compatible with Shortcode Attributes.</li>'."\n";
				echo '<li>Please check the <strong>Shortcode Attributes</strong> Tab in <a href="http://www.s2member.com/kb/jwplayer-s2stream-shortcodes/#using-s2stream-shortcodes" target="_blank" rel="external">this KB article</a> for further details on everything here.</li>'."\n";
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_during_shortcode_attrs_s2stream_lis", get_defined_vars());
				echo '</ul>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<h4 style="margin:0;"><code>[s2Key /]</code> Shortcode Attributes:</h4>'."\n";
				echo '<p style="margin:0;"><strong>See also:</strong> API Function <a href="http://www.s2member.com/codex/stable/s2member/api_functions/package-functions/#src_doc_s2member_file_download_key()" target="_blank" rel="external">s2member_file_download_key()</a> for PHP integration.</p>'."\n";
				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr style="padding-top:0;">'."\n";

				echo '<td style="padding-top:0;">'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>file_download="file.zip"</code> Location of the file, relative to the <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/</code> directory; or, relative to the root of your Amazon S3 Bucket, when applicable.</li>'."\n";
				echo '<li><code>directive=""</code> Defaults to <code>[empty]</code>. If <code>directive="ip-forever|universal"</code>, s2Member will return a special File Download Key. If you set <code>directive="ip-forever"</code>, the File Download Key that s2Member generates will last forever, for a specific IP Address; otherwise, by default, all File Download Keys expire after 24 hours automatically. If you set <code>directive="universal"</code>, s2Member will generate a File Download Key that is good for anyone/everyone forever, with NO restrictions on who/where/when a file is accessed <em>(be careful with this one)</em>.</li>'."\n";
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_during_shortcode_attrs_s2key_lis", get_defined_vars());
				echo '</ul>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_after_shortcode_attrs", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_down_ops_page_during_left_sections_display_gzip_conflicts", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_before_gzip_conflicts", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Preventing GZIP Conflicts On Server">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-gzip-conflicts-section">'."\n";
				echo '<h3>Preventing GZIP Conflicts On Server (Instructions)</h3>'."\n";
				echo '<p>Protected files served by s2Member through PHP scripts, are already compressed. Therefore, <a href="http://s2member.com/r/gzip-compression-explained/" target="_blank" rel="nofollow external xlink">GZIP compression</a> is not needed during protected file delivery. Some web servers (i.e., Apache, LiteSpeed, and similar) include GZIP compression rules through server-side extensions, like <code>mod_deflate</code> for example. While s2Member encourages the use of extensions like <code>mod_deflate</code>, it is best to disable GZIP automatically (i.e., temporarily) during s2Member\'s delivery of a protected file through a PHP script. This avoids conflicts on the server which might otherwise lead to corrupted file downloads. s2Member makes a valiant effort to accomplish this via PHP, all on its own. However, it never hurts to add this section of code to the root <code>.htaccess</code> file for your WordPress installation. Optional, but highly recommended.</p>'."\n";
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_during_gzip_conflicts", get_defined_vars());

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<p style="margin:0; font-weight:bold;">s2Member automatically adds this to your <code>.htaccess</code> file upon activation of the plugin.</p>'."\n";
				echo '<p style="margin:0;">The following <code>mod_rewrite</code> rule goes inside this file: <code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path(ABSPATH.".htaccess")).'</code></p>'."\n";
				echo '<pre class="code"><code>'.esc_html(trim(c_ws_plugin__s2member_utilities::evl(file_get_contents($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_no_gzip_htaccess"])))).'</code></pre>';
				echo '<p><strong>* Tip:</strong> this covers all types of integration with s2Member File Downloads, even if you\'re using s2Member\'s Advanced Mod Rewrite Linkage.</p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-file-download-content-encodong-none">'."\n";
				echo 'Also Force a <code>Content-Encoding: none</code> Header?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_file_download_content_encodong_none" id="ws-plugin--s2member-file-download-content-encodong-none">'."\n";
				echo '<option value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["file_download_content_encodong_none"]) ? ' selected="selected"' : '').'>No (remain standards-compliant; I will configure my server properly)</option>'."\n";
				echo '<option value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["file_download_content_encodong_none"]) ? ' selected="selected"' : '').'>Yes (my web server is stubborn; downloads are corrupted without this)</option>'."\n";
				echo '</select>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<p><code>Content-Encoding: none</code> can be forced by s2Member in order to workaround stubborn web server configurations. However, please note that <code>Content-Encoding: none</code> is an invalid header (NOT standards compliant), so don\'t enable this unless you absolutely need to. For instance, if files downloaded via s2Member are always corrupt, you could enable this to workaround the issue. The issue being... that your web server is ignoring all of s2Member\'s attempts to serve a file without Content-Encoding. While <code>Content-Encoding: none</code> is indeed a hack, it\'s a relatively common hack that most modern browsers will understand just fine; making this a viable solution when/if necessary.</p>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_after_gzip_conflicts", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_down_ops_page_during_left_sections_display_custom_capability_files", (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()), get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_before_custom_capability_files", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Custom Capability &amp; Member Level Files">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-custom-capability-files-section">'."\n";
				echo '<h3>Restricting Files, Based On Custom Capabilities</h3>'."\n";
				echo '<p>If you\'re NOT familiar with Custom Capabilities yet, please read: <strong>Dashboard → s2Member → API Scripting → Custom Capability Packages</strong>. Once you understand the basic concept of Custom Capabilities &amp; Protected File Downloads, you\'ll see that (by default) s2Member does NOT handle File Download Protection with respect to Custom Capabilities. That\'s where Custom Capability Sub-directories come in.</p>'."\n";
				echo '<p>You can create Custom Capability Sub-directories under: <code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'</code>. For instance, if you have a Custom Capability <code>music</code>, you can place protected files that should ONLY be accessible to Members with <code>access_s2member_ccap_music</code>, inside: <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/access-s2member-ccap-music/</code>. Some examples are provided below.</p>'."\n";
				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_during_custom_capability_files", get_defined_vars());

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<p><strong>Custom Capabilities:</strong> (music,videos)</p>'."\n";
				echo '<p>Sub-Directory: <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/access-s2member-ccap-music</code><br />Sub-Directory: <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/access-s2member-ccap-videos</code></p>'."\n";
				echo '<p>Protected File: <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/access-s2member-ccap-music/file.mp3</code><br />Protected File: <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/access-s2member-ccap-videos/file.avi</code></p>'."\n";
				echo '<p>Now, here are some link examples, using Custom Capability Sub-directories:</p>'."\n";
				echo '<p>'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/ccap-file-downloads.x-php")).'</p>'."\n";
				echo '<p><em>These links will ONLY work for Members who are logged-in, with the proper Capabilities.</em></p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<p><strong>Membership Levels:</strong> (this also works fine)</p>'."\n";
				echo '<p>Sub-Directory: <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/access-s2member-level0</code><br />Sub-Directory: <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/access-s2member-level1</code><br />Sub-Directory: <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/access-s2member-level2</code><br />Sub-Directory: <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/access-s2member-level3</code><br />Sub-Directory: <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/access-s2member-level4</code></p>'."\n";
				echo '<p>Protected File: <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/access-s2member-level0/tiger.doc</code><br />Protected File: <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/access-s2member-level1/zebra.pdf</code><br />Protected File: <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/access-s2member-level2/elephant.doc</code><br />Protected File: <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/access-s2member-level3/rhino.pdf</code><br />Protected File: <code>/'.esc_html(c_ws_plugin__s2member_utils_dirs::basename_dir_app_data($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_dir"])).'/access-s2member-level4/lion.doc</code></p>'."\n";
				echo '<p>Now, here are some link examples, using Member Level Sub-directories:</p>'."\n";
				echo '<p>'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/level-file-downloads.x-php")).'</p>'."\n";
				echo '<p><em>These links will ONLY work for Members who are logged-in, with an adequate Membership Level.</em></p>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_down_ops_page_during_left_sections_after_custom_capability_files", get_defined_vars());
			}
			do_action("ws_plugin__s2member_during_down_ops_page_after_left_sections", get_defined_vars());

			echo '<p class="submit"><input type="submit" value="Save All Changes" /></p>'."\n";

			echo '</form>'."\n";

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

new c_ws_plugin__s2member_menu_page_down_ops();