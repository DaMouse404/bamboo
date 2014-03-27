<?php

namespace Bamboo\Models;

class Version extends Base
{
    // Standard version properties
    public $kind = "";
    public $availability;
    public $type = "";
    public $hd = false;
    public $download = false;
    public $duration;
    public $rrc;
    public $guidance;
    // @codingStandardsIgnoreStart
    public $credits_timestamp = "";
    public $first_broadcast = '';
    // @codingStandardsIgnoreEnd
    public $events;

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
     * Get the availability for this version
     *
     * @param string $type start, end or remaining
     * @return string
     */
    public function getAvailability($type = 'end') {
        if (isset($this->availability->$type)) {
            return $this->availability->$type;
        }
        return "";
    }

    /**
     * Get onward journey time
     *
     * @return string
     */
    public function getOnwardJourneyTime() {
        foreach ($this->events as $event) {
            if ($event->kind == 'onward_journey') {
                // @codingStandardsIgnoreStart
                return $event->time_offset_seconds;
                // @codingStandardsIgnoreEnd
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
        if (isset($this->availability->remaining)) {
            return $this->availability->remaining->text;
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
        if (isset($this->duration->text)) {
            return $this->duration->text;
        }
        return "";
    }

    /**
     * Get the version kind
     *
     * @return string
     */
    public function getKind() {
        return $this->kind;
    }

    /**
     * Get the version RRC
     *
     * @return stdClass
     */
    public function getRRC() {
        return $this->rrc;
    }

    /**
     * Get the version RRC short description
     *
     * @return string
     */
    public function getRRCShort() {
        if (isset($this->rrc->description) && isset($this->rrc->description->small)) {
            return $this->rrc->description->small;
        }

        return '';
    }

    /**
     * Get the version RRC long description
     *
     * @return string
     */
    public function getRRCLong() {
        if (isset($this->rrc->description) && isset($this->rrc->description->large)) {
            return $this->rrc->description->large;
        }

        return '';
    }

    /**
     * Get the version RRC URL
     *
     * @return string
     */
    public function getRRCURL() {
        if (isset($this->rrc->url)) {
            return $this->rrc->url;
        }

        return '';
    }

    /**
     * Get the version guidance object
     *
     * @return stdClass
     */
    public function getGuidanceObj() {
        return $this->guidance;
    }

    /**
     * Get the first broadcast date
     * NOTE: This is different to the episode release date!
     *
     * @return string
     */
    public function getFirstBroadcast() {
        // @codingStandardsIgnoreStart
        return $this->first_broadcast;
        // @codingStandardsIgnoreEnd
    }

    public function hasFutureFirstBroadcast() {
        return $this->isFutureDate($this->getFirstBroadcast());
    }

    /**
     * Get the small guidance message (if available)
     *
     * @return string
     */
    public function getSmallGuidance() {
        if (isset($this->guidance->text) && isset($this->guidance->text->small)) {
            return $this->guidance->text->small;
        }
        return '';
    }

    /**
     * Get the medium guidance message (if available)
     *
     * @return string
     */
    public function getMediumGuidance() {
        if (isset($this->guidance->text) && isset($this->guidance->text->medium)) {
            return $this->guidance->text->medium;
        }
        return '';
    }

    /**
     * Get the large guidance message (if available)
     *
     * @return string
     */
    public function getLargeGuidance() {
        if (isset($this->guidance->text) && isset($this->guidance->text->large)) {
            return $this->guidance->text->large;
        }
        return '';
    }

    /**
     * Get the version guidance ID
     *
     * @return string
     */
    public function getGuidanceID() {
        if (isset($this->guidance->id)) {
            return $this->guidance->id;
        }

        return '';
    }

    /**
     * Is the version downloadable
     *
     * @return bool
     */
    public function isDownload() {
        return !!$this->download;
    }

    /**
     * Get the version HD
     *
     * @return bool
     */
    public function isHD() {
        return !!$this->hd;
    }

    /**
     * Get the slug of the version.
     * This can be used in URLs for episode playback
     *
     * @return string
     */
    public function getSlug() {
        switch ($this->kind) {
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
