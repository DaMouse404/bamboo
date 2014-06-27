<?php

namespace Bamboo\Http;
use Bamboo\Log;
class Fake extends Base implements GuzzleInterface
{

    public function get($feed, $params = array(), $queryParams = array()) {
        //setup request object
        $this->_buildPath($feed);
        Log::debug('BAMBOO: Faking feed: ' . $feed);

        return $this;
    }

    public function send($requests=null) {
        if (is_array($requests)) {
            return $this->sendAll($requests);
        }

        Log::debug('BAMBOO: Using Fixture: ' . $this->_path);

        //grab json from fixture
        $this->_response = file_get_contents($this->_path);

        return $this;
    }

    public function sendAll($requests) {

        Log::debug('BAMBOO: Using Fixture for all ' . count($requests) . ' parallel requests: ' . $this->_path);
        $responses = array();
        $feedContents = file_get_contents($this->_path);
        foreach ($requests as $request) {
            $responses[] = clone $feedContents;
        }

        return $responses;
    }

    public function json() {
        //return body of fixture, return array of data

        // Split file so header is ignored
        $response = explode('UTF-8', $this->_response);
        if (isset($response[1])) {
            return json_decode($response[1], true);
        }
        // No header found
        return json_decode($this->_response, true);
    }
}
