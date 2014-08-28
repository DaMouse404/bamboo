<?php

namespace Bamboo\Http;
use Bamboo\Log;
class Fake extends Base implements GuzzleInterface
{
    public function get($feed, $params = array(), $queryParams = array()) {
        //setup request object
        $this->_buildPath($feed);
        Log::info('BAMBOO: Faking feed: ' . $feed);

        return $this;
    }

    public function send($requests=null) {
        if (is_array($requests)) {
            return $this->sendAll($requests);
        }

        Log::info('BAMBOO: Using Fixture: ' . $this->_path);

        //grab json from fixture
        $this->_response = file_get_contents($this->_path);

        return $this;
    }

    public function sendAll($requests) {

        Log::info('BAMBOO: Using Fixture for all ' . count($requests) . ' parallel requests: ' . $this->_path);
        $this->_response = array();
        $responses = array();
        $feedContents = file_get_contents($this->_path);
        foreach ($requests as $request) {
            $response = clone $this;
            $response->_response = $feedContents;
            $responses[] = $response;
        }
        return $responses;
    }

    public function getBody($asString = false)
    {
        //return body of fixture, return array of data
        // Split file so header is ignored
        $response = explode('UTF-8', $this->_response);

        if (isset($response[1])) {
            $body = $response[1];
        } else {
            // No header found
            $body = $this->_response;
        }

        return $asString ? (string) $body : $body;
    }

    public function json() {
        return json_decode($this->getBody(true), true);
    }
}
