<?php
/**
 * Menu page for the s2Member plugin (Logs page).
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
 * @since 130210
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");

if(!class_exists("c_ws_plugin__s2member_menu_page_logs"))
{
	/**
	 * Menu page for the s2Member plugin (Integrations page).
	 *
	 * @package s2Member\Menu_Pages
	 * @since 110531
	 */
	class c_ws_plugin__s2member_menu_page_logs
	{
		public function __construct()
		{
			echo '<div class="wrap ws-menu-page">'."\n";

			echo '<div class="ws-menu-page-toolbox">'."\n";
			c_ws_plugin__s2member_menu_pages_tb::display();
			echo '</div>'."\n";

			echo '<h2>Log Files</h2>'."\n";

			echo '<table class="ws-menu-page-table">'."\n";
			echo '<tbody class="ws-menu-page-table-tbody">'."\n";
			echo '<tr class="ws-menu-page-table-tr">'."\n";
			echo '<td class="ws-menu-page-table-l">'."\n";

			do_action("ws_plugin__s2member_during_logs_page_before_left_sections", get_defined_vars());

			if(apply_filters("ws_plugin__s2member_during_logs_page_during_left_sections_display_log_settings", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_logs_page_during_left_sections_before_log_settings", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Logging Configuration">'."\n";
				echo '<div class="ws-menu-page-section ws-plugin--s2member-log-settings-section">'."\n";

				echo '<h3>Logging Configuration</h3>'."\n";

				echo '<div class="info">'."\n";
				echo '<p style="margin-top:0;"><span>We highly recommend that you enable logging during your initial testing phase. Logs produce lots of useful details that can help in debugging. Logs can help you find issues in your configuration and/or problems that occur during processing. Enable logging here, and then view your log files below, in the s2Member Log Viewer.</span></p>'."\n";
				echo '<p style="margin-bottom:0;"><span class="ws-menu-page-error">However, it is very important to disable logging once you go live. Log files may contain personally identifiable information, credit card numbers, secret API credentials, passwords and/or other sensitive information. We strongly suggest that logging be disabled on a live site (for security reasons).</span></p>'."\n";
				echo '</div>'."\n";

				echo '<div class="warning" style="margin-bottom:0;">'."\n";
				echo '<p style="margin:0;"><span>Regarding s2Member Security Badges. If debug logging is enabled, your site will not qualify for an s2Member Security Badge until you disable logging (and you must also download, and then delete any existing log files). For further details, please see KB Article: <a href="http://www.s2member.com/kb/security-badges/" target="_blank" rel="external">s2Member Security Badges</a>.</span></p>'."\n";
				echo '</div>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				do_action("ws_plugin__s2member_during_logs_page_during_left_sections_during_log_settings", get_defined_vars());

				echo '<form method="post" name="ws_plugin__s2member_options_form" id="ws-plugin--s2member-options-form" autocomplete="off">'."\n";
				echo '<input type="hidden" name="ws_plugin__s2member_options_save" id="ws-plugin--s2member-options-save" value="'.esc_attr(wp_create_nonce("ws-plugin--s2member-options-save")).'" />'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-gateway-debug-logs">'."\n";
				echo 'Enable Logging Routines?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="radio" name="ws_plugin__s2member_gateway_debug_logs" id="ws-plugin--s2member-gateway-debug-logs-0" value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["gateway_debug_logs"]) ? ' checked="checked"' : '').' /> <label for="ws-plugin--s2member-gateway-debug-logs-0">No</label> &nbsp;&nbsp;&nbsp; <input type="radio" name="ws_plugin__s2member_gateway_debug_logs" id="ws-plugin--s2member-gateway-debug-logs-1" value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["gateway_debug_logs"]) ? ' checked="checked"' : '').' /> <label for="ws-plugin--s2member-gateway-debug-logs-1">Yes, enable debugging w/ HTTP, API, IPN &amp; Return Page logging (and List Server API logs too).</label><br />'."\n";
				echo '<em>This enables logging overall. Includes s2Member HTTP, API, IPN and Return Page logging. Also logs any List Server integrations.</em><br />'."\n";
				echo '<em>* Use only for debugging. This should NEVER be enabled on a live site.<br />* The log files are stored here: <code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["logs_dir"])).'</code></em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-gateway-debug-logs-extensive">'."\n";
				echo 'Enable Additional Logging Routines?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="radio" name="ws_plugin__s2member_gateway_debug_logs_extensive" id="ws-plugin--s2member-gateway-debug-logs-extensive-0" value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["gateway_debug_logs_extensive"]) ? ' checked="checked"' : '').' /> <label for="ws-plugin--s2member-gateway-debug-logs-extensive-0">No</label> &nbsp;&nbsp;&nbsp; <input type="radio" name="ws_plugin__s2member_gateway_debug_logs_extensive" id="ws-plugin--s2member-gateway-debug-logs-extensive-1" value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["gateway_debug_logs_extensive"]) ? ' checked="checked"' : '').' /> <label for="ws-plugin--s2member-gateway-debug-logs-extensive-1">Yes, enable debugging w/ HTTP connection logging for ALL of WordPress.</label><br />'."\n";
				echo '<em>This enables HTTP connection logging for ALL of WordPress (quite extensive).<br />* Use only for debugging. This should NEVER be enabled on a live site.<br />* Creates the additional log file: <code>wp-http-api-debug.log</code></em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<p class="submit">'."\n";
				echo '<input type="submit" value="Update Logging Configuration" />'."\n";
				echo '</p>'."\n";

				echo '</form>'."\n";

				echo '</div>'."\n";
				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_logs_page_during_left_sections_after_log_settings", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_logs_page_during_left_sections_display_logs", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_logs_page_during_left_sections_before_logs", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Logs Viewer" default-state="open">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-logs-section">'."\n";
				echo '<h3>Debugging Tools/Tips &amp; Other Important Details (<a href="#" onclick="jQuery(\'div#ws-plugin--s2member-debugging-tips-details\').toggle(); return false;" class="ws-dotted-link">click here to toggle</a>)</h3>'."\n";

				echo '<div id="ws-plugin--s2member-debugging-tips-details" style="display:none;">'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<form method="post" onsubmit="if(!confirm(\'Archive all existing log files?\n\nAll of your current log files will be archived (i.e., they will simply be renamed with an ARCHIVED tag &amp; date in their file name); and new log files will be created automatically the next time s2Member logs something on your installation.\n\nPlease click OK to confirm this action.\')) return false;" autocomplete="off">'."\n";
				echo '<input type="hidden" name="ws_plugin__s2member_logs_archive_start_fresh" value="'.esc_attr(wp_create_nonce("ws-plugin--s2member-logs-archive-start-fresh")).'" />'."\n";
				echo '<input type="submit" value="Archive All Current Log Files" class="ws-menu-page-right ws-plugin--s2member-archive-logs-start-fresh-button" style="font-size:110%; font-weight:normal; clear:right; min-width:300px;" />'."\n";
				echo '</form>'."\n";

				echo '<form method="post" onsubmit="if(!confirm(\'Delete all existing log files?\n\nThis will permanently delete ALL of your existing log files (including any archived log files).\n\nPlease click OK to confirm this action.\')) return false;" autocomplete="off">'."\n";
				echo '<input type="hidden" name="ws_plugin__s2member_logs_delete_start_fresh" value="'.esc_attr(wp_create_nonce("ws-plugin--s2member-logs-delete-start-fresh")).'" />'."\n";
				echo '<input type="submit" value="Permanently Delete All Log Files" class="ws-menu-page-right ws-plugin--s2member-delete-logs-start-fresh-button" style="font-size:110%; font-weight:normal; clear:right; min-width:300px;" />'."\n";
				echo '</form>'."\n";

				echo '<form method="post" autocomplete="off">'."\n";
				echo '<input type="hidden" name="ws_plugin__s2member_logs_download_zip" value="'.esc_attr(wp_create_nonce("ws-plugin--s2member-logs-download-zip")).'" />'."\n";
				echo '<input type="submit" value="Download All Log Files (Zip File)" class="ws-menu-page-right ws-plugin--s2member-logs-download-zip-button" style="font-size:110%; font-weight:normal; clear:right; min-width:300px;" />'."\n";
				echo '</form>'."\n";

				echo '<p><strong>Debugging Tips:</strong> &nbsp;&nbsp; It is normal to see a few errors in your log files. This is because s2Member logs <em>all</em> of its communication with Payment Gateways. Everything—not just successes. With that in mind, there will be some failures that s2Member expects (to a certain extent); and s2Member deals with these gracefully. What you\'re looking for here, are things that jump right out at you as being a major issue (e.g., when s2Member makes a point of providing details to you in a log entry about problems that should be corrected on your installation). Please read carefully.</p>'."\n";
				echo '<p><strong>Test Transaction Tips:</strong> &nbsp;&nbsp; Generally speaking, it is best to run test transactions for yourself. Be sure to run your final test transactions against a live Payment Gateway that is <em>not</em> in Sandbox/Test Mode (<a href="#" onclick="alert(\'While some Payment Gateways make it possible for you to run test transactions in Sandbox/Test Mode, these are not a reliable way to test s2Member.\n\nOften times (particularly with PayPal) Sandbox/Test mode behaves somewhat differently—often with buggy behavior. This can really create frustration for site owners. Therefore, it is always a good idea to run low-dollar test transactions against a live Payment Gateway.\n\nAlso, please be sure that you are not logged in as an Administrator when running test transactions. For most test transactions, you will want to be completely logged-out of your site before completing checkout (just like a new Customer would be). If you are testing an upgrade or downgrade (where you do need to be logged-in), please do not attempt this under an Administrative account. s2Member will not upgrade/downgrade Administrative accounts—for security purposes.\'); return false;">click here for details</a>). After running test transactions, please review the log file entries pertaining to your transaction. Does s2Member report any major issues? If so, please read through any details that s2Member provides in the log file. If you need assistance, please <a href="http://www.s2member.com/quick-s.php" target="_blank" rel="external">search s2Member.com</a> for answers to common questions.</p>'."\n";
				echo '<p><strong>s2 Core Processors:</strong> &nbsp;&nbsp; It is normal to have a <code>gateway-core-ipn.log</code> and/or a <code>gateway-core-rtn.log</code> file at all times. Ultimately, all Payment Gateway integrations supported by s2Member pass through it\'s core post-processing handlers. If you\'re having trouble, and you don\'t find any errors in your Payment Gateway log files, please check the <code>gateway-core-ipn.log</code> and <code>gateway-core-rtn.log</code> files too. Regarding s2Member Pro-Forms... If you\'ve integrated s2Member Pro-Forms, you will not have a <code>gateway-core-rtn.log</code> file, because that particular processor is not used with Pro-Form integrations. However, you will have a <code>gateway-core-ipn.log</code> file, and you will need to make a point of inspecting this file to ensure there were no post-processing issues.</p>'."\n";
				echo '<p><strong>s2 HTTP API Logs:</strong> &nbsp;&nbsp; If s2Member is not behaving as expected, and you cannot find errors anywhere in your Payment Gateway log files (or with any core processors), please review your <code>s2-http-api-debug.log</code> file too. Look for any HTTP connections where s2Member is getting <code>403</code>, <code>404</code>, <code>503</code> errors from your server. This can sometimes happen due to <a href="http://www.s2member.com/kb/mod-security-random-503-403-errors/" target="_blank" rel="external">paranoid Mod Security configurations</a>, and it may require you to contact your hosting company for assistance.</p>'."\n";
				echo '<p style="font-style:italic;"><strong>Archived Log Files:</strong> &nbsp;&nbsp; All s2Member log files are stored here: <code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["logs_dir"])).'</code>. Any log files that contain the word <code>ARCHIVED</code> in their name, are files that reached a size of more than 2MB; so s2Member archived them automatically to prevent any single log file from becoming too large. Archived log file names will also contain the date/time they were archived by s2Member. These archived log files typically contain much older (and possibly outdated) log entries.</p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<h3>s2Member Log File Descriptions (for <em>all</em> possible log file names)</h3>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<ul class="ws-menu-page-li-margins">'."\n";
				foreach(c_ws_plugin__s2member_utils_logs::$log_file_descriptions as $_k => $_v)
					echo '<li><code><strong>'.esc_html(preg_replace(array('/^\/|\/$/', '/\\\\+/'), '', $_k)).'.log</strong></code> &nbsp;&nbsp; '.esc_html($_v["long"]).'</li>'."\n";
				unset($_k, $_v); // Housekeeping.
				echo '</ul>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_logs_page_during_left_sections_during_logs", get_defined_vars());

				$log_file_options = ""; // Initialize to an empty string.
				$view_log_file    = (!empty($_POST["ws_plugin__s2member_log_file"])) ? esc_html($_POST["ws_plugin__s2member_log_file"]) : "";
				$logs_dir         = $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["logs_dir"];

				if(is_dir($logs_dir)) // Do we have a logs directory on this installation?
				{
					$log_files = scandir($logs_dir);
					sort($log_files, SORT_STRING);

					$log_file_options .= '<optgroup label="Current Log Files">';
					foreach($log_files as $_log_file) // Build options for each current log file.
					{
						$_log_file_description = array("short" => "No description available.", "long" => "No description available.");

						foreach(c_ws_plugin__s2member_utils_logs::$log_file_descriptions as $_k => $_v)
							if(preg_match($_k, $_log_file))
							{
								$_log_file_description = $_v;
								break; // Stop here.
							}
						unset($_k, $_v); // Housekeeping.

						if(preg_match("/\.log$/", $_log_file) && stripos($_log_file, "-ARCHIVED-") === FALSE)
							$log_file_options .= '<option data-type="current" title="'.esc_attr($_log_file_description["long"]).'" value="'.esc_attr($_log_file).'"'.(($view_log_file === $_log_file) ? ' style="font-weight:bold;" selected="selected"' : '').'>'.esc_html($_log_file).'—'.esc_html($_log_file_description["short"]).'</option>';
					}
					unset($_log_file_description, $_log_file); // Housekeeping.
					$log_file_options .= '</optgroup>';

					if(stripos($log_file_options, '<option data-type="current"') === FALSE)
						$log_file_options .= '<option value="" disabled="disabled">— No current log files yet. —</option>';

					$log_file_options .= '<option value="" disabled="disabled"></option>';

					$log_file_options .= '<optgroup label="Archived Log Files">';
					foreach($log_files as $_log_file) // Build options for each ARCHIVED log file.
					{
						if(preg_match("/\.log$/", $_log_file) && stripos($_log_file, "-ARCHIVED-") !== FALSE)
							$log_file_options .= '<option data-type="archived" value="'.esc_attr($_log_file).'"'.(($view_log_file === $_log_file) ? ' style="font-weight:bold;" selected="selected"' : '').'>'.esc_html($_log_file).'</option>';
					}
					$log_file_options .= '</optgroup>';

					if(stripos($log_file_options, '<option data-type="archived"') === FALSE)
						$log_file_options .= '<option value="" disabled="disabled">— No log files archived yet. —</option>';
				}
				$log_file_options = '<option value="">— Choose a Log File to View —</option>'.
				                    '<option value="" disabled="disabled"></option>'.
				                    $log_file_options;

				echo '<form method="post" name="ws_plugin__s2member_log_viewer" id="ws-plugin--s2member-log-viewer" autocomplete="off">'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<td style="width:80%;">'."\n";
				echo '<select name="ws_plugin__s2member_log_file" id="ws-plugin--s2member-log-file">'."\n";
				echo $log_file_options."\n";
				echo '</select>'."\n";
				echo '</td>'."\n";

				echo '<td style="width:20%; padding-left:5px;">'."\n";
				echo '<input type="submit" value="View" style="font-size:120%; font-weight:normal;" />'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";

				if($view_log_file && file_exists($logs_dir."/".$view_log_file) && filesize($logs_dir."/".$view_log_file))
				{
					$_log_file_description = array("short" => "", "long" => "");

					foreach(c_ws_plugin__s2member_utils_logs::$log_file_descriptions as $_k => $_v)
						if(preg_match($_k, $view_log_file))
						{
							$_log_file_description = $_v;
							break; // Stop here.
						}
					unset($_k, $_v); // Housekeeping.

					if(!empty($_log_file_description["long"])) // Do we have a description that we can display here?
						echo '<p style="clear:both; width:80%; font-family:\'Georgia\', serif; font-style:italic;"><strong>Description for <a href="'.esc_attr(add_query_arg(array("ws_plugin__s2member_download_log_file" => $view_log_file, "ws_plugin__s2member_download_log_file_v" => wp_create_nonce("ws-plugin--s2member-download-log-file-v")))).'">'.esc_html($view_log_file).'</a></strong>: '.esc_html($_log_file_description["long"]).'</p>'."\n";
					unset($_log_file_description); // Just a little housekeeping here.

					echo '<p style="float:left; text-align:left;"><strong>Viewing:</strong> <a href="'.esc_attr(add_query_arg(array("ws_plugin__s2member_download_log_file" => $view_log_file, "ws_plugin__s2member_download_log_file_v" => wp_create_nonce("ws-plugin--s2member-download-log-file-v")))).'">'.esc_html($view_log_file).'</a> (log entries oldest to newest)</p>'."\n";
					echo '<p style="float:right; text-align:right;">[ <a href="'.esc_attr(add_query_arg(array("ws_plugin__s2member_download_log_file" => $view_log_file, "ws_plugin__s2member_download_log_file_v" => wp_create_nonce("ws-plugin--s2member-download-log-file-v")))).'"><strong>download file</strong></a> ]</p>'."\n";
					echo '<p style="margin-right:10px; float:right; text-align:right;"><a href="#" class="ws-plugin--s2member-log-file-viewport-toggle" style="text-decoration:none;">&#8659; expand viewport &#8659;</a></p>'."\n";

					echo '<textarea id="ws-plugin--s2member-log-file-viewer" rows="20" wrap="on" spellcheck="false" style="box-shadow:inset 0 0 5px rgba(0,0,0,0.5); background:#EEEEEE; color:#000000; overflow-y:scroll;">'.htmlspecialchars(file_get_contents($logs_dir."/".$view_log_file)).'</textarea>'."\n";

					echo '<p style="float:left; text-align:left;"><strong>Viewing:</strong> <a href="'.esc_attr(add_query_arg(array("ws_plugin__s2member_download_log_file" => $view_log_file, "ws_plugin__s2member_download_log_file_v" => wp_create_nonce("ws-plugin--s2member-download-log-file-v")))).'">'.esc_html($view_log_file).'</a> (log entries oldest to newest)</p>'."\n";
					echo '<p style="float:right; text-align:right;">[ <a href="'.esc_attr(add_query_arg(array("ws_plugin__s2member_download_log_file" => $view_log_file, "ws_plugin__s2member_download_log_file_v" => wp_create_nonce("ws-plugin--s2member-download-log-file-v")))).'"><strong>download file</strong></a> ]</p>'."\n";
					echo '<p style="margin-right:10px; float:right; text-align:right;"><a href="#" class="ws-plugin--s2member-log-file-viewport-toggle" style="text-decoration:none;">&#8659; expand viewport &#8659;</a></p>'."\n";
				}
				else if($view_log_file && file_exists($logs_dir."/".$view_log_file))
					echo '<textarea id="ws-plugin--s2member-log-file-viewer" rows="20" wrap="on" spellcheck="false" style="box-shadow:inset 0 0 5px rgba(0,0,0,0.5); background:#EEEEEE; color:#000000; overflow-y:scroll; font-style:italic;">— Empty at this time —</textarea>'."\n";

				else if($view_log_file && !file_exists($logs_dir."/".$view_log_file))
					echo '<textarea id="ws-plugin--s2member-log-file-viewer" rows="20" wrap="on" spellcheck="false" style="box-shadow:inset 0 0 5px rgba(0,0,0,0.5); background:#EEEEEE; color:#000000; overflow-y:scroll; font-style:italic;">— File no longer exists —</textarea>'."\n";

				else // Display an empty textarea in this default scenario.
					echo '<textarea id="ws-plugin--s2member-log-file-viewer" rows="20" wrap="on" spellcheck="false" style="box-shadow:inset 0 0 5px rgba(0,0,0,0.5); background:#EEEEEE; color:#000000; overflow-y:scroll; font-style:italic;"></textarea>'."\n";

				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '</form>'."\n";

				echo '</div>'."\n";
				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_logs_page_during_left_sections_after_logs", get_defined_vars());
			}
			do_action("ws_plugin__s2member_during_logs_page_after_left_sections", get_defined_vars());

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

new c_ws_plugin__s2member_menu_page_logs();
