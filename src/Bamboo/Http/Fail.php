<?php

namespace Bamboo\Http;

use Bamboo\Log;
use \Guzzle\Http\Message\Response;

/*
 * Has some key differences to fake client.
 * If ?_fail and no @fixture will:
 *  - use feed as fixture name (for unit tests)
 *  - use empty_feed.json in RW (for website cukes)
 */
class Fail extends Base implements GuzzleInterface
{
    private $_errorClass;
    private $_errorMessage;
    private $_statusCode;

    public function __construct() {
        $this->_errorClass = 'Guzzle\Http\Exception\ServerErrorResponseException';
        $this->_errorMessage = '500 Error on IBL';
        $this->_statusCode = 500;
    }

    public function setErrorClass($errorClass) {
        $this->_errorClass = $errorClass;
    }

    public function setErrorMessage($errorMessage) {
        $this->_errorMessage = $errorMessage;
    }

    public function setStatusCode($statusCode) {
        $this->_statusCode = $statusCode;
    }

    public function get($feed, $params = array(), $queryParams = array()) {
        // Setup request object
        $this->_buildPath($feed);
        Log::debug('BAMBOO: Failing feed: %s', $feed);
        return $this;
    }

    /*
     * Grab file contents from fixture.
     * Create exception class/object as handed down from above.
     * Add fixture contents to exception object (in form of Response)
     *
     * @return exception
     */
    public function send() {

        $fixture = $this->_loadFixture();

        $exception = new $this->_errorClass(
            $this->_errorMessage,
            $this->_statusCode
        );

        $response = new Response(
            $this->_statusCode,
            array(), // Headers
            $fixture['body']  // Response contents
        );
        Log::info('BAMBOO: Failing with contents from: %s', $this->_path);

        $exception->setResponse($response);

        throw $exception;
    }

    public function sendAll() {
        $excep = new MultiTransferException();
        $excep->addException(new Exception('Failing request on purpose'));
        throw $excep;
    }

    public function json() {
        // Return body of fixture, return array of data

        return;
    }

    private function _loadFixture() {
        //return body of fixture, return array of data
        // Split file so header is ignored
        $fixture = file_get_contents($this->_path);
        $contents = explode('UTF-8', $fixture);

        $response = array();

        if (isset($contents[1])) {
            $response['head'] = $contents[0];
            $response['body'] = $contents[1];
        } else {
            // No header found
            $response['head'] = '';
            $response['body'] = $fixture;
        }

        return $response;
    }
}
