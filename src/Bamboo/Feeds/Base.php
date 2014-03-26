<?php

namespace Bamboo\Feeds;

use Guzzle\Http\Client;
use Bamboo\Feeds\ClientFake;

class Base {

    public static $_baseUrl = "http://d.bbc.co.uk/";
    public static $_version = "ibl/v1/";
    public static $params;

    public $defaultParams = array(
                                "api_key" => "",
                                "availability" => "all",
                                "lang" => "en",
                                "rights" => "web"
            );

    public function __construct() {
        $client = self::getClient();

        try {
          $request = $client->get(self::$_version . $feed . ".json", 
              array(), 
              array(
                'query' => $params,
                'proxy' =>  '',
              )
          );
        } catch (RequestException $e) {

        }

        $object = $this->_parseResponse($request);

        return $object;
    }

    public static function getInstance() {
        if (!self::$instance) {
        self::$instance = new self();
        }

        return self::$instance;
    }

    private function _parseResponse($request) {
        $response = $request->send();
        $response->getBody();
        $array = $response->json();

        $json = json_encode($array);
        $object = json_decode($json);

        return $object;
    }

    public function setConfig($params) {
        die('1');
        self::$params = $params;
    }
    public static function getClient() {
        if (!self::$_client) {    
          $client = self::setClient();
          self::$_client = $client;
        }
        return self::$_client;
    }

    public static function setClient() {
        if (isset($_GET['_fake'])) {
        return new ClientFake();
        } 
        return new Client(self::$_baseUrl);
    }
}