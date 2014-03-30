<?php

namespace Bamboo\Models;

class Episode extends Elements {

  protected $_subtitle;
  protected $_versions = array();
  protected $_labels;
  protected $_release_date;
  protected $_duration;

  public function getTimelinessLabel() {
      if (isset($this->_labels->time)) {
          return $this->_labels->time;
      }
      return "";
  }


  public function getCompleteTitle() {
      return $this->_title . ($this->_subtitle ? ' - ' . $this->_subtitle : '');
  }

  public function getSlug() {
      // Use title - subtitle and remove leading and trailing whitespace
      $title = trim($this->getCompleteTitle());
      // Replace accented characters with unaccented equivalent
      $title = $this->_unaccent($title);
      // Lowercase the title
      $title = mb_strtolower($title);
      // Remove non-alphanumeric-or-whitespace characters
      $title = preg_replace('/[^\w\s]/', '', $title);
      // Reduce multiple spaces to a single hyphen
      $title = preg_replace('/\s\s*/', '-', $title);
      return $title;
  }

  // Convert accented characters to their 'normal' alternative
  private function _unaccent($string) {
      //If locale is "0", the current setting is returned.
      $oldLocale = setlocale(LC_ALL, 0);
      setlocale(LC_ALL, 'en_GB');
      $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
      setlocale(LC_ALL, $oldLocale);
      return $string;
  }


    /**
     * Gets the priority version and returns its slug. If no version exists it returns an empty string. An optional
     * preference can be specified in which case the version of that kind will be returned if it exists, else the
     * version with highest priority is returned.
     *
     * @param string $preference a specific version to return
     *
     * @return string
     */
    public function getPriorityVersionSlug($preference = null) {
        $version = $this->getPriorityVersion($preference);

        if ($version) {
          //var_dump($version);die
            return $this->getVersionSlug($version);
            //return $version->getSlug();
        }
        return "";
    }

    /**
     * Get the slug of the version.
     * This can be used in URLs for episode playback
     *
     * @return string
     */
    public function getVersionSlug($version) {
        switch ($version->getKind()) {
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

      /**
     * Gets the version with highest priority attached to the episode. A preference can be provided to override the
     * default. If the preference is not found then the default will be returned instead.
     *
     * @param string $preference a specific version to look for
     *
     * @return string
     */
    public function getPriorityVersion($preference = null) {
        if (isset($this->_versions[0])) {
            $result = new Version($this->_versions[0]);
            if ($preference) {
                foreach ($this->_versions as $version) {
                    if ($version->kind === $preference) {
                        $result = new Version($version);
                    }
                }
            }
            return $result;
        }
        return "";
    }

    public function getSubtitle() {
      return $this->_subtitle;
    }


    /**
     * Get the editorial label
     *
     * @return string
     */
    public function getEditorialLabel() {
        if (isset($this->_labels->editorial)) {
            return $this->_labels->editorial;
        }
        return "";
    }

    /**
     * Returns true if a date follows a specific format including the time in a 12-hour format
     * Will return true for 8pm 23 Feb 2010
     * And false for 23 Feb 2010
     *
     * @param string $date
     * @return bool
     */
    private function _hasTimeInDate($date) {
        preg_match("/^[0-9]{1,2}(pm|am) [0-9]{1,2} [A-Z]{1}[a-z]{2} [0-9]{4}$/", $date, $matches);
        return count($matches)>0;
    }

    /**
     * Has the episode a release date set in the future?
     *
     * @return bool
     */
    public function hasFutureReleaseDate() {
        return $this->isFutureDate($this->getReleaseDate());
    }

    public function isFutureDate($date) {
        //If there's only years, make it New Year
        if (is_numeric($date)) {
            $date = "1 Jan ".$date;
        }

        //If date has time we have to take the current time when comparing.
        if ($this->_hasTimeInDate($date)) {
            $now = mktime();
        } else {
            //If not, compare with right before midnight
            $now = mktime(23, 59, 59, date('m'), date('d'), date('y'));
        }

        $releaseTime = strtotime($date);
        if ($releaseTime === false) {
            return false;
        }
        return $now < $releaseTime;
    }

    /**
     * Get the duration of the highest priority version. If the episode has no version return an empty string.
     *
     * @return string
     */
    public function getDuration() {
        $version = $this->getPriorityVersion();
        if ($version) {
            return $this->getPriorityDuration($version);
        }
        return '';
    }

    /**
     * Get the release date
     *
     * @return string
     */
    public function getReleaseDate() {
        // @codingStandardsIgnoreStart
        return $this->_release_date;
        // @codingStandardsIgnoreEnd
    }

    public function getPriorityDuration($version) {
        if (isset($this->_duration->text)) {
            return $this->_duration->text;
        }
        return "";
    }

    /**
     * Checks if we need to show version flags
     *
     * @return bool
     */
    public function showFlags()
    {
        // if the episode has any versions at all
        if (isset($this->_versions[0])) {
            $version = new Version($this->_versions[0]);
            // we won't need version flags in this scenario: there's only the original version and has no HD
            if ((count($this->_versions) === 1) &&
                ($version->getKind() === 'original') &&
                !($version->isHD())) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Checks if HD exists on any version
     *
     * @return bool
     */
    public function hasHD()
    {
        if (isset($this->_versions[0])) {
            foreach ($this->_versions as $version) {
                $version = new Version($version);
                if ($version->isHD()) {
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    /**
     * Get the versions attached to this episode. Returns an array of {@link BBC_Service_Bamboo_Models_Version} objects
     *
     * @return array
     */
    public function getVersions() {
        $versions = array();
        foreach ($this->_versions as $version) {
          $versions[] = new Version($version);
        }
        return $versions;
    }

}