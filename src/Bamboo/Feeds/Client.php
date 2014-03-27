<?php

namespace Bamboo\Feeds;

use Guzzle\Http;
use Bamboo\Feeds\ClientFake;

class Client {

    private $_baseUrl = "http://d.bbc.co.uk/";
    private $_version = "ibl/v1/";
    private $_params;
    private $_client;
    private $_defaultParams = array(
                                "api_key" => "",
                                "availability" => "all",
                                "lang" => "en",
                                "rights" => "web"
            );

    public static $instance;

    public function __construct() {
        $this->_client = $this->_getClient();
    }

    public static function getInstance() {
        if (!self::$instance) {
        self::$instance = new self();
        }

        return self::$instance;
    }

    public function setConfig($params) {
        $this->_params = $params;
    }

    public function request($feed, $params) {

        $params = array_merge($this->_defaultParams, $this->_params, $params);

        try {
          $request = $this->_client->get($this->_version . $feed . ".json", 
              array(), 
              array(
                'query' => $params,
                'proxy' =>  'tcp://www-cache.reith.bbc.co.uk:80',
              )
          );
        } catch (RequestException $e) {

        }

        $response = $request->send();
        $object = $this->_parseResponse($response);

        return $object;
    }

    private function _parseResponse($response) {
        
        $response->getBody();
        $array = $response->json();

        $json = json_encode($array);
        $object = json_decode($json);

        return $object;
    }

    private function _getClient() {
        if (isset($_GET['_fake'])) {
            return new ClientFake();
        } 
        return new Http\Client($this->_baseUrl);
    }
}