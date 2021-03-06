<?php

namespace Bamboo\Models;

class Broadcast extends Elements
{

    const AVAILABLE = 'available';
    const COMING_SOON = 'coming_soon';

    // @codingStandardsIgnoreStart
    protected $_scheduled_start = "";
    protected $_scheduled_end = "";
    protected $_transmission_start = "";
    protected $_transmission_end = "";
    // @codingStandardsIgnoreEnd
    protected $_duration;
    protected $_episode;
    protected $_blanked;
    protected $_repeat;

    /**
     * Get start time from episode
     *
     * @return string
     */
    public function getStartTime($useTransmission = false) {
        // @codingStandardsIgnoreStart
        if ($useTransmission && $this->_transmission_start) {
            return $this->_transmission_start;
        }
        return $this->_scheduled_start;
        // @codingStandardsIgnoreEnd
    }

    /**
     * Get end time from broadcast
     *
     * @return string
     */
    public function getEndTime($useTransmission = false) {
        // @codingStandardsIgnoreStart
        if ($useTransmission && $this->_transmission_end) {
            return $this->_transmission_end;
        }
        return $this->_scheduled_end;
        // @codingStandardsIgnoreEnd
    }

    /**
     * Get episode inside Broadcast
     *
     * @return Bamboo\Models\Episode
     */
    public function getEpisode() {
        $episodeModel = new Episode($this->_episode);
        return $episodeModel;
    }

    /**
     * Is broadcast banned from simulcast
     *
     * @return boolean
     */
    public function isBlanked() {
        return !!$this->_blanked;
    }

    /**
     * Calculates whether this broadcast has a simulcast available
     * @return type boolean
     */
    public function isSimulcast() {
        return !$this->isBlanked() && $this->isOnNow();
    }

    /**
     * @return type boolean
     */
    public function isCatchUp() {
        return ($this->getEpisode()->getStatus() === self::AVAILABLE);
    }

    /**
     * Returns whether this broadcast is available to watch either via catchup or simulcast
     * @return type boolean
     */
    public function isAvailableToWatch() {
        return $this->isCatchUp() || $this->isSimulcast();
    }

    /**
     * Returns whether this broadcast will be available on catchup in the future
     * @return type boolean
     */
    public function isComingSoon() {
        return ($this->getEpisode()->getStatus() === self::COMING_SOON);
    }

    /**
     * Is broadcast a repetition
     *
     * @return boolean
     */
    public function isRepeat() {
        return !!$this->_repeat;
    }

    public function isOnNow() {
        $time = new \DateTime();
        $startTime = new \DateTime($this->getStartTime());
        $endTime = new \DateTime($this->getEndTime());

        return (
            $startTime->getTimestamp() <= $time->getTimestamp() &&
            $endTime->getTimestamp() > $time->getTimestamp()
        );
    }

    public function isOnNext() {
        $time = new \DateTime();
        $startTime = new \DateTime($this->getStartTime());

        return $startTime->getTimestamp() > $time->getTimestamp();
    }

    public static function cleanBroadcasts($broadcasts) {
        $filtered = array();

        foreach ($broadcasts as $current) {
            if (count($filtered) === 0) {
                $filtered[] = $current;
                continue;
            }
            $previous = end($filtered);

            $currentStart = $current->getStartTime();
            $previousEnd = $previous->getEndTime();

            $previousEndDate = new \DateTime($previousEnd);
            $currentStartDate = new \DateTime($currentStart);

            // All is good
            if ($currentStart === $previousEnd) {
                $filtered[] = $current;
                continue;
            }

            // Gap
            if ($currentStartDate > $previousEndDate) {
                $filtered[] = self::getEmptyBroadcast($previousEnd, $currentStart);
                $filtered[] = $current;
                continue;
            }
            // Overlap
            if ($currentStartDate < $previousEndDate) {
                continue;
            }

        }
        return $filtered;
    }

    public static function getEmptyBroadcast($startDate, $endDate) {
        return new Broadcast(
            (object) array(
                'id' => null,
                'type' => 'broadcast',
                'start_time' => $startDate,
                'end_time' => $endDate,
                'scheduled_start' => $startDate,
                'scheduled_end' => $endDate
            )
        );
    }
}
