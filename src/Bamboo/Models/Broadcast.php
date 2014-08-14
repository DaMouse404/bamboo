<?php

namespace Bamboo\Models;

class Broadcast extends Elements
{
    // @codingStandardsIgnoreStart
    protected $_start_time = "";
    protected $_end_time = "";
    // @codingStandardsIgnoreEnd
    protected $_duration;
    protected $_episode;
    protected $_blanked;

    /**
     * Get start time from episode
     * 
     * @return string
     */
    public function getStartTime() {
        // @codingStandardsIgnoreStart
        return $this->_start_time;
        // @codingStandardsIgnoreEnd
    }

    /**
     * Get end time from episode
     * 
     * @return string
     */
    public function getEndTime() {
        // @codingStandardsIgnoreStart
        return $this->_end_time;
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
}
