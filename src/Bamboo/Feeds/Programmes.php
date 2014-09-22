<?php

namespace Bamboo\Feeds;

use Bamboo\Models\Programme;

class Programmes extends BaseParallel
{

    protected $_feedName = 'programmes/{pids}';
    protected $_feeds = array();
    protected $_responses;
    protected $_response;

    public function __construct($params, $pids) {
        $this->_setPids($pids);
        parent::__construct($params);

        // grab all the parallel responses and add them all to a single response object for easy access
        $responses = $this->_responses;

        $baseResponse = array_shift($responses);
        foreach ($responses as $response) {
            $baseResponse->programmes = array_merge($baseResponse->programmes, $response->programmes);
        }

        $this->_response = $baseResponse;
    }

    private function _setPids($pidSets) {
        $feedName = $this->_feedName;
        $this->_feeds = array_map(
            function ($pids) use ($feedName) {
                if (is_array($pids)) {
                    $pids = join($pids, ",");
                }
                return str_replace("{pids}", $pids, $feedName);
            },
            $pidSets
        );
    }

    /*
     * Return array of Programme models
     */
    public function getElements() {
        return $this->_buildModels($this->_response->programmes);
    }

    /*
     * Returns the raw response
     */
    public function getResponse() {
        return $this->_response;
    }
}
