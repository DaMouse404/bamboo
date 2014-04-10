<?php

namespace Bamboo\Models\Programme;

use Bamboo\Models\Elements;
use Bamboo\Models\Episode;

class Large extends Elements
{
  
     // @codingStandardsIgnoreStart
    protected $_initial_children = array();
    protected $_count = 0;
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
        foreach ($this->_initial_children as $item) {
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
        return $this->_count;
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
     * @return Bamboo\Models\Episode
     */
    public function getLatestAvailableEpisode() {
        // @codingStandardsIgnoreStart
        if (isset($this->_initial_children[0])) {
            return new Episode($this->_initial_children[0]);
        }
        return "";
        // @codingStandardsIgnoreEnd
    }
}