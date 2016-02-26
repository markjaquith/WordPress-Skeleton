<?php
if (! class_exists ( "PostmanNonOAuthAuthenticationManager" )) {
	
	require_once 'PostmanAuthenticationManager.php';
	class PostmanNonOAuthAuthenticationManager implements PostmanAuthenticationManager {
		
		/**
		 */
		public function isAccessTokenExpired() {
			return false;
		}
		
		/**
		 * (non-PHPdoc)
		 *
		 * @see PostmanAuthenticationManager::requestVerificationCode()
		 */
		public function requestVerificationCode($transactionId) {
			// otherwise known as IllegaStateException
			assert ( false );
		}
		public function processAuthorizationGrantCode($transactionId) {
			// otherwise known as IllegaStateException
			assert ( false );
		}
		public function refreshToken() {
			// no-op
		}
		public function getAuthorizationUrl() {
			return null;
		}
		public function getTokenUrl() {
			return null;
		}
		public function getCallbackUri() {
			return null;
		}
		public function generateRequestTransactionId() {
			// otherwise known as IllegaStateException
			assert ( false );
		}
	}
}
