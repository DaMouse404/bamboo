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
        Log::info('Fetching Static feed from /Assets: %s', $feed);
        try {
            $json = $this->fetchFile($feed);
        } catch (\Exception $e) {
            $this->throwEmpty($feed);
        }
        // can return false or file contents, so falsyness is a failure
        if (!$json) {
            $this->throwEmpty($feed);
        } else {
            return json_decode($json);
        }
    }

    private function throwEmpty($feed)
    {
        throw new EmptyFeed('Could not find file in /Assets/ for feed: ' . $feed);
    }

    private function fetchFile($feed) {
        return file_get_contents(dirname(__FILE__) . '/../Assets/' . $feed . '.json');
    }
}
