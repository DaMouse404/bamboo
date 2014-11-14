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

    public static function findMatchingFeed ($feed, $list) {
        $fixtureFile = false;
        foreach ($list as $partialName => $fixture) {
            $pos = mb_strpos($feed, $partialName);
            if ($pos !== false) {
                $fixtureFile = $fixture;
            }
        }

        return $fixtureFile;
    }

    /*
     * Build path to fixture.
     */
    protected function _buildPath($feed, $list) {
        return $this->_fixtureLocation .
               $this->findMatchingFeed($feed, $list) .
               '.json';

    }
}
