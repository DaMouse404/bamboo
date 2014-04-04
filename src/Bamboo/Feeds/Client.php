<?php

namespace Bamboo\Feeds;

use Guzzle\Http;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Guzzle\Http\Exception\BadResponseException;
use Bamboo\Feeds\HttpFail;
use Bamboo\Feeds\Log;
use Bamboo\Feeds\Counter;
use Bamboo\Feeds\Exception;
use Bamboo\Feeds\Exception\EmptyFeed;

class Client
{

    const PARAM_DEGRADE = '_fake';
    const PARAM_FAIL = '_fail';

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
            $response = $request->send();
        } catch (ServerErrorResponseException $e) {
            $this->_logAndThrowError(
                "Bamboo\Feeds\Exception\ServerError", 
                "BAMBOO_SERVERERROR", 
                $e, $feed
            );
        } catch (ClientErrorResponseException $e) {
            $this->_logAndThrowError(
                "Bamboo\Feeds\Exception\ClientError", 
                "BAMBOO_NOTFOUND", 
                $e, $feed
            );
        } catch (BadResponseException $e) {
            $this->_logAndThrowError(
                "Bamboo\Feeds\Exception\BadResponse", 
                "BAMBOO_BADREQUEST", 
                $e, $feed
            );
        } catch(\Exception $e){
            // General Exception
            $this->_logAndThrowError(
                "Exception", 
                "BAMBOO_OTHER", 
                $e, $feed
            );
        }
 
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

        if ($this->_useFixture($feed)) {
            return $this->_fakeHttpClient;
        }

        if ($this->_useFailure($feed)) {
            return new HttpFail();
        }

        return new Http\Client($this->_baseUrl);

    }
    
    /*
     * Check if the current feed matches ?_fail one
     */
    private function _useFailure($feed) {
        if (!isset($_GET[self::PARAM_FAIL])) {
            return false;
        }

        $fakedFeed = $_GET[self::PARAM_FAIL];

        if ($this->_doesHaveMatches($feed, $fakedFeed)) {
            return true;
        }
        return false;
    }

    /*
     * Check if this request needs to use a fixture.
     * Does part before @ pattern match current Feed?
     */
    private function _useFixture($feed) {

        if (!isset($_GET[self::PARAM_DEGRADE])) {
            return false;
        }

        $fakedFeed = "";
        $fakePath = $_GET[self::PARAM_DEGRADE];
        $exploded = explode('@', $fakePath);
        if (isset($exploded[1])) {
            // Grab just fixture filename
            $fakedFeed = $exploded[0];
        }

        if ($this->_doesHaveMatches($feed, $fakedFeed)) {
            return true;
        }
        return false;
    }

    private function _doesHaveMatches($feed, $fakedFeed) {
        preg_match('/' . $fakedFeed . '/', $feed, $matches);
        if (count($matches) > 0) {
            // matches, so use the fixtureFile
            return true;          
        }
    }
    /*
     * Logs the error, throws 
     */
    private function _logAndThrowError($errorClass, $counterName, $e, $feed) {
        // Log Error
        Log::err("Bamboo error on feed $feed.");

        // Increment Counter
        Counter::increment($counterName);

        // Throw Exception
        $exception = new $errorClass(
            $e->getMessage(),
            $e->getCode(),
            $e
        );
        throw $exception;
    }

}