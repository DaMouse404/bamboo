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
        // Append .json to enable checking the very end of the feed

        \Bamboo\Log::info('BAMBOO: Initial feed in base: %s', $feed);
        foreach ($list as $partialName => $fixture) {
            \Bamboo\Log::info('BAMBOO: Checking feed: %s matches: %s', $feed, $partialName);
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
