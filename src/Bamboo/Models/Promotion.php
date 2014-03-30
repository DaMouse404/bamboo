<?php

namespace Bamboo\Models;

class Promotion extends Elements {

    protected $url = "";
    protected $subtitle = "";
    protected $labels;


    /**
     * Returns the subtitle of the promotion, which is stored in the synopses
     *
     * @access public
     * @return string
     */
    public function getSubtitle() {
        return $this->_subtitle;
    }

    /**
     * Returns the description of the promotion, which is stored in the synopses
     *
     * @access public
     * @return string
     */
    public function getDescription() {
        if (isset($this->_synopses->small)) {
            return $this->_synopses->small;
        }
        return "";
    }

    /**
     * Returns the editorial label of the promotion, which is stored in the title
     *
     * @access public
     * @return string
     */
    public function getPromotionLabel() {
        if (isset($this->_labels->promotion)) {
            return $this->_labels->promotion;
        }
        return "";
    }

    /**
     * Get the Promotion URL
     * Even though the underlying field is URL, other models use getHref.
     *
     * @return string
     */
    public function getUrl() {
        return $this->_url;
    }

}