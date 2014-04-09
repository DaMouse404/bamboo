<?php

namespace Bamboo\Feeds;

use Bamboo\Models\Episode;

class Episodes extends Base
{

    protected $_feed = 'episodes/{pids}';
    protected $_response;

    public function __construct($params, $pids) {
        $this->_setPids($pids);
        parent::__construct($params);
    }

    private function _setPids($pids) {
        if (is_array($pids)) {
            $pids = join($pids, ",");
        }
        $this->_feed = str_replace("{pids}", $pids, $this->_feed); 
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

}