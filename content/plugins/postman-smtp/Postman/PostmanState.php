<?php
if (! class_exists ( "PostmanState" )) {
	
	/**
	 * http://stackoverflow.com/questions/23880928/use-oauth-refresh-token-to-obtain-new-access-token-google-api
	 * http://pastebin.com/jA9sBNTk
	 *
	 * Make sure these emails are permitted (see http://en.wikipedia.org/wiki/E-mail_address#Internationalization):
	 */
	class PostmanState {
		
		private $logger;
		
		// the option database name
		const SLUG = 'postman_state';
		const TOO_LONG_SEC = 2592000; // 30 days
		                              
		// the options fields
		const VERSION = 'version';
		const INSTALL_DATE = 'install_date';
		const FILE_LOCKING_ENABLED = 'locking_enabled';
		const DELIVERY_SUCCESS_TOTAL = 'delivery_success_total';
		const DELIVERY_FAILURE_TOTAL = 'delivery_fail_total';
		
		// options data
		private $options;
		
		// singleton instance
		public static function getInstance() {
			static $inst = null;
			if ($inst === null) {
				$inst = new PostmanState ();
			}
			return $inst;
		}
		
		/**
		 * private constructor
		 */
		private function __construct() {
			$this->logger = new PostmanLogger(get_class($this));
			$this->load();
		}
		//
		public function save() {
			update_option ( self::SLUG, $this->options );
		}
		public function reload() {
			$this->load ();
		}
		private function load() {
			$this->options = get_option ( self::SLUG );
		}
		/**
		 * Shows the review feature of Postman up to thirty days after install
		 *
		 * @return boolean
		 */
		public function isTimeToReviewPostman() {
			if (! empty ( $this->options [self::INSTALL_DATE] )) {
				$successful = PostmanState::getInstance ()->getSuccessfulDeliveries () > 0;
				$maxTime = $this->options [self::INSTALL_DATE] + self::TOO_LONG_SEC;
				return $successful && time () <= $maxTime;
			}
		}
		public function isFileLockingEnabled() {
			if (isset ( $this->options [self::FILE_LOCKING_ENABLED] ))
				return $this->options [self::FILE_LOCKING_ENABLED];
			else
				return false;
		}
		public function setFileLockingEnabled($enabled) {
			$this->options [self::FILE_LOCKING_ENABLED] = $enabled;
			$this->save ();
		}
		public function getVersion() {
			if (! empty ( $this->options [self::VERSION] )) {
				return $this->options [self::VERSION];
			}
		}
		function getSuccessfulDeliveries() {
			if (isset ( $this->options [PostmanState::DELIVERY_SUCCESS_TOTAL] ))
				return $this->options [PostmanState::DELIVERY_SUCCESS_TOTAL];
			else
				return 0;
		}
		function setSuccessfulDelivery($total) {
			$this->options [PostmanState::DELIVERY_SUCCESS_TOTAL] = $total;
		}
		function incrementSuccessfulDelivery() {
			$this->setSuccessfulDelivery ( $this->getSuccessfulDeliveries () + 1 );
			$this->logger->debug ( 'incrementing success count: ' . $this->getSuccessfulDeliveries () );
			$this->save ();
		}
		function getFailedDeliveries() {
			if (isset ( $this->options [PostmanState::DELIVERY_FAILURE_TOTAL] ))
				return $this->options [PostmanState::DELIVERY_FAILURE_TOTAL];
			else
				return 0;
		}
		function setFailedDelivery($total) {
			$this->options [PostmanState::DELIVERY_FAILURE_TOTAL] = $total;
		}
		function incrementFailedDelivery() {
			$this->setFailedDelivery ( $this->getFailedDeliveries () + 1 );
			$this->logger->debug ( 'incrementing failure count: ' . $this->getFailedDeliveries () );
			$this->save ();
		}
	}
}