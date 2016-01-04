<?php
/*
 * Copyright 2010 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

/**
 * Service definition for ShoppingContent (v2).
 *
 * <p>
 * Manage product items, inventory, and Merchant Center accounts for Google
 * Shopping.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/shopping-content/v2/" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Postman_Google_Service_ShoppingContent extends Postman_Google_Service
{
  /** Manage your product listings and accounts for Google Shopping. */
  const CONTENT =
      "https://www.googleapis.com/auth/content";

  public $accounts;
  public $accountstatuses;
  public $datafeeds;
  public $datafeedstatuses;
  public $inventory;
  public $products;
  public $productstatuses;
  

  /**
   * Constructs the internal representation of the ShoppingContent service.
   *
   * @param Postman_Google_Client $client
   */
  public function __construct(Postman_Google_Client $client)
  {
    parent::__construct($client);
    $this->servicePath = 'content/v2/';
    $this->version = 'v2';
    $this->serviceName = 'content';

    $this->accounts = new Postman_Google_Service_ShoppingContent_Accounts_Resource(
        $this,
        $this->serviceName,
        'accounts',
        array(
          'methods' => array(
            'custombatch' => array(
              'path' => 'accounts/batch',
              'httpMethod' => 'POST',
              'parameters' => array(),
            ),'delete' => array(
              'path' => '{merchantId}/accounts/{accountId}',
              'httpMethod' => 'DELETE',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'accountId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'get' => array(
              'path' => '{merchantId}/accounts/{accountId}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'accountId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'insert' => array(
              'path' => '{merchantId}/accounts',
              'httpMethod' => 'POST',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'list' => array(
              'path' => '{merchantId}/accounts',
              'httpMethod' => 'GET',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'pageToken' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'maxResults' => array(
                  'location' => 'query',
                  'type' => 'integer',
                ),
              ),
            ),'patch' => array(
              'path' => '{merchantId}/accounts/{accountId}',
              'httpMethod' => 'PATCH',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'accountId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'update' => array(
              'path' => '{merchantId}/accounts/{accountId}',
              'httpMethod' => 'PUT',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'accountId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),
          )
        )
    );
    $this->accountstatuses = new Postman_Google_Service_ShoppingContent_Accountstatuses_Resource(
        $this,
        $this->serviceName,
        'accountstatuses',
        array(
          'methods' => array(
            'custombatch' => array(
              'path' => 'accountstatuses/batch',
              'httpMethod' => 'POST',
              'parameters' => array(),
            ),'get' => array(
              'path' => '{merchantId}/accountstatuses/{accountId}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'accountId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'list' => array(
              'path' => '{merchantId}/accountstatuses',
              'httpMethod' => 'GET',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'pageToken' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'maxResults' => array(
                  'location' => 'query',
                  'type' => 'integer',
                ),
              ),
            ),
          )
        )
    );
    $this->datafeeds = new Postman_Google_Service_ShoppingContent_Datafeeds_Resource(
        $this,
        $this->serviceName,
        'datafeeds',
        array(
          'methods' => array(
            'custombatch' => array(
              'path' => 'datafeeds/batch',
              'httpMethod' => 'POST',
              'parameters' => array(),
            ),'delete' => array(
              'path' => '{merchantId}/datafeeds/{datafeedId}',
              'httpMethod' => 'DELETE',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'datafeedId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'get' => array(
              'path' => '{merchantId}/datafeeds/{datafeedId}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'datafeedId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'insert' => array(
              'path' => '{merchantId}/datafeeds',
              'httpMethod' => 'POST',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'list' => array(
              'path' => '{merchantId}/datafeeds',
              'httpMethod' => 'GET',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'pageToken' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'maxResults' => array(
                  'location' => 'query',
                  'type' => 'integer',
                ),
              ),
            ),'patch' => array(
              'path' => '{merchantId}/datafeeds/{datafeedId}',
              'httpMethod' => 'PATCH',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'datafeedId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'update' => array(
              'path' => '{merchantId}/datafeeds/{datafeedId}',
              'httpMethod' => 'PUT',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'datafeedId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),
          )
        )
    );
    $this->datafeedstatuses = new Postman_Google_Service_ShoppingContent_Datafeedstatuses_Resource(
        $this,
        $this->serviceName,
        'datafeedstatuses',
        array(
          'methods' => array(
            'custombatch' => array(
              'path' => 'datafeedstatuses/batch',
              'httpMethod' => 'POST',
              'parameters' => array(),
            ),'get' => array(
              'path' => '{merchantId}/datafeedstatuses/{datafeedId}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'datafeedId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'list' => array(
              'path' => '{merchantId}/datafeedstatuses',
              'httpMethod' => 'GET',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'pageToken' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'maxResults' => array(
                  'location' => 'query',
                  'type' => 'integer',
                ),
              ),
            ),
          )
        )
    );
    $this->inventory = new Postman_Google_Service_ShoppingContent_Inventory_Resource(
        $this,
        $this->serviceName,
        'inventory',
        array(
          'methods' => array(
            'custombatch' => array(
              'path' => 'inventory/batch',
              'httpMethod' => 'POST',
              'parameters' => array(),
            ),'set' => array(
              'path' => '{merchantId}/inventory/{storeCode}/products/{productId}',
              'httpMethod' => 'POST',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'storeCode' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'productId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),
          )
        )
    );
    $this->products = new Postman_Google_Service_ShoppingContent_Products_Resource(
        $this,
        $this->serviceName,
        'products',
        array(
          'methods' => array(
            'custombatch' => array(
              'path' => 'products/batch',
              'httpMethod' => 'POST',
              'parameters' => array(
                'dryRun' => array(
                  'location' => 'query',
                  'type' => 'boolean',
                ),
              ),
            ),'delete' => array(
              'path' => '{merchantId}/products/{productId}',
              'httpMethod' => 'DELETE',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'productId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'dryRun' => array(
                  'location' => 'query',
                  'type' => 'boolean',
                ),
              ),
            ),'get' => array(
              'path' => '{merchantId}/products/{productId}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'productId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'insert' => array(
              'path' => '{merchantId}/products',
              'httpMethod' => 'POST',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'dryRun' => array(
                  'location' => 'query',
                  'type' => 'boolean',
                ),
              ),
            ),'list' => array(
              'path' => '{merchantId}/products',
              'httpMethod' => 'GET',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'pageToken' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'maxResults' => array(
                  'location' => 'query',
                  'type' => 'integer',
                ),
              ),
            ),
          )
        )
    );
    $this->productstatuses = new Postman_Google_Service_ShoppingContent_Productstatuses_Resource(
        $this,
        $this->serviceName,
        'productstatuses',
        array(
          'methods' => array(
            'custombatch' => array(
              'path' => 'productstatuses/batch',
              'httpMethod' => 'POST',
              'parameters' => array(),
            ),'get' => array(
              'path' => '{merchantId}/productstatuses/{productId}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'productId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'list' => array(
              'path' => '{merchantId}/productstatuses',
              'httpMethod' => 'GET',
              'parameters' => array(
                'merchantId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'pageToken' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'maxResults' => array(
                  'location' => 'query',
                  'type' => 'integer',
                ),
              ),
            ),
          )
        )
    );
  }
}


/**
 * The "accounts" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contentService = new Postman_Google_Service_ShoppingContent(...);
 *   $accounts = $contentService->accounts;
 *  </code>
 */
class Postman_Google_Service_ShoppingContent_Accounts_Resource extends Postman_Google_Service_Resource
{

  /**
   * Retrieves, inserts, updates, and deletes multiple Merchant Center
   * (sub-)accounts in a single request. (accounts.custombatch)
   *
   * @param Postman_Google_AccountsCustomBatchRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_AccountsCustomBatchResponse
   */
  public function custombatch(Postman_Google_Service_ShoppingContent_AccountsCustomBatchRequest $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('custombatch', array($params), "Postman_Google_Service_ShoppingContent_AccountsCustomBatchResponse");
  }

  /**
   * Deletes a Merchant Center sub-account. (accounts.delete)
   *
   * @param string $merchantId The ID of the managing account.
   * @param string $accountId The ID of the account.
   * @param array $optParams Optional parameters.
   */
  public function delete($merchantId, $accountId, $optParams = array())
  {
    $params = array('merchantId' => $merchantId, 'accountId' => $accountId);
    $params = array_merge($params, $optParams);
    return $this->call('delete', array($params));
  }

  /**
   * Retrieves a Merchant Center account. (accounts.get)
   *
   * @param string $merchantId The ID of the managing account.
   * @param string $accountId The ID of the account.
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_Account
   */
  public function get($merchantId, $accountId, $optParams = array())
  {
    $params = array('merchantId' => $merchantId, 'accountId' => $accountId);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Postman_Google_Service_ShoppingContent_Account");
  }

  /**
   * Creates a Merchant Center sub-account. (accounts.insert)
   *
   * @param string $merchantId The ID of the managing account.
   * @param Postman_Google_Account $postBody
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_Account
   */
  public function insert($merchantId, Postman_Google_Service_ShoppingContent_Account $postBody, $optParams = array())
  {
    $params = array('merchantId' => $merchantId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('insert', array($params), "Postman_Google_Service_ShoppingContent_Account");
  }

  /**
   * Lists the sub-accounts in your Merchant Center account.
   * (accounts.listAccounts)
   *
   * @param string $merchantId The ID of the managing account.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pageToken The token returned by the previous request.
   * @opt_param string maxResults The maximum number of accounts to return in the
   * response, used for paging.
   * @return Postman_Google_Service_ShoppingContent_AccountsListResponse
   */
  public function listAccounts($merchantId, $optParams = array())
  {
    $params = array('merchantId' => $merchantId);
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Postman_Google_Service_ShoppingContent_AccountsListResponse");
  }

  /**
   * Updates a Merchant Center account. This method supports patch semantics.
   * (accounts.patch)
   *
   * @param string $merchantId The ID of the managing account.
   * @param string $accountId The ID of the account.
   * @param Postman_Google_Account $postBody
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_Account
   */
  public function patch($merchantId, $accountId, Postman_Google_Service_ShoppingContent_Account $postBody, $optParams = array())
  {
    $params = array('merchantId' => $merchantId, 'accountId' => $accountId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('patch', array($params), "Postman_Google_Service_ShoppingContent_Account");
  }

  /**
   * Updates a Merchant Center account. (accounts.update)
   *
   * @param string $merchantId The ID of the managing account.
   * @param string $accountId The ID of the account.
   * @param Postman_Google_Account $postBody
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_Account
   */
  public function update($merchantId, $accountId, Postman_Google_Service_ShoppingContent_Account $postBody, $optParams = array())
  {
    $params = array('merchantId' => $merchantId, 'accountId' => $accountId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('update', array($params), "Postman_Google_Service_ShoppingContent_Account");
  }
}

/**
 * The "accountstatuses" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contentService = new Postman_Google_Service_ShoppingContent(...);
 *   $accountstatuses = $contentService->accountstatuses;
 *  </code>
 */
class Postman_Google_Service_ShoppingContent_Accountstatuses_Resource extends Postman_Google_Service_Resource
{

  /**
   * (accountstatuses.custombatch)
   *
   * @param Postman_Google_AccountstatusesCustomBatchRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_AccountstatusesCustomBatchResponse
   */
  public function custombatch(Postman_Google_Service_ShoppingContent_AccountstatusesCustomBatchRequest $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('custombatch', array($params), "Postman_Google_Service_ShoppingContent_AccountstatusesCustomBatchResponse");
  }

  /**
   * Retrieves the status of a Merchant Center account. (accountstatuses.get)
   *
   * @param string $merchantId The ID of the managing account.
   * @param string $accountId The ID of the account.
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_AccountStatus
   */
  public function get($merchantId, $accountId, $optParams = array())
  {
    $params = array('merchantId' => $merchantId, 'accountId' => $accountId);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Postman_Google_Service_ShoppingContent_AccountStatus");
  }

  /**
   * Lists the statuses of the sub-accounts in your Merchant Center account.
   * (accountstatuses.listAccountstatuses)
   *
   * @param string $merchantId The ID of the managing account.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pageToken The token returned by the previous request.
   * @opt_param string maxResults The maximum number of account statuses to return
   * in the response, used for paging.
   * @return Postman_Google_Service_ShoppingContent_AccountstatusesListResponse
   */
  public function listAccountstatuses($merchantId, $optParams = array())
  {
    $params = array('merchantId' => $merchantId);
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Postman_Google_Service_ShoppingContent_AccountstatusesListResponse");
  }
}

/**
 * The "datafeeds" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contentService = new Postman_Google_Service_ShoppingContent(...);
 *   $datafeeds = $contentService->datafeeds;
 *  </code>
 */
class Postman_Google_Service_ShoppingContent_Datafeeds_Resource extends Postman_Google_Service_Resource
{

  /**
   * (datafeeds.custombatch)
   *
   * @param Postman_Google_DatafeedsCustomBatchRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_DatafeedsCustomBatchResponse
   */
  public function custombatch(Postman_Google_Service_ShoppingContent_DatafeedsCustomBatchRequest $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('custombatch', array($params), "Postman_Google_Service_ShoppingContent_DatafeedsCustomBatchResponse");
  }

  /**
   * Deletes a datafeed from your Merchant Center account. (datafeeds.delete)
   *
   * @param string $merchantId
   * @param string $datafeedId
   * @param array $optParams Optional parameters.
   */
  public function delete($merchantId, $datafeedId, $optParams = array())
  {
    $params = array('merchantId' => $merchantId, 'datafeedId' => $datafeedId);
    $params = array_merge($params, $optParams);
    return $this->call('delete', array($params));
  }

  /**
   * Retrieves a datafeed from your Merchant Center account. (datafeeds.get)
   *
   * @param string $merchantId
   * @param string $datafeedId
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_Datafeed
   */
  public function get($merchantId, $datafeedId, $optParams = array())
  {
    $params = array('merchantId' => $merchantId, 'datafeedId' => $datafeedId);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Postman_Google_Service_ShoppingContent_Datafeed");
  }

  /**
   * Registers a datafeed with your Merchant Center account. (datafeeds.insert)
   *
   * @param string $merchantId
   * @param Postman_Google_Datafeed $postBody
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_Datafeed
   */
  public function insert($merchantId, Postman_Google_Service_ShoppingContent_Datafeed $postBody, $optParams = array())
  {
    $params = array('merchantId' => $merchantId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('insert', array($params), "Postman_Google_Service_ShoppingContent_Datafeed");
  }

  /**
   * Lists the datafeeds in your Merchant Center account.
   * (datafeeds.listDatafeeds)
   *
   * @param string $merchantId The ID of the managing account.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pageToken The token returned by the previous request.
   * @opt_param string maxResults The maximum number of products to return in the
   * response, used for paging.
   * @return Postman_Google_Service_ShoppingContent_DatafeedsListResponse
   */
  public function listDatafeeds($merchantId, $optParams = array())
  {
    $params = array('merchantId' => $merchantId);
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Postman_Google_Service_ShoppingContent_DatafeedsListResponse");
  }

  /**
   * Updates a datafeed of your Merchant Center account. This method supports
   * patch semantics. (datafeeds.patch)
   *
   * @param string $merchantId
   * @param string $datafeedId
   * @param Postman_Google_Datafeed $postBody
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_Datafeed
   */
  public function patch($merchantId, $datafeedId, Postman_Google_Service_ShoppingContent_Datafeed $postBody, $optParams = array())
  {
    $params = array('merchantId' => $merchantId, 'datafeedId' => $datafeedId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('patch', array($params), "Postman_Google_Service_ShoppingContent_Datafeed");
  }

  /**
   * Updates a datafeed of your Merchant Center account. (datafeeds.update)
   *
   * @param string $merchantId
   * @param string $datafeedId
   * @param Postman_Google_Datafeed $postBody
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_Datafeed
   */
  public function update($merchantId, $datafeedId, Postman_Google_Service_ShoppingContent_Datafeed $postBody, $optParams = array())
  {
    $params = array('merchantId' => $merchantId, 'datafeedId' => $datafeedId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('update', array($params), "Postman_Google_Service_ShoppingContent_Datafeed");
  }
}

/**
 * The "datafeedstatuses" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contentService = new Postman_Google_Service_ShoppingContent(...);
 *   $datafeedstatuses = $contentService->datafeedstatuses;
 *  </code>
 */
class Postman_Google_Service_ShoppingContent_Datafeedstatuses_Resource extends Postman_Google_Service_Resource
{

  /**
   * (datafeedstatuses.custombatch)
   *
   * @param Postman_Google_DatafeedstatusesCustomBatchRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_DatafeedstatusesCustomBatchResponse
   */
  public function custombatch(Postman_Google_Service_ShoppingContent_DatafeedstatusesCustomBatchRequest $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('custombatch', array($params), "Postman_Google_Service_ShoppingContent_DatafeedstatusesCustomBatchResponse");
  }

  /**
   * Retrieves the status of a datafeed from your Merchant Center account.
   * (datafeedstatuses.get)
   *
   * @param string $merchantId
   * @param string $datafeedId
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_DatafeedStatus
   */
  public function get($merchantId, $datafeedId, $optParams = array())
  {
    $params = array('merchantId' => $merchantId, 'datafeedId' => $datafeedId);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Postman_Google_Service_ShoppingContent_DatafeedStatus");
  }

  /**
   * Lists the statuses of the datafeeds in your Merchant Center account.
   * (datafeedstatuses.listDatafeedstatuses)
   *
   * @param string $merchantId The ID of the managing account.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pageToken The token returned by the previous request.
   * @opt_param string maxResults The maximum number of products to return in the
   * response, used for paging.
   * @return Postman_Google_Service_ShoppingContent_DatafeedstatusesListResponse
   */
  public function listDatafeedstatuses($merchantId, $optParams = array())
  {
    $params = array('merchantId' => $merchantId);
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Postman_Google_Service_ShoppingContent_DatafeedstatusesListResponse");
  }
}

/**
 * The "inventory" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contentService = new Postman_Google_Service_ShoppingContent(...);
 *   $inventory = $contentService->inventory;
 *  </code>
 */
class Postman_Google_Service_ShoppingContent_Inventory_Resource extends Postman_Google_Service_Resource
{

  /**
   * Updates price and availability for multiple products or stores in a single
   * request. (inventory.custombatch)
   *
   * @param Postman_Google_InventoryCustomBatchRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_InventoryCustomBatchResponse
   */
  public function custombatch(Postman_Google_Service_ShoppingContent_InventoryCustomBatchRequest $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('custombatch', array($params), "Postman_Google_Service_ShoppingContent_InventoryCustomBatchResponse");
  }

  /**
   * Updates price and availability of a product in your Merchant Center account.
   * (inventory.set)
   *
   * @param string $merchantId The ID of the managing account.
   * @param string $storeCode The code of the store for which to update price and
   * availability. Use online to update price and availability of an online
   * product.
   * @param string $productId The ID of the product for which to update price and
   * availability.
   * @param Postman_Google_InventorySetRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_InventorySetResponse
   */
  public function set($merchantId, $storeCode, $productId, Postman_Google_Service_ShoppingContent_InventorySetRequest $postBody, $optParams = array())
  {
    $params = array('merchantId' => $merchantId, 'storeCode' => $storeCode, 'productId' => $productId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('set', array($params), "Postman_Google_Service_ShoppingContent_InventorySetResponse");
  }
}

/**
 * The "products" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contentService = new Postman_Google_Service_ShoppingContent(...);
 *   $products = $contentService->products;
 *  </code>
 */
class Postman_Google_Service_ShoppingContent_Products_Resource extends Postman_Google_Service_Resource
{

  /**
   * Retrieves, inserts, and deletes multiple products in a single request.
   * (products.custombatch)
   *
   * @param Postman_Google_ProductsCustomBatchRequest $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool dryRun Flag to run the request in dry-run mode.
   * @return Postman_Google_Service_ShoppingContent_ProductsCustomBatchResponse
   */
  public function custombatch(Postman_Google_Service_ShoppingContent_ProductsCustomBatchRequest $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('custombatch', array($params), "Postman_Google_Service_ShoppingContent_ProductsCustomBatchResponse");
  }

  /**
   * Deletes a product from your Merchant Center account. (products.delete)
   *
   * @param string $merchantId The ID of the managing account.
   * @param string $productId The ID of the product.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool dryRun Flag to run the request in dry-run mode.
   */
  public function delete($merchantId, $productId, $optParams = array())
  {
    $params = array('merchantId' => $merchantId, 'productId' => $productId);
    $params = array_merge($params, $optParams);
    return $this->call('delete', array($params));
  }

  /**
   * Retrieves a product from your Merchant Center account. (products.get)
   *
   * @param string $merchantId The ID of the managing account.
   * @param string $productId The ID of the product.
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_Product
   */
  public function get($merchantId, $productId, $optParams = array())
  {
    $params = array('merchantId' => $merchantId, 'productId' => $productId);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Postman_Google_Service_ShoppingContent_Product");
  }

  /**
   * Uploads a product to your Merchant Center account. (products.insert)
   *
   * @param string $merchantId The ID of the managing account.
   * @param Postman_Google_Product $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool dryRun Flag to run the request in dry-run mode.
   * @return Postman_Google_Service_ShoppingContent_Product
   */
  public function insert($merchantId, Postman_Google_Service_ShoppingContent_Product $postBody, $optParams = array())
  {
    $params = array('merchantId' => $merchantId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('insert', array($params), "Postman_Google_Service_ShoppingContent_Product");
  }

  /**
   * Lists the products in your Merchant Center account. (products.listProducts)
   *
   * @param string $merchantId The ID of the managing account.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pageToken The token returned by the previous request.
   * @opt_param string maxResults The maximum number of products to return in the
   * response, used for paging.
   * @return Postman_Google_Service_ShoppingContent_ProductsListResponse
   */
  public function listProducts($merchantId, $optParams = array())
  {
    $params = array('merchantId' => $merchantId);
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Postman_Google_Service_ShoppingContent_ProductsListResponse");
  }
}

/**
 * The "productstatuses" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contentService = new Postman_Google_Service_ShoppingContent(...);
 *   $productstatuses = $contentService->productstatuses;
 *  </code>
 */
class Postman_Google_Service_ShoppingContent_Productstatuses_Resource extends Postman_Google_Service_Resource
{

  /**
   * Gets the statuses of multiple products in a single request.
   * (productstatuses.custombatch)
   *
   * @param Postman_Google_ProductstatusesCustomBatchRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_ProductstatusesCustomBatchResponse
   */
  public function custombatch(Postman_Google_Service_ShoppingContent_ProductstatusesCustomBatchRequest $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('custombatch', array($params), "Postman_Google_Service_ShoppingContent_ProductstatusesCustomBatchResponse");
  }

  /**
   * Gets the status of a product from your Merchant Center account.
   * (productstatuses.get)
   *
   * @param string $merchantId The ID of the managing account.
   * @param string $productId The ID of the product.
   * @param array $optParams Optional parameters.
   * @return Postman_Google_Service_ShoppingContent_ProductStatus
   */
  public function get($merchantId, $productId, $optParams = array())
  {
    $params = array('merchantId' => $merchantId, 'productId' => $productId);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Postman_Google_Service_ShoppingContent_ProductStatus");
  }

  /**
   * Lists the statuses of the products in your Merchant Center account.
   * (productstatuses.listProductstatuses)
   *
   * @param string $merchantId The ID of the managing account.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pageToken The token returned by the previous request.
   * @opt_param string maxResults The maximum number of product statuses to return
   * in the response, used for paging.
   * @return Postman_Google_Service_ShoppingContent_ProductstatusesListResponse
   */
  public function listProductstatuses($merchantId, $optParams = array())
  {
    $params = array('merchantId' => $merchantId);
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Postman_Google_Service_ShoppingContent_ProductstatusesListResponse");
  }
}




class Postman_Google_Service_ShoppingContent_Account extends Postman_Google_Collection
{
  protected $collection_key = 'users';
  protected $internal_gapi_mappings = array(
  );
  public $adultContent;
  protected $adwordsLinksType = 'Postman_Google_Service_ShoppingContent_AccountAdwordsLink';
  protected $adwordsLinksDataType = 'array';
  public $id;
  public $kind;
  public $name;
  public $reviewsUrl;
  public $sellerId;
  protected $usersType = 'Postman_Google_Service_ShoppingContent_AccountUser';
  protected $usersDataType = 'array';
  public $websiteUrl;


  public function setAdultContent($adultContent)
  {
    $this->adultContent = $adultContent;
  }
  public function getAdultContent()
  {
    return $this->adultContent;
  }
  public function setAdwordsLinks($adwordsLinks)
  {
    $this->adwordsLinks = $adwordsLinks;
  }
  public function getAdwordsLinks()
  {
    return $this->adwordsLinks;
  }
  public function setId($id)
  {
    $this->id = $id;
  }
  public function getId()
  {
    return $this->id;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
  public function setName($name)
  {
    $this->name = $name;
  }
  public function getName()
  {
    return $this->name;
  }
  public function setReviewsUrl($reviewsUrl)
  {
    $this->reviewsUrl = $reviewsUrl;
  }
  public function getReviewsUrl()
  {
    return $this->reviewsUrl;
  }
  public function setSellerId($sellerId)
  {
    $this->sellerId = $sellerId;
  }
  public function getSellerId()
  {
    return $this->sellerId;
  }
  public function setUsers($users)
  {
    $this->users = $users;
  }
  public function getUsers()
  {
    return $this->users;
  }
  public function setWebsiteUrl($websiteUrl)
  {
    $this->websiteUrl = $websiteUrl;
  }
  public function getWebsiteUrl()
  {
    return $this->websiteUrl;
  }
}

class Postman_Google_Service_ShoppingContent_AccountAdwordsLink extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $adwordsId;
  public $status;


  public function setAdwordsId($adwordsId)
  {
    $this->adwordsId = $adwordsId;
  }
  public function getAdwordsId()
  {
    return $this->adwordsId;
  }
  public function setStatus($status)
  {
    $this->status = $status;
  }
  public function getStatus()
  {
    return $this->status;
  }
}

class Postman_Google_Service_ShoppingContent_AccountStatus extends Postman_Google_Collection
{
  protected $collection_key = 'dataQualityIssues';
  protected $internal_gapi_mappings = array(
  );
  public $accountId;
  protected $dataQualityIssuesType = 'Postman_Google_Service_ShoppingContent_AccountStatusDataQualityIssue';
  protected $dataQualityIssuesDataType = 'array';
  public $kind;


  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  public function getAccountId()
  {
    return $this->accountId;
  }
  public function setDataQualityIssues($dataQualityIssues)
  {
    $this->dataQualityIssues = $dataQualityIssues;
  }
  public function getDataQualityIssues()
  {
    return $this->dataQualityIssues;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
}

class Postman_Google_Service_ShoppingContent_AccountStatusDataQualityIssue extends Postman_Google_Collection
{
  protected $collection_key = 'exampleItems';
  protected $internal_gapi_mappings = array(
  );
  public $country;
  public $displayedValue;
  protected $exampleItemsType = 'Postman_Google_Service_ShoppingContent_AccountStatusExampleItem';
  protected $exampleItemsDataType = 'array';
  public $id;
  public $lastChecked;
  public $numItems;
  public $severity;
  public $submittedValue;


  public function setCountry($country)
  {
    $this->country = $country;
  }
  public function getCountry()
  {
    return $this->country;
  }
  public function setDisplayedValue($displayedValue)
  {
    $this->displayedValue = $displayedValue;
  }
  public function getDisplayedValue()
  {
    return $this->displayedValue;
  }
  public function setExampleItems($exampleItems)
  {
    $this->exampleItems = $exampleItems;
  }
  public function getExampleItems()
  {
    return $this->exampleItems;
  }
  public function setId($id)
  {
    $this->id = $id;
  }
  public function getId()
  {
    return $this->id;
  }
  public function setLastChecked($lastChecked)
  {
    $this->lastChecked = $lastChecked;
  }
  public function getLastChecked()
  {
    return $this->lastChecked;
  }
  public function setNumItems($numItems)
  {
    $this->numItems = $numItems;
  }
  public function getNumItems()
  {
    return $this->numItems;
  }
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  public function getSeverity()
  {
    return $this->severity;
  }
  public function setSubmittedValue($submittedValue)
  {
    $this->submittedValue = $submittedValue;
  }
  public function getSubmittedValue()
  {
    return $this->submittedValue;
  }
}

class Postman_Google_Service_ShoppingContent_AccountStatusExampleItem extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $itemId;
  public $link;
  public $submittedValue;
  public $title;
  public $valueOnLandingPage;


  public function setItemId($itemId)
  {
    $this->itemId = $itemId;
  }
  public function getItemId()
  {
    return $this->itemId;
  }
  public function setLink($link)
  {
    $this->link = $link;
  }
  public function getLink()
  {
    return $this->link;
  }
  public function setSubmittedValue($submittedValue)
  {
    $this->submittedValue = $submittedValue;
  }
  public function getSubmittedValue()
  {
    return $this->submittedValue;
  }
  public function setTitle($title)
  {
    $this->title = $title;
  }
  public function getTitle()
  {
    return $this->title;
  }
  public function setValueOnLandingPage($valueOnLandingPage)
  {
    $this->valueOnLandingPage = $valueOnLandingPage;
  }
  public function getValueOnLandingPage()
  {
    return $this->valueOnLandingPage;
  }
}

class Postman_Google_Service_ShoppingContent_AccountUser extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $admin;
  public $emailAddress;


  public function setAdmin($admin)
  {
    $this->admin = $admin;
  }
  public function getAdmin()
  {
    return $this->admin;
  }
  public function setEmailAddress($emailAddress)
  {
    $this->emailAddress = $emailAddress;
  }
  public function getEmailAddress()
  {
    return $this->emailAddress;
  }
}

class Postman_Google_Service_ShoppingContent_AccountsCustomBatchRequest extends Postman_Google_Collection
{
  protected $collection_key = 'entries';
  protected $internal_gapi_mappings = array(
  );
  protected $entriesType = 'Postman_Google_Service_ShoppingContent_AccountsCustomBatchRequestEntry';
  protected $entriesDataType = 'array';


  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  public function getEntries()
  {
    return $this->entries;
  }
}

class Postman_Google_Service_ShoppingContent_AccountsCustomBatchRequestEntry extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $accountType = 'Postman_Google_Service_ShoppingContent_Account';
  protected $accountDataType = '';
  public $accountId;
  public $batchId;
  public $merchantId;
  public $method;


  public function setAccount(Postman_Google_Service_ShoppingContent_Account $account)
  {
    $this->account = $account;
  }
  public function getAccount()
  {
    return $this->account;
  }
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  public function getAccountId()
  {
    return $this->accountId;
  }
  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  public function getBatchId()
  {
    return $this->batchId;
  }
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  public function getMerchantId()
  {
    return $this->merchantId;
  }
  public function setMethod($method)
  {
    $this->method = $method;
  }
  public function getMethod()
  {
    return $this->method;
  }
}

class Postman_Google_Service_ShoppingContent_AccountsCustomBatchResponse extends Postman_Google_Collection
{
  protected $collection_key = 'entries';
  protected $internal_gapi_mappings = array(
  );
  protected $entriesType = 'Postman_Google_Service_ShoppingContent_AccountsCustomBatchResponseEntry';
  protected $entriesDataType = 'array';
  public $kind;


  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  public function getEntries()
  {
    return $this->entries;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
}

class Postman_Google_Service_ShoppingContent_AccountsCustomBatchResponseEntry extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $accountType = 'Postman_Google_Service_ShoppingContent_Account';
  protected $accountDataType = '';
  public $batchId;
  protected $errorsType = 'Postman_Google_Service_ShoppingContent_Errors';
  protected $errorsDataType = '';
  public $kind;


  public function setAccount(Postman_Google_Service_ShoppingContent_Account $account)
  {
    $this->account = $account;
  }
  public function getAccount()
  {
    return $this->account;
  }
  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  public function getBatchId()
  {
    return $this->batchId;
  }
  public function setErrors(Postman_Google_Service_ShoppingContent_Errors $errors)
  {
    $this->errors = $errors;
  }
  public function getErrors()
  {
    return $this->errors;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
}

class Postman_Google_Service_ShoppingContent_AccountsListResponse extends Postman_Google_Collection
{
  protected $collection_key = 'resources';
  protected $internal_gapi_mappings = array(
  );
  public $kind;
  public $nextPageToken;
  protected $resourcesType = 'Postman_Google_Service_ShoppingContent_Account';
  protected $resourcesDataType = 'array';


  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  public function getResources()
  {
    return $this->resources;
  }
}

class Postman_Google_Service_ShoppingContent_AccountstatusesCustomBatchRequest extends Postman_Google_Collection
{
  protected $collection_key = 'entries';
  protected $internal_gapi_mappings = array(
  );
  protected $entriesType = 'Postman_Google_Service_ShoppingContent_AccountstatusesCustomBatchRequestEntry';
  protected $entriesDataType = 'array';


  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  public function getEntries()
  {
    return $this->entries;
  }
}

class Postman_Google_Service_ShoppingContent_AccountstatusesCustomBatchRequestEntry extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $accountId;
  public $batchId;
  public $merchantId;
  public $method;


  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  public function getAccountId()
  {
    return $this->accountId;
  }
  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  public function getBatchId()
  {
    return $this->batchId;
  }
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  public function getMerchantId()
  {
    return $this->merchantId;
  }
  public function setMethod($method)
  {
    $this->method = $method;
  }
  public function getMethod()
  {
    return $this->method;
  }
}

class Postman_Google_Service_ShoppingContent_AccountstatusesCustomBatchResponse extends Postman_Google_Collection
{
  protected $collection_key = 'entries';
  protected $internal_gapi_mappings = array(
  );
  protected $entriesType = 'Postman_Google_Service_ShoppingContent_AccountstatusesCustomBatchResponseEntry';
  protected $entriesDataType = 'array';
  public $kind;


  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  public function getEntries()
  {
    return $this->entries;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
}

class Postman_Google_Service_ShoppingContent_AccountstatusesCustomBatchResponseEntry extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $accountStatusType = 'Postman_Google_Service_ShoppingContent_AccountStatus';
  protected $accountStatusDataType = '';
  public $batchId;
  protected $errorsType = 'Postman_Google_Service_ShoppingContent_Errors';
  protected $errorsDataType = '';


  public function setAccountStatus(Postman_Google_Service_ShoppingContent_AccountStatus $accountStatus)
  {
    $this->accountStatus = $accountStatus;
  }
  public function getAccountStatus()
  {
    return $this->accountStatus;
  }
  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  public function getBatchId()
  {
    return $this->batchId;
  }
  public function setErrors(Postman_Google_Service_ShoppingContent_Errors $errors)
  {
    $this->errors = $errors;
  }
  public function getErrors()
  {
    return $this->errors;
  }
}

class Postman_Google_Service_ShoppingContent_AccountstatusesListResponse extends Postman_Google_Collection
{
  protected $collection_key = 'resources';
  protected $internal_gapi_mappings = array(
  );
  public $kind;
  public $nextPageToken;
  protected $resourcesType = 'Postman_Google_Service_ShoppingContent_AccountStatus';
  protected $resourcesDataType = 'array';


  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  public function getResources()
  {
    return $this->resources;
  }
}

class Postman_Google_Service_ShoppingContent_Datafeed extends Postman_Google_Collection
{
  protected $collection_key = 'intendedDestinations';
  protected $internal_gapi_mappings = array(
  );
  public $attributeLanguage;
  public $contentLanguage;
  public $contentType;
  protected $fetchScheduleType = 'Postman_Google_Service_ShoppingContent_DatafeedFetchSchedule';
  protected $fetchScheduleDataType = '';
  public $fileName;
  protected $formatType = 'Postman_Google_Service_ShoppingContent_DatafeedFormat';
  protected $formatDataType = '';
  public $id;
  public $intendedDestinations;
  public $kind;
  public $name;
  public $targetCountry;


  public function setAttributeLanguage($attributeLanguage)
  {
    $this->attributeLanguage = $attributeLanguage;
  }
  public function getAttributeLanguage()
  {
    return $this->attributeLanguage;
  }
  public function setContentLanguage($contentLanguage)
  {
    $this->contentLanguage = $contentLanguage;
  }
  public function getContentLanguage()
  {
    return $this->contentLanguage;
  }
  public function setContentType($contentType)
  {
    $this->contentType = $contentType;
  }
  public function getContentType()
  {
    return $this->contentType;
  }
  public function setFetchSchedule(Postman_Google_Service_ShoppingContent_DatafeedFetchSchedule $fetchSchedule)
  {
    $this->fetchSchedule = $fetchSchedule;
  }
  public function getFetchSchedule()
  {
    return $this->fetchSchedule;
  }
  public function setFileName($fileName)
  {
    $this->fileName = $fileName;
  }
  public function getFileName()
  {
    return $this->fileName;
  }
  public function setFormat(Postman_Google_Service_ShoppingContent_DatafeedFormat $format)
  {
    $this->format = $format;
  }
  public function getFormat()
  {
    return $this->format;
  }
  public function setId($id)
  {
    $this->id = $id;
  }
  public function getId()
  {
    return $this->id;
  }
  public function setIntendedDestinations($intendedDestinations)
  {
    $this->intendedDestinations = $intendedDestinations;
  }
  public function getIntendedDestinations()
  {
    return $this->intendedDestinations;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
  public function setName($name)
  {
    $this->name = $name;
  }
  public function getName()
  {
    return $this->name;
  }
  public function setTargetCountry($targetCountry)
  {
    $this->targetCountry = $targetCountry;
  }
  public function getTargetCountry()
  {
    return $this->targetCountry;
  }
}

class Postman_Google_Service_ShoppingContent_DatafeedFetchSchedule extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $dayOfMonth;
  public $fetchUrl;
  public $hour;
  public $password;
  public $timeZone;
  public $username;
  public $weekday;


  public function setDayOfMonth($dayOfMonth)
  {
    $this->dayOfMonth = $dayOfMonth;
  }
  public function getDayOfMonth()
  {
    return $this->dayOfMonth;
  }
  public function setFetchUrl($fetchUrl)
  {
    $this->fetchUrl = $fetchUrl;
  }
  public function getFetchUrl()
  {
    return $this->fetchUrl;
  }
  public function setHour($hour)
  {
    $this->hour = $hour;
  }
  public function getHour()
  {
    return $this->hour;
  }
  public function setPassword($password)
  {
    $this->password = $password;
  }
  public function getPassword()
  {
    return $this->password;
  }
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  public function setUsername($username)
  {
    $this->username = $username;
  }
  public function getUsername()
  {
    return $this->username;
  }
  public function setWeekday($weekday)
  {
    $this->weekday = $weekday;
  }
  public function getWeekday()
  {
    return $this->weekday;
  }
}

class Postman_Google_Service_ShoppingContent_DatafeedFormat extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $columnDelimiter;
  public $fileEncoding;
  public $quotingMode;


  public function setColumnDelimiter($columnDelimiter)
  {
    $this->columnDelimiter = $columnDelimiter;
  }
  public function getColumnDelimiter()
  {
    return $this->columnDelimiter;
  }
  public function setFileEncoding($fileEncoding)
  {
    $this->fileEncoding = $fileEncoding;
  }
  public function getFileEncoding()
  {
    return $this->fileEncoding;
  }
  public function setQuotingMode($quotingMode)
  {
    $this->quotingMode = $quotingMode;
  }
  public function getQuotingMode()
  {
    return $this->quotingMode;
  }
}

class Postman_Google_Service_ShoppingContent_DatafeedStatus extends Postman_Google_Collection
{
  protected $collection_key = 'warnings';
  protected $internal_gapi_mappings = array(
  );
  public $datafeedId;
  protected $errorsType = 'Postman_Google_Service_ShoppingContent_DatafeedStatusError';
  protected $errorsDataType = 'array';
  public $itemsTotal;
  public $itemsValid;
  public $kind;
  public $processingStatus;
  protected $warningsType = 'Postman_Google_Service_ShoppingContent_DatafeedStatusError';
  protected $warningsDataType = 'array';


  public function setDatafeedId($datafeedId)
  {
    $this->datafeedId = $datafeedId;
  }
  public function getDatafeedId()
  {
    return $this->datafeedId;
  }
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  public function getErrors()
  {
    return $this->errors;
  }
  public function setItemsTotal($itemsTotal)
  {
    $this->itemsTotal = $itemsTotal;
  }
  public function getItemsTotal()
  {
    return $this->itemsTotal;
  }
  public function setItemsValid($itemsValid)
  {
    $this->itemsValid = $itemsValid;
  }
  public function getItemsValid()
  {
    return $this->itemsValid;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
  public function setProcessingStatus($processingStatus)
  {
    $this->processingStatus = $processingStatus;
  }
  public function getProcessingStatus()
  {
    return $this->processingStatus;
  }
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  public function getWarnings()
  {
    return $this->warnings;
  }
}

class Postman_Google_Service_ShoppingContent_DatafeedStatusError extends Postman_Google_Collection
{
  protected $collection_key = 'examples';
  protected $internal_gapi_mappings = array(
  );
  public $code;
  public $count;
  protected $examplesType = 'Postman_Google_Service_ShoppingContent_DatafeedStatusExample';
  protected $examplesDataType = 'array';
  public $message;


  public function setCode($code)
  {
    $this->code = $code;
  }
  public function getCode()
  {
    return $this->code;
  }
  public function setCount($count)
  {
    $this->count = $count;
  }
  public function getCount()
  {
    return $this->count;
  }
  public function setExamples($examples)
  {
    $this->examples = $examples;
  }
  public function getExamples()
  {
    return $this->examples;
  }
  public function setMessage($message)
  {
    $this->message = $message;
  }
  public function getMessage()
  {
    return $this->message;
  }
}

class Postman_Google_Service_ShoppingContent_DatafeedStatusExample extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $itemId;
  public $lineNumber;
  public $value;


  public function setItemId($itemId)
  {
    $this->itemId = $itemId;
  }
  public function getItemId()
  {
    return $this->itemId;
  }
  public function setLineNumber($lineNumber)
  {
    $this->lineNumber = $lineNumber;
  }
  public function getLineNumber()
  {
    return $this->lineNumber;
  }
  public function setValue($value)
  {
    $this->value = $value;
  }
  public function getValue()
  {
    return $this->value;
  }
}

class Postman_Google_Service_ShoppingContent_DatafeedsCustomBatchRequest extends Postman_Google_Collection
{
  protected $collection_key = 'entries';
  protected $internal_gapi_mappings = array(
  );
  protected $entriesType = 'Postman_Google_Service_ShoppingContent_DatafeedsCustomBatchRequestEntry';
  protected $entriesDataType = 'array';


  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  public function getEntries()
  {
    return $this->entries;
  }
}

class Postman_Google_Service_ShoppingContent_DatafeedsCustomBatchRequestEntry extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $batchId;
  protected $datafeedType = 'Postman_Google_Service_ShoppingContent_Datafeed';
  protected $datafeedDataType = '';
  public $datafeedId;
  public $merchantId;
  public $method;


  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  public function getBatchId()
  {
    return $this->batchId;
  }
  public function setDatafeed(Postman_Google_Service_ShoppingContent_Datafeed $datafeed)
  {
    $this->datafeed = $datafeed;
  }
  public function getDatafeed()
  {
    return $this->datafeed;
  }
  public function setDatafeedId($datafeedId)
  {
    $this->datafeedId = $datafeedId;
  }
  public function getDatafeedId()
  {
    return $this->datafeedId;
  }
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  public function getMerchantId()
  {
    return $this->merchantId;
  }
  public function setMethod($method)
  {
    $this->method = $method;
  }
  public function getMethod()
  {
    return $this->method;
  }
}

class Postman_Google_Service_ShoppingContent_DatafeedsCustomBatchResponse extends Postman_Google_Collection
{
  protected $collection_key = 'entries';
  protected $internal_gapi_mappings = array(
  );
  protected $entriesType = 'Postman_Google_Service_ShoppingContent_DatafeedsCustomBatchResponseEntry';
  protected $entriesDataType = 'array';
  public $kind;


  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  public function getEntries()
  {
    return $this->entries;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
}

class Postman_Google_Service_ShoppingContent_DatafeedsCustomBatchResponseEntry extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $batchId;
  protected $datafeedType = 'Postman_Google_Service_ShoppingContent_Datafeed';
  protected $datafeedDataType = '';
  protected $errorsType = 'Postman_Google_Service_ShoppingContent_Errors';
  protected $errorsDataType = '';


  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  public function getBatchId()
  {
    return $this->batchId;
  }
  public function setDatafeed(Postman_Google_Service_ShoppingContent_Datafeed $datafeed)
  {
    $this->datafeed = $datafeed;
  }
  public function getDatafeed()
  {
    return $this->datafeed;
  }
  public function setErrors(Postman_Google_Service_ShoppingContent_Errors $errors)
  {
    $this->errors = $errors;
  }
  public function getErrors()
  {
    return $this->errors;
  }
}

class Postman_Google_Service_ShoppingContent_DatafeedsListResponse extends Postman_Google_Collection
{
  protected $collection_key = 'resources';
  protected $internal_gapi_mappings = array(
  );
  public $kind;
  public $nextPageToken;
  protected $resourcesType = 'Postman_Google_Service_ShoppingContent_Datafeed';
  protected $resourcesDataType = 'array';


  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  public function getResources()
  {
    return $this->resources;
  }
}

class Postman_Google_Service_ShoppingContent_DatafeedstatusesCustomBatchRequest extends Postman_Google_Collection
{
  protected $collection_key = 'entries';
  protected $internal_gapi_mappings = array(
  );
  protected $entriesType = 'Postman_Google_Service_ShoppingContent_DatafeedstatusesCustomBatchRequestEntry';
  protected $entriesDataType = 'array';


  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  public function getEntries()
  {
    return $this->entries;
  }
}

class Postman_Google_Service_ShoppingContent_DatafeedstatusesCustomBatchRequestEntry extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $batchId;
  public $datafeedId;
  public $merchantId;
  public $method;


  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  public function getBatchId()
  {
    return $this->batchId;
  }
  public function setDatafeedId($datafeedId)
  {
    $this->datafeedId = $datafeedId;
  }
  public function getDatafeedId()
  {
    return $this->datafeedId;
  }
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  public function getMerchantId()
  {
    return $this->merchantId;
  }
  public function setMethod($method)
  {
    $this->method = $method;
  }
  public function getMethod()
  {
    return $this->method;
  }
}

class Postman_Google_Service_ShoppingContent_DatafeedstatusesCustomBatchResponse extends Postman_Google_Collection
{
  protected $collection_key = 'entries';
  protected $internal_gapi_mappings = array(
  );
  protected $entriesType = 'Postman_Google_Service_ShoppingContent_DatafeedstatusesCustomBatchResponseEntry';
  protected $entriesDataType = 'array';
  public $kind;


  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  public function getEntries()
  {
    return $this->entries;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
}

class Postman_Google_Service_ShoppingContent_DatafeedstatusesCustomBatchResponseEntry extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $batchId;
  protected $datafeedStatusType = 'Postman_Google_Service_ShoppingContent_DatafeedStatus';
  protected $datafeedStatusDataType = '';
  protected $errorsType = 'Postman_Google_Service_ShoppingContent_Errors';
  protected $errorsDataType = '';


  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  public function getBatchId()
  {
    return $this->batchId;
  }
  public function setDatafeedStatus(Postman_Google_Service_ShoppingContent_DatafeedStatus $datafeedStatus)
  {
    $this->datafeedStatus = $datafeedStatus;
  }
  public function getDatafeedStatus()
  {
    return $this->datafeedStatus;
  }
  public function setErrors(Postman_Google_Service_ShoppingContent_Errors $errors)
  {
    $this->errors = $errors;
  }
  public function getErrors()
  {
    return $this->errors;
  }
}

class Postman_Google_Service_ShoppingContent_DatafeedstatusesListResponse extends Postman_Google_Collection
{
  protected $collection_key = 'resources';
  protected $internal_gapi_mappings = array(
  );
  public $kind;
  public $nextPageToken;
  protected $resourcesType = 'Postman_Google_Service_ShoppingContent_DatafeedStatus';
  protected $resourcesDataType = 'array';


  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  public function getResources()
  {
    return $this->resources;
  }
}

class Postman_Google_Service_ShoppingContent_Error extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $domain;
  public $message;
  public $reason;


  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  public function getDomain()
  {
    return $this->domain;
  }
  public function setMessage($message)
  {
    $this->message = $message;
  }
  public function getMessage()
  {
    return $this->message;
  }
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  public function getReason()
  {
    return $this->reason;
  }
}

class Postman_Google_Service_ShoppingContent_Errors extends Postman_Google_Collection
{
  protected $collection_key = 'errors';
  protected $internal_gapi_mappings = array(
  );
  public $code;
  protected $errorsType = 'Postman_Google_Service_ShoppingContent_Error';
  protected $errorsDataType = 'array';
  public $message;


  public function setCode($code)
  {
    $this->code = $code;
  }
  public function getCode()
  {
    return $this->code;
  }
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  public function getErrors()
  {
    return $this->errors;
  }
  public function setMessage($message)
  {
    $this->message = $message;
  }
  public function getMessage()
  {
    return $this->message;
  }
}

class Postman_Google_Service_ShoppingContent_Inventory extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $availability;
  public $kind;
  protected $priceType = 'Postman_Google_Service_ShoppingContent_Price';
  protected $priceDataType = '';
  public $quantity;
  protected $salePriceType = 'Postman_Google_Service_ShoppingContent_Price';
  protected $salePriceDataType = '';
  public $salePriceEffectiveDate;


  public function setAvailability($availability)
  {
    $this->availability = $availability;
  }
  public function getAvailability()
  {
    return $this->availability;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
  public function setPrice(Postman_Google_Service_ShoppingContent_Price $price)
  {
    $this->price = $price;
  }
  public function getPrice()
  {
    return $this->price;
  }
  public function setQuantity($quantity)
  {
    $this->quantity = $quantity;
  }
  public function getQuantity()
  {
    return $this->quantity;
  }
  public function setSalePrice(Postman_Google_Service_ShoppingContent_Price $salePrice)
  {
    $this->salePrice = $salePrice;
  }
  public function getSalePrice()
  {
    return $this->salePrice;
  }
  public function setSalePriceEffectiveDate($salePriceEffectiveDate)
  {
    $this->salePriceEffectiveDate = $salePriceEffectiveDate;
  }
  public function getSalePriceEffectiveDate()
  {
    return $this->salePriceEffectiveDate;
  }
}

class Postman_Google_Service_ShoppingContent_InventoryCustomBatchRequest extends Postman_Google_Collection
{
  protected $collection_key = 'entries';
  protected $internal_gapi_mappings = array(
  );
  protected $entriesType = 'Postman_Google_Service_ShoppingContent_InventoryCustomBatchRequestEntry';
  protected $entriesDataType = 'array';


  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  public function getEntries()
  {
    return $this->entries;
  }
}

class Postman_Google_Service_ShoppingContent_InventoryCustomBatchRequestEntry extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $batchId;
  protected $inventoryType = 'Postman_Google_Service_ShoppingContent_Inventory';
  protected $inventoryDataType = '';
  public $merchantId;
  public $productId;
  public $storeCode;


  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  public function getBatchId()
  {
    return $this->batchId;
  }
  public function setInventory(Postman_Google_Service_ShoppingContent_Inventory $inventory)
  {
    $this->inventory = $inventory;
  }
  public function getInventory()
  {
    return $this->inventory;
  }
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  public function getMerchantId()
  {
    return $this->merchantId;
  }
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  public function getProductId()
  {
    return $this->productId;
  }
  public function setStoreCode($storeCode)
  {
    $this->storeCode = $storeCode;
  }
  public function getStoreCode()
  {
    return $this->storeCode;
  }
}

class Postman_Google_Service_ShoppingContent_InventoryCustomBatchResponse extends Postman_Google_Collection
{
  protected $collection_key = 'entries';
  protected $internal_gapi_mappings = array(
  );
  protected $entriesType = 'Postman_Google_Service_ShoppingContent_InventoryCustomBatchResponseEntry';
  protected $entriesDataType = 'array';
  public $kind;


  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  public function getEntries()
  {
    return $this->entries;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
}

class Postman_Google_Service_ShoppingContent_InventoryCustomBatchResponseEntry extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $batchId;
  protected $errorsType = 'Postman_Google_Service_ShoppingContent_Errors';
  protected $errorsDataType = '';
  public $kind;


  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  public function getBatchId()
  {
    return $this->batchId;
  }
  public function setErrors(Postman_Google_Service_ShoppingContent_Errors $errors)
  {
    $this->errors = $errors;
  }
  public function getErrors()
  {
    return $this->errors;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
}

class Postman_Google_Service_ShoppingContent_InventorySetRequest extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $availability;
  protected $priceType = 'Postman_Google_Service_ShoppingContent_Price';
  protected $priceDataType = '';
  public $quantity;
  protected $salePriceType = 'Postman_Google_Service_ShoppingContent_Price';
  protected $salePriceDataType = '';
  public $salePriceEffectiveDate;


  public function setAvailability($availability)
  {
    $this->availability = $availability;
  }
  public function getAvailability()
  {
    return $this->availability;
  }
  public function setPrice(Postman_Google_Service_ShoppingContent_Price $price)
  {
    $this->price = $price;
  }
  public function getPrice()
  {
    return $this->price;
  }
  public function setQuantity($quantity)
  {
    $this->quantity = $quantity;
  }
  public function getQuantity()
  {
    return $this->quantity;
  }
  public function setSalePrice(Postman_Google_Service_ShoppingContent_Price $salePrice)
  {
    $this->salePrice = $salePrice;
  }
  public function getSalePrice()
  {
    return $this->salePrice;
  }
  public function setSalePriceEffectiveDate($salePriceEffectiveDate)
  {
    $this->salePriceEffectiveDate = $salePriceEffectiveDate;
  }
  public function getSalePriceEffectiveDate()
  {
    return $this->salePriceEffectiveDate;
  }
}

class Postman_Google_Service_ShoppingContent_InventorySetResponse extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $kind;


  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
}

class Postman_Google_Service_ShoppingContent_LoyaltyPoints extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $name;
  public $pointsValue;
  public $ratio;


  public function setName($name)
  {
    $this->name = $name;
  }
  public function getName()
  {
    return $this->name;
  }
  public function setPointsValue($pointsValue)
  {
    $this->pointsValue = $pointsValue;
  }
  public function getPointsValue()
  {
    return $this->pointsValue;
  }
  public function setRatio($ratio)
  {
    $this->ratio = $ratio;
  }
  public function getRatio()
  {
    return $this->ratio;
  }
}

class Postman_Google_Service_ShoppingContent_Price extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $currency;
  public $value;


  public function setCurrency($currency)
  {
    $this->currency = $currency;
  }
  public function getCurrency()
  {
    return $this->currency;
  }
  public function setValue($value)
  {
    $this->value = $value;
  }
  public function getValue()
  {
    return $this->value;
  }
}

class Postman_Google_Service_ShoppingContent_Product extends Postman_Google_Collection
{
  protected $collection_key = 'warnings';
  protected $internal_gapi_mappings = array(
  );
  public $additionalImageLinks;
  public $adult;
  public $adwordsGrouping;
  public $adwordsLabels;
  public $adwordsRedirect;
  public $ageGroup;
  public $availability;
  public $availabilityDate;
  public $brand;
  public $channel;
  public $color;
  public $condition;
  public $contentLanguage;
  protected $customAttributesType = 'Postman_Google_Service_ShoppingContent_ProductCustomAttribute';
  protected $customAttributesDataType = 'array';
  protected $customGroupsType = 'Postman_Google_Service_ShoppingContent_ProductCustomGroup';
  protected $customGroupsDataType = 'array';
  public $customLabel0;
  public $customLabel1;
  public $customLabel2;
  public $customLabel3;
  public $customLabel4;
  public $description;
  protected $destinationsType = 'Postman_Google_Service_ShoppingContent_ProductDestination';
  protected $destinationsDataType = 'array';
  public $energyEfficiencyClass;
  public $expirationDate;
  public $gender;
  public $googleProductCategory;
  public $gtin;
  public $id;
  public $identifierExists;
  public $imageLink;
  protected $installmentType = 'Postman_Google_Service_ShoppingContent_ProductInstallment';
  protected $installmentDataType = '';
  public $isBundle;
  public $itemGroupId;
  public $kind;
  public $link;
  protected $loyaltyPointsType = 'Postman_Google_Service_ShoppingContent_LoyaltyPoints';
  protected $loyaltyPointsDataType = '';
  public $material;
  public $mobileLink;
  public $mpn;
  public $multipack;
  public $offerId;
  public $onlineOnly;
  public $pattern;
  protected $priceType = 'Postman_Google_Service_ShoppingContent_Price';
  protected $priceDataType = '';
  public $productType;
  protected $salePriceType = 'Postman_Google_Service_ShoppingContent_Price';
  protected $salePriceDataType = '';
  public $salePriceEffectiveDate;
  protected $shippingType = 'Postman_Google_Service_ShoppingContent_ProductShipping';
  protected $shippingDataType = 'array';
  public $shippingLabel;
  protected $shippingWeightType = 'Postman_Google_Service_ShoppingContent_ProductShippingWeight';
  protected $shippingWeightDataType = '';
  public $sizeSystem;
  public $sizeType;
  public $sizes;
  public $targetCountry;
  protected $taxesType = 'Postman_Google_Service_ShoppingContent_ProductTax';
  protected $taxesDataType = 'array';
  public $title;
  protected $unitPricingBaseMeasureType = 'Postman_Google_Service_ShoppingContent_ProductUnitPricingBaseMeasure';
  protected $unitPricingBaseMeasureDataType = '';
  protected $unitPricingMeasureType = 'Postman_Google_Service_ShoppingContent_ProductUnitPricingMeasure';
  protected $unitPricingMeasureDataType = '';
  public $validatedDestinations;
  protected $warningsType = 'Postman_Google_Service_ShoppingContent_Error';
  protected $warningsDataType = 'array';


  public function setAdditionalImageLinks($additionalImageLinks)
  {
    $this->additionalImageLinks = $additionalImageLinks;
  }
  public function getAdditionalImageLinks()
  {
    return $this->additionalImageLinks;
  }
  public function setAdult($adult)
  {
    $this->adult = $adult;
  }
  public function getAdult()
  {
    return $this->adult;
  }
  public function setAdwordsGrouping($adwordsGrouping)
  {
    $this->adwordsGrouping = $adwordsGrouping;
  }
  public function getAdwordsGrouping()
  {
    return $this->adwordsGrouping;
  }
  public function setAdwordsLabels($adwordsLabels)
  {
    $this->adwordsLabels = $adwordsLabels;
  }
  public function getAdwordsLabels()
  {
    return $this->adwordsLabels;
  }
  public function setAdwordsRedirect($adwordsRedirect)
  {
    $this->adwordsRedirect = $adwordsRedirect;
  }
  public function getAdwordsRedirect()
  {
    return $this->adwordsRedirect;
  }
  public function setAgeGroup($ageGroup)
  {
    $this->ageGroup = $ageGroup;
  }
  public function getAgeGroup()
  {
    return $this->ageGroup;
  }
  public function setAvailability($availability)
  {
    $this->availability = $availability;
  }
  public function getAvailability()
  {
    return $this->availability;
  }
  public function setAvailabilityDate($availabilityDate)
  {
    $this->availabilityDate = $availabilityDate;
  }
  public function getAvailabilityDate()
  {
    return $this->availabilityDate;
  }
  public function setBrand($brand)
  {
    $this->brand = $brand;
  }
  public function getBrand()
  {
    return $this->brand;
  }
  public function setChannel($channel)
  {
    $this->channel = $channel;
  }
  public function getChannel()
  {
    return $this->channel;
  }
  public function setColor($color)
  {
    $this->color = $color;
  }
  public function getColor()
  {
    return $this->color;
  }
  public function setCondition($condition)
  {
    $this->condition = $condition;
  }
  public function getCondition()
  {
    return $this->condition;
  }
  public function setContentLanguage($contentLanguage)
  {
    $this->contentLanguage = $contentLanguage;
  }
  public function getContentLanguage()
  {
    return $this->contentLanguage;
  }
  public function setCustomAttributes($customAttributes)
  {
    $this->customAttributes = $customAttributes;
  }
  public function getCustomAttributes()
  {
    return $this->customAttributes;
  }
  public function setCustomGroups($customGroups)
  {
    $this->customGroups = $customGroups;
  }
  public function getCustomGroups()
  {
    return $this->customGroups;
  }
  public function setCustomLabel0($customLabel0)
  {
    $this->customLabel0 = $customLabel0;
  }
  public function getCustomLabel0()
  {
    return $this->customLabel0;
  }
  public function setCustomLabel1($customLabel1)
  {
    $this->customLabel1 = $customLabel1;
  }
  public function getCustomLabel1()
  {
    return $this->customLabel1;
  }
  public function setCustomLabel2($customLabel2)
  {
    $this->customLabel2 = $customLabel2;
  }
  public function getCustomLabel2()
  {
    return $this->customLabel2;
  }
  public function setCustomLabel3($customLabel3)
  {
    $this->customLabel3 = $customLabel3;
  }
  public function getCustomLabel3()
  {
    return $this->customLabel3;
  }
  public function setCustomLabel4($customLabel4)
  {
    $this->customLabel4 = $customLabel4;
  }
  public function getCustomLabel4()
  {
    return $this->customLabel4;
  }
  public function setDescription($description)
  {
    $this->description = $description;
  }
  public function getDescription()
  {
    return $this->description;
  }
  public function setDestinations($destinations)
  {
    $this->destinations = $destinations;
  }
  public function getDestinations()
  {
    return $this->destinations;
  }
  public function setEnergyEfficiencyClass($energyEfficiencyClass)
  {
    $this->energyEfficiencyClass = $energyEfficiencyClass;
  }
  public function getEnergyEfficiencyClass()
  {
    return $this->energyEfficiencyClass;
  }
  public function setExpirationDate($expirationDate)
  {
    $this->expirationDate = $expirationDate;
  }
  public function getExpirationDate()
  {
    return $this->expirationDate;
  }
  public function setGender($gender)
  {
    $this->gender = $gender;
  }
  public function getGender()
  {
    return $this->gender;
  }
  public function setGoogleProductCategory($googleProductCategory)
  {
    $this->googleProductCategory = $googleProductCategory;
  }
  public function getGoogleProductCategory()
  {
    return $this->googleProductCategory;
  }
  public function setGtin($gtin)
  {
    $this->gtin = $gtin;
  }
  public function getGtin()
  {
    return $this->gtin;
  }
  public function setId($id)
  {
    $this->id = $id;
  }
  public function getId()
  {
    return $this->id;
  }
  public function setIdentifierExists($identifierExists)
  {
    $this->identifierExists = $identifierExists;
  }
  public function getIdentifierExists()
  {
    return $this->identifierExists;
  }
  public function setImageLink($imageLink)
  {
    $this->imageLink = $imageLink;
  }
  public function getImageLink()
  {
    return $this->imageLink;
  }
  public function setInstallment(Postman_Google_Service_ShoppingContent_ProductInstallment $installment)
  {
    $this->installment = $installment;
  }
  public function getInstallment()
  {
    return $this->installment;
  }
  public function setIsBundle($isBundle)
  {
    $this->isBundle = $isBundle;
  }
  public function getIsBundle()
  {
    return $this->isBundle;
  }
  public function setItemGroupId($itemGroupId)
  {
    $this->itemGroupId = $itemGroupId;
  }
  public function getItemGroupId()
  {
    return $this->itemGroupId;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
  public function setLink($link)
  {
    $this->link = $link;
  }
  public function getLink()
  {
    return $this->link;
  }
  public function setLoyaltyPoints(Postman_Google_Service_ShoppingContent_LoyaltyPoints $loyaltyPoints)
  {
    $this->loyaltyPoints = $loyaltyPoints;
  }
  public function getLoyaltyPoints()
  {
    return $this->loyaltyPoints;
  }
  public function setMaterial($material)
  {
    $this->material = $material;
  }
  public function getMaterial()
  {
    return $this->material;
  }
  public function setMobileLink($mobileLink)
  {
    $this->mobileLink = $mobileLink;
  }
  public function getMobileLink()
  {
    return $this->mobileLink;
  }
  public function setMpn($mpn)
  {
    $this->mpn = $mpn;
  }
  public function getMpn()
  {
    return $this->mpn;
  }
  public function setMultipack($multipack)
  {
    $this->multipack = $multipack;
  }
  public function getMultipack()
  {
    return $this->multipack;
  }
  public function setOfferId($offerId)
  {
    $this->offerId = $offerId;
  }
  public function getOfferId()
  {
    return $this->offerId;
  }
  public function setOnlineOnly($onlineOnly)
  {
    $this->onlineOnly = $onlineOnly;
  }
  public function getOnlineOnly()
  {
    return $this->onlineOnly;
  }
  public function setPattern($pattern)
  {
    $this->pattern = $pattern;
  }
  public function getPattern()
  {
    return $this->pattern;
  }
  public function setPrice(Postman_Google_Service_ShoppingContent_Price $price)
  {
    $this->price = $price;
  }
  public function getPrice()
  {
    return $this->price;
  }
  public function setProductType($productType)
  {
    $this->productType = $productType;
  }
  public function getProductType()
  {
    return $this->productType;
  }
  public function setSalePrice(Postman_Google_Service_ShoppingContent_Price $salePrice)
  {
    $this->salePrice = $salePrice;
  }
  public function getSalePrice()
  {
    return $this->salePrice;
  }
  public function setSalePriceEffectiveDate($salePriceEffectiveDate)
  {
    $this->salePriceEffectiveDate = $salePriceEffectiveDate;
  }
  public function getSalePriceEffectiveDate()
  {
    return $this->salePriceEffectiveDate;
  }
  public function setShipping($shipping)
  {
    $this->shipping = $shipping;
  }
  public function getShipping()
  {
    return $this->shipping;
  }
  public function setShippingLabel($shippingLabel)
  {
    $this->shippingLabel = $shippingLabel;
  }
  public function getShippingLabel()
  {
    return $this->shippingLabel;
  }
  public function setShippingWeight(Postman_Google_Service_ShoppingContent_ProductShippingWeight $shippingWeight)
  {
    $this->shippingWeight = $shippingWeight;
  }
  public function getShippingWeight()
  {
    return $this->shippingWeight;
  }
  public function setSizeSystem($sizeSystem)
  {
    $this->sizeSystem = $sizeSystem;
  }
  public function getSizeSystem()
  {
    return $this->sizeSystem;
  }
  public function setSizeType($sizeType)
  {
    $this->sizeType = $sizeType;
  }
  public function getSizeType()
  {
    return $this->sizeType;
  }
  public function setSizes($sizes)
  {
    $this->sizes = $sizes;
  }
  public function getSizes()
  {
    return $this->sizes;
  }
  public function setTargetCountry($targetCountry)
  {
    $this->targetCountry = $targetCountry;
  }
  public function getTargetCountry()
  {
    return $this->targetCountry;
  }
  public function setTaxes($taxes)
  {
    $this->taxes = $taxes;
  }
  public function getTaxes()
  {
    return $this->taxes;
  }
  public function setTitle($title)
  {
    $this->title = $title;
  }
  public function getTitle()
  {
    return $this->title;
  }
  public function setUnitPricingBaseMeasure(Postman_Google_Service_ShoppingContent_ProductUnitPricingBaseMeasure $unitPricingBaseMeasure)
  {
    $this->unitPricingBaseMeasure = $unitPricingBaseMeasure;
  }
  public function getUnitPricingBaseMeasure()
  {
    return $this->unitPricingBaseMeasure;
  }
  public function setUnitPricingMeasure(Postman_Google_Service_ShoppingContent_ProductUnitPricingMeasure $unitPricingMeasure)
  {
    $this->unitPricingMeasure = $unitPricingMeasure;
  }
  public function getUnitPricingMeasure()
  {
    return $this->unitPricingMeasure;
  }
  public function setValidatedDestinations($validatedDestinations)
  {
    $this->validatedDestinations = $validatedDestinations;
  }
  public function getValidatedDestinations()
  {
    return $this->validatedDestinations;
  }
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  public function getWarnings()
  {
    return $this->warnings;
  }
}

class Postman_Google_Service_ShoppingContent_ProductCustomAttribute extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $name;
  public $type;
  public $unit;
  public $value;


