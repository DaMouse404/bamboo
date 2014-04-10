<?php

namespace Bamboo\Feeds\Http;

use Guzzle\Http\Exception\ServerErrorResponseException;

class Fail
{

    public function get($feed, $params = array(), $queryParams = array()) {
        //setup request object
        return $this;
    }

    public function send() {
        throw new ServerErrorResponseException("500 Error on IBL", 500);
    }

    public function json() {
        //return body of fixture, return array of data

        return;
    }



}
