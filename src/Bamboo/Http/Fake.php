<?php

namespace Bamboo\Http;

class Fake extends Base implements GuzzleInterface
{

    public function get($feed, $params = array(), $queryParams = array()) {
        //setup request object
        $this->_buildPath($feed);

        return $this;
    }

    public function send() {
        //grab json from fixture
        $this->_response = file_get_contents($this->_path);

        return $this;
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