  public function setName($name)
  {
    $this->name = $name;
  }
  public function getName()
  {
    return $this->name;
  }
  public function setType($type)
  {
    $this->type = $type;
  }
  public function getType()
  {
    return $this->type;
  }
  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  public function getUnit()
  {
    return $this->unit;
  }
  public function setValue($value)
  {
    $this->value = $value;
  }
  public function getValue()
  {
    return $this->value;
  }
}

class Postman_Google_Service_ShoppingContent_ProductCustomGroup extends Postman_Google_Collection
{
  protected $collection_key = 'attributes';
  protected $internal_gapi_mappings = array(
  );
  protected $attributesType = 'Postman_Google_Service_ShoppingContent_ProductCustomAttribute';
  protected $attributesDataType = 'array';
  public $name;


  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  public function getAttributes()
  {
    return $this->attributes;
  }
  public function setName($name)
  {
    $this->name = $name;
  }
  public function getName()
  {
    return $this->name;
  }
}

class Postman_Google_Service_ShoppingContent_ProductDestination extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $destinationName;
  public $intention;


  public function setDestinationName($destinationName)
  {
    $this->destinationName = $destinationName;
  }
  public function getDestinationName()
  {
    return $this->destinationName;
  }
  public function setIntention($intention)
  {
    $this->intention = $intention;
  }
  public function getIntention()
  {
    return $this->intention;
  }
}

