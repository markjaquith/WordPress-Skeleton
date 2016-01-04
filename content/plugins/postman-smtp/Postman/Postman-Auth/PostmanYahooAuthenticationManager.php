<?php
if (! class_exists ( "PostmanYahooAuthenticationManager" )) {
	
	require_once 'PostmanAbstractAuthenticationManager.php';
	require_once 'PostmanStateIdMissingException.php';
	
	/**
	 * Super-simple.
	 * I should have started with Yahoo.
	 *
	 * https://developer.yahoo.com/oauth2/guide/
	 * Get a Client ID at https://developer.apps.yahoo.com/projects
	 *
	 * @author jasonhendriks
	 */
	class PostmanYahooAuthenticationManager extends PostmanAbstractAuthenticationManager implements PostmanAuthenticationManager {
		
		// This endpoint is the target of the initial request. It handles active session lookup, authenticating the user, and user consent.
		const AUTHORIZATION_URL = 'https://api.login.yahoo.com/oauth2/request_auth';
		const GET_TOKEN_URL = 'https://api.login.yahoo.com/oauth2/get_token';
		
		// The SESSION key for the OAuth Transaction Id
		const AUTH_TEMP_ID = 'OAUTH_TEMP_ID';
		const VENDOR_NAME = 'yahoo';
		
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
					'state' => $transactionId,
					'language' => get_locale () 
			);
			
			$authUrl = $this->getAuthorizationUrl () . '?' . build_query ( $params );
			
			$this->getLogger ()->debug ( 'Requesting verification code from Yahoo' );
			PostmanUtils::redirect ( $authUrl );
		}
		
		/**
		 * After receiving the authorization code, your application can exchange the code
		 * (along with a client ID and client secret) for an access token and, in some cases,
		 * a refresh token.
		 *
		 * (non-PHPdoc)
		 *
		 * @see PostmanAuthenticationManager::processAuthorizationGrantCode()
		 */
		public function processAuthorizationGrantCode($transactionId) {
			if (isset ( $_GET ['code'] )) {
				$code = $_GET ['code'];
				$this->getLogger ()->debug ( sprintf ( 'Found authorization code %s in request header', $code ) );
				if (isset ( $_GET ['state'] ) && $_GET ['state'] == $transactionId) {
					$this->getLogger ()->debug ( 'Found valid state in request header' );
				} else {
					$this->getLogger ()->error ( 'The grant code from Yahoo had no accompanying state and may be a forgery' );
					throw new PostmanStateIdMissingException ();
				}
				// Note: The Authorization: Basic authorization header is generated through a Base64 encoding of client_id:client_secret per RFC 2617.
				// header("Authorization: Basic " . base64_encode($username . ":" . $password);
				$headers = array (
						'Authorization' => sprintf ( "Basic %s", base64_encode ( $this->getClientId () . ':' . $this->getClientSecret () ) ) 
				);
				$postvals = array (
						'code' => $code,
						'grant_type' => 'authorization_code',
						'redirect_uri' => $this->getCallbackUri () 
				);
				$response = PostmanUtils::remotePostGetBodyOnly ( $this->getTokenUrl (), $postvals, $headers );
				$this->processResponse ( $response );
				$this->getAuthorizationToken ()->setVendorName ( self::VENDOR_NAME );
				return true;
			} else {
				$this->getLogger ()->debug ( 'Expected code in the request header but found none - user probably denied request' );
				return false;
			}
		}
		
		/**
		 * Step 5: Exchange refresh token for new access token
		 * After the access token expires, you can use the refresh token, which has a long lifetime, to get a new access token.
		 */
		public function refreshToken() {
			$this->getLogger ()->debug ( 'Refreshing Token' );
			$refreshUrl = $this->getTokenUrl ();
			$callbackUrl = $this->getCallbackUri ();
			assert ( ! empty ( $refreshUrl ) );
			assert ( ! empty ( $callbackUrl ) );
			$headers = array (
					'Authorization' => sprintf ( "Basic %s", base64_encode ( $this->getClientId () . ':' . $this->getClientSecret () ) ) 
			);
			$postvals = array (
					'redirect_uri' => $callbackUrl,
					'grant_type' => 'refresh_token',
					'refresh_token' => $this->getAuthorizationToken ()->getRefreshToken () 
			);
			$response = PostmanUtils::remotePostGetBodyOnly ( $this->getTokenUrl (), $postvals, $headers );
			$this->processResponse ( $response );
		}
		public function getAuthorizationUrl() {
			return self::AUTHORIZATION_URL;
		}
		public function getTokenUrl() {
			return self::GET_TOKEN_URL;
		}
	}
}
