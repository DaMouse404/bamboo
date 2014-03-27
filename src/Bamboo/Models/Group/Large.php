<?php

namespace Bamboo\Models\Group;

use Bamboo\Models\Elements;
use Bamboo\Models\Broadcast;
use Bamboo\Models\Episode;

class Large extends Elements {
  
    public $initial_children;
    public $pisodes;
    public $stacked;
    public $count = 0;

    /**
     * @todo Not sure this is relevant any longer
     */
    public function getEpisodes() {
        // @codingStandardsIgnoreStart
        $episodes = array();
        foreach ($this->initial_children as $episode) {
            if ($episode->type === 'broadcast') {
                $object = new Broadcast($episode);
            } else {
                $object = new Episode($episode);
            }
            $episodes[] = $object;
        }
        return $episodes;
        // @codingStandardsIgnoreEnd
    }

    /**
     * Get the number of episodes within this group object
     */
    public function getEpisodeCount() {
        return count($this->getEpisodes());
    }

    /**
     * Get the total number of episodes in this group
     */
    public function getTotalEpisodeCount() {
        // @codingStandardsIgnoreStart
        return $this->count;
        // @codingStandardsIgnoreEnd
    }

    /**
     * Is the group stacked? (All episodes share programme)
     *
     * @return boolean
     */
    public function isStacked() {
        return !!$this->stacked;
    }
}