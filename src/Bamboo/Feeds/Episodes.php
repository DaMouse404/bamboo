<?php

namespace Bamboo\Feeds;

use Bamboo\Models\Episode;

class Episodes extends BaseParallel
{

    protected $_feed = 'episodes/{pids}';
    protected $_feeds = array();
    protected $_response;

    public function __construct($params, $pids) {
        $this->_setPids($pids);
        parent::__construct($params);

        // grab all the parallel responses and add them all to a single response object for easy access
        $responses = $this->_responses;

        $baseResponse = array_shift($responses);
        foreach ($responses as $response) {
            $baseResponse->episodes = array_merge($baseResponse->episodes, $response->episodes);
        }

        $this->_response = $baseResponse;
    }

    private function _setPids($pids) {
        $feedName = $this->_feed;

        if (!is_array($pids)) {
            $pids = array($pids);
        }

        $this->_feeds = array_map(
            function ($pid) use ($feedName) {
                return str_replace("{pids}", $pid, $feedName);
            },
            $pids
        );
    }

    /*
     * Return array of Channel models
     */
    public function getElements() {
        $episodes = array();
        foreach ($this->_response->episodes as $episode) {
            $episodes[] = new Episode($episode);
        }

        return $episodes;
    }

     /*
    * Returns the raw episodes response
    */
    public function getRawEpisodes() {
        return $this->_response->episodes;
    }
}
