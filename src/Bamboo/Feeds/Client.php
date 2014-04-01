<?php

namespace Bamboo\Feeds;

use Guzzle\Http;
use Bamboo\Feeds\ClientFake;

class Client
{

    private $_baseUrl = "http://d.bbc.co.uk/";
    private $_version = "ibl/v1/";
    private $_proxy = "";
    private $_config = array();
    private $_httpClient;
    private $_defaultParams = array(
                                "api_key" => "",
                                "availability" => "all",
                                "lang" => "en",
                                "rights" => "web"
            );

    public static $instance;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function setHttpClient($httpClient) {
        $this->_httpClient = $httpClient;
    }

    public function setConfig($config) {
        $this->_config = $config;
    }

    public function setProxy($proxy) {
        $this->_proxy = $proxy;
    }

    public function request($feed, $params = array()) {
        $client = $this->_getClient();
        $params = array_merge($this->_defaultParams, $this->_config, $params);

        try {
          $request = $client->get(
              $this->_version . $feed . ".json", 
              array(), 
              array(
                'query' => $params,
                'proxy' =>  $this->_proxy,
              )
          );
        } catch (RequestException $e) {
            //$request->getUrl()
            die($e->getMessage());
        }
 
        $response = $request->send();
        $object = $this->_parseResponse($response);

        return $object;
    }

    private function _parseResponse($response) {
        $array = $response->json();
        $json = json_encode($array);
        $object = json_decode($json);

        return $object;
    }

    private function _getClient() {
        if ($this->_httpClient) {
            return $this->_httpClient;
        }
        return new Http\Client($this->_baseUrl);
    }
}