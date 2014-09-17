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
            $broadcasts = $this->_cleanBroadcasts($broadcasts);
            $this->_broadcasts = $broadcasts;
        }
        return $this->_broadcasts;
    }

    private function _cleanBroadcasts($broadcasts) {
        $filtered = array();
        foreach ($broadcasts as $i => $current) {
            if ($i === 0) {
                $filtered[] = $current;
                continue;
            }
            $previous = $broadcasts[$i - 1];

            //The previous broadcast was marked as useless
            if (!$previous) {
                $filtered[] = $current;
                continue;
            }

            $currentStart = $current->getStartTime();
            $currentEnd = $current->getEndTime();
            $previousStart = $previous->getStartTime();
            $previousEnd = $previous->getEndTime();

            $previousEndDate = new \DateTime($previousEnd); 
            $currentEndDate = new \DateTime($currentEnd);
            $currentStartDate = new \DateTime($currentStart);

            //Insert off-air gap
            if ($currentStartDate > $previousEndDate) {
                $filtered[] = $this->_getEmptyBroadcast($previousEnd, $currentStart);
                $filtered[] = $current;

                //Duplicates: get rid of both
            } else if ($currentStart === $previousStart && $currentEnd === $previousEnd) {
                $filtered[count($filtered) - 1] = $this->_getEmptyBroadcast($previousStart, $previousEnd);

                //One programme inside another: get rid of both
            } else if ($currentEndDate < $previousEndDate) {
                $filtered[count($filtered) - 1] = $this->_getEmptyBroadcast($previousStart, $previousEnd);
                $broadcasts[$i] = false; //Don't take this programme into account for the next iteration

                //Overlap: get rid of both
            } else if ($currentStartDate < $previousEndDate) {
                $filtered[count($filtered) - 1] = $this->_getEmptyBroadcast($previousStart, $currentEnd);

            } else {
                $filtered[] = $current;
            }
        }
        return $filtered;
    }

    private function _getEmptyBroadcast($startDate, $endDate) {
        return new Broadcast(
            (object) array(
                'type' => 'broadcast',
                'start_time' => $startDate,
                'end_time' => $endDate,
                'scheduled_start' => $startDate,
                'scheduled_end' => $endDate
            )
        );
    }

    private function _setDate($date) {
        $this->_feed = str_replace("{date}", $date, $this->_feed); 
    }

    private function _setChannel($channel) {
        $this->_feed = str_replace("{channel}", $channel, $this->_feed); 
    }

}
