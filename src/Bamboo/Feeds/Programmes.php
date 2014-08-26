<?php

namespace Bamboo\Feeds;

use Bamboo\Models\Programme;

class Programmes extends BaseParallel
{

    protected $_feedName = 'programmes/{pids}';
    protected $_feeds = array();
    protected $_response;

    public function __construct($params, $pids) {
        $this->_setPids($pids);
        parent::__construct($params);
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
        $programmes = array();
        foreach ($this->_response->programmes as $programme) {
            $programmes[] = new Programme($programme);
        }

        return $programmes;
    }

}