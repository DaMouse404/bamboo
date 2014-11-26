<?php

namespace Bamboo;

use Guzzle\Plugin\Cache\CachePlugin;
use Guzzle\Plugin\Cache\DefaultCacheStorage;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\MultiTransferException;
use Guzzle\Http\Exception\CurlException;
use Bamboo\Log;
use Bamboo\Counter;
use Bamboo\Configuration;
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

    const LOCALE_LENGTH = 2;

    /*
     * The service used by iBL to respond to clients. Default state is off. Errors are handled differently.
     * @var boolean
     */
    private $_serviceProxy = false;
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
        $baseUrl = Configuration::getBaseUrl();

        $firstFeed = array_shift(array_values($feeds));
        $client = $this->getClient($baseUrl . $firstFeed[0] . ".json");

        try {
            $requests = array();
            // Get a key to use to group requests together in the logs
            $requestGroupKey = mb_substr(microtime(), 3, 4);
            foreach ($feeds as $feed) {
                $params = array_merge($this->_defaultParams, Configuration::getConfig(), $feed[1]);
                $fullUrl = Configuration::getHost() . $baseUrl . $feed[0];
                $log = 'BAMBOO: (#%s) Parallel iBL feed: %s.json?%s';
                Log::info($log, $requestGroupKey, $fullUrl, http_build_query($params));

                $feedUrl = $baseUrl . $feed[0] . ".json";
                $requests[] = $this->_getRequestObject($client, $feedUrl, $params);
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
        $params = array_merge($this->_defaultParams, Configuration::getConfig(), $params);
        $baseUrl = Configuration::getBaseUrl();
        $fullUrl = Configuration::getHost() . $baseUrl . $feed;
        Log::info('Fetching iBL feed: %s.json?%s', $fullUrl, http_build_query($params));

        try {
            $feedUrl = $baseUrl . $feed . ".json";
            $client = $this->getClient($feedUrl);

            $request = $this->_getRequestObject($client, $feedUrl, $params);
            $response = $request->send();
        } catch (\Exception $e) {
            $this->_parseRequestException($e, $feed);
        }

        $object = $this->_parseResponse($response, $feed, $params);

        return $object;
    }

    private function _getRequestObject($client, $feedUrl, $params) {
        $networkProxy = Configuration::getNetworkProxy();

        return $client->get(
            $feedUrl,
            array(),
            array(
                'query' => $params,
                'proxy' => $networkProxy,
                'timeout' => 6, // 6 seconds
                'connect_timeout' => 5 // 5 seconds
            )
        );
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
        if ($e instanceof BadResponseException) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
        } else {
            // Assume timeout
            $statusCode = 418;
        }
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
        \Bamboo\Log::info('BAMBOO: Checking in client for: %s for faking', $feed);

        if ($this->_useFixture($feed) !== false) {
            return Configuration::getFakeHttpClient();
        }

        if ($this->_useFailure($feed) !== false) {
            return Configuration::getFailHttpClient();
        }

        $client = new \Guzzle\Http\Client(Configuration::getHost());

        if (Configuration::getCache()) {
            $client->addSubscriber(Configuration::getCache());
        }

        return $client;
    }

    /*
     * Check if the current feed matches ?_fail one
     */
    private function _useFailure($feed) {
        return \Bamboo\Http\Base::findMatchingFeed($feed, Configuration::getFailRequests());
    }

    /*
     * Check if this request needs to use a fixture.
     * Does part before @ pattern match current Feed?
     */
    private function _useFixture($feed) {
        return \Bamboo\Http\Base::findMatchingFeed($feed, Configuration::getFakeRequests());
    }

    /*
     * Logs the error, throws
     */
    private function _logAndThrowError($errorClass, $counterName, $e, $feed) {
        if ($e instanceof BadResponseException) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
        } else {
            // Assume timeout
            $statusCode = 418;
        }
        $message = $e->getMessage();

        list($errorSource, $sourceMessage) = $this->_getErrorSource($e);
        $fullCounterName = str_replace("{service}", $errorSource, $counterName);

        // Log Error
        Log::err(
            "Bamboo Error: %s, " .
            "Feed: %s, " .
            "Status code: %d, " .
            "Message: %s, " .
            "Source: %s, " .
            "Source Message: %s",
            $errorClass,
            $feed,
            $statusCode,
            $message,
            $errorSource,
            $sourceMessage
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
