<?php
if (! class_exists ( 'PostmanMessageHandler' )) {
	
	require_once ('PostmanSession.php');
	class PostmanMessageHandler {
		
		// The Session variables that carry messages
		const ERROR_CLASS = 'error';
		const WARNING_CLASS = 'update-nag';
		const SUCCESS_CLASS = 'updated';
		private $logger;
		
		/**
		 *
		 * @param unknown $options        	
		 */
		function __construct() {
			$this->logger = new PostmanLogger ( get_class ( $this ) );
			
			// we'll let the 'init' functions run first; some of them may end the request
			add_action ( 'admin_notices', Array (
					$this,
					'displayAllMessages' 
			) );
		}
		
		/**
		 *
		 * @param unknown $message        	
		 */
		public function addError($message) {
			$this->storeMessage ( $message, 'error' );
		}
		/**
		 *
		 * @param unknown $message        	
		 */
		public function addWarning($message) {
			$this->storeMessage ( $message, 'warning' );
		}
		/**
		 *
		 * @param unknown $message        	
		 */
		public function addMessage($message) {
			$this->storeMessage ( $message, 'notify' );
		}
		
		/**
		 * store messages for display later
		 *
		 * @param unknown $message        	
		 * @param unknown $type        	
		 */
		private function storeMessage($message, $type) {
			$messageArray = array ();
			$oldMessageArray = PostmanSession::getInstance ()->getMessage ();
			if ($oldMessageArray) {
				$messageArray = $oldMessageArray;
			}
			$weGotIt = false;
			foreach ( $messageArray as $storedMessage ) {
				if ($storedMessage ['message'] === $message) {
					$weGotIt = true;
				}
			}
			if (! $weGotIt) {
				$m = array (
						'type' => $type,
						'message' => $message 
				);
				array_push ( $messageArray, $m );
				PostmanSession::getInstance ()->setMessage ( $messageArray );
			}
		}
		/**
		 * Retrieve the messages and show them
		 */
		public function displayAllMessages() {
			$messageArray = PostmanSession::getInstance ()->getMessage ();
			if ($messageArray) {
				PostmanSession::getInstance ()->unsetMessage ();
				foreach ( $messageArray as $m ) {
					$type = $m ['type'];
					switch ($type) {
						case 'error' :
							$className = self::ERROR_CLASS;
							break;
						case 'warning' :
							$className = self::WARNING_CLASS;
							break;
						default :
							$className = self::SUCCESS_CLASS;
							break;
					}
					$message = $m ['message'];
					$this->printMessage ( $message, $className );
				}
			}
		}
		
		/**
		 * putput message
		 *
		 * @param unknown $message        	
		 * @param unknown $className        	
		 */
		public function printMessage($message, $className) {
			printf ( '<div class="%s"><p>%s</p></div>', $className, $message );
		}
	}
}
