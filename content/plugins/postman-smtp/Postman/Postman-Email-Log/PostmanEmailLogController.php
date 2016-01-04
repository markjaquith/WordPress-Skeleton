<?php
require_once 'PostmanEmailLogService.php';
require_once 'PostmanEmailLogView.php';

/**
 *
 * @author jasonhendriks
 *        
 */
class PostmanEmailLogController {
	const RESEND_MAIL_AJAX_SLUG = 'postman_resend_mail';
	private $rootPluginFilenameAndPath;
	private $logger;
	
	/**
	 */
	function __construct($rootPluginFilenameAndPath) {
		$this->rootPluginFilenameAndPath = $rootPluginFilenameAndPath;
		$this->logger = new PostmanLogger ( get_class ( $this ) );
		if (PostmanOptions::getInstance ()->isMailLoggingEnabled ()) {
			add_action ( 'admin_menu', array (
					$this,
					'postmanAddMenuItem' 
			) );
		} else {
			$this->logger->trace ( 'not creating PostmanEmailLog admin menu item' );
		}
		if (PostmanUtils::isCurrentPagePostmanAdmin ( 'postman_email_log' )) {
			$this->logger->trace ( 'on postman email log page' );
			// $this->logger->debug ( 'Registering ' . $actionName . ' Action Post handler' );
			add_action ( 'admin_post_delete', array (
					$this,
					'delete_log_item' 
			) );
			add_action ( 'admin_post_view', array (
					$this,
					'view_log_item' 
			) );
			add_action ( 'admin_post_transcript', array (
					$this,
					'view_transcript_log_item' 
			) );
			add_action ( 'admin_init', array (
					$this,
					'on_admin_init' 
			) );
		}
		if (is_admin ()) {
			$actionName = self::RESEND_MAIL_AJAX_SLUG;
			$fullname = 'wp_ajax_' . $actionName;
			// $this->logger->debug ( 'Registering ' . 'wp_ajax_' . $fullname . ' Ajax handler' );
			add_action ( $fullname, array (
					$this,
					'resendMail' 
			) );
		}
	}
	
	/**
	 */
	function on_admin_init() {
		$this->handleBulkAction ();
		// register the stylesheet and javascript external resources
		$pluginData = apply_filters ( 'postman_get_plugin_metadata', null );
		wp_register_script ( 'postman_resend_email_script', plugins_url ( 'script/postman_resend_email_sript.js', $this->rootPluginFilenameAndPath ), array (
				PostmanViewController::JQUERY_SCRIPT,
				PostmanViewController::POSTMAN_SCRIPT 
		), $pluginData ['version'] );
	}
	
	/**
	 */
	public function resendMail() {
		// get the email address of the recipient from the HTTP Request
		$postid = $this->getRequestParameter ( 'email' );
		if (! empty ( $postid )) {
			$post = get_post ( $postid );
			$meta_values = get_post_meta ( $postid );
			
			$success = wp_mail ( $meta_values ['original_to'] [0], $meta_values ['original_subject'] [0], $meta_values ['original_message'] [0], $meta_values ['original_headers'] [0] );
			
			// Postman API: retrieve the result of sending this message from Postman
			$result = apply_filters ( 'postman_wp_mail_result', null );
			$transcript = $result ['transcript'];
			
			// post-handling
			if ($success) {
				$this->logger->debug ( 'Email was successfully re-sent' );
				// the message was sent successfully, generate an appropriate message for the user
				$statusMessage = sprintf ( __ ( 'Your message was delivered (%d ms) to the SMTP server! Congratulations :)', Postman::TEXT_DOMAIN ), $result ['time'] );
				
				// compose the JSON response for the caller
				$response = array (
						'message' => $statusMessage,
						'transcript' => $transcript 
				);
				$this->logger->trace ( 'AJAX response' );
				$this->logger->trace ( $response );
				// send the JSON response
				wp_send_json_success ( $response );
			} else {
				$this->logger->error ( 'Email was not successfully re-sent - ' . $result ['exception']->getCode () );
				// the message was NOT sent successfully, generate an appropriate message for the user
				$statusMessage = $result ['exception']->getMessage ();
				
				// compose the JSON response for the caller
				$response = array (
						'message' => $statusMessage,
						'transcript' => $transcript 
				);
				$this->logger->trace ( 'AJAX response' );
				$this->logger->trace ( $response );
				// send the JSON response
				wp_send_json_error ( $response );
			}
		} else {
			// compose the JSON response for the caller
			$response = array ();
			// send the JSON response
			wp_send_json_error ( $response );
		}
	}
	
