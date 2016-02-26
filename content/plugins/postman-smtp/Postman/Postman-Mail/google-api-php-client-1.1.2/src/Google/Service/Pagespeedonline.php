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
 * Service definition for Pagespeedonline (v1).
 *
 * <p>
 * Lets you analyze the performance of a web page and get tailored suggestions
 * to make that page faster.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/speed/docs/insights/v1/getting_started" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Postman_Google_Service_Pagespeedonline extends Postman_Google_Service
{


  public $pagespeedapi;
  

  /**
   * Constructs the internal representation of the Pagespeedonline service.
   *
   * @param Postman_Google_Client $client
   */
  public function __construct(Postman_Google_Client $client)
  {
    parent::__construct($client);
    $this->servicePath = 'pagespeedonline/v1/';
    $this->version = 'v1';
    $this->serviceName = 'pagespeedonline';

    $this->pagespeedapi = new Postman_Google_Service_Pagespeedonline_Pagespeedapi_Resource(
        $this,
        $this->serviceName,
        'pagespeedapi',
        array(
          'methods' => array(
            'runpagespeed' => array(
              'path' => 'runPagespeed',
              'httpMethod' => 'GET',
              'parameters' => array(
                'url' => array(
                  'location' => 'query',
                  'type' => 'string',
                  'required' => true,
                ),
                'screenshot' => array(
                  'location' => 'query',
                  'type' => 'boolean',
                ),
                'locale' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'rule' => array(
                  'location' => 'query',
                  'type' => 'string',
                  'repeated' => true,
                ),
                'strategy' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'filter_third_party_resources' => array(
                  'location' => 'query',
                  'type' => 'boolean',
                ),
              ),
            ),
          )
        )
    );
  }
}


/**
 * The "pagespeedapi" collection of methods.
 * Typical usage is:
 *  <code>
 *   $pagespeedonlineService = new Postman_Google_Service_Pagespeedonline(...);
 *   $pagespeedapi = $pagespeedonlineService->pagespeedapi;
 *  </code>
 */
class Postman_Google_Service_Pagespeedonline_Pagespeedapi_Resource extends Postman_Google_Service_Resource
{

  /**
   * Runs Page Speed analysis on the page at the specified URL, and returns a Page
   * Speed score, a list of suggestions to make that page faster, and other
   * information. (pagespeedapi.runpagespeed)
   *
   * @param string $url The URL to fetch and analyze
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool screenshot Indicates if binary data containing a screenshot
   * should be included
   * @opt_param string locale The locale used to localize formatted results
   * @opt_param string rule A Page Speed rule to run; if none are given, all rules
   * are run
   * @opt_param string strategy The analysis strategy to use
   * @opt_param bool filter_third_party_resources Indicates if third party
   * resources should be filtered out before PageSpeed analysis.
   * @return Postman_Google_Service_Pagespeedonline_Result
   */
  public function runpagespeed($url, $optParams = array())
  {
    $params = array('url' => $url);
    $params = array_merge($params, $optParams);
    return $this->call('runpagespeed', array($params), "Postman_Google_Service_Pagespeedonline_Result");
  }
}




class Postman_Google_Service_Pagespeedonline_Result extends Postman_Google_Collection
{
  protected $collection_key = 'invalidRules';
  protected $internal_gapi_mappings = array(
  );
  protected $formattedResultsType = 'Postman_Google_Service_Pagespeedonline_ResultFormattedResults';
  protected $formattedResultsDataType = '';
  public $id;
  public $invalidRules;
  public $kind;
  protected $pageStatsType = 'Postman_Google_Service_Pagespeedonline_ResultPageStats';
  protected $pageStatsDataType = '';
  public $responseCode;
  public $score;
  protected $screenshotType = 'Postman_Google_Service_Pagespeedonline_ResultScreenshot';
  protected $screenshotDataType = '';
  public $title;
  protected $versionType = 'Postman_Google_Service_Pagespeedonline_ResultVersion';
  protected $versionDataType = '';


  public function setFormattedResults(Postman_Google_Service_Pagespeedonline_ResultFormattedResults $formattedResults)
  {
    $this->formattedResults = $formattedResults;
  }
  public function getFormattedResults()
  {
    return $this->formattedResults;
  }
  public function setId($id)
  {
    $this->id = $id;
  }
  public function getId()
  {
    return $this->id;
  }
  public function setInvalidRules($invalidRules)
  {
    $this->invalidRules = $invalidRules;
  }
  public function getInvalidRules()
  {
    return $this->invalidRules;
  }
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  public function getKind()
  {
    return $this->kind;
  }
  public function setPageStats(Postman_Google_Service_Pagespeedonline_ResultPageStats $pageStats)
  {
    $this->pageStats = $pageStats;
  }
  public function getPageStats()
  {
    return $this->pageStats;
  }
  public function setResponseCode($responseCode)
  {
    $this->responseCode = $responseCode;
  }
  public function getResponseCode()
  {
    return $this->responseCode;
  }
  public function setScore($score)
  {
    $this->score = $score;
  }
  public function getScore()
  {
    return $this->score;
  }
  public function setScreenshot(Postman_Google_Service_Pagespeedonline_ResultScreenshot $screenshot)
  {
    $this->screenshot = $screenshot;
  }
  public function getScreenshot()
  {
    return $this->screenshot;
  }
  public function setTitle($title)
  {
    $this->title = $title;
  }
  public function getTitle()
  {
    return $this->title;
  }
  public function setVersion(Postman_Google_Service_Pagespeedonline_ResultVersion $version)
  {
    $this->version = $version;
  }
  public function getVersion()
  {
    return $this->version;
  }
}

