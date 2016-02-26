<?php
/**
 * Log utilities.
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
 * @package s2Member\Utilities
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_utils_logs'))
{
	/**
	 * Log utilities.
	 *
	 * @package s2Member\Utilities
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_utils_logs
	{
		/**
		 * Logs an entry.
		 *
		 * @since 130315
		 * @package s2Member\Utilities
		 *
		 * @param string $slug The file name; i.e., a slug.
		 *    e.g., `mailchimp-api`, `s2-http-api-debug`.
		 *
		 * @param mixed  $data The data to log.
		 */
		public static function log_entry($slug, $data)
		{
			if(!($slug = trim((string)$slug)))
				return; // Not possible.

			if(!$data) return; // Nothing to log.

			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['gateway_debug_logs'])
				return; // Nothing to do; logging not enabled right now.

			if(!is_dir($logs_dir = $GLOBALS['WS_PLUGIN__']['s2member']['c']['logs_dir']))
				return; // Log directory does not exist at this time.

			if(!is_writable($logs_dir)) return; // Not writable.

			$logt = c_ws_plugin__s2member_utilities::time_details();
			$logv = c_ws_plugin__s2member_utilities::ver_details();
			$logm = c_ws_plugin__s2member_utilities::mem_details();

			$log4 = ''; // Initialize.
			if(is_multisite() && !is_main_site()) // Child blog in a multisite network?
				$log4 .= $GLOBALS['current_blog']->domain.$GLOBALS['current_blog']->path."\n";
			$log4 .= @$_SERVER['HTTP_HOST'].@$_SERVER['REQUEST_URI']."\n";
			$log4 .= 'User-Agent: '.@$_SERVER['HTTP_USER_AGENT'];

			$log2 = $slug.'.log'; // Initialize.
			if(is_multisite() && !is_main_site()) // Child blog in a multisite network?
				$log2 = $slug.'-4-'.trim(preg_replace('/[^a-z0-9]/i', '-', $GLOBALS['current_blog']->domain.$GLOBALS['current_blog']->path), '-').'.log';

			c_ws_plugin__s2member_utils_logs::archive_oversize_log_files();

			file_put_contents(
				$logs_dir.'/'.$log2,
			    'LOG ENTRY: '.$logt."\n".$logv."\n".$logm."\n".$log4."\n".
					c_ws_plugin__s2member_utils_logs::conceal_private_info(print_r($data, TRUE))."\n\n",
			    FILE_APPEND // Append to an existing log file; if exists.
			);
		}

		/**
		 * Logs HTTP communication (if enabled).
		 *
		 * @since 120212
		 * @package s2Member\Utilities
		 *
		 * @attaches-to `http_api_debug` hook.
		 *
		 * @param mixed $response Passed by action.
		 * @param mixed $state Passed by action.
		 * @param mixed $class Passed by action.
		 * @param mixed $args Passed by action.
		 * @param mixed $url Passed by action.
		 */
		public static function http_api_debug($response = NULL, $state = NULL, $class = NULL, $args = NULL, $url = NULL)
		{
			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['gateway_debug_logs'])
				return; // Logging is NOT enabled in this case.

			$http_api_debug = array(
				'state' => $state, 'transport_class' => $class,
				'args'  => $args, 'url' => $url, 'response' => $response
			);
			if(!empty($args['s2member']) || strpos($url, 's2member') !== FALSE)
				self::log_entry('s2-http-api-debug', $http_api_debug);

			if($GLOBALS['WS_PLUGIN__']['s2member']['o']['gateway_debug_logs_extensive'])
				self::log_entry('wp-http-api-debug', $http_api_debug);
		}

		/**
		 * Attempts to conceal private details in log entries.
		 *
		 * @since 130315
		 * @package s2Member\Utilities
		 *
		 * @param string $log_entry The log entry we need to conceal private details in.
		 *
		 * @return string Filtered string with some data X'd out :-)
		 */
		public static function conceal_private_info($log_entry)
		{
			if(!($log_entry = trim((string)$log_entry)))
				return $log_entry; // Nothing to do.

			$log_entry = preg_replace('/\b([3456][0-9]{10,11})([0-9]{4})\b/', 'xxxxxxxxxxxx'.'$2', $log_entry);

			$log_entry = preg_replace('/(\'.*pass_?(?:word)?(?:[0-9]+)?\'\s*\=\>\s*\')([^\']+)(\')/', '$1'.'xxxxxxxx/pass'.'$3', $log_entry);
			$log_entry = preg_replace('/([&?][^&]*pass_?(?:word)?(?:[0-9]+)?\=)([^&]+)/', '$1'.'xxxxxxxx/pass', $log_entry);

			$log_entry = preg_replace('/(\'api_?(?:key|secret)\'\s*\=\>\s*\')([^\']+)(\')/', '$1'.'xxxxxxxx/api/key/sec'.'$3', $log_entry);
			$log_entry = preg_replace('/([&?][^&]api_?(?:key|secret)\=)([^&]+)/', '$1'.'xxxxxxxx/api/key/sec', $log_entry);

			$log_entry = preg_replace('/(\'(?:PWD|SIGNATURE)\'\s*\=\>\s*\')([^\']+)(\')/', '$1'.'xxxxxxxx/PWD/SIG'.'$3', $log_entry);
			$log_entry = preg_replace('/([&?][^&](?:PWD|SIGNATURE)\=)([^&]+)/', '$1'.'xxxxxxxx/PWD/SIG', $log_entry);

			$log_entry = preg_replace('/(\'(?:x_login|x_tran_key)\'\s*\=\>\s*\')([^\']+)(\')/', '$1'.'xxxxxxxx/key/tran'.'$3', $log_entry);
			$log_entry = preg_replace('/([&?][^&](?:x_login|x_tran_key)\=)([^&]+)/', '$1'.'xxxxxxxx/key/tran', $log_entry);

			return $log_entry; // With some private info concealed now.
		}

		/**
		 * Archives logs to prevent HUGE files from building up over time.
		 *
		 * @since 3.5
		 * @package s2Member\Utilities
		 *
		 * @param bool $stagger Defaults to a `TRUE` value.
		 *
		 * @return bool Always returns a `TRUE` value.
		 */
		public static function archive_oversize_log_files($stagger = TRUE)
		{
			if($stagger && !is_float($stagger = time() / 2))
				return TRUE; // Bypass this time.

			if(!is_dir($dir = $GLOBALS['WS_PLUGIN__']['s2member']['c']['logs_dir']) || !is_writable($dir))
				return TRUE; // Not necessary; directory is nonexistent or not writable.

			if(!($log_files = scandir($dir)) || !shuffle($log_files))
				return TRUE; // No files; not necessary.

			$counter = 1; // Initialize archiving counter.
			$max     = apply_filters('ws_plugin__s2member_oversize_log_file_bytes', 2097152, get_defined_vars());

			foreach($log_files as $file) // Go through each log file. Up to 25 files at a time.
			{
				if(preg_match('/\.log$/i', $file) && !preg_match('/\-ARCHIVED\-/i', $file) && is_file($dir_file = $dir.'/'.$file))
					if(filesize($dir_file) > $max && is_writable($dir_file)) // The file must be writable.
						if($log = preg_replace('/\.log$/i', '', $dir_file)) // Strip .log before renaming.
							rename($dir_file, $log.'-ARCHIVED-'.date('m-d-Y').'-'.time().'.log');

				if(($counter = $counter + 1) > 25) // Up to 25 files at a time.
					break; // Stop for now.
			}
			return TRUE; // Always returns a `TRUE` value.
		}

		/**
		 * Removes expired Transients inserted into the database by s2Member.
		 *
		 * @since 3.5
		 * @package s2Member\Utilities
		 *
		 * @param bool $stagger Defaults to a `TRUE` value.
		 *
		 * @return bool Always returns a `TRUE` value.
		 */
		public static function cleanup_expired_s2m_transients($stagger = TRUE)
		{
			global $wpdb; /** @var wpdb $wpdb */

			if($stagger && !is_float($stagger = time() / 2))
				return TRUE; // Bypass this time.

			if(($expired_s2m_transients = $wpdb->get_results("SELECT * FROM `".$wpdb->options."` WHERE `option_name` LIKE '".esc_sql(c_ws_plugin__s2member_utils_strings::like_escape('_transient_timeout_s2m_'))."%' AND `option_value` < '".esc_sql(time())."' LIMIT 5")))
				foreach($expired_s2m_transients as $_expired_s2m_transient) // Delete the _timeout, and also the transient entry name itself.
					if(($_id = $_expired_s2m_transient->option_id) && ($_name = preg_replace('/_transient_timeout_/i', '_transient_', $_expired_s2m_transient->option_name, 1)))
						$wpdb->query("DELETE FROM `".$wpdb->options."` WHERE (`option_id` = '".esc_sql($_id)."' OR `option_name` = '".esc_sql($_name)."')");

			return TRUE; // Always returns a `TRUE` value.
		}

		/**
		 * Array of log file descriptions.
		 *
		 * @since 120214
		 * @package s2Member\Utilities
		 *
		 * @var array Array of log file descriptions.
		 */
		public static $log_file_descriptions = array
		( // Array keys are regex patterns matching their associated log file names.
		  '/gateway\-core\-ipn/'   => array('short' => 'Core IPN and post-processing handler.', 'long' => 'This log file records all communication between s2Member and the IPN/Webhook/Callback services associated with various payment gateways. All transactions pass through s2Member\'s core processor, and they will be logged in this file; including transactions processed via s2Member Pro-Forms—for all Payment Gateway integrations.'),
		  '/gateway\-core\-rtn/'   => array('short' => 'Core PDT/Auto-Return communication.', 'long' => 'This log file records all communication between s2Member and the PDT/Auto-Return/Thank-You services associated with various payment gateways (i.e., routines that help s2Member process Thank-You pages). This log file is not used in s2Member Pro-Form integrations however.'),

		  '/stripe\-api/'          => array('short' => 'Stripe API communication.', 'long' => 'This log file records all communication between s2Member and Stripe APIs. See also: gateway-core-ipn.log (s2Member\'s core processor).'),
		  '/stripe\-ipn/'          => array('short' => 'Stripe Webhook/IPN communication.', 'long' => 'This log file records the Webhook/IPN data that Stripe sends to s2Member. See also: gateway-core-ipn.log (s2Member\'s core processor).'),

		  '/paypal\-api/'          => array('short' => 'PayPal API communication.', 'long' => 'This log file records all communication between s2Member and PayPal APIs. Such as PayPal Button Encryption and PayPal Pro API calls that process transactions. This log file may be used (in some scenarios), even if you\'re running a PayPal Payments Pro (Payflow Edition) account. See also: gateway-core-ipn.log (s2Member\'s core processor).'),
		  '/paypal\-payflow\-api/' => array('short' => 'PayPal Pro (PayFlow Edition) API communication.', 'long' => 'This log file records all communication between s2Member and the PayPal Payments Pro (PayFlow Edition) APIs. This log file is only used if you operate a PayPal Payments Pro (PayFlow Edition) account; i.e., only if you integrate s2Member Pro with Payflow for Recurring Billing. See also: gateway-core-ipn.log (s2Member\'s core processor).'),

		  '/authnet\-api/'         => array('short' => 'Authorize.Net API communication.', 'long' => 'This log file records all communication between s2Member and Authorize.Net APIs (for both AIM and ARB integrations). See also: gateway-core-ipn.log (s2Member\'s core processor).'),
		  '/authnet\-arb/'         => array('short' => 'Authorize.Net ARB Subscription status checks.', 'long' => 'This log file records s2Member\'s Authorize.Net ARB Subscription status checks. s2Member polls the ARB service periodically to check the status of existing Members (e.g., to see if billing is still active or not).'),
		  '/authnet\-ipn/'         => array('short' => 'Authorize.Net Silent Post/IPN communication.', 'long' => 'This log file records the Silent Post/IPN data Authorize.Net sends to s2Member with details regarding new transactions. See also: gateway-core-ipn.log (s2Member\'s core processor).'),

		  '/alipay\-ipn/'          => array('short' => 'AliPay IPN communication.', 'long' => 'This log file records the IPN data AliPay sends to s2Member with details regarding new transactions. See also: gateway-core-ipn.log (s2Member\'s core processor).'),
		  '/alipay\-rtn/'          => array('short' => 'AliPay Auto-Return communication.', 'long' => 'This log file records the Auto-Return data AliPay sends to s2Member with details regarding new transactions (i.e., logs routines that help s2Member process Thank-You pages). See also: gateway-core-rtn.log (s2Member\'s core processor).'),

		  '/clickbank\-ipn/'       => array('short' => 'ClickBank IPN communication.', 'long' => 'This log file records the IPN data ClickBank sends to s2Member with details regarding new transactions, cancellations, expirations, etc. See also: gateway-core-ipn.log (s2Member\'s core processor).'),
		  '/clickbank\-rtn/'       => array('short' => 'ClickBank Auto-Return communication.', 'long' => 'This log file records the Auto-Return data ClickBank sends to s2Member with details regarding new transactions (i.e., logs routines that help s2Member process Thank-You pages). See also: gateway-core-rtn.log (s2Member\'s core processor).'),

		  '/google\-rtn/'          => array('short' => 'Google Auto-Return communication.', 'long' => 'This log file records the Auto-Return data Google sends to s2Member with details regarding new transactions (i.e., logs routines that help s2Member process Thank-You pages). See also: gateway-core-rtn.log (s2Member\'s core processor). NOTE (regarding Google Wallet)... this particular log file is currently implemented for a possible future use ONLY. At this time there is no need for an Auto-Return handler with Google Wallet, because Google Wallet return handling is done via email-only at this time.'),
		  '/google\-ipn/'          => array('short' => 'Google Postback/IPN communication.', 'long' => 'This log file records the Postback/IPN data Google sends to s2Member with details regarding new transactions, cancellations, expirations, etc. See also: gateway-core-ipn.log (s2Member\'s core processor).'),

		  '/ccbill\-ipn/'          => array('short' => 'ccBill Bg Post/IPN communication.', 'long' => 'This log file records the Bg Post/IPN data ccBill sends to s2Member with details regarding new transactions. See also: gateway-core-ipn.log (s2Member\'s core processor).'),
		  '/ccbill\-rtn/'          => array('short' => 'ccBill Auto-Return communication.', 'long' => 'This log file records the Auto-Return data ccBill sends to s2Member with details regarding new transactions (i.e., logs routines that help s2Member process Thank-You pages). See also: gateway-core-rtn.log (s2Member\'s core processor).'),
		  '/ccbill\-dl\-ipn/'      => array('short' => 'ccBill Datalink Subscription status checks.', 'long' => 'This log file records s2Member\'s ccBill Datalink Subscription status checks that may result in actions taken by s2Member. s2Member polls the ccBill Datalink service periodically to check the status of existing Members (e.g., to see if billing is still active or not).'),
		  '/ccbill\-dl/'           => array('short' => 'ccBill Datalink collections.', 'long' => 'This log file records s2Member\'s ccBill Datalink connections. s2Member polls the ccBill Datalink service periodically to obtain information about existing Users/Members.'),

		  '/mailchimp\-api/'       => array('short' => 'MailChimp API communication.', 'long' => 'This log file records all of s2Member\'s communication with MailChimp APIs.'),
		  '/aweber\-api/'          => array('short' => 'AWeber API communication.', 'long' => 'This log file records all of s2Member\'s communication with AWeber APIs.'),
		  '/getresponse\-api/'     => array('short' => 'GetResponse API communication.', 'long' => 'This log file records all of s2Member\'s communication with GetResponse APIs.'),

		  '/reg\-handler/'         => array('short' => 'User registrations processed by s2Member.', 'long' => 'This log file records all User/Member registrations processed by s2Member (either directly or indirectly). This includes both free and paid registrations. It also logs registrations that occur as a result of new Users/Members being created from the Dashboard by a site owner. It also includes registrations that occur through the s2Member Pro Remote Operations API.'),

		  '/s2\-http\-api\-debug/' => array('short' => 'All outgoing HTTP connections related to s2Member.', 'long' => 'This log file records all outgoing WP_Http connections that are specifically related to s2Member. This log file can be extremely helpful. It includes technical details about remote HTTP connections that are not available in other log files.'),
		  '/wp\-http\-api\-debug/' => array('short' => 'All outgoing WordPress HTTP connections.', 'long' => 'This log file records all outgoing HTTP connections processed by the WP_Http class. This includes everything processed by WordPress; even things unrelated to s2Member. This log file can be extremely helpful. It includes technical details about remote HTTP connections that are not available in other log files.'),

		  '/auto\-eot\-system/'    => array('short' => 'EOTs processed via CRON job.', 'long' => 'This log file records all EOTs processed by the WP_Cron job that powers the s2Member Auto-EOT System. Once a customer has an EOT Time, the CRON job comes in and actually handles a demotion or deletion (based on your configuration). That is what this log file shows; i.e., the actual demotion or deletion taking place.'),
		  '/eot\-reminders/'       => array('short' => 'EOT reminder emails processed via CRON job.', 'long' => 'This log file records all EOT reminder emails processed by the WP_Cron job that powers the s2Member Auto-EOT System. EOT Renewal/Reminder Email notifications are available only in the pro version of s2Member.'),
		);
	}
}
