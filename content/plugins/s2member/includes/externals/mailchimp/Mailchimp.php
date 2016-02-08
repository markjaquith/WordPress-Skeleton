<?php

require_once 'Mailchimp/Folders.php';
require_once 'Mailchimp/Templates.php';
require_once 'Mailchimp/Users.php';
require_once 'Mailchimp/Helper.php';
require_once 'Mailchimp/Mobile.php';
require_once 'Mailchimp/Conversations.php';
require_once 'Mailchimp/Ecomm.php';
require_once 'Mailchimp/Neapolitan.php';
require_once 'Mailchimp/Lists.php';
require_once 'Mailchimp/Campaigns.php';
require_once 'Mailchimp/Vip.php';
require_once 'Mailchimp/Reports.php';
require_once 'Mailchimp/Gallery.php';
require_once 'Mailchimp/Goal.php';
require_once 'Mailchimp/Exceptions.php';

class Mailchimp {
    
    public $apikey;
    public $ch;
    public $root  = 'https://api.mailchimp.com/2.0';
    public $debug = false;

    public static $error_map = array(
        "ValidationError" => "Mailchimp_ValidationError",
        "ServerError_MethodUnknown" => "Mailchimp_ServerError_MethodUnknown",
        "ServerError_InvalidParameters" => "Mailchimp_ServerError_InvalidParameters",
        "Unknown_Exception" => "Mailchimp_Unknown_Exception",
        "Request_TimedOut" => "Mailchimp_Request_TimedOut",
        "Zend_Uri_Exception" => "Mailchimp_Zend_Uri_Exception",
        "PDOException" => "Mailchimp_PDOException",
        "Avesta_Db_Exception" => "Mailchimp_Avesta_Db_Exception",
        "XML_RPC2_Exception" => "Mailchimp_XML_RPC2_Exception",
        "XML_RPC2_FaultException" => "Mailchimp_XML_RPC2_FaultException",
        "Too_Many_Connections" => "Mailchimp_Too_Many_Connections",
        "Parse_Exception" => "Mailchimp_Parse_Exception",
        "User_Unknown" => "Mailchimp_User_Unknown",
        "User_Disabled" => "Mailchimp_User_Disabled",
        "User_DoesNotExist" => "Mailchimp_User_DoesNotExist",
        "User_NotApproved" => "Mailchimp_User_NotApproved",
        "Invalid_ApiKey" => "Mailchimp_Invalid_ApiKey",
        "User_UnderMaintenance" => "Mailchimp_User_UnderMaintenance",
        "Invalid_AppKey" => "Mailchimp_Invalid_AppKey",
        "Invalid_IP" => "Mailchimp_Invalid_IP",
        "User_DoesExist" => "Mailchimp_User_DoesExist",
        "User_InvalidRole" => "Mailchimp_User_InvalidRole",
        "User_InvalidAction" => "Mailchimp_User_InvalidAction",
        "User_MissingEmail" => "Mailchimp_User_MissingEmail",
        "User_CannotSendCampaign" => "Mailchimp_User_CannotSendCampaign",
        "User_MissingModuleOutbox" => "Mailchimp_User_MissingModuleOutbox",
        "User_ModuleAlreadyPurchased" => "Mailchimp_User_ModuleAlreadyPurchased",
        "User_ModuleNotPurchased" => "Mailchimp_User_ModuleNotPurchased",
        "User_NotEnoughCredit" => "Mailchimp_User_NotEnoughCredit",
        "MC_InvalidPayment" => "Mailchimp_MC_InvalidPayment",
        "List_DoesNotExist" => "Mailchimp_List_DoesNotExist",
        "List_InvalidInterestFieldType" => "Mailchimp_List_InvalidInterestFieldType",
        "List_InvalidOption" => "Mailchimp_List_InvalidOption",
        "List_InvalidUnsubMember" => "Mailchimp_List_InvalidUnsubMember",
        "List_InvalidBounceMember" => "Mailchimp_List_InvalidBounceMember",
        "List_AlreadySubscribed" => "Mailchimp_List_AlreadySubscribed",
        "List_NotSubscribed" => "Mailchimp_List_NotSubscribed",
        "List_InvalidImport" => "Mailchimp_List_InvalidImport",
        "MC_PastedList_Duplicate" => "Mailchimp_MC_PastedList_Duplicate",
        "MC_PastedList_InvalidImport" => "Mailchimp_MC_PastedList_InvalidImport",
        "Email_AlreadySubscribed" => "Mailchimp_Email_AlreadySubscribed",
        "Email_AlreadyUnsubscribed" => "Mailchimp_Email_AlreadyUnsubscribed",
        "Email_NotExists" => "Mailchimp_Email_NotExists",
        "Email_NotSubscribed" => "Mailchimp_Email_NotSubscribed",
        "List_MergeFieldRequired" => "Mailchimp_List_MergeFieldRequired",
        "List_CannotRemoveEmailMerge" => "Mailchimp_List_CannotRemoveEmailMerge",
        "List_Merge_InvalidMergeID" => "Mailchimp_List_Merge_InvalidMergeID",
        "List_TooManyMergeFields" => "Mailchimp_List_TooManyMergeFields",
        "List_InvalidMergeField" => "Mailchimp_List_InvalidMergeField",
        "List_InvalidInterestGroup" => "Mailchimp_List_InvalidInterestGroup",
        "List_TooManyInterestGroups" => "Mailchimp_List_TooManyInterestGroups",
        "Campaign_DoesNotExist" => "Mailchimp_Campaign_DoesNotExist",
        "Campaign_StatsNotAvailable" => "Mailchimp_Campaign_StatsNotAvailable",
        "Campaign_InvalidAbsplit" => "Mailchimp_Campaign_InvalidAbsplit",
        "Campaign_InvalidContent" => "Mailchimp_Campaign_InvalidContent",
        "Campaign_InvalidOption" => "Mailchimp_Campaign_InvalidOption",
        "Campaign_InvalidStatus" => "Mailchimp_Campaign_InvalidStatus",
        "Campaign_NotSaved" => "Mailchimp_Campaign_NotSaved",
        "Campaign_InvalidSegment" => "Mailchimp_Campaign_InvalidSegment",
        "Campaign_InvalidRss" => "Mailchimp_Campaign_InvalidRss",
        "Campaign_InvalidAuto" => "Mailchimp_Campaign_InvalidAuto",
        "MC_ContentImport_InvalidArchive" => "Mailchimp_MC_ContentImport_InvalidArchive",
        "Campaign_BounceMissing" => "Mailchimp_Campaign_BounceMissing",
        "Campaign_InvalidTemplate" => "Mailchimp_Campaign_InvalidTemplate",
        "Invalid_EcommOrder" => "Mailchimp_Invalid_EcommOrder",
        "Absplit_UnknownError" => "Mailchimp_Absplit_UnknownError",
        "Absplit_UnknownSplitTest" => "Mailchimp_Absplit_UnknownSplitTest",
        "Absplit_UnknownTestType" => "Mailchimp_Absplit_UnknownTestType",
        "Absplit_UnknownWaitUnit" => "Mailchimp_Absplit_UnknownWaitUnit",
        "Absplit_UnknownWinnerType" => "Mailchimp_Absplit_UnknownWinnerType",
        "Absplit_WinnerNotSelected" => "Mailchimp_Absplit_WinnerNotSelected",
        "Invalid_Analytics" => "Mailchimp_Invalid_Analytics",
        "Invalid_DateTime" => "Mailchimp_Invalid_DateTime",
        "Invalid_Email" => "Mailchimp_Invalid_Email",
        "Invalid_SendType" => "Mailchimp_Invalid_SendType",
        "Invalid_Template" => "Mailchimp_Invalid_Template",
        "Invalid_TrackingOptions" => "Mailchimp_Invalid_TrackingOptions",
        "Invalid_Options" => "Mailchimp_Invalid_Options",
        "Invalid_Folder" => "Mailchimp_Invalid_Folder",
        "Invalid_URL" => "Mailchimp_Invalid_URL",
        "Module_Unknown" => "Mailchimp_Module_Unknown",
        "MonthlyPlan_Unknown" => "Mailchimp_MonthlyPlan_Unknown",
        "Order_TypeUnknown" => "Mailchimp_Order_TypeUnknown",
        "Invalid_PagingLimit" => "Mailchimp_Invalid_PagingLimit",
        "Invalid_PagingStart" => "Mailchimp_Invalid_PagingStart",
        "Max_Size_Reached" => "Mailchimp_Max_Size_Reached",
        "MC_SearchException" => "Mailchimp_MC_SearchException",
        "Goal_SaveFailed" => "Mailchimp_Goal_SaveFailed",
        "Conversation_DoesNotExist" => "Mailchimp_Conversation_DoesNotExist",
        "Conversation_ReplySaveFailed" => "Mailchimp_Conversation_ReplySaveFailed",
        "File_Not_Found_Exception" => "Mailchimp_File_Not_Found_Exception",
        "Folder_Not_Found_Exception" => "Mailchimp_Folder_Not_Found_Exception",
        "Folder_Exists_Exception" => "Mailchimp_Folder_Exists_Exception"
    );

