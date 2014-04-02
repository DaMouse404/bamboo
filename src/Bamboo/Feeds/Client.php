<?php

namespace Bamboo\Feeds;

use Guzzle\Http;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Guzzle\Http\Exception\BadResponseException;
use Bamboo\Feeds\ClientFake;
use Bamboo\Feeds\Log;
use Bamboo\Feeds\Exception;
use Bamboo\Feeds\Exception\ServerError;
use Bamboo\Feeds\Exception\ClientError;
use Bamboo\Feeds\Exception\BadResponse;
use Bamboo\Feeds\Exception\EmptyFeed;

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

    /*
     *
     * Log Error...Translate Exception and throw
     */
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
        } catch (ServerErrorResponseException $e) {
            $this->_logAndThrowError("ServerError", $e);
        } catch (ClientErrorResponseException $e) {
            $this->_logAndThrowError("ClientError", $e);
        } catch (BadResponseException $e) {
            $this->_logAndThrowError("BadResponse", $e);
        } catch(\Exception $e){
            // General Exception
            $this->_logAndThrowError("Exception", $e);
        }
 
        $response = $request->send();
        $object = $this->_parseResponse($response, $feed, $params);

        return $object;
    }

    private function _parseResponse($response, $feedName, $params) {
        $array = $response->json();
        $json = json_encode($array);
        $object = json_decode($json);

        if (!$object) {
            throw new EmptyFeed(
                'iBL returned an empty response from the "' . $feedName . '" feed ' .
                'with parameters ' . http_build_query($params, ',')
            );
        }
        return $object;
    }

    private function _getClient() {
        if ($this->_httpClient) {
            return $this->_httpClient;
        }
        return new Http\Client($this->_baseUrl);
    }

    private function _logAndThrowError($errorClass, $e = "") {
        // Log Error
        $req = $e->getRequest();
        $resp = $e->getResponse();
        Log::err("Bamboo error with request : $req. 
            Response : $resp");

        // Throw Exception
        $exception = new $errorClass(
            $e->getMessage(),
            $e->getCode(),
            $e
        );
        throw $exception;
    }

}