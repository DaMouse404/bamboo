<?php

namespace Bamboo\Http;

/* 
 * Contract/Interface used by Guzzle which our HTTP Clients must adhere to.
 */

Interface GuzzleInterface
{
    public function get($feed, $params = array(), $queryParams = array());

    public function send();

    public function json();
}