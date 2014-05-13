<?php

namespace Bamboo\Http;

class Base 
{

    protected $_path;
    protected $_response;
    protected $_fixtureLocation =  '/../../../tests/fixtures/';

    public function setFixturesPath($location) {
        $this->_fixtureLocation = $location;
    }

    /*
     * Build path to fixture.
     */
    protected function _buildPath($feed) {

        // Strip unnecessary values from feed
        $feed = str_replace("ibl/v1/", "", $feed);
        $feed = str_replace(".json", "", $feed);
        $feed = str_replace("/", "_", $feed);

        // Map _fake to fixture file
        $this->_path =  $this->_fixtureLocation . $this->_fixtureFile($feed) . '.json';
    }
    
    /*
     * From the URL determine filename of fixture
     * For part after @ match to a fixture file.
     */
    protected function _fixtureFile($feed) {

        if ($_GET['_fake']) {
            $fakePath = (isset($_GET['_fake'])) ? $_GET['_fake'] : '';
            $exploded = explode('@', $fakePath);

            // Split query string by the @
            if (isset($exploded[1])) {
                $fakedFeed = $exploded[0];
                $fixtureFile = $exploded[1];
            } else {
                // No @ so just use feed as fixture name
                $fakedFeed = $fakePath;
                $fixtureFile = $fakePath;
            }
            
            $fixtureFile = str_replace("-", "_", $fixtureFile);
        } else {
            $fixtureFile = $feed;
        }

        return $fixtureFile;
    }
}