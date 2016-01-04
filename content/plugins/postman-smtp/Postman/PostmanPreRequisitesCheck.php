<?php
if (! class_exists ( 'PostmanPreRequisitesCheck' )) {
	class PostmanPreRequisitesCheck {
		public static function checkIconv() {
			return function_exists ( 'iconv' );
		}
		public static function checkSpl() {
			return function_exists ( 'spl_autoload_register' );
		}
		public static function checkZlibEncode() {
			return extension_loaded ( "zlib" ) && function_exists ( 'gzcompress' ) && function_exists ( 'gzuncompress' );
		}
		public static function checkOpenSsl() {
			// apparently curl can use ssl libraries in the OS, and doesn't need ssl in PHP
			return extension_loaded ( 'openssl' ) || extension_loaded ( 'php_openssl' );
		}
		public static function checkSockets() {
			return extension_loaded ( 'sockets' ) || extension_loaded ( 'php_sockets' );
		}
		public static function checkAllowUrlFopen() {
			return filter_var ( ini_get ( 'allow_url_fopen' ), FILTER_VALIDATE_BOOLEAN );
		}
		public static function checkMcrypt() {
			return function_exists ( 'mcrypt_get_iv_size' ) && function_exists ( 'mcrypt_create_iv' ) && function_exists ( 'mcrypt_encrypt' ) && function_exists ( 'mcrypt_decrypt' );
		}
		/**
		 * Return an array of state:
		 * [n][name=>x,ready=>true|false,required=true|false]
		 */
		public static function getState() {
			$state = array ();
			array_push ( $state, array (
					'name' => 'iconv',
					'ready' => self::checkIconv (),
					'required' => true 
			) );
			array_push ( $state, array (
					'name' => 'spl_autoload',
					'ready' => self::checkSpl (),
					'required' => true 
			) );
			array_push ( $state, array (
					'name' => 'openssl',
					'ready' => self::checkOpenSsl (),
					'required' => false 
			) );
			array_push ( $state, array (
					'name' => 'sockets',
					'ready' => self::checkSockets (),
					'required' => false 
			) );
			array_push ( $state, array (
					'name' => 'allow_url_fopen',
					'ready' => self::checkAllowUrlFopen (),
					'required' => false 
			) );
			array_push ( $state, array (
					'name' => 'mcrypt',
					'ready' => self::checkMcrypt (),
					'required' => false 
			) );
			array_push ( $state, array (
					'name' => 'zlib_encode',
					'ready' => self::checkZlibEncode (),
					'required' => false 
			) );
			return $state;
		}
		/**
		 *
		 * @return boolean
		 */
		public static function isReady() {
			$states = self::getState ();
			foreach ( $states as $state ) {
				if ($state ['ready'] == false && $state ['required'] == true) {
					return false;
				}
			}
			
			return true;
		}
	}
}