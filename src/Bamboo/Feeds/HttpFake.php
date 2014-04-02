<?php

namespace Bamboo\Feeds;

use Bamboo\Feeds\Client;

class HttpFake
{

    private $_path;
    private $_response;
    private $_fixtureLocation =  '/../../../tests/fixtures/';

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
        $response = explode('UTF-8',$this->_response);
        if ($response[1]) {
            return json_decode($response[1], true);
        }
        // No header found
        return json_decode($this->_response, true);
    }

    public function setFixturesPath($location) {
        $this->_fixtureLocation = $location;
    }
    /*
     * Build path to fixture.
     */
    private function _buildPath($feed) {

        // Strip unnecessary values from feed
        $feed = str_replace("ibl/v1/", "", $feed);
        $feed = str_replace(".json", "", $feed);
        $feed = str_replace("/", "_", $feed);

        // Map _fake to fixture file
        $fakePath = $_GET['_fake'];
        $exploded = explode('@', $fakePath);
        if (isset($exploded[1])) {
            $fakedFeed = $exploded[0];
            $fixtureFile = $exploded[1];
        } else {
            $fakedFeed = $fakePath;
            $fixtureFile = $fakePath;
        }

        // Does feed match that one given
        if ($fakedFeed === $feed) {
            // matches, so use the fixtureFile

            // No @ so just use feed as fixture name
            if ($fixtureFile === $feed) {
                $file = $feed;
            } else {
                $file = $fixtureFile;
            }            
        } else {
            //no match, use feeds fixture
            $file = $feed;
        }

        $jsonFile = $file . '.json';
        $this->_path = $this->_fixtureLocation . $jsonFile;
    }
}
