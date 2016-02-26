<?php
/*
 * Copyright 2010 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once realpath(dirname(__FILE__) . '/../../../autoload.php');

/**
 * Abstract class for the Authentication in the API client
 * @author Chris Chabot <chabotc@google.com>
 *
 */
abstract class Postman_Google_Auth_Abstract
{
  /**
   * An utility function that first calls $this->auth->sign($request) and then
   * executes makeRequest() on that signed request. Used for when a request
   * should be authenticated
   * @param Postman_Google_Http_Request $request
   * @return Postman_Google_Http_Request $request
   */
  abstract public function authenticatedRequest(Postman_Google_Http_Request $request);
  abstract public function sign(Postman_Google_Http_Request $request);
}