	/**
	 * TODO move this somewhere reusable
	 *
	 * @param unknown $parameterName        	
	 * @return unknown
	 */
	private function getRequestParameter($parameterName) {
		if (isset ( $_POST [$parameterName] )) {
			$value = $_POST [$parameterName];
			$this->logger->trace ( sprintf ( 'Found parameter "%s"', $parameterName ) );
			$this->logger->trace ( $value );
			return $value;
		}
	}
	
	/**
	 * From https://www.skyverge.com/blog/add-custom-bulk-action/
	 */
	function handleBulkAction() {
		// only do this for administrators
		if (PostmanUtils::isAdmin () && isset ( $_REQUEST ['email_log_entry'] )) {
			$this->logger->trace ( 'handling bulk action' );
			if (wp_verify_nonce ( $_REQUEST ['_wpnonce'], 'bulk-email_log_entries' )) {
				$this->logger->trace ( sprintf ( 'nonce "%s" passed validation', $_REQUEST ['_wpnonce'] ) );
				if (isset ( $_REQUEST ['action'] ) && ($_REQUEST ['action'] == 'bulk_delete' || $_REQUEST ['action2'] == 'bulk_delete')) {
					$this->logger->trace ( sprintf ( 'handling bulk delete' ) );
					$purger = new PostmanEmailLogPurger ();
					$postids = $_REQUEST ['email_log_entry'];
					foreach ( $postids as $postid ) {
						$purger->verifyLogItemExistsAndRemove ( $postid );
					}
					$mh = new PostmanMessageHandler ();
					$mh->addMessage ( __ ( 'Mail Log Entries were deleted.', Postman::TEXT_DOMAIN ) );
				} else {
					$this->logger->warn ( sprintf ( 'action "%s" not recognized', $_REQUEST ['action'] ) );
				}
			} else {
				$this->logger->warn ( sprintf ( 'nonce "%s" failed validation', $_REQUEST ['_wpnonce'] ) );
			}
			$this->redirectToLogPage ();
		}
	}
	
	/**
	 */
	function delete_log_item() {
		// only do this for administrators
		if (PostmanUtils::isAdmin ()) {
			$this->logger->trace ( 'handling delete item' );
			$postid = $_REQUEST ['email'];
			if (wp_verify_nonce ( $_REQUEST ['_wpnonce'], 'delete_email_log_item_' . $postid )) {
				$this->logger->trace ( sprintf ( 'nonce "%s" passed validation', $_REQUEST ['_wpnonce'] ) );
				$purger = new PostmanEmailLogPurger ();
				$purger->verifyLogItemExistsAndRemove ( $postid );
				$mh = new PostmanMessageHandler ();
				$mh->addMessage ( __ ( 'Mail Log Entry was deleted.', Postman::TEXT_DOMAIN ) );
			} else {
				$this->logger->warn ( sprintf ( 'nonce "%s" failed validation', $_REQUEST ['_wpnonce'] ) );
			}
			$this->redirectToLogPage ();
		}
	}
	