    public function __construct($apikey=null, $opts=array()) {
        if (!$apikey) {
            $apikey = getenv('MAILCHIMP_APIKEY');
        }

        if (!$apikey) {
            $apikey = $this->readConfigs();
        }

        if (!$apikey) {
            throw new Mailchimp_Error('You must provide a MailChimp API key');
        }

        $this->apikey = $apikey;
        $dc           = "us1";

        if (strstr($this->apikey, "-")){
            list($key, $dc) = explode("-", $this->apikey, 2);
            if (!$dc) {
                $dc = "us1";
            }
        }

        $this->root = str_replace('https://api', 'https://' . $dc . '.api', $this->root);
        $this->root = rtrim($this->root, '/') . '/';

        if (!isset($opts['timeout']) || !is_int($opts['timeout'])){
            $opts['timeout'] = 600;
        }
        if (isset($opts['debug'])){
            $this->debug = true;
        }


        $this->ch = curl_init();

        if (isset($opts['CURLOPT_FOLLOWLOCATION']) && $opts['CURLOPT_FOLLOWLOCATION'] === true) {
            curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);    
        }

        curl_setopt($this->ch, CURLOPT_USERAGENT, 'MailChimp-PHP/2.0.5');
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $opts['timeout']);


        $this->folders = new Mailchimp_Folders($this);
        $this->templates = new Mailchimp_Templates($this);
        $this->users = new Mailchimp_Users($this);
        $this->helper = new Mailchimp_Helper($this);
        $this->mobile = new Mailchimp_Mobile($this);
        $this->conversations = new Mailchimp_Conversations($this);
        $this->ecomm = new Mailchimp_Ecomm($this);
        $this->neapolitan = new Mailchimp_Neapolitan($this);
        $this->lists = new Mailchimp_Lists($this);
        $this->campaigns = new Mailchimp_Campaigns($this);
        $this->vip = new Mailchimp_Vip($this);
        $this->reports = new Mailchimp_Reports($this);
        $this->gallery = new Mailchimp_Gallery($this);
        $this->goal = new Mailchimp_Goal($this);
    }

    public function __destruct() {
        curl_close($this->ch);
    }

    public function call($url, $params) {
        $params['apikey'] = $this->apikey;
        
        $params = json_encode($params);
        $ch     = $this->ch;

        curl_setopt($ch, CURLOPT_URL, $this->root . $url . '.json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);

        $start = microtime(true);
        $this->log('Call to ' . $this->root . $url . '.json: ' . $params);
        if($this->debug) {
            $curl_buffer = fopen('php://memory', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $curl_buffer);
        }

        $response_body = curl_exec($ch);

        $info = curl_getinfo($ch);
        $time = microtime(true) - $start;
        if($this->debug) {
            rewind($curl_buffer);
            $this->log(stream_get_contents($curl_buffer));
            fclose($curl_buffer);
        }
        $this->log('Completed in ' . number_format($time * 1000, 2) . 'ms');
        $this->log('Got response: ' . $response_body);

        if(curl_error($ch)) {
            throw new Mailchimp_HttpError("API call to $url failed: " . curl_error($ch));
        }
        $result = json_decode($response_body, true);
        
        if(floor($info['http_code'] / 100) >= 4) {
            throw $this->castError($result);
        }

        return $result;
    }

    public function readConfigs() {
        $paths = array('~/.mailchimp.key', '/etc/mailchimp.key');
        foreach($paths as $path) {
            if(file_exists($path)) {
                $apikey = trim(file_get_contents($path));
                if ($apikey) {
                    return $apikey;
                }
            }
        }
        return false;
    }

    public function castError($result) {
        if ($result['status'] !== 'error' || !$result['name']) {
            throw new Mailchimp_Error('We received an unexpected error: ' . json_encode($result));
        }

        $class = (isset(self::$error_map[$result['name']])) ? self::$error_map[$result['name']] : 'Mailchimp_Error';
        return new $class($result['error'], $result['code']);
    }

    public function log($msg) {
        if ($this->debug) {
            error_log($msg);
        }
    }
}


