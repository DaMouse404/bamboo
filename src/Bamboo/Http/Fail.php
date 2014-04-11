<?php

namespace Bamboo\Http;

class Fail
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
        //setup request object
        return $this;
    }

    public function send() {

        throw new $this->_errorClass(
            $this->_errorMessage, 
            $this->_statusCode
        );
    }

    public function json() {
        //return body of fixture, return array of data

        return;
    }



}
