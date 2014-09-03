<?php

namespace Bamboo\Models;

class Version extends Base
{
    // Standard version properties
    protected $_kind = "";
    protected $_availability;
    protected $_type = "";
    protected $_hd = false;
    protected $_download = false;
    protected $_duration;
    protected $_rrc;
    protected $_guidance;
    // @codingStandardsIgnoreStart
    protected $_credits_timestamp = "";
    protected $_first_broadcast = '';
    // @codingStandardsIgnoreEnd
    protected $_events;

    /**
     * Returns the 2 letter abbreviation used for a version
     * @return string the abbreviation (SD, HD, AD or SL)
     */
    public function getAbbreviation() {
        $abbr = '';
        switch ($this->getKind()) {
            case 'audio-described':
                $abbr = 'AD';
                break;
            case 'signed':
                $abbr = 'SL';
                break;
            default:
                $abbr = $this->isHD() ? 'HD' : 'SD';
                break;
        }

        return $abbr;
    }

    /**
     * Get total days availability for this Version
     *
     * @return integer
     */
    public function getTotalDaysAvailable() {
        $start = $this->getAvailability('start');
        $end = $this->getAvailability('end');

        if ( $start && $end ) {
            $start = strtotime($start);
            $end = strtotime($end);

            return ceil(($end - $start) / (24 * 60 * 60));
        }

        return 0;
    }

    /**
     * Get remaining availability for this Version
     *
     * @return integer
     */
    public function getRemainingDaysAvailable() {
        $end = $this->getAvailability('end');

        if ( $end ) {
            $end = strtotime($end);
            return ceil(($end - time()) / (24 * 60 * 60));
        }

        return 0;
    }

    /**
     * Get availability period of for this Version in days
     *
     * i.e. If it's 30 days available and there are 25 remaining
     * then we're on the 6th day of our availability as 5 have already
     * elapsed and thus the +1 is there to ensure we're on the next day slot.
     *
     * @return integer
     */
    public function getAvailabilityDay() {
        $start = $this->getAvailability('start');

        if ( $start ) {
            $start = strtotime($start);

            return ceil((time() - $start) / (24 * 60 * 60));
        }

        return 0;
    }


    /**
     * Get the availability for this version
     *
     * @param string $type start, end or remaining
     * @return string
     */
    public function getAvailability($type = 'end') {
        if (isset($this->_availability->$type)) {
            return $this->_availability->$type;
        }
        return "";
    }

    /**
     * Get onward journey time
     *
     * @return string
     */
    public function getOnwardJourneyTime() {
        if ($this->_events) {
            foreach ($this->_events as $event) {
                if ($event->kind == 'onward_journey') {
                    // @codingStandardsIgnoreStart
                    return $event->time_offset_seconds;
                    // @codingStandardsIgnoreEnd
                }
            }
        }
        return "";
    }
    /**
     * getRemainingAvailability
     *
     * @access public
     * @return void
     */
    public function getRemainingAvailability()
    {
        if (isset($this->_availability->remaining)) {
            return $this->_availability->remaining->text;
        }
        return "";
    }

    /**
     * Get the version duration
     *
     * @return string
     */
    public function getDuration()
    {
        if (isset($this->_duration->text)) {
            return $this->_duration->text;
        }
        return "";
    }

    /**
     * Get the version duration in seconds
     * by parsing the duration code
     *
     * @return int
     */
    public function getDurationInSecs()
    {
        $secs = 0;
        if (isset($this->_duration->value)) {
            $date = new \DateInterval($this->_duration->value);
            $dt = (new \DateTime());
            $secs = $dt->setTimeStamp(0)->add($date)->getTimestamp();
        }
        return $secs;
    }

    /**
     * Get the version duration in minutes
     * by parsing the duration code
     *
     * @return int
     */
    public function getDurationInMins()
    {
        $secs = $this->getDurationInSecs();
        $mins = $secs / 60;
        return ceil($mins);
    }

    /**
     * Get the version kind
     *
     * @return string
     */
    public function getKind() {
        return $this->_kind;
    }

    /**
     * Get the version RRC
     *
     * @return stdClass
     */
    public function getRRC() {
        return $this->_rrc;
    }

    /**
     * Get the version RRC short description
     *
     * @return string
     */
    public function getRRCShort() {
        if (isset($this->_rrc->description) && isset($this->_rrc->description->small)) {
            return $this->_rrc->description->small;
        }

        return '';
    }

    /**
     * Get the version RRC long description
     *
     * @return string
     */
    public function getRRCLong() {
        if (isset($this->_rrc->description) && isset($this->_rrc->description->large)) {
            return $this->_rrc->description->large;
        }

        return '';
    }

    /**
     * Get the version RRC URL
     *
     * @return string
     */
    public function getRRCURL() {
        if (isset($this->_rrc->url)) {
            return $this->_rrc->url;
        }

        return '';
    }

    /**
     * Get the version guidance object
     *
     * @return stdClass
     */
    public function getGuidanceObj() {
        return $this->_guidance;
    }

    /**
     * Get the first broadcast date
     * NOTE: This is different to the episode release date!
     *
     * @return string
     */
    public function getFirstBroadcast() {
        // @codingStandardsIgnoreStart
        return $this->_first_broadcast;
        // @codingStandardsIgnoreEnd
    }

    /**
     * Get the small guidance message (if available)
     *
     * @return string
     */
    public function getSmallGuidance() {
        if (isset($this->_guidance->text) && isset($this->_guidance->text->small)) {
            return $this->_guidance->text->small;
        }
        return '';
    }

    /**
     * Get the medium guidance message (if available)
     *
     * @return string
     */
    public function getMediumGuidance() {
        if (isset($this->_guidance->text) && isset($this->_guidance->text->medium)) {
            return $this->_guidance->text->medium;
        }
        return '';
    }

    /**
     * Get the large guidance message (if available)
     *
     * @return string
     */
    public function getLargeGuidance() {
        if (isset($this->_guidance->text) && isset($this->_guidance->text->large)) {
            return $this->_guidance->text->large;
        }
        return '';
    }

    /**
     * Get the version guidance ID
     *
     * @return string
     */
    public function getGuidanceID() {
        if (isset($this->_guidance->id)) {
            return $this->_guidance->id;
        }

        return '';
    }

    /**
     * Is the version downloadable
     *
     * @return bool
     */
    public function isDownload() {
        return !!$this->_download;
    }

    /**
     * Get the version HD
     *
     * @return bool
     */
    public function isHD() {
        return !!$this->_hd;
    }

    /**
     * Get the slug of the version.
     * This can be used in URLs for episode playback
     *
     * @return string
     */
    public function getSlug() {
        switch ($this->_kind) {
            case 'signed':
                $slug = 'sign';
                break;
            case 'audio-described':
                $slug = 'ad';
                break;
            default:
                $slug = '';
        }
        return $slug;
    }
}
