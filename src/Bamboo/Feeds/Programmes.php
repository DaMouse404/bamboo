<?php

namespace Bamboo\Feeds;

use Bamboo\Models\Programme;

class Programmes extends Base
{

    protected $_feed = 'programmes/{pids}';
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
        $programmes = array();
        foreach ($this->_response->programmes as $programme) {
            $programmes[] = new Programme($programme);
        }

        return $programmes;
    }

    public function getResponse() {
        return json_encode($this->_response);
    }

}