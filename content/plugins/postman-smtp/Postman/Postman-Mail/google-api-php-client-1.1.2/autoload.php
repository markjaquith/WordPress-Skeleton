<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
function postman_google_api_php_client_autoload($className) {
	$logger = new PostmanLogger ( 'postman_google_api_php_client_autoload' );
	$logger->trace ( 'Autoloading ' . $className );
	$classPath = explode ( '_', $className );
	// @jason: make sure the first segment of the classname is 'Postman'
	if (empty ( $classPath [0] ) || empty ( $classPath [1] )) {
		return;
	}
	if ($classPath [0] != 'Postman' && $classPath [1] != 'Google') {
		return;
	} else {
		// @jason: get rid of the first segment of the classname
		$classPath = array_slice ( $classPath, 1 );
	}
	if (count ( $classPath ) > 3) {
		// Maximum class file path depth in this project is 3.
		$classPath = array_slice ( $classPath, 1, 4 );
	}
	$filePath = dirname ( __FILE__ ) . '/src/' . implode ( '/', $classPath ) . '.php';
	if (file_exists ( $filePath )) {
		require_once ($filePath);
	}
}

spl_autoload_register ( 'postman_google_api_php_client_autoload' );
