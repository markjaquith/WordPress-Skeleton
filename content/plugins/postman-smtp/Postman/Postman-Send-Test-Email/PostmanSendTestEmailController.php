<?php
class PostmanSendTestEmailController {
	const EMAIL_TEST_SLUG = 'postman/email_test';
	const RECIPIENT_EMAIL_FIELD_NAME = 'postman_recipient_email';
	
	// logging
	private $logger;
	private $options;
	
	// Holds the values to be used in the fields callbacks
	private $rootPluginFilenameAndPath;
	
	/**
	 * Constructor
	 *
	 * @param unknown $rootPluginFilenameAndPath        	
	 */
	public function __construct($rootPluginFilenameAndPath) {
		assert ( ! empty ( $rootPluginFilenameAndPath ) );
		assert ( PostmanUtils::isAdmin () );
		assert ( is_admin () );
		
		$this->logger = new PostmanLogger ( get_class ( $this ) );
		$this->rootPluginFilenameAndPath = $rootPluginFilenameAndPath;
		$this->options = PostmanOptions::getInstance ();
		
		PostmanUtils::registerAdminMenu ( $this, 'addEmailTestSubmenu' );
		
		// hook on the init event
		add_action ( 'init', array (
				$this,
				'on_init' 
		) );
		
		// initialize the scripts, stylesheets and form fields
		add_action ( 'admin_init', array (
				$this,
				'on_admin_init' 
		) );
	}
	
	/**
	 * Functions to execute on the init event
	 *
	 * "Typically used by plugins to initialize. The current user is already authenticated by this time."
	 * ref: http://codex.wordpress.org/Plugin_API/Action_Reference#Actions_Run_During_a_Typical_Request
	 */
	public function on_init() {
		// register Ajax handlers
		new PostmanSendTestEmailAjaxController ();
	}
	
	/**
	 * Fires on the admin_init method
	 */
	public function on_admin_init() {
		//
		$this->registerStylesAndScripts ();
	}
	
	/**
	 * Get the settings option array and print one of its values
	 */
	public function test_email_callback() {
		printf ( '<input type="text" id="%s" name="postman_test_options[test_email]" value="%s" class="required email" size="40"/>', self::RECIPIENT_EMAIL_FIELD_NAME, wp_get_current_user ()->user_email );
	}
	
	/**
	 * Register and add settings
	 */
	private function registerStylesAndScripts() {
		if ($this->logger->isTrace ()) {
			$this->logger->trace ( 'registerStylesAndScripts()' );
		}
		
		//
		$pluginData = apply_filters ( 'postman_get_plugin_metadata', null );
		
		// register the stylesheet resource
		wp_register_style ( 'postman_send_test_email', plugins_url ( 'Postman/Postman-Send-Test-Email/postman_send_test_email.css', $this->rootPluginFilenameAndPath ), PostmanViewController::POSTMAN_STYLE, $pluginData ['version'] );
		
		// register the javascript resource
		wp_register_script ( 'postman_test_email_wizard_script', plugins_url ( 'Postman/Postman-Send-Test-Email/postman_send_test_email.js', $this->rootPluginFilenameAndPath ), array (
				PostmanViewController::JQUERY_SCRIPT,
				'jquery_validation',
				'jquery_steps_script',
				PostmanViewController::POSTMAN_SCRIPT 
		), $pluginData ['version'] );
	}
	
	/**
	 * Register the Email Test screen
	 */
	public function addEmailTestSubmenu() {
		$page = add_submenu_page ( null, sprintf ( __ ( '%s Setup', Postman::TEXT_DOMAIN ), __ ( 'Postman SMTP', Postman::TEXT_DOMAIN ) ), __ ( 'Postman SMTP', Postman::TEXT_DOMAIN ), Postman::MANAGE_POSTMAN_CAPABILITY_NAME, PostmanSendTestEmailController::EMAIL_TEST_SLUG, array (
				$this,
				'outputTestEmailContent' 
		) );
		// When the plugin options page is loaded, also load the stylesheet
		add_action ( 'admin_print_styles-' . $page, array (
				$this,
				'enqueueEmailTestResources' 
		) );
	}
	
