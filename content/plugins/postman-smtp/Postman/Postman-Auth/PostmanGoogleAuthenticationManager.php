<?php
if (! class_exists ( "PostmanGoogleAuthenticationManager" )) {
	
	require_once 'PostmanAbstractAuthenticationManager.php';
	require_once 'PostmanStateIdMissingException.php';
	
	/**
	 * https://developers.google.com/accounts/docs/OAuth2WebServer
	 * https://developers.google.com/gmail/xoauth2_protocol
	 * https://developers.google.com/gmail/api/auth/scopes
	 */
	class PostmanGoogleAuthenticationManager extends PostmanAbstractAuthenticationManager implements PostmanAuthenticationManager {
		
		// This endpoint is the target of the initial request. It handles active session lookup, authenticating the user, and user consent.
		const GOOGLE_ENDPOINT = 'https://accounts.google.com/o/oauth2/auth';
		const GOOGLE_REFRESH = 'https://www.googleapis.com/oauth2/v3/token';
		
		// this scope doesn't work
		// Create, read, update, and delete drafts. Send messages and drafts.
		const SCOPE_COMPOSE = 'https://www.googleapis.com/auth/gmail.compose';
		
		// this scope doesn't work
		// All read/write operations except immediate, permanent deletion of threads and messages, bypassing Trash.
		const SCOPE_MODIFY = 'https://www.googleapis.com/auth/gmail.modify';
		
		// Full access to the account, including permanent deletion of threads and messages. This scope should only be requested if your application needs to immediately and permanently delete threads and messages, bypassing Trash; all other actions can be performed with less permissive scopes.
		const SCOPE_FULL_ACCESS = 'https://mail.google.com/';
		const AUTH_TEMP_ID = 'GOOGLE_OAUTH_TEMP_ID';
		const VENDOR_NAME = 'google';
		// the sender email address
		private $senderEmail;
		
		/**
		 * Constructor
		 *
		 * Get a Client ID from https://account.live.com/developers/applications/index
		 */
		public function __construct($clientId, $clientSecret, PostmanOAuthToken $authorizationToken, $callbackUri, $senderEmail) {
			assert ( ! empty ( $clientId ) );
			assert ( ! empty ( $clientSecret ) );
			assert ( ! empty ( $authorizationToken ) );
			assert ( ! empty ( $senderEmail ) );
			$logger = new PostmanLogger ( get_class ( $this ) );
			$this->senderEmail = $senderEmail;
			parent::__construct ( $logger, $clientId, $clientSecret, $authorizationToken, $callbackUri );
		}
		
		/**
		 * The authorization sequence begins when your application redirects a browser to a Google URL;
		 * the URL includes query parameters that indicate the type of access being requested.
		 *
		 * As in other scenarios, Google handles user authentication, session selection, and user consent.
		 * The result is an authorization code, which Google returns to your application in a query string.
		 *
		 * (non-PHPdoc)
		 *
		 * @see PostmanAuthenticationManager::requestVerificationCode()
		 */
		public function requestVerificationCode($transactionId) {
			$params = array (
					'response_type' => 'code',
					'redirect_uri' => urlencode ( $this->getCallbackUri () ),
					'client_id' => $this->getClientId (),
					'scope' => urlencode ( self::SCOPE_FULL_ACCESS ),
					'access_type' => 'offline',
					'approval_prompt' => 'force',
					'state' => $transactionId,
					'login_hint' => $this->senderEmail 
			);
			
			$authUrl = $this->getAuthorizationUrl () . '?' . build_query ( $params );
			
			$this->getLogger ()->debug ( 'Requesting verification code from Google' );
			PostmanUtils::redirect ( $authUrl );
		}
		
		/**
		 * After receiving the authorization code, your application can exchange the code
		 * (along with a client ID and client secret) for an access token and, in some cases,
		 * a refresh token.
		 *
		 * This code is identical for Google and Hotmail
		 *
		 * @see PostmanAuthenticationManager::processAuthorizationGrantCode()
		 */
		public function processAuthorizationGrantCode($transactionId) {
			if (isset ( $_GET ['code'] )) {
				$this->getLogger ()->debug ( 'Found authorization code in request header' );
				$code = $_GET ['code'];
				if (isset ( $_GET ['state'] ) && $_GET ['state'] == $transactionId) {
					$this->getLogger ()->debug ( 'Found valid state in request header' );
				} else {
					$this->getLogger ()->error ( 'The grant code from Google had no accompanying state and may be a forgery' );
					throw new PostmanStateIdMissingException ();
				}
				$postvals = array (
						'client_id' => $this->getClientId (),
						'client_secret' => $this->getClientSecret (),
						'grant_type' => 'authorization_code',
						'redirect_uri' => $this->getCallbackUri (),
						'code' => $code 
				);
				$response = PostmanUtils::remotePostGetBodyOnly ( $this->getTokenUrl (), $postvals );
				$this->processResponse ( $response );
				$this->getAuthorizationToken ()->setVendorName ( self::VENDOR_NAME );
				return true;
			} else {
				$this->getLogger ()->debug ( 'Expected code in the request header but found none - user probably denied request' );
				return false;
			}
		}
		public function getAuthorizationUrl() {
			return self::GOOGLE_ENDPOINT;
		}
		public function getTokenUrl() {
			return self::GOOGLE_REFRESH;
		}
	}
}