	/**
	 */
	function view_log_item() {
		// only do this for administrators
		if (PostmanUtils::isAdmin ()) {
			$this->logger->trace ( 'handling view item' );
			$postid = $_REQUEST ['email'];
			$post = get_post ( $postid );
			$meta_values = get_post_meta ( $postid );
			// https://css-tricks.com/examples/hrs/
			print '<html><head><style>body {font-family: monospace;} hr {
    border: 0;
    border-bottom: 1px dashed #ccc;
    background: #bbb;
}</style></head><body>';
			print '<table>';
			if (! empty ( $meta_values ['from_header'] [0] )) {
				printf ( '<tr><th style="text-align:right">%s:</th><td>%s</td></tr>', _x ( 'From', 'Who is this message From?', Postman::TEXT_DOMAIN ), esc_html ( $meta_values ['from_header'] [0] ) );
			}
			// show the To header (it's optional)
			if (! empty ( $meta_values ['to_header'] [0] )) {
				printf ( '<tr><th style="text-align:right">%s:</th><td>%s</td></tr>', _x ( 'To', 'Who is this message To?', Postman::TEXT_DOMAIN ), esc_html ( $meta_values ['to_header'] [0] ) );
			}
			// show the Cc header (it's optional)
			if (! empty ( $meta_values ['cc_header'] [0] )) {
				printf ( '<tr><th style="text-align:right">%s:</th><td>%s</td></tr>', _x ( 'Cc', 'Who is this message Cc\'d to?', Postman::TEXT_DOMAIN ), esc_html ( $meta_values ['cc_header'] [0] ) );
			}
			// show the Bcc header (it's optional)
			if (! empty ( $meta_values ['bcc_header'] [0] )) {
				printf ( '<tr><th style="text-align:right">%s:</th><td>%s</td></tr>', _x ( 'Bcc', 'Who is this message Bcc\'d to?', Postman::TEXT_DOMAIN ), esc_html ( $meta_values ['bcc_header'] [0] ) );
			}
			// show the Reply-To header (it's optional)
			if (! empty ( $meta_values ['reply_to_header'] [0] )) {
				printf ( '<tr><th style="text-align:right">%s:</th><td>%s</td></tr>', __ ( 'Reply-To', Postman::TEXT_DOMAIN ), esc_html ( $meta_values ['reply_to_header'] [0] ) );
			}
			printf ( '<tr><th style="text-align:right">%s:</th><td>%s</td></tr>', _x ( 'Date', 'What is the date today?', Postman::TEXT_DOMAIN ), $post->post_date );
			printf ( '<tr><th style="text-align:right">%s:</th><td>%s</td></tr>', _x ( 'Subject', 'What is the subject of this message?', Postman::TEXT_DOMAIN ), esc_html ( $post->post_title ) );
			// The Transport UI is always there, in more recent versions that is
			if (! empty ( $meta_values ['transport_uri'] [0] )) {
				printf ( '<tr><th style="text-align:right">%s:</th><td>%s</td></tr>', _x ( 'Delivery-URI', 'What is the unique URI of the configuration?', Postman::TEXT_DOMAIN ), esc_html ( $meta_values ['transport_uri'] [0] ) );
			}
			print '</table>';
			print '<hr/>';
			print '<pre>';
			print esc_html ( $post->post_content );
			print '</pre>';
			print '</body></html>';
			die ();
		}
	}
	
	/**
	 */
	function view_transcript_log_item() {
		// only do this for administrators
		if (PostmanUtils::isAdmin ()) {
			$this->logger->trace ( 'handling view transcript item' );
			$postid = $_REQUEST ['email'];
			$post = get_post ( $postid );
			$meta_values = get_post_meta ( $postid );
			// https://css-tricks.com/examples/hrs/
			print '<html><head><style>body {font-family: monospace;} hr {
    border: 0;
    border-bottom: 1px dashed #ccc;
    background: #bbb;
}</style></head><body>';
			printf ( '<p>%s</p>', __ ( 'This is the conversation between Postman and the mail server. It can be useful for diagnosing problems. <b>DO NOT</b> post it on-line, it may contain your account password.', Postman::TEXT_DOMAIN ) );
			print '<hr/>';
			print '<pre>';
			if (! empty ( $meta_values ['session_transcript'] [0] )) {
				print esc_html ( $meta_values ['session_transcript'] [0] );
			} else {
				/* Translators: Meaning "Not Applicable" */
				print __ ( 'n/a', Postman::TEXT_DOMAIN );
			}
			print '</pre>';
			print '</body></html>';
			die ();
		}
	}
	
	/**
	 * For whatever reason, PostmanUtils::get..url doesn't work here? :(
	 */
	function redirectToLogPage() {
		PostmanUtils::redirect ( PostmanUtils::POSTMAN_EMAIL_LOG_PAGE_RELATIVE_URL );
		die ();
	}
	
	/**
	 * Register the page
	 */
	function postmanAddMenuItem() {
		// only do this for administrators
		if (PostmanUtils::isAdmin ()) {
			$this->logger->trace ( 'created PostmanEmailLog admin menu item' );
			/* Translators where (%s) is the name of the plugin */
			$page = add_management_page ( sprintf ( __ ( '%s Email Log', Postman::TEXT_DOMAIN ), __ ( 'Postman SMTP', Postman::TEXT_DOMAIN ) ), _x ( 'Email Log', 'The log of Emails that have been delivered', Postman::TEXT_DOMAIN ), 'read_private_posts', 'postman_email_log', array (
					$this,
					'postman_render_email_page' 
			) );
			// When the plugin options page is loaded, also load the stylesheet
			add_action ( 'admin_print_styles-' . $page, array (
					$this,
					'postman_email_log_enqueue_resources' 
			) );
		}
	}
	function postman_email_log_enqueue_resources() {
		$pluginData = apply_filters ( 'postman_get_plugin_metadata', null );
		wp_register_style ( 'postman_email_log', plugins_url ( 'style/postman-email-log.css', $this->rootPluginFilenameAndPath ), null, $pluginData ['version'] );
		wp_enqueue_style ( 'postman_email_log' );
		wp_enqueue_script ( 'postman_resend_email_script' );
		wp_enqueue_script ( 'sprintf' );
		wp_localize_script ( 'postman_resend_email_script', 'postman_js_email_was_resent', __ ( 'Email was successfully resent (but without attachments)', Postman::TEXT_DOMAIN ) );
		/* Translators: Where %s is an error message */
		wp_localize_script ( 'postman_resend_email_script', 'postman_js_email_not_resent', __ ( 'Email could not be resent. Error: %s', Postman::TEXT_DOMAIN ) );
		wp_localize_script ( 'postman_resend_email_script', 'postman_js_resend_label', __ ( 'Resend', Postman::TEXT_DOMAIN ) );
	}
	
	/**
	 * *************************** RENDER TEST PAGE ********************************
	 * ******************************************************************************
	 * This function renders the admin page and the example list table.
	 * Although it's
	 * possible to call prepare_items() and display() from the constructor, there
	 * are often times where you may need to include logic here between those steps,
	 * so we've instead called those methods explicitly. It keeps things flexible, and
	 * it's the way the list tables are used in the WordPress core.
	 */
	function postman_render_email_page() {
		
		// Create an instance of our package class...
		$testListTable = new PostmanEmailLogView ();
		wp_enqueue_script ( 'postman_resend_email_script' );
		// Fetch, prepare, sort, and filter our data...
		$testListTable->prepare_items ();
		
		?>
<div class="wrap">

	<div id="icon-users" class="icon32">
		<br />
	</div>
	<h2><?php 
/* Translators where (%s) is the name of the plugin */
		echo sprintf ( __ ( '%s Email Log', Postman::TEXT_DOMAIN ), __ ( 'Postman SMTP', Postman::TEXT_DOMAIN ) )?></h2>

	<div
		style="background: #ECECEC; border: 1px solid #CCC; padding: 0 10px; margin-top: 5px; border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px;">
		<p><?php
		
		echo __ ( 'This is a record of deliveries made to the mail server. It does not neccessarily indicate sucessful delivery to the recipient.', Postman::TEXT_DOMAIN )?></p>
	</div>

	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="movies-filter" method="get">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
		<input type="hidden" name="page"
			value="<?php echo $_REQUEST['page'] ?>" />
		<!-- Now we can render the completed list table -->
            <?php $testListTable->display()?>
        </form>
        
        <?php add_thickbox(); ?>

</div>
<?php
	}
}