	/**
	 */
	function enqueueEmailTestResources() {
		wp_enqueue_style ( 'jquery_steps_style' );
		wp_enqueue_style ( PostmanViewController::POSTMAN_STYLE );
		wp_enqueue_style ( 'postman_send_test_email' );
		wp_enqueue_script ( 'postman_test_email_wizard_script' );
		wp_localize_script ( PostmanViewController::POSTMAN_SCRIPT, 'postman_email_test', array (
				'recipient' => '#' . self::RECIPIENT_EMAIL_FIELD_NAME,
				'not_started' => _x ( 'In Outbox', 'Email Test Status', Postman::TEXT_DOMAIN ),
				'sending' => _x ( 'Sending...', 'Email Test Status', Postman::TEXT_DOMAIN ),
				'success' => _x ( 'Success', 'Email Test Status', Postman::TEXT_DOMAIN ),
				'failed' => _x ( 'Failed', 'Email Test Status', Postman::TEXT_DOMAIN ),
				'ajax_error' => __ ( 'Ajax Error', Postman::TEXT_DOMAIN ) 
		) );
	}
	
	/**
	 */
	public function outputTestEmailContent() {
		print '<div class="wrap">';
		
		PostmanViewController::outputChildPageHeader ( __ ( 'Send a Test Email', Postman::TEXT_DOMAIN ) );
		
		printf ( '<form id="postman_test_email_wizard" method="post" action="%s">', PostmanUtils::getSettingsPageUrl () );
		
		// Step 1
		printf ( '<h5>%s</h5>', __ ( 'Specify the Recipient', Postman::TEXT_DOMAIN ) );
		print '<fieldset>';
		printf ( '<legend>%s</legend>', __ ( 'Who is this message going to?', Postman::TEXT_DOMAIN ) );
		printf ( '<p>%s', __ ( 'This utility allows you to send an email message for testing.', Postman::TEXT_DOMAIN ) );
		print ' ';
		/* translators: where %d is an amount of time, in seconds */
		printf ( '%s</p>', sprintf ( _n ( 'If there is a problem, Postman will give up after %d second.', 'If there is a problem, Postman will give up after %d seconds.', $this->options->getReadTimeout (), Postman::TEXT_DOMAIN ), $this->options->getReadTimeout () ) );
		printf ( '<label for="postman_test_options[test_email]">%s</label>', _x ( 'Recipient Email Address', 'Configuration Input Field', Postman::TEXT_DOMAIN ) );
		print $this->test_email_callback ();
		print '</fieldset>';
		
		// Step 2
		printf ( '<h5>%s</h5>', __ ( 'Send The Message', Postman::TEXT_DOMAIN ) );
		print '<fieldset>';
		print '<legend>';
		print __ ( 'Sending the message:', Postman::TEXT_DOMAIN );
		printf ( ' <span id="postman_test_message_status">%s</span>', _x ( 'In Outbox', 'Email Test Status', Postman::TEXT_DOMAIN ) );
		print '</legend>';
		print '<section>';
		printf ( '<p><label>%s</label></p>', __ ( 'Status', Postman::TEXT_DOMAIN ) );
		print '<textarea id="postman_test_message_error_message" readonly="readonly" cols="65" rows="4"></textarea>';
		print '</section>';
		print '</fieldset>';
		
		// Step 3
		printf ( '<h5>%s</h5>', __ ( 'Session Transcript', Postman::TEXT_DOMAIN ) );
		print '<fieldset>';
		printf ( '<legend>%s</legend>', __ ( 'Examine the Session Transcript if you need to.', Postman::TEXT_DOMAIN ) );
		printf ( '<p>%s</p>', __ ( 'This is the conversation between Postman and the mail server. It can be useful for diagnosing problems. <b>DO NOT</b> post it on-line, it may contain your account password.', Postman::TEXT_DOMAIN ) );
		print '<section>';
		printf ( '<p><label for="postman_test_message_transcript">%s</label></p>', __ ( 'Session Transcript', Postman::TEXT_DOMAIN ) );
		print '<textarea readonly="readonly" id="postman_test_message_transcript" cols="65" rows="8"></textarea>';
		print '</section>';
		print '</fieldset>';
		
		print '</form>';
		print '</div>';
	}
}

/**
 *
 * @author jasonhendriks
 *        
 */
class PostmanSendTestEmailAjaxController extends PostmanAbstractAjaxHandler {
	
	/**
	 * Constructor
	 *
	 * @param PostmanOptions $options        	
	 * @param PostmanOAuthToken $authorizationToken        	
	 * @param PostmanConfigTextHelper $oauthScribe        	
	 */
	function __construct() {
		parent::__construct ();
		PostmanUtils::registerAjaxHandler ( 'postman_send_test_email', $this, 'sendTestEmailViaAjax' );
	}
	
	/**
	 * Yes, this procedure is just for testing.
	 *
	 * @return boolean
	 */
	function test_mode() {
		return true;
	}
	
	/**
	 * This Ajax sends a test email
	 */
	function sendTestEmailViaAjax() {
		// get the email address of the recipient from the HTTP Request
		$email = $this->getRequestParameter ( 'email' );
		
		// get the name of the server from the HTTP Request
		$serverName = PostmanUtils::postmanGetServerName ();
		
		/* translators: where %s is the domain name of the site */
		$subject = sprintf ( _x ( 'Postman SMTP Test (%s)', 'Test Email Subject', Postman::TEXT_DOMAIN ), $serverName );
		
		// Postman API: indicate to Postman this is just for testing
		add_filter ( 'postman_test_email', array (
				$this,
				'test_mode' 
		) );
		
		// this header specifies that there are many parts (one text part, one html part)
		$header = 'Content-Type: multipart/alternative;';
		
		// createt the message content
		$message = $this->createMessageContent ();
		
		// send the message
		$success = wp_mail ( $email, $subject, $message, $header );
		
		// Postman API: remove the testing indicator
		remove_filter ( 'postman_test_email', array (
				$this,
				'test_mode' 
		) );
		
		// Postman API: retrieve the result of sending this message from Postman
		$result = apply_filters ( 'postman_wp_mail_result', null );
		
		// post-handling
		if ($success) {
			$this->logger->debug ( 'Test Email delivered to server' );
			// the message was sent successfully, generate an appropriate message for the user
			$statusMessage = sprintf ( __ ( 'Your message was delivered (%d ms) to the SMTP server! Congratulations :)', Postman::TEXT_DOMAIN ), $result ['time'] );
			
			//
			$this->logger->debug ( 'statusmessage: ' . $statusMessage );
			
			// compose the JSON response for the caller
			$response = array (
					'message' => $statusMessage,
					'transcript' => $result ['transcript'] 
			);
			
			// log the response
			if ($this->logger->isTrace ()) {
				$this->logger->trace ( 'Ajax Response:' );
				$this->logger->trace ( $response );
			}
			
			// send the JSON response
			wp_send_json_success ( $response );
		} else {
			$this->logger->error ( 'Test Email NOT delivered to server - ' . $result ['exception']->getCode () );
			// the message was NOT sent successfully, generate an appropriate message for the user
			$statusMessage = $result ['exception']->getMessage ();
			
			//
			$this->logger->debug ( 'statusmessage: ' . $statusMessage );
			
			// compose the JSON response for the caller
			$response = array (
					'message' => $statusMessage,
					'transcript' => $result ['transcript'] 
			);
			
			// log the response
			if ($this->logger->isTrace ()) {
				$this->logger->trace ( 'Ajax Response:' );
				$this->logger->trace ( $response );
			}
			
			// send the JSON response
			wp_send_json_error ( $response );
		}
	}
	
