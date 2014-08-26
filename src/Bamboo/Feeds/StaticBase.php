<?php

namespace Bamboo\Feeds;
use Bamboo\Exception\NotFound;
use Bamboo\Exception\EmptyFeed;
use Bamboo\Log;

class StaticBase extends Base
{
    public function __construct() {
        $this->_response = $this->fetchAssetFeed($this->_feed);
    }

    /**
     * Fetch a feed by reading a JSON file from /Assets
     */
    public function fetchAssetFeed($feed) {
        Log::info('Fetching Static feed from /Assets: ' . $feed);

        try {
            $contents = $this->fetchFile($feed);
        } catch (Exception $e) {
            $this->throwEmpty($feed);
        }

        // can return false or file contents, so falsyness is a failure
        $json = $this->decodeFixture($contents);
        if (!$json) {
            $this->throwEmpty($feed);
        } else {
            return $json;
        }
    }

    public function decodeFixture($fixture) {
        $response = explode('UTF-8', $fixture);

        if (isset($response[1])) {
            $body = $response[1];
        } else {
            $body = $fixture;
        }

        return json_decode($body);
    }

    private function throwEmpty($feed) {
        throw new EmptyFeed('Could not find feed in /Assets/ for feed: '. $feed);
    }

    private function fetchFile($feed) {
        $filePath = dirname(__FILE__) . '/../Assets/' . $feed . '.json';

        if ( !file_exists($filePath) ) {
            throw new NotFound('Could not find file in /Assets/ for feed: '. $feed);
        }

        return file_get_contents($filePath);
    }
}
