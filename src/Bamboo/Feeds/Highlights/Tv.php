<?php

namespace Bamboo\Feeds\Highlights;

use Bamboo\Feeds;

class Tv extends Feeds\Base
{

    protected $_feed = 'tv/highlights';
    protected $_response;

    public function getElements() {
        return $this->_buildModels($this->_response->tv_highlights->elements);
    }

}
