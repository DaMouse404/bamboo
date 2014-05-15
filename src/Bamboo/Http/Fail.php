<?php

namespace Bamboo\Http;

use \Guzzle\Http\Message\Response;

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

        $exception = new $this->_errorClass(
            $this->_errorMessage, 
            $this->_statusCode
        );

        $response = new Response(
            $this->_statusCode, 
            array(), // Headers
            file_get_contents($this->_path)  // Response contents
        );

        $exception->setResponse($response);

        throw $exception;
    }

    public function json() {
        // Return body of fixture, return array of data

        return;
    }


}
