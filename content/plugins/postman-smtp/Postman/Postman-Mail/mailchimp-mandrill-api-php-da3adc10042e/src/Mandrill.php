<?php

require_once 'Mandrill/Templates.php';
require_once 'Mandrill/Exports.php';
require_once 'Mandrill/Users.php';
require_once 'Mandrill/Rejects.php';
require_once 'Mandrill/Inbound.php';
require_once 'Mandrill/Tags.php';
require_once 'Mandrill/Messages.php';
require_once 'Mandrill/Whitelists.php';
require_once 'Mandrill/Ips.php';
require_once 'Mandrill/Internal.php';
require_once 'Mandrill/Subaccounts.php';
require_once 'Mandrill/Urls.php';
require_once 'Mandrill/Webhooks.php';
require_once 'Mandrill/Senders.php';
require_once 'Mandrill/Metadata.php';
require_once 'Mandrill/Exceptions.php';

class Postman_Mandrill {
    
    public $apikey;
    public $ch;
    public $root = 'https://mandrillapp.com/api/1.0';
    public $debug = false;

    public static $error_map = array(
        "ValidationError" => "Postman_Mandrill_ValidationError",
        "Invalid_Key" => "Postman_Mandrill_Invalid_Key",
        "PaymentRequired" => "Postman_Mandrill_PaymentRequired",
        "Unknown_Subaccount" => "Postman_Mandrill_Unknown_Subaccount",
        "Unknown_Template" => "Postman_Mandrill_Unknown_Template",
        "ServiceUnavailable" => "Postman_Mandrill_ServiceUnavailable",
        "Unknown_Message" => "Postman_Mandrill_Unknown_Message",
        "Invalid_Tag_Name" => "Postman_Mandrill_Invalid_Tag_Name",
        "Invalid_Reject" => "Postman_Mandrill_Invalid_Reject",
        "Unknown_Sender" => "Postman_Mandrill_Unknown_Sender",
        "Unknown_Url" => "Postman_Mandrill_Unknown_Url",
        "Unknown_TrackingDomain" => "Postman_Mandrill_Unknown_TrackingDomain",
        "Invalid_Template" => "Postman_Mandrill_Invalid_Template",
        "Unknown_Webhook" => "Postman_Mandrill_Unknown_Webhook",
        "Unknown_InboundDomain" => "Postman_Mandrill_Unknown_InboundDomain",
        "Unknown_InboundRoute" => "Postman_Mandrill_Unknown_InboundRoute",
        "Unknown_Export" => "Postman_Mandrill_Unknown_Export",
        "IP_ProvisionLimit" => "Postman_Mandrill_IP_ProvisionLimit",
        "Unknown_Pool" => "Postman_Mandrill_Unknown_Pool",
        "NoSendingHistory" => "Postman_Mandrill_NoSendingHistory",
        "PoorReputation" => "Postman_Mandrill_PoorReputation",
        "Unknown_IP" => "Postman_Mandrill_Unknown_IP",
        "Invalid_EmptyDefaultPool" => "Postman_Mandrill_Invalid_EmptyDefaultPool",
        "Invalid_DeleteDefaultPool" => "Postman_Mandrill_Invalid_DeleteDefaultPool",
        "Invalid_DeleteNonEmptyPool" => "Postman_Mandrill_Invalid_DeleteNonEmptyPool",
        "Invalid_CustomDNS" => "Postman_Mandrill_Invalid_CustomDNS",
        "Invalid_CustomDNSPending" => "Postman_Mandrill_Invalid_CustomDNSPending",
        "Metadata_FieldLimit" => "Postman_Mandrill_Metadata_FieldLimit",
        "Unknown_MetadataField" => "Postman_Mandrill_Unknown_MetadataField"
    );

    public function __construct($apikey=null) {
        if(!$apikey) $apikey = getenv('MANDRILL_APIKEY');
        if(!$apikey) $apikey = $this->readConfigs();
        if(!$apikey) throw new Postman_Mandrill_Error('You must provide a Mandrill API key');
        $this->apikey = $apikey;

        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mandrill-PHP/1.0.55');
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 600);

        $this->root = rtrim($this->root, '/') . '/';

        $this->templates = new Postman_Mandrill_Templates($this);
        $this->exports = new Postman_Mandrill_Exports($this);
        $this->users = new Postman_Mandrill_Users($this);
        $this->rejects = new Postman_Mandrill_Rejects($this);
        $this->inbound = new Postman_Mandrill_Inbound($this);
        $this->tags = new Postman_Mandrill_Tags($this);
        $this->messages = new Postman_Mandrill_Messages($this);
        $this->whitelists = new Postman_Mandrill_Whitelists($this);
        $this->ips = new Postman_Mandrill_Ips($this);
        $this->internal = new Postman_Mandrill_Internal($this);
        $this->subaccounts = new Postman_Mandrill_Subaccounts($this);
        $this->urls = new Postman_Mandrill_Urls($this);
        $this->webhooks = new Postman_Mandrill_Webhooks($this);
        $this->senders = new Postman_Mandrill_Senders($this);
        $this->metadata = new Postman_Mandrill_Metadata($this);
    }

    public function __destruct() {
        curl_close($this->ch);
    }

    public function call($url, $params) {
        $params['key'] = $this->apikey;
        $params = json_encode($params);
        $ch = $this->ch;

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
            throw new Postman_Mandrill_HttpError("API call to $url failed: " . curl_error($ch));
        }
        $result = json_decode($response_body, true);
        if($result === null) throw new Postman_Mandrill_Error('We were unable to decode the JSON response from the Mandrill API: ' . $response_body);
        
        if(floor($info['http_code'] / 100) >= 4) {
            throw $this->castError($result);
        }

        return $result;
    }

    public function readConfigs() {
        $paths = array('~/.mandrill.key', '/etc/mandrill.key');
        foreach($paths as $path) {
            if(file_exists($path)) {
                $apikey = trim(file_get_contents($path));
                if($apikey) return $apikey;
            }
        }
        return false;
    }

    public function castError($result) {
        if($result['status'] !== 'error' || !$result['name']) throw new Postman_Mandrill_Error('We received an unexpected error: ' . json_encode($result));

        $class = (isset(self::$error_map[$result['name']])) ? self::$error_map[$result['name']] : 'Mandrill_Error';
        return new $class($result['message'], $result['code']);
    }

    public function log($msg) {
        if($this->debug) error_log($msg);
    }
}


