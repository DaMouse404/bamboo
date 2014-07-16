<?php

namespace Bamboo;

use Guzzle\Http;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\MultiTransferException;
use Guzzle\Http\Exception\CurlException;
use Bamboo\Log;
use Bamboo\Counter;
use Bamboo\Exception;
use Bamboo\Exception\EmptyFeed;

/*
 * Client Responsibility:
 * - pre fetch -> grab correct client
 * - fetch -> make response
 * - post fetch -> parse correct response OR error translating/handling
 */
class Client
{

    const PARAM_DEGRADE = '_fake';
    const PARAM_FAIL = '_fail';
    const LOCALE_LENGTH = 2;

    /*
     * Hostname used for the request.
     * @var string
     */
    private $_host = "";
    /*
     * URL prepended to all feeds for the service. Contains provider name and version.
     * @var string
     */
    private $_baseUrl = "";
    /*
     * Tells the HTTP Client what proxy it must route traffic through if necessary.
     * @var string
     */
    private $_networkProxy = "";
    /*
     * The service used by iBL to respond to clients. Default state is off. Errors are handled differently.
     * @var boolean
     */
    private $_serviceProxy = false;
    /*
     * Used to set api_key and any other important feed info which is later merged over $_defaultParams.
     * @var array
     */
    private $_config = array();
    /*
     * The HTTP Client to use for normal unit tests, cukes and reading fixtures.
     * @var object
     */
    private $_fakeHttpClient;
    /*
     * The HTTP Client to use to to do all the above except for error and fail states/responses.
     * @var object
     */
    private $_failHttpClient;
    /*
     * An array of params appended onto every request as a query string.
     * @var array
     */
    private $_defaultParams = array(
                                "api_key" => "",
                                "availability" => "all",
                                "lang" => "en",
                                "rights" => "web"
            );

    /*
     * Singleton interface for the client.
     */
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

    public function setNetworkProxy($proxy) {
        $this->_networkProxy = $proxy;
    }

    public function setServiceProxy($bool) {
        $this->_serviceProxy = $bool;
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
    public function requestAll($feeds) {
        $client = $this->getClient($feeds[0][0]);

        try {
            $requests = array();
            foreach ($feeds as $feed) {
                $params = array_merge($this->_defaultParams, $this->_config, $feed[1]);

                $fullUrl = $this->_host . $this->_baseUrl . $feed[0];
                Log::info('BAMBOO: Parallel iBL feed: ' . $fullUrl . '.json?' . http_build_query($params));
                $requests[] = $client->get(
                    $this->_baseUrl . $feed[0] . '.json',
                    array(),
                    array(
                        'query' => $params,
                        'proxy' =>  $this->_networkProxy,
                        'timeout'         => 6, // 6 seconds
                        'connect_timeout' => 5 // 5 seconds
                    )
                );
            }

            $responses = $client->send($requests);
        } catch (MultiTransferException $e) {
            // Only deal with first exception as we'll throw it and quit anyway
            $this->_parseRequestException($e->getFirst(), $feed);
        }
        $objects = array();
        foreach ($responses as $response) {
            $objects[] = $this->_parseResponse($response, $feed, $params);
        }
        return $objects;
    }

    /*
     * Log Error...Translate Exception and throw
     */
    public function request($feed, $params) {
        $client = $this->getClient($feed);
        $params = array_merge($this->_defaultParams, $this->_config, $params);

        $fullUrl = $this->_host . $this->_baseUrl . $feed;

        Log::info('Fetching iBL feed: ' . $fullUrl . '.json with params: "' . http_build_query($params) . '"');

        try {
            $request = $client->get(
                $this->_baseUrl . $feed . ".json",
                array(),
                array(
                    'query' => $params,
                    'proxy' =>  $this->_networkProxy,
                    'timeout'         => 6, // 6 seconds
                    'connect_timeout' => 5 // 5 seconds
                )
            );
            $response = $request->send();
        } catch (\Exception $e) {
            $this->_parseRequestException($e, $feed);
        }

        $object = $this->_parseResponse($response, $feed, $params);

        return $object;
    }

    private function _parseRequestException ($e, $feed) {
        switch (get_class($e)) {
            case 'Guzzle\Http\Exception\ServerErrorResponseException':
                $this->_logAndThrowError(
                    "Bamboo\Exception\ServerError",
                    "BAMBOO_{service}_SERVERERROR",
                    $e, $feed
                );

            case 'Guzzle\Http\Exception\ClientErrorResponseException':
                $errorArray = $this->_translateClientError($e);
                $this->_logAndThrowError(
                    "Bamboo\Exception" . $errorArray['class'],
                    $errorArray['counter'],
                    $e, $feed
                );

            case 'Guzzle\Http\Exception\CurlException':
                // Response/Connection Timeout
                $this->_logAndThrowError(
                    "Bamboo\Exception\CurlError",
                    "BAMBOO_{service}_CURLERROR",
                    $e, $feed
                );

            default:
                // Anything else
                $this->_logAndThrowError(
                    "Exception",
                    "BAMBOO_{service}_OTHER",
                    $e, $feed
                );
        }
    }

    /*
     * Translate the 4** errors into counter and error class.
     * @return array
     */
    private function _translateClientError($e) {
        $response = $e->getResponse();
        $statusCode = $response->getStatusCode();
        switch ($statusCode) {
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
        // The response comes back in array format.
        // We go to json then back again to make sure it's an object
        // We don't cast to ArrayObject because it only casts one level down the nested items
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
        $response = $e->getResponse();
        $statusCode = $response->getStatusCode();
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
        $response = null;

        // Certain exceptions dont have the method.
        // These will have no response (timeouts) so set to default source.
        if (method_exists($e, 'getResponse')) {
            $response = $e->getResponse();
            if ($response) {
                $response = $response->getBody(true);
                $object = json_decode($response);
            }
        }

        $source = 'IBL';
        if ($this->_serviceProxy) {
            $source = 'PROXY';
        }
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
