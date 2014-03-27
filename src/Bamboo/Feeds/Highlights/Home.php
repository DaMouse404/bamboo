<?php

namespace Bamboo\Feeds\Highlights;

use Bamboo\Feeds\Base;
use Bamboo\Models\Episode;

class Home extends Base {

    protected $_feed = 'home/highlights';
    protected $_response;

    public function getElements() {
    	$elements = array();
    	foreach ($this->_response->home_highlights->elements as $item) {
    		$className = $this->_className($item->type);
    		$elements[] = new $className($item);
    	}
    	return $elements;
    }
}