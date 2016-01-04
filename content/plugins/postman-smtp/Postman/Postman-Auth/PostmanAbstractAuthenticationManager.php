<?php
if (! class_exists ( "PostmanAbstractAuthenticationManager" )) {
	
	require_once 'PostmanAuthenticationManager.php';
	
	/**
	 */
	abstract class PostmanAbstractAuthenticationManager implements PostmanAuthenticationManager {
		
		// constants
		const APPROVAL_PROMPT = 'force';
		const ACCESS_TYPE = 'offline';
		const ACCESS_TOKEN = 'access_token';
		const REFRESH_TOKEN = 'refresh_token';
		const EXPIRES = 'expires_in';
		
		// the oauth authorization options
		private $clientId;
		private $clientSecret;
		private $authorizationToken;
		private $callbackUri;
		private $logger;
		
		/**
		 * Constructor
		 */
		public function __construct(PostmanLogger $logger, $clientId, $clientSecret, PostmanOAuthToken $authorizationToken, $callbackUri) {
			assert ( ! empty ( $clientId ) );
			assert ( ! empty ( $clientSecret ) );
			assert ( ! empty ( $authorizationToken ) );
			assert ( ! empty ( $callbackUri ) );
			$this->logger = $logger;
			$this->clientId = $clientId;
			$this->clientSecret = $clientSecret;
			$this->authorizationToken = $authorizationToken;
			$this->callbackUri = $callbackUri;
		}
		protected function getLogger() {
			return $this->logger;
		}
		protected function getClientId() {
			return $this->clientId;
		}
		protected function getClientSecret() {
			return $this->clientSecret;
		}
		protected function getAuthorizationToken() {
			return $this->authorizationToken;
		}
		
		/**
		 * Create a state token to prevent request forgery.
		 * Store it in the session for later validation.
		 */
		public function generateRequestTransactionId() {
			return $state = md5 ( rand () );
		}
		
		/**
		 */
		public function isAccessTokenExpired() {
			$expireTime = ($this->authorizationToken->getExpiryTime () - self::FORCE_REFRESH_X_SECONDS_BEFORE_EXPIRE);
			$tokenHasExpired = time () > $expireTime;
			$this->logger->debug ( 'Access Token Expiry Time is ' . $expireTime . ', expires_in=' . ($expireTime - time ()) . ', expired=' . ($tokenHasExpired ? 'yes' : 'no') );
			return $tokenHasExpired;
		}
		
		/**
		 * Decoded the received token
		 * This code is identical for Google and Hotmail
		 *
		 * @param unknown $response        	
		 * @throws Exception
		 */
		protected function processResponse($response) {
			$authToken = json_decode ( stripslashes ( $response ) );
			if ($authToken === NULL) {
				$this->getLogger ()->error ( $response );
				throw new Exception ( $response );
			} else if (isset ( $authToken->{'error'} )) {
				if (isset ( $authToken->{'error_description'} )) {
					$this->getLogger ()->error ( $authToken->{'error'} . ' processing response: ' . $authToken->{'error_description'} );
					throw new Exception ( $authToken->{'error_description'} . '(' . $authToken->{'error'} . ')' );
				} else {
					// Yahoo doesn't give descriptions
					$this->getLogger ()->error ( $authToken->{'error'} . ' processing response' );
					throw new Exception ( $authToken->{'error'} );
				}
			} else {
				$this->getLogger ()->trace ( 'Processing response:' );
				$this->getLogger ()->trace ( $response );
				$this->decodeReceivedAuthorizationToken ( $authToken );
			}
		}
		
		/**
		 * Parses the authorization token and extracts the expiry time, accessToken,
		 * and if this is a first-time authorization, a refresh token.
		 *
		 * This code is identical for Google and Hotmail
		 *
		 * @param unknown $client        	
		 */
		protected function decodeReceivedAuthorizationToken($newtoken) {
			assert ( ! empty ( $newtoken ) );
			assert ( ! empty ( $newtoken->{self::EXPIRES} ) );
			assert ( ! empty ( $newtoken->{self::ACCESS_TOKEN} ) );
			
			// update expiry time
			if (empty ( $newtoken->{self::EXPIRES} )) {
				throw new Exception ( '[expires_in] value is missing from the authentication token' );
			}
			$newExpiryTime = time () + $newtoken->{self::EXPIRES};
			$this->getAuthorizationToken ()->setExpiryTime ( $newExpiryTime );
			$this->getLogger ()->debug ( 'Updating Access Token Expiry Time ' );
			
			// update acccess token
			if (empty ( $newtoken->{self::ACCESS_TOKEN} )) {
				throw new Exception ( '[access_token] value is missing from the authentication token' );
			}
			$newAccessToken = $newtoken->{self::ACCESS_TOKEN};
			$this->getAuthorizationToken ()->setAccessToken ( $newAccessToken );
			$this->getLogger ()->debug ( 'Updating Access Token' );
			
			// update refresh token, if there is one
			if (isset ( $newtoken->{self::REFRESH_TOKEN} )) {
				$newRefreshToken = $newtoken->{self::REFRESH_TOKEN};
				$this->getAuthorizationToken ()->setRefreshToken ( $newRefreshToken );
				$this->getLogger ()->debug ( 'Updating Refresh Token ' );
			}
		}
		
		/**
		 * Given an OAuth provider-specific URL and redirectUri,
		 * issue an HttpRequest to refresh the access token
		 *
		 * This code is identical for Google and Hotmail
		 */
		public function refreshToken() {
			$this->getLogger ()->debug ( 'Refreshing Token' );
			$refreshUrl = $this->getTokenUrl ();
			$callbackUrl = $this->getCallbackUri ();
			assert ( ! empty ( $refreshUrl ) );
			assert ( ! empty ( $callbackUrl ) );
			// the format of the URL is
			// client_id=CLIENT_ID&client_secret=CLIENT_SECRET&redirect_uri=REDIRECT_URI&grant_type=refresh_token&refresh_token=REFRESH_TOKEN
			$postvals = array (
					'client_id' => $this->getClientId (),
					'client_secret' => $this->getClientSecret (),
					'redirect_uri' => $callbackUrl,
					'grant_type' => 'refresh_token',
					'refresh_token' => $this->getAuthorizationToken ()->getRefreshToken () 
			);
			// example request string
			// client_id=0000000603DB0F&redirect_uri=http%3A%2F%2Fwww.contoso.com%2Fcallback.php&client_secret=LWILlT555GicSrIATma5qgyBXebRI&refresh_token=*LA9...//refresh token string shortened for example//...xRoX&grant_type=refresh_token
			$response = PostmanUtils::remotePostGetBodyOnly ( $refreshUrl, $postvals );
			$this->processResponse ( $response );
		}
		/**
		 * (non-PHPdoc)
		 *
		 * @see PostmanAuthenticationManager::getCallbackUri()
		 */
		public function getCallbackUri() {
			return $this->callbackUri;
		}
	}
}
