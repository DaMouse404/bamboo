<?php

namespace Bamboo\Models\Programme;

use Bamboo\Models\Elements;
use Bamboo\Models\Episode;

class Large extends Elements {
  
     // @codingStandardsIgnoreStart
    protected $initial_children = array();
    protected $count = 0;
    // @codingStandardsIgnoreEnd

    /**
     * Get the number of episodes within this programme object
     *
     * @return int
     */
    public function getEpisodeCount() {
        // @codingStandardsIgnoreStart
        return count($this->getEpisodes());
        // @codingStandardsIgnoreEnd
    }

    /**
     * Get the episodes on this programme
     */
    public function getEpisodes() {
        // @codingStandardsIgnoreStart
        $list = array();
        foreach ($this->initial_children as $item) {
            $list[] = new Episode($item);
        }
        //return $this->initial_children;
        return $list;
        // @codingStandardsIgnoreEnd
    }

    /**
     * Get the total number of episodes available for this programme
     */
    public function getTotalEpisodeCount() {
        // @codingStandardsIgnoreStart
        return $this->count;
        // @codingStandardsIgnoreEnd
    }

    /**
     * Returns true if this programme has an available episode, otherwise false
     */
    public function hasAvailableEpisodes() {
        return count($this->getEpisodes()) > 0;
    }

    /**
     * Get the latest episode
     *
     * @return BBC_Service_Bamboo_Models_Episode
     */
    public function getLatestAvailableEpisode() {
        // @codingStandardsIgnoreStart
        if (isset($this->initial_children[0])) {
            return new Episode($this->initial_children[0]);
        }
        return "";
        // @codingStandardsIgnoreEnd
    }
}