<?php

namespace Bamboo\Feeds\Highlights;

use Bamboo\Feeds;

class Home extends Feeds\Base
{

    protected $_feed = 'home/highlights';
    protected $_response;

    public function getElements() {
        return $this->_buildModels($this->_response->home_highlights->elements);
    }

}