class Postman_Google_Service_Pagespeedonline_ResultFormattedResults extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $locale;
  protected $ruleResultsType = 'Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElement';
  protected $ruleResultsDataType = 'map';


  public function setLocale($locale)
  {
    $this->locale = $locale;
  }
  public function getLocale()
  {
    return $this->locale;
  }
  public function setRuleResults($ruleResults)
  {
    $this->ruleResults = $ruleResults;
  }
  public function getRuleResults()
  {
    return $this->ruleResults;
  }
}

class Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResults extends Postman_Google_Model
{
}

class Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElement extends Postman_Google_Collection
{
  protected $collection_key = 'urlBlocks';
  protected $internal_gapi_mappings = array(
  );
  public $localizedRuleName;
  public $ruleImpact;
  protected $urlBlocksType = 'Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocks';
  protected $urlBlocksDataType = 'array';


  public function setLocalizedRuleName($localizedRuleName)
  {
    $this->localizedRuleName = $localizedRuleName;
  }
  public function getLocalizedRuleName()
  {
    return $this->localizedRuleName;
  }
  public function setRuleImpact($ruleImpact)
  {
    $this->ruleImpact = $ruleImpact;
  }
  public function getRuleImpact()
  {
    return $this->ruleImpact;
  }
  public function setUrlBlocks($urlBlocks)
  {
    $this->urlBlocks = $urlBlocks;
  }
  public function getUrlBlocks()
  {
    return $this->urlBlocks;
  }
}

class Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocks extends Postman_Google_Collection
{
  protected $collection_key = 'urls';
  protected $internal_gapi_mappings = array(
  );
  protected $headerType = 'Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocksHeader';
  protected $headerDataType = '';
  protected $urlsType = 'Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocksUrls';
  protected $urlsDataType = 'array';


  public function setHeader(Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocksHeader $header)
  {
    $this->header = $header;
  }
  public function getHeader()
  {
    return $this->header;
  }
  public function setUrls($urls)
  {
    $this->urls = $urls;
  }
  public function getUrls()
  {
    return $this->urls;
  }
}

class Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocksHeader extends Postman_Google_Collection
{
  protected $collection_key = 'args';
  protected $internal_gapi_mappings = array(
  );
  protected $argsType = 'Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocksHeaderArgs';
  protected $argsDataType = 'array';
  public $format;


  public function setArgs($args)
  {
    $this->args = $args;
  }
  public function getArgs()
  {
    return $this->args;
  }
  public function setFormat($format)
  {
    $this->format = $format;
  }
  public function getFormat()
  {
    return $this->format;
  }
}

class Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocksHeaderArgs extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $type;
  public $value;


  public function setType($type)
  {
    $this->type = $type;
  }
  public function getType()
  {
    return $this->type;
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

class Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocksUrls extends Postman_Google_Collection
{
  protected $collection_key = 'details';
  protected $internal_gapi_mappings = array(
  );
  protected $detailsType = 'Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocksUrlsDetails';
  protected $detailsDataType = 'array';
  protected $resultType = 'Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocksUrlsResult';
  protected $resultDataType = '';


  public function setDetails($details)
  {
    $this->details = $details;
  }
  public function getDetails()
  {
    return $this->details;
  }
  public function setResult(Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocksUrlsResult $result)
  {
    $this->result = $result;
  }
  public function getResult()
  {
    return $this->result;
  }
}

class Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocksUrlsDetails extends Postman_Google_Collection
{
  protected $collection_key = 'args';
  protected $internal_gapi_mappings = array(
  );
  protected $argsType = 'Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocksUrlsDetailsArgs';
  protected $argsDataType = 'array';
  public $format;


  public function setArgs($args)
  {
    $this->args = $args;
  }
  public function getArgs()
  {
    return $this->args;
  }
  public function setFormat($format)
  {
    $this->format = $format;
  }
  public function getFormat()
  {
    return $this->format;
  }
}

class Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocksUrlsDetailsArgs extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $type;
  public $value;


  public function setType($type)
  {
    $this->type = $type;
  }
  public function getType()
  {
    return $this->type;
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

class Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocksUrlsResult extends Postman_Google_Collection
{
  protected $collection_key = 'args';
  protected $internal_gapi_mappings = array(
  );
  protected $argsType = 'Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocksUrlsResultArgs';
  protected $argsDataType = 'array';
  public $format;


  public function setArgs($args)
  {
    $this->args = $args;
  }
  public function getArgs()
  {
    return $this->args;
  }
  public function setFormat($format)
  {
    $this->format = $format;
  }
  public function getFormat()
  {
    return $this->format;
  }
}

class Postman_Google_Service_Pagespeedonline_ResultFormattedResultsRuleResultsElementUrlBlocksUrlsResultArgs extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $type;
  public $value;


  public function setType($type)
  {
    $this->type = $type;
  }
  public function getType()
  {
    return $this->type;
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

class Postman_Google_Service_Pagespeedonline_ResultPageStats extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $cssResponseBytes;
  public $flashResponseBytes;
  public $htmlResponseBytes;
  public $imageResponseBytes;
  public $javascriptResponseBytes;
  public $numberCssResources;
  public $numberHosts;
  public $numberJsResources;
  public $numberResources;
  public $numberStaticResources;
  public $otherResponseBytes;
  public $textResponseBytes;
  public $totalRequestBytes;


  public function setCssResponseBytes($cssResponseBytes)
  {
    $this->cssResponseBytes = $cssResponseBytes;
  }
  public function getCssResponseBytes()
  {
    return $this->cssResponseBytes;
  }
  public function setFlashResponseBytes($flashResponseBytes)
  {
    $this->flashResponseBytes = $flashResponseBytes;
  }
  public function getFlashResponseBytes()
  {
    return $this->flashResponseBytes;
  }
  public function setHtmlResponseBytes($htmlResponseBytes)
  {
    $this->htmlResponseBytes = $htmlResponseBytes;
  }
  public function getHtmlResponseBytes()
  {
    return $this->htmlResponseBytes;
  }
  public function setImageResponseBytes($imageResponseBytes)
  {
    $this->imageResponseBytes = $imageResponseBytes;
  }
  public function getImageResponseBytes()
  {
    return $this->imageResponseBytes;
  }
  public function setJavascriptResponseBytes($javascriptResponseBytes)
  {
    $this->javascriptResponseBytes = $javascriptResponseBytes;
  }
  public function getJavascriptResponseBytes()
  {
    return $this->javascriptResponseBytes;
  }
  public function setNumberCssResources($numberCssResources)
  {
    $this->numberCssResources = $numberCssResources;
  }
  public function getNumberCssResources()
  {
    return $this->numberCssResources;
  }
  public function setNumberHosts($numberHosts)
  {
    $this->numberHosts = $numberHosts;
  }
  public function getNumberHosts()
  {
    return $this->numberHosts;
  }
  public function setNumberJsResources($numberJsResources)
  {
    $this->numberJsResources = $numberJsResources;
  }
  public function getNumberJsResources()
  {
    return $this->numberJsResources;
  }
  public function setNumberResources($numberResources)
  {
    $this->numberResources = $numberResources;
  }
  public function getNumberResources()
  {
    return $this->numberResources;
  }
  public function setNumberStaticResources($numberStaticResources)
  {
    $this->numberStaticResources = $numberStaticResources;
  }
  public function getNumberStaticResources()
  {
    return $this->numberStaticResources;
  }
  public function setOtherResponseBytes($otherResponseBytes)
  {
    $this->otherResponseBytes = $otherResponseBytes;
  }
  public function getOtherResponseBytes()
  {
    return $this->otherResponseBytes;
  }
  public function setTextResponseBytes($textResponseBytes)
  {
    $this->textResponseBytes = $textResponseBytes;
  }
  public function getTextResponseBytes()
  {
    return $this->textResponseBytes;
  }
  public function setTotalRequestBytes($totalRequestBytes)
  {
    $this->totalRequestBytes = $totalRequestBytes;
  }
  public function getTotalRequestBytes()
  {
    return $this->totalRequestBytes;
  }
}

class Postman_Google_Service_Pagespeedonline_ResultScreenshot extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
        "mimeType" => "mime_type",
  );
  public $data;
  public $height;
  public $mimeType;
  public $width;


  public function setData($data)
  {
    $this->data = $data;
  }
  public function getData()
  {
    return $this->data;
  }
  public function setHeight($height)
  {
    $this->height = $height;
  }
  public function getHeight()
  {
    return $this->height;
  }
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  public function getMimeType()
  {
    return $this->mimeType;
  }
  public function setWidth($width)
  {
    $this->width = $width;
  }
  public function getWidth()
  {
    return $this->width;
  }
}

class Postman_Google_Service_Pagespeedonline_ResultVersion extends Postman_Google_Model
{
  protected $internal_gapi_mappings = array(
  );
  public $major;
  public $minor;


  public function setMajor($major)
  {
    $this->major = $major;
  }
  public function getMajor()
  {
    return $this->major;
  }
  public function setMinor($minor)
  {
    $this->minor = $minor;
  }
  public function getMinor()
  {
    return $this->minor;
  }
}