class Postman_Google_Service_ShoppingContent_ProductInstallment extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  protected $amountType = 'Postman_Google_Service_ShoppingContent_Price';
  protected $amountDataType = '';
  public $months;


  public function setAmount(Postman_Google_Service_ShoppingContent_Price $amount)
  {
    $this->amount = $amount;
  }
  public function getAmount()
  {
    return $this->amount;
  }
  public function setMonths($months)
  {
    $this->months = $months;
  }
  public function getMonths()
  {
    return $this->months;
  }
}

class Postman_Google_Service_ShoppingContent_ProductShipping extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $country;
  public $locationGroupName;
  public $locationId;
  public $postalCode;
  protected $priceType = 'Postman_Google_Service_ShoppingContent_Price';
  protected $priceDataType = '';
  public $region;
  public $service;


  public function setCountry($country)
  {
    $this->country = $country;
  }
  public function getCountry()
  {
    return $this->country;
  }
  public function setLocationGroupName($locationGroupName)
  {
    $this->locationGroupName = $locationGroupName;
  }
  public function getLocationGroupName()
  {
    return $this->locationGroupName;
  }
  public function setLocationId($locationId)
  {
    $this->locationId = $locationId;
  }
  public function getLocationId()
  {
    return $this->locationId;
  }
  public function setPostalCode($postalCode)
  {
    $this->postalCode = $postalCode;
  }
  public function getPostalCode()
  {
    return $this->postalCode;
  }
  public function setPrice(Postman_Google_Service_ShoppingContent_Price $price)
  {
    $this->price = $price;
  }
  public function getPrice()
  {
    return $this->price;
  }
  public function setRegion($region)
  {
    $this->region = $region;
  }
  public function getRegion()
  {
    return $this->region;
  }
  public function setService($service)
  {
    $this->service = $service;
  }
  public function getService()
  {
    return $this->service;
  }
}

class Postman_Google_Service_ShoppingContent_ProductShippingWeight extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $unit;
  public $value;


  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  public function getUnit()
  {
    return $this->unit;
  }
  public function setValue($value)
  {
    $this->value = $value;
  }
  public function getValue()
  {
    return $this->value;
  }
}

class Postman_Google_Service_ShoppingContent_ProductStatus extends Postman_Google_Collection
{
  protected $collection_key = 'destinationStatuses';
  protected $internal_gapi_mappings = array(
  );
  protected $dataQualityIssuesType = 'Postman_Google_Service_ShoppingContent_ProductStatusDataQualityIssue';
  protected $dataQualityIssuesDataType = 'array';
  protected $destinationStatusesType = 'Postman_Google_Service_ShoppingContent_ProductStatusDestinationStatus';
  protected $destinationStatusesDataType = 'array';
  public $kind;
  public $link;
  public $productId;
  public $title;


  public function setDataQualityIssues($dataQualityIssues)
  {
    $this->dataQualityIssues = $dataQualityIssues;
  }
  public function getDataQualityIssues()
  {
    return $this->dataQualityIssues;
  }
  public function setDestinationStatuses($destinationStatuses)
  {
    $this->destinationStatuses = $destinationStatuses;
  }
  public function getDestinationStatuses()
  {
    return $this->destinationStatuses;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
  public function setLink($link)
  {
    $this->link = $link;
  }
  public function getLink()
  {
    return $this->link;
  }
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  public function getProductId()
  {
    return $this->productId;
  }
  public function setTitle($title)
  {
    $this->title = $title;
  }
  public function getTitle()
  {
    return $this->title;
  }
}

class Postman_Google_Service_ShoppingContent_ProductStatusDataQualityIssue extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $detail;
  public $fetchStatus;
  public $id;
  public $location;
  public $timestamp;
  public $valueOnLandingPage;
  public $valueProvided;


  public function setDetail($detail)
  {
    $this->detail = $detail;
  }
  public function getDetail()
  {
    return $this->detail;
  }
  public function setFetchStatus($fetchStatus)
  {
    $this->fetchStatus = $fetchStatus;
  }
  public function getFetchStatus()
  {
    return $this->fetchStatus;
  }
  public function setId($id)
  {
    $this->id = $id;
  }
  public function getId()
  {
    return $this->id;
  }
  public function setLocation($location)
  {
    $this->location = $location;
  }
  public function getLocation()
  {
    return $this->location;
  }
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  public function getTimestamp()
  {
    return $this->timestamp;
  }
  public function setValueOnLandingPage($valueOnLandingPage)
  {
    $this->valueOnLandingPage = $valueOnLandingPage;
  }
  public function getValueOnLandingPage()
  {
    return $this->valueOnLandingPage;
  }
  public function setValueProvided($valueProvided)
  {
    $this->valueProvided = $valueProvided;
  }
  public function getValueProvided()
  {
    return $this->valueProvided;
  }
}

class Postman_Google_Service_ShoppingContent_ProductStatusDestinationStatus extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $approvalStatus;
  public $destination;
  public $intention;


  public function setApprovalStatus($approvalStatus)
  {
    $this->approvalStatus = $approvalStatus;
  }
  public function getApprovalStatus()
  {
    return $this->approvalStatus;
  }
  public function setDestination($destination)
  {
    $this->destination = $destination;
  }
  public function getDestination()
  {
    return $this->destination;
  }
  public function setIntention($intention)
  {
    $this->intention = $intention;
  }
  public function getIntention()
  {
    return $this->intention;
  }
}

