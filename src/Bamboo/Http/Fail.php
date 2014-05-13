<?php

namespace Bamboo\Http;

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
        //setup request object

        $this->_buildPath($feed);

        return $this;
    }

    public function send() {

        // attach to error as response->body
        $response = file_get_contents($this->_path);
        $e = new $this->_errorClass(
            $this->_errorMessage, 
            $this->_statusCode
        );
//var_dump($e);
//die('2');
        $response = new \Guzzle\Http\Message\Response($this->_statusCode, $this->_errorMessage, $response);
        var_dump($response);
die('4');
        $e->setResponse($response);
die('3');
var_dump($e);
die;
        throw $e;
    }

    public function json() {
        //return body of fixture, return array of data

        return;
    }


}