	/**
	 * Create the multipart message content
	 *
	 * @return string
	 */
	private function createMessageContent() {
		// Postman API: Get the plugin metadata
		$pluginData = apply_filters ( 'postman_get_plugin_metadata', null );
		
		/* translators: where %s is the Postman plugin version number (e.g. 1.4) */
		// English - Mandarin - French - Hindi - Spanish - Portuguese - Russian - Japanese
		// http://www.pinyin.info/tools/converter/chars2uninumbers.html
		$greeting = 'Hello! - &#20320;&#22909; - Bonjour! - &#2344;&#2350;&#2360;&#2381;&#2340;&#2375; - ¡Hola! - Ol&#225; - &#1055;&#1088;&#1080;&#1074;&#1077;&#1090;! - &#20170;&#26085;&#12399;';
		$sentBy = sprintf ( _x ( 'Sent by Postman %s', 'Test Email Tagline', Postman::TEXT_DOMAIN ), $pluginData ['version'] );
		$imageSource = __ ( 'Image source', Postman::TEXT_DOMAIN );
		$withPermission = __ ( 'Used with permission', Postman::TEXT_DOMAIN );
		$messageArray = array (
				'Content-Type: text/plain; charset = "UTF-8"',
				'Content-Transfer-Encoding: 8bit',
				'',
				'Hello!',
				'',
				sprintf ( '%s - https://wordpress.org/plugins/postman-smtp/', $sentBy ),
				'',
				'Content-Type: text/html; charset=UTF-8',
				'Content-Transfer-Encoding: quoted-printable',
				'',
				'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
				'<html xmlns="http://www.w3.org/1999/xhtml">',
				'<head>',
				'<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />',
				'<style type="text/css" media="all">',
				'.wporg-notification .im {',
				'	color: #888;',
				'} /* undo a GMail-inserted style */',
				'</style>',
				'</head>',
				'<body class="wporg-notification">',
				'	<div style="background: #e8f6fe; font-family: &amp; quot; Helvetica Neue&amp;quot; , Helvetica ,Arial,sans-serif; font-size: 14px; color: #666; text-align: center; margin: 0; padding: 0">',
				'		<table border="0" cellspacing="0" cellpadding="0" bgcolor="#e8f6fe"	style="background: #e8f6fe; width: 100%;">',
				'			<tbody>',
				'				<tr>',
				'					<td>',
				'						<table border="0" cellspacing="0" cellpadding="0" align="center" style="padding: 0px; width: 100%;"">',
				'							<tbody>',
				'								<tr>',
				'									<td>',
				'										<div style="max-width: 600px; height: 400px; margin: 0 auto; overflow: hidden;background-image:url(\'https://ps.w.org/postman-smtp/assets/email/poofytoo.png\');background-repeat: no-repeat;">',
				sprintf ( '											<div style="margin:50px 0 0 300px; width:300px; font-size:2em;">%s</div>', $greeting ),
				sprintf ( '											<div style="text-align:right;font-size: 1.4em; color:black;margin:150px 0 0 200px;">%s', $sentBy ),
				'												<br/><span style="font-size: 0.8em"><a style="color:#3f73b9" href="https://wordpress.org/plugins/postman-smtp/">https://wordpress.org/plugins/postman-smtp/</a></span>',
				'											</div>',
				'										</div>',
				'									</td>',
				'								</tr>',
				'							</tbody>',
				'						</table>',
				sprintf ( '						<br><span style="font-size:0.9em;color:#94c0dc;">%s: <a style="color:#94c0dc" href="http://poofytoo.com">poofytoo.com</a> - %s</span>', $imageSource, $withPermission ),
				'					</td>',
				'				</tr>',
				'			</tbody>',
				'		</table>',
				'</body>',
				'</html>' 
		);
		return implode ( PostmanMessage::EOL, $messageArray );
	}
}
