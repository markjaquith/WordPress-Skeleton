<?php
if (! class_exists ( 'PostmanAbstractAjaxHandler' )) {
	
	require_once ('PostmanPreRequisitesCheck.php');
	require_once ('Postman-Mail/PostmanMessage.php');
	
	/**
	 *
	 * @author jasonhendriks
	 */
	abstract class PostmanAbstractAjaxHandler {
		protected $logger;
		function __construct() {
			$this->logger = new PostmanLogger ( get_class ( $this ) );
		}
		/**
		 *
		 * @param unknown $actionName        	
		 * @param unknown $callbackName        	
		 */
		protected function registerAjaxHandler($actionName, $class, $callbackName) {
			if (is_admin ()) {
				$fullname = 'wp_ajax_' . $actionName;
				// $this->logger->debug ( 'Registering ' . 'wp_ajax_' . $fullname . ' Ajax handler' );
				add_action ( $fullname, array (
						$class,
						$callbackName 
				) );
			}
		}
		
		/**
		 *
		 * @param unknown $parameterName        	
		 * @return mixed
		 */
		protected function getBooleanRequestParameter($parameterName) {
			return filter_var ( $this->getRequestParameter ( $parameterName ), FILTER_VALIDATE_BOOLEAN );
		}
		
		/**
		 *
		 * @param unknown $parameterName        	
		 * @return unknown
		 */
		protected function getRequestParameter($parameterName) {
			if (isset ( $_POST [$parameterName] )) {
				$value = $_POST [$parameterName];
				$this->logger->trace ( sprintf ( 'Found parameter "%s"', $parameterName ) );
				$this->logger->trace ( $value );
				return $value;
			}
		}
	}
}

require_once ('Postman-Controller/PostmanManageConfigurationAjaxHandler.php');
