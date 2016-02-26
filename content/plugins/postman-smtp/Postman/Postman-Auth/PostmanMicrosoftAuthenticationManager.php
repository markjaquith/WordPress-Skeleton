<?php
if (! class_exists ( "PostmanMicrosoftAuthenticationManager" )) {
	
	require_once 'PostmanAbstractAuthenticationManager.php';
	
	/**
	 * https://msdn.microsoft.com/en-us/library/hh243647.aspx (Seems to be the most up-to-date doc on OAuth 2.0
	 * https://msdn.microsoft.com/en-us/library/hh243649.aspx (Seems to be the most up-to-date examples on using the API)
	 * https://msdn.microsoft.com/en-us/library/ff750690.aspx OAuth WRAP (Messenger Connect)
	 * https://msdn.microsoft.com/en-us/library/ff749624.aspx Working with OAuth WRAP (Messenger Connect)
	 * https://gist.github.com/kayalshri/5262641 Working example from Giriraj Namachivayam (kayalshri)
	 */
	class PostmanMicrosoftAuthenticationManager extends PostmanAbstractAuthenticationManager implements PostmanAuthenticationManager {
		
		// constants
		const WINDOWS_LIVE_ENDPOINT = 'https://login.live.com/oauth20_authorize.srf';
		const WINDOWS_LIVE_REFRESH = 'https://login.live.com/oauth20_token.srf';
		
		// http://stackoverflow.com/questions/7163786/messenger-connect-oauth-wrap-api-to-get-user-emails
		// http://quabr.com/26329398/outlook-oauth-send-emails-with-wl-imap-scope-in-php
		const SCOPE = 'wl.imap,wl.offline_access';
		const VENDOR_NAME = 'microsoft';
		
		/**
		 * Constructor
		 *
		 * Get a Client ID from https://account.live.com/developers/applications/index
		 */
		public function __construct($clientId, $clientSecret, PostmanOAuthToken $authorizationToken, $callbackUri) {
			assert ( ! empty ( $clientId ) );
			assert ( ! empty ( $clientSecret ) );
			assert ( ! empty ( $authorizationToken ) );
			assert ( ! empty ( $callbackUri ) );
			$logger = new PostmanLogger ( get_class ( $this ) );
			parent::__construct ( $logger, $clientId, $clientSecret, $authorizationToken, $callbackUri );
		}
		
		/**
		 * **********************************************
		 * Request Verification Code
		 * https://msdn.microsoft.com/en-us/library/ff749592.aspx
		 *
		 * The following example shows a URL that enables
		 * a user to provide consent to an application by
		 * using a Windows Live ID.
		 *
		 * When successful, this URL returns the user to
		 * your application, along with a verification
		 * code.
		 * **********************************************
		 */
		public function requestVerificationCode($transactionId) {
			$params = array (
					'response_type' => 'code',
					'redirect_uri' => urlencode ( $this->getCallbackUri () ),
					'client_id' => $this->getClientId (),
					'client_secret' => $this->getClientSecret (),
					'scope' => urlencode ( self::SCOPE ),
					'access_type' => 'offline',
					'approval_prompt' => 'force' 
			);
			
			$authUrl = $this->getAuthorizationUrl () . '?' . build_query ( $params );
			
			$this->getLogger ()->debug ( 'Requesting verification code from Microsoft' );
			PostmanUtils::redirect ( $authUrl );
		}
		
		/**
		 * **********************************************
		 * If we have a code back from the OAuth 2.0 flow,
		 * we need to exchange that for an access token.
		 * We store the resultant access token
		 * bundle in the session, and redirect to ourself.
		 * **********************************************
		 */
		public function processAuthorizationGrantCode($transactionId) {
			if (isset ( $_GET ['code'] )) {
				$code = $_GET ['code'];
				$this->getLogger ()->debug ( 'Found authorization code in request header' );
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
			return self::WINDOWS_LIVE_ENDPOINT;
		}
		public function getTokenUrl() {
			return self::WINDOWS_LIVE_REFRESH;
		}
	}
}
