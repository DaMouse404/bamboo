<?php

namespace Bamboo\Models;

class Broadcast extends Elements {

    protected $_start_time = "";
    protected $_end_time = "";
    protected $_duration;
    protected $_episode;

    /**
     * Get start time from episode
     * 
     * @return string
     */
    public function getStartTime() {
        return $this->_start_time;
    }

    /**
     * Get end time from episode
     * 
     * @return string
     */
    public function getEndTime() {
        return $this->_end_time;
    }

    /**
     * Get episode inside Broadcast
     * 
     * @return BBC_Service_Bamboo_Models_Episode
     */
    public function getEpisode() {
        $episodeModel = new Episode($this->_episode);
        return $episodeModel;
    }
}