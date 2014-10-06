<?php

namespace Bamboo\Models;

class Channel extends Elements
{

    protected $_region = "";

    // @codingStandardsIgnoreStart
    protected $_has_schedule = false;
    // @codingStandardsIgnoreEnd

    public function getUnregionalisedID() {
        if ($this->_id === 'bbc_radio_one') {
            return $this->_id;
        }
        if (preg_match('/(bbc_[a-z]+)(_.+)/i', $this->_id, $matches)) {
            return $matches[1];
        }
        return $this->_id;
    }

    public function getSlug() {
        // This filters out the 24 in bbc_news_24 as well as _'s
        return preg_replace('/[24_]/', '', $this->getUnregionalisedID());
    }

    public function getRegion() {
        return $this->_region;
    }

    /**
     * Returns whether this channel is a children's channel
     * @return bool
     */
    public function isChildrens() {
        return $this->_id === 'cbbc' || $this->_id === 'cbeebies';
    }

    /**
     * Returns whether this channel has a schedule available
     * @return bool
     */
    public function hasSchedule() {
        // @codingStandardsIgnoreStart
        return !!$this->_has_schedule;
        // @codingStandardsIgnoreEnd
    }
}