class Postman_Google_Service_ShoppingContent_ProductTax extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $country;
  public $locationId;
  public $postalCode;
  public $rate;
  public $region;
  public $taxShip;


  public function setCountry($country)
  {
    $this->country = $country;
  }
  public function getCountry()
  {
    return $this->country;
  }
  public function setLocationId($locationId)
  {
    $this->locationId = $locationId;
  }
  public function getLocationId()
  {
    return $this->locationId;
  }
  public function setPostalCode($postalCode)
  {
    $this->postalCode = $postalCode;
  }
  public function getPostalCode()
  {
    return $this->postalCode;
  }
  public function setRate($rate)
  {
    $this->rate = $rate;
  }
  public function getRate()
  {
    return $this->rate;
  }
  public function setRegion($region)
  {
    $this->region = $region;
  }
  public function getRegion()
  {
    return $this->region;
  }
  public function setTaxShip($taxShip)
  {
    $this->taxShip = $taxShip;
  }
  public function getTaxShip()
  {
    return $this->taxShip;
  }
}

class Postman_Google_Service_ShoppingContent_ProductUnitPricingBaseMeasure extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $unit;
  public $value;


  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  public function getUnit()
  {
    return $this->unit;
  }
  public function setValue($value)
  {
    $this->value = $value;
  }
  public function getValue()
  {
    return $this->value;
  }
}

class Postman_Google_Service_ShoppingContent_ProductUnitPricingMeasure extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $unit;
  public $value;


  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  public function getUnit()
  {
    return $this->unit;
  }
  public function setValue($value)
  {
    $this->value = $value;
  }
  public function getValue()
  {
    return $this->value;
  }
}

class Postman_Google_Service_ShoppingContent_ProductsCustomBatchRequest extends Postman_Google_Collection
{
  protected $collection_key = 'entries';
  protected $internal_gapi_mappings = array(
  );
  protected $entriesType = 'Postman_Google_Service_ShoppingContent_ProductsCustomBatchRequestEntry';
  protected $entriesDataType = 'array';


  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  public function getEntries()
  {
    return $this->entries;
  }
}

class Postman_Google_Service_ShoppingContent_ProductsCustomBatchRequestEntry extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $batchId;
  public $merchantId;
  public $method;
  protected $productType = 'Postman_Google_Service_ShoppingContent_Product';
  protected $productDataType = '';
  public $productId;


  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  public function getBatchId()
  {
    return $this->batchId;
  }
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  public function getMerchantId()
  {
    return $this->merchantId;
  }
  public function setMethod($method)
  {
    $this->method = $method;
  }
  public function getMethod()
  {
    return $this->method;
  }
  public function setProduct(Postman_Google_Service_ShoppingContent_Product $product)
  {
    $this->product = $product;
  }
  public function getProduct()
  {
    return $this->product;
  }
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  public function getProductId()
  {
    return $this->productId;
  }
}

class Postman_Google_Service_ShoppingContent_ProductsCustomBatchResponse extends Postman_Google_Collection
{
  protected $collection_key = 'entries';
  protected $internal_gapi_mappings = array(
  );
  protected $entriesType = 'Postman_Google_Service_ShoppingContent_ProductsCustomBatchResponseEntry';
  protected $entriesDataType = 'array';
  public $kind;


  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  public function getEntries()
  {
    return $this->entries;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
}

class Postman_Google_Service_ShoppingContent_ProductsCustomBatchResponseEntry extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $batchId;
  protected $errorsType = 'Postman_Google_Service_ShoppingContent_Errors';
  protected $errorsDataType = '';
  public $kind;
  protected $productType = 'Postman_Google_Service_ShoppingContent_Product';
  protected $productDataType = '';


  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  public function getBatchId()
  {
    return $this->batchId;
  }
  public function setErrors(Postman_Google_Service_ShoppingContent_Errors $errors)
  {
    $this->errors = $errors;
  }
  public function getErrors()
  {
    return $this->errors;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
  public function setProduct(Postman_Google_Service_ShoppingContent_Product $product)
  {
    $this->product = $product;
  }
  public function getProduct()
  {
    return $this->product;
  }
}

class Postman_Google_Service_ShoppingContent_ProductsListResponse extends Postman_Google_Collection
{
  protected $collection_key = 'resources';
  protected $internal_gapi_mappings = array(
  );
  public $kind;
  public $nextPageToken;
  protected $resourcesType = 'Postman_Google_Service_ShoppingContent_Product';
  protected $resourcesDataType = 'array';


  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  public function getResources()
  {
    return $this->resources;
  }
}

class Postman_Google_Service_ShoppingContent_ProductstatusesCustomBatchRequest extends Postman_Google_Collection
{
  protected $collection_key = 'entries';
  protected $internal_gapi_mappings = array(
  );
  protected $entriesType = 'Postman_Google_Service_ShoppingContent_ProductstatusesCustomBatchRequestEntry';
  protected $entriesDataType = 'array';


  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  public function getEntries()
  {
    return $this->entries;
  }
}

class Postman_Google_Service_ShoppingContent_ProductstatusesCustomBatchRequestEntry extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $batchId;
  public $merchantId;
  public $method;
  public $productId;


  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  public function getBatchId()
  {
    return $this->batchId;
  }
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  public function getMerchantId()
  {
    return $this->merchantId;
  }
  public function setMethod($method)
  {
    $this->method = $method;
  }
  public function getMethod()
  {
    return $this->method;
  }
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  public function getProductId()
  {
    return $this->productId;
  }
}

class Postman_Google_Service_ShoppingContent_ProductstatusesCustomBatchResponse extends Postman_Google_Collection
{
  protected $collection_key = 'entries';
  protected $internal_gapi_mappings = array(
  );
  protected $entriesType = 'Postman_Google_Service_ShoppingContent_ProductstatusesCustomBatchResponseEntry';
  protected $entriesDataType = 'array';
  public $kind;


  public function setEntries($entries)
  {
    $this->entries = $entries;
  }
  public function getEntries()
  {
    return $this->entries;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
}

class Postman_Google_Service_ShoppingContent_ProductstatusesCustomBatchResponseEntry extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $batchId;
  protected $errorsType = 'Postman_Google_Service_ShoppingContent_Errors';
  protected $errorsDataType = '';
  public $kind;
  protected $productStatusType = 'Postman_Google_Service_ShoppingContent_ProductStatus';
  protected $productStatusDataType = '';


  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  public function getBatchId()
  {
    return $this->batchId;
  }
  public function setErrors(Postman_Google_Service_ShoppingContent_Errors $errors)
  {
    $this->errors = $errors;
  }
  public function getErrors()
  {
    return $this->errors;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
  public function setProductStatus(Postman_Google_Service_ShoppingContent_ProductStatus $productStatus)
  {
    $this->productStatus = $productStatus;
  }
  public function getProductStatus()
  {
    return $this->productStatus;
  }
}

class Postman_Google_Service_ShoppingContent_ProductstatusesListResponse extends Postman_Google_Collection
{
  protected $collection_key = 'resources';
  protected $internal_gapi_mappings = array(
  );
  public $kind;
  public $nextPageToken;
  protected $resourcesType = 'Postman_Google_Service_ShoppingContent_ProductStatus';
  protected $resourcesDataType = 'array';


  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  public function getResources()
  {
    return $this->resources;
  }
}
