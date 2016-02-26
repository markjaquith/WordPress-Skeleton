<?php

class SendGrid
{
    const VERSION = '3.2.0';

    protected
        $namespace = 'SendGrid',
        $headers = array('Content-Type' => 'application/json'),
        $client,
        $options;

    public
        $apiUser,
        $apiKey,
        $url,
        $endpoint,
        $version = self::VERSION;

    public function __construct($apiUserOrKey, $apiKeyOrOptions = null, $options = array())
    {
        // Check if given a username + password or api key
        if (is_string($apiKeyOrOptions)) {
            // Username and password
            $this->apiUser = $apiUserOrKey;
            $this->apiKey = $apiKeyOrOptions;
            $this->options = $options;
        } elseif (is_array($apiKeyOrOptions) || $apiKeyOrOptions === null) {
            // API key
            $this->apiKey = $apiUserOrKey;
            $this->apiUser = null;

            // With options
            if (is_array($apiKeyOrOptions)) {
                $this->options = $apiKeyOrOptions;
            }
        } else {
            // Won't be thrown?
            throw new InvalidArgumentException('Need a username + password or api key!');
        }

        $this->options['turn_off_ssl_verification'] = (isset($this->options['turn_off_ssl_verification']) && $this->options['turn_off_ssl_verification'] == true);
        if (!isset($this->options['raise_exceptions'])) {
            $this->options['raise_exceptions'] = true;
        }
        $protocol = isset($this->options['protocol']) ? $this->options['protocol'] : 'https';
        $host = isset($this->options['host']) ? $this->options['host'] : 'api.sendgrid.com';
        $port = isset($this->options['port']) ? $this->options['port'] : '';

        $this->url = isset($this->options['url']) ? $this->options['url'] : $protocol . '://' . $host . ($port ? ':' . $port : '');
        $this->endpoint = isset($this->options['endpoint']) ? $this->options['endpoint'] : '/api/mail.send.json';

        $this->client = $this->prepareHttpClient();
    }

    /**
     * Prepares the HTTP client
     *
     * @return \Guzzle\Http\Client
     */
    private function prepareHttpClient()
    {
        $guzzleOption = array(
            'request.options' => array(
                'verify' => !$this->options['turn_off_ssl_verification'],
                'exceptions' => (isset($this->options['enable_guzzle_exceptions']) && $this->options['enable_guzzle_exceptions'] == true)
            )
        );

        // Using api key
        if ($this->apiUser === null) {
            $guzzleOption['request.options']['headers'] = array('Authorization' => 'Bearer ' . $this->apiKey);
        }

        // Using http proxy
        if (isset($this->options['proxy'])) {
            $guzzleOption['request.options']['proxy'] = $this->options['proxy'];
        }

        $client = new \Guzzle\Http\Client($this->url, $guzzleOption);
        $client->setUserAgent('sendgrid/' . $this->version . ';php');

        return $client;
    }

    /**
     * @return array The protected options array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Makes a post request to SendGrid to send an email
     *
     * @param SendGrid\Email $email Email object built
     *
     * @throws SendGrid\Exception if the response code is not 200
     * @return stdClass SendGrid response object
     */
    public function send(SendGrid\Email $email)
    {
        $form = $email->toWebFormat();

        // Using username password
        if ($this->apiUser !== null) {
            $form['api_user'] = $this->apiUser;
            $form['api_key'] = $this->apiKey;
        }

        $response = $this->postRequest($this->endpoint, $form);

        if ($response->code != 200 && $this->options['raise_exceptions']) {
            throw new SendGrid\Exception($response->raw_body, $response->code);
        }

        return $response;
    }

    /**
     * Makes the actual HTTP request to SendGrid
     *
     * @param $endpoint string endpoint to post to
     * @param $form array web ready version of SendGrid\Email
     *
     * @return SendGrid\Response
     */
    public function postRequest($endpoint, $form)
    {
        $req = $this->client->post($endpoint, null, $form);

        $res = $req->send();

        $response = new SendGrid\Response($res->getStatusCode(), $res->getHeaders(), $res->getBody(true), $res->json());

        return $response;
    }

    public static function register_autoloader()
    {
        spl_autoload_register(array('SendGrid', 'autoloader'));
    }

    public static function autoloader($class)
    {
        // Check that the class starts with 'SendGrid'
        if ($class == 'SendGrid' || stripos($class, 'SendGrid\\') === 0) {
            $file = str_replace('\\', '/', $class);

            if (file_exists(dirname(__FILE__) . '/' . $file . '.php')) {
                require_once(dirname(__FILE__) . '/' . $file . '.php');
            }
        }
    }
}
