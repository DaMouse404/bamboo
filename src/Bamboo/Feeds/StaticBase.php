<?php

namespace Bamboo\Feeds;
use Bamboo\Exceptions\NotFound;
use Bamboo\Exceptions\EmptyFeed;
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
        $json = $this->fetchFile($feed);
        Log::debug('Fetching Static feed from /Assets: ' . $feed);
        // can return false or file contents, so falsyness is a failure
        if (!$json) {
            throw new EmptyFeed('Could not find file in /Assets/ for feed: '. $feed);
        } else {
            return json_decode($json);
        }
    }

    private function fetchFile($feed) {
        return file_get_contents(dirname(__FILE__) . '/../Assets/' . $feed . '.json');
    }
}
