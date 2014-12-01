<?php

namespace Bamboo\Models;

class Group extends Elements
{
    protected $_subtitle = "";
    // @codingStandardsIgnoreStart
    protected $_initial_children = array();
    protected $_count = 0;
    protected $_labels = "";
    protected $_related_links = array();
    protected $_stacked = "";
    // @codingStandardsIgnoreEnd
    private $_broadcastType = 'broadcast';

   /**
     * Returns the related links
     *
     * @return array
     */
    public function getRelatedLinks() {
        $relatedLinksArray = array();
        // @codingStandardsIgnoreStart
        $relatedLinks = $this->_related_links;
        // @codingStandardsIgnoreEnd
        foreach ($relatedLinks as $relatedLink) {
            $relatedLinksArray[] = new Related($relatedLink);
        }
        return $relatedLinksArray;
    }

   /**
     * Returns the first related link of the
     * type specified
     *
     * @return array
     */
    public function getRelatedLinksByKind($kind) {
        $relatedLinksArray = array();
        foreach ($this->getRelatedLinks() as $link) {
            if ($link->getKind() === $kind) {
                $relatedLinksArray[] = $link;
            }
        }
        return $relatedLinksArray;
    }

    /**
     * Returns the subtitle of the episode
     *
     * @return string
     */
    public function getSubtitle() {
        return $this->_subtitle;
    }

    /**
     * @todo Not sure this is relevant any longer
     */
    public function getEpisodes() {
        // @codingStandardsIgnoreStart
        $episodes = array();
        foreach ($this->_initial_children as $episode) {
            if ($episode->type === $this->_broadcastType) {
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
        return $this->_count;
        // @codingStandardsIgnoreEnd
    }

    /**
     * Is the group stacked? (All episodes share programme)
     *
     * @return boolean
     */
    public function isStacked() {
        return !!$this->_stacked;
    }

    /**
     * Get the editorial label
     */
    public function getEditorialLabel() {
        if (isset($this->_labels->editorial)) {
            return $this->_labels->editorial;
        }
        return "";
    }

    /**
     * @todo Not sure this is relevant any longer
     */
    public function getLabels() {
        return $this->_labels;
    }

    /**
     * Get the iStats type of the group
     */
    public function getIstatsType() {
        if ($this->getId() === 'popular') {
            $type = 'most-popular';
        } elseif ($this->isStacked()) {
            $type = 'series-catchup';
        } else {
            $type = 'editorial';
        }
        return $type;
    }
}
