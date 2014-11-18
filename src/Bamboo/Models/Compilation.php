<?php

namespace Bamboo\Models;

class Compilation extends Elements
{
    protected $_title = "";
    protected $_synopses;
    protected $_images;

    public function getShortSynopsis() {
        return $this->_synopses->small;
    }

    public function getMediumSynopsis() {
        return $this->_synopses->medium;
    }

    public function getLongSynopsis() {
        return $this->_synopses->long;
    }

    public function getHeroImage($width = 336, $height = 189) {
        return $this->getImage('hero', $width, $height);
    }
}
