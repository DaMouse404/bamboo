<?php

namespace Bamboo\Tests;

use \Bamboo\Client;
use Bamboo\Feeds\Atoz;

class ClientTest extends BambooTestCase
{

    public function testServerError() {
        parent::setupFailRequest('atoz');
        $this->setExpectedException('Bamboo\Exception\ServerError');
        $feedObject = new Atoz(array(), 'a');
    }

    public function testBadRequest() {
        parent::setupFailRequest(
            'atoz', 
            'Guzzle\Http\Exception\ClientErrorResponseException', 
            400
        );
        $this->setExpectedException('Bamboo\Exception\BadRequest');
        $feedObject = new Atoz(array(), 'a');
    }

    public function testNotFound() {
        parent::setupFailRequest(
            'atoz', 
            'Guzzle\Http\Exception\ClientErrorResponseException', 
            404
        );
        $this->setExpectedException('Bamboo\Exception\NotFound');
        $feedObject = new Atoz(array(), 'a');
    }
}