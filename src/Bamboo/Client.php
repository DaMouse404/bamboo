<?php

namespace Bamboo;

use Guzzle\Http;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\CurlException;
use Bamboo\Log;
use Bamboo\Counter;
use Bamboo\Exception;
use Bamboo\Exception\EmptyFeed;

class Client
{

    const PARAM_DEGRADE = '_fake';
    const PARAM_FAIL = '_fail';

    private $_host = "";
    private $_baseUrl = "";
    private $_proxy = "";
    private $_config = array();
    private $_fakeHttpClient;
    private $_failHttpClient;
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

    public function setHost($host) {
        $this->_host = $host;
    }

    public function setBaseUrl($baseUrl) {
        $this->_baseUrl = $baseUrl;
    }

    public function setFakeHttpClient($fakeHttpClient) {
        $this->_fakeHttpClient = $fakeHttpClient;
    }

    public function setFailHttpClient($failHttpClient) {
        $this->_failHttpClient = $failHttpClient;
    }

    public function setConfig($config) {
        $this->_config = $config;
    }

    public function setProxy($proxy) {
        $this->_proxy = $proxy;
    }

    /*
     * Log Error...Translate Exception and throw
     */
    public function request($feed, $params = array()) {
        $client = $this->_getClient($feed);
        $params = array_merge($this->_defaultParams, $this->_config, $params);

        try {
            $request = $client->get(
                $this->_baseUrl . $feed . ".json", 
                array(), 
                array(
                    'query' => $params,
                    'proxy' =>  $this->_proxy,
                    'timeout'         => 6, // 6 seconds
                    'connect_timeout' => 5 // 5 seconds
                )
            );
            $response = $request->send();
        } catch (ServerErrorResponseException $e) {
            $this->_logAndThrowError(
                "Bamboo\Exception\ServerError", 
                "BAMBOO_SERVERERROR", 
                $e, $feed
            );
        } catch (ClientErrorResponseException $e) {
            $errorArray = $this->_translateClientError($e);
            $this->_logAndThrowError(
                "Bamboo\Exception" . $errorArray['class'], 
                $errorArray['counter'], 
                $e, $feed
            );
        } catch (CurlException $e) {
            // Response/Connection Timeout
            $this->_logAndThrowError(
                "Bamboo\Exception\CurlError", 
                "BAMBOO_CURLERROR", 
                $e, $feed
            );
        } catch(\Exception $e) {
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

    /*
     * Translate the 4** errors into counter and error class.
     * @return array
     */
    private function _translateClientError($e) {
        switch ($e->getCode()) {
            case 400:
                $errorClass = "\BadRequest";
                $counterName = "BAMBOO_BADREQUEST";
                break;
            case 403:
                $errorClass = "\Unauthorized";
                $counterName = "BAMBOO_UNAUTHORISED";
                break;
            case 404:
                $errorClass = "\NotFound";
                $counterName = "BAMBOO_NOTFOUND";
                break;
            case 405:
                $errorClass = "\MethodNotAllowed";
                $counterName = "BAMBOO_METHODNOTALLOWED";
                break;
            default:
                $errorClass = "\ClientError";
                $counterName = "BAMBOO_OTHER";
                break;
        }

        return array(
            'class' => $errorClass, 
            'counter' => $counterName
        );
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
            return $this->_failHttpClient;
        }

        return new Http\Client($this->_host);

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
        $statusCode = $e->getCode();
        $message = $e->getMessage();
        
        // Log Error
        Log::err("Bamboo Error: $errorClass. Feed: $feed. Status code: $statusCode. Message: $message.");

        // Increment Counter
        Counter::increment($counterName);

        // Throw Exception
        $exception = new $errorClass(
            $message,
            $statusCode,
            $e
        );
        throw $exception;
    }

}