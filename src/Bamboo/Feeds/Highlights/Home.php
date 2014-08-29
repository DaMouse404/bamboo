<?php

namespace Bamboo\Feeds\Highlights;

use Bamboo\Feeds\Base;
use Bamboo\Models\Episode;

class Home extends Base
{

    protected $_feed = 'home/highlights';
    protected $_response;

    public function getElements() {
        return $this->_buildModels($this->_response->home_highlights->elements);
    }
}