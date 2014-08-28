<?php

namespace Bamboo\Models;

class Broadcast extends Elements
{
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
     * Get end time from episode
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
     * Is broadcast a repetition
     *
     * @return boolean
     */
    public function isRepeat() {
        return !!$this->_repeat;
    }
}
