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

/*
 * Client Responsibility:
 * - pre fetch -> grab correct client
 * - fetch -> make response
 * - post fetch -> 
 * -- parse correct response OR
 * -- error translating/handling
 */
class Client
{

    const PARAM_DEGRADE = '_fake';
    const PARAM_FAIL = '_fail';
    const LOCALE_LENGTH = 2;

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
     * Set locale for remainder of requests.
     * Needs to be lowercased, without a suffix and 2 chars long.
     * Default is English.
     */
    public function setLang($locale) {
        $locale = mb_strtolower($locale);
        if (mb_strlen($locale) === self::LOCALE_LENGTH) {
            $this->_defaultParams['lang'] = $locale;
        }
    }

    public function getParam($key) {
        return $this->_defaultParams[$key];
    }    

    /*
     * Log Error...Translate Exception and throw
     */
    public function request($feed, $params = array()) {
        $client = $this->getClient($feed);
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
                "BAMBOO_{service}_SERVERERROR", 
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
                "BAMBOO_{service}_CURLERROR", 
                $e, $feed
            );
        } catch(\Exception $e) {
            // General Exception
            $this->_logAndThrowError(
                "Exception", 
                "BAMBOO_{service}_OTHER", 
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
                $counterName = "BAMBOO_{service}_BADREQUEST";
                break;
            case 403:
                $errorClass = "\Unauthorized";
                $counterName = "BAMBOO_{service}_UNAUTHORISED";
                break;
            case 404:
                $errorClass = "\NotFound";
                $counterName = "BAMBOO_{service}_NOTFOUND";
                break;
            case 405:
                $errorClass = "\MethodNotAllowed";
                $counterName = "BAMBOO_{service}_METHODNOTALLOWED";
                break;
            default:
                $errorClass = "\ClientError";
                $counterName = "BAMBOO_{service}_OTHER";
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
    public function getClient($feed) {

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
        return $this->_setupAndCheckMatches($feed, self::PARAM_FAIL);
    }

    /*
     * Check if this request needs to use a fixture.
     * Does part before @ pattern match current Feed?
     */
    private function _useFixture($feed) {
        return $this->_setupAndCheckMatches($feed, self::PARAM_DEGRADE);
    }

    private function _setupAndCheckMatches($feed, $type) {
        if (!isset($_GET[$type])) {
            return false;
        }

        $fakedFeed = "";
        $fakePath = $_GET[$type];
        $exploded = explode('@', $fakePath);
        if (isset($exploded[1])) {
            // Grab just fixture filename
            $fakedFeed = $exploded[0];
        } else if ($type === self::PARAM_FAIL) {
            // Nothing @ given and ?_fail
            // Largely for backwards compatibility with RW cukes
            $fakePath = str_replace("/", "_", $fakePath);
            $fakedFeed = $fakePath;
        }

        if ($this->_doesHaveMatches($feed, $fakedFeed)) {
            return true;
        }
    }

    /*
     * Check if current feed matches fixture given
     * Feed contains / which is convert to _ for fixture.
     *
     * @return bool
     */ 
    private function _doesHaveMatches($feed, $fakedFeed) {
        if ($fakedFeed) {
            $feed = str_replace("/", "_", $feed);
            preg_match('/' . $fakedFeed . '/', $feed, $matches);
            if (count($matches) > 0) {
                // matches, so use the fixtureFile
                return true;          
            }
        }
        return false;
    }

    /*
     * Logs the error, throws 
     */
    private function _logAndThrowError($errorClass, $counterName, $e, $feed) {
        $statusCode = $e->getCode();
        $message = $e->getMessage();

        list($errorSource, $sourceMessage) = $this->_getErrorSource($e);
        $fullCounterName = str_replace("{service}", $errorSource, $counterName); 

        // Log Error
        Log::err(
            "Bamboo Error: $errorClass, " .
            "Feed: $feed, " .
            "Status code: $statusCode, " .
            "Message: $message, " . 
            "Source: $errorSource, " .
            "Source Message: $sourceMessage"
        );

        // Increment Counter
        Counter::increment($fullCounterName);

        // Throw Exception
        $exception = new $errorClass(
            $message,
            $statusCode,
            $e
        );
        throw $exception;
    }

    /*
     * Used to detect who the error comes from.
     * Is Proxy if:
     *  - No json response is available 
     *  - Is a response but it does NOT contain 'error->details'
     *  - Response has 'fault->faultString'
     *
     * @return array(string $source, string $message)
     */
    private function _getErrorSource($e) {
        $object = null;
        $response = $e->getResponse();
        if ($response) {
            $response = $response->getBody(true);
            $object = json_decode($response);
        }

        $source = 'PROXY';
        $message = 'Something has gone wrong.';
        if (isset($object->fault, $object->fault->faultString)) {
            $message = $object->fault->faultString;
        }  

        if (isset($object->error, $object->error->details)) {
            $source = 'IBL';
            $message = $object->error->details;
        }

        return array($source, $message);
    }

}