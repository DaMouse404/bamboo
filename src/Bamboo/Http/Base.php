<?php

namespace Bamboo\Http;

use Bamboo\Client;

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
        $this->_path =  $this->_fixtureLocation . 
                        $this->_getFixtureFile() . 
                        '.json';
    }
    
    /*
     * From the URL determine filename of fixture
     * For part after @ match to a fixture file.
     */
    protected function _getFixtureFile() {

        if (isset($_GET[Client::PARAM_DEGRADE])) {
            $fixtureFile = $this->_setupFixturePath(Client::PARAM_DEGRADE);
        } else if (isset($_GET[Client::PARAM_FAIL])) {
            $fixtureFile = $this->_setupFixturePath(Client::PARAM_FAIL);
        }

        return $fixtureFile;
    }

    private function _setupFixturePath($type) {
        $fakePath = (isset($_GET[$type])) ? $_GET[$type] : '';
        $exploded = explode('@', $fakePath);

        // Split query string by the @
        if (isset($exploded[1])) {
            //$fakedFeed = $exploded[0];
            $fixtureFile = $exploded[1];
        } else if ($type === Client::PARAM_FAIL) {
            // ?_fail and no @ given 
            // For backwards compatibility with RW cukes
            $fixtureFile = 'empty_feed';
        } else {
            // No @ so just use feed as fixture name
            $fakedFeed = $fakePath;
            $fixtureFile = $fakePath;
        }

        return str_replace("-", "_", $fixtureFile);
    }
}
