<?php

namespace Bamboo\Feeds;

use Bamboo\Models\Broadcast;

class Schedules extends Base
{

    protected $_feed = 'channels/{channel}/schedule/{date}';
    protected $_response;
    protected $_broadcasts;

    public function __construct($params, $channel, $date) {
        $this->_setChannel($channel);
        $this->_setDate($date);
        parent::__construct($params);
    }

    public function getBroadcasts() {
        if (!$this->_broadcasts) {
            $broadcasts = array();
            foreach ($this->_response->schedule->elements as $broadcast) {
                $broadcasts[] = new Broadcast($broadcast);
            }
            $broadcasts = Broadcast::cleanBroadcasts($broadcasts);
            $this->_broadcasts = $broadcasts;
        }
        return $this->_broadcasts;
    }

    private function _setDate($date) {
        $this->_feed = str_replace("{date}", $date, $this->_feed); 
    }

    private function _setChannel($channel) {
        $this->_feed = str_replace("{channel}", $channel, $this->_feed); 
    }

}
