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
    private $_fakeHttpClient;
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

    public function setFakeHttpClient($fakeHttpClient) {
        $this->_fakeHttpClient = $fakeHttpClient;
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
        $client = $this->_getClient($feed);
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
            $this->_logAndThrowError("ServerError", $e, $feed);
        } catch (ClientErrorResponseException $e) {
            $this->_logAndThrowError("ClientError", $e, $feed);
        } catch (BadResponseException $e) {
            $this->_logAndThrowError("BadResponse", $e, $feed);
        } catch(\Exception $e){
            // General Exception
            $this->_logAndThrowError("Exception", $e, $feed);
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

    /* 
     * Return Client to use for this request.
     */
    private function _getClient($feed) {
        $fakeHttpClient = $this->_fakeHttpClient;

        if ($this->useFixture($feed) && $fakeHttpClient) {
            return $fakeHttpClient;
        }
        return new Http\Client($this->_baseUrl);

    }
    
    /*
     * Check if this request needs to use a fixture.
     * Does part before @ match current Feed?
     */
    public function useFixture($feed) {
        // Strip unnecessary values from feed
        $feed = str_replace("ibl/v1/", "", $feed);
        $feed = str_replace(".json", "", $feed);
        $feed = str_replace("/", "_", $feed);

        if (!isset($_GET['_fake'])) {
            return false;
        }

        $fakePath = $_GET['_fake'];
        $exploded = explode('@', $fakePath);
        if (isset($exploded[1])) {
            // Grab just fixture filename
            $fakedFeed = $exploded[0];
        } else {
            // No @ so use whole fixture
            $fakedFeed = $fakePath;
        }

        preg_match('/' . $fakedFeed . '/', $feed, $matches);
        if (count($matches) > 0) {
            // matches, so use the fixtureFile
            return true;          
        }
        return false;
    }

    private function _logAndThrowError($errorClass, $e, $feed) {
        // Log Error
        Log::err("Bamboo error on feed $feed.");

        // Throw Exception
        $exception = new $errorClass(
            $e->getMessage(),
            $e->getCode(),
            $e
        );
        throw $exception;
    }

}