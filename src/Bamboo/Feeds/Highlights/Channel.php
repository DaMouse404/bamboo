<?php

namespace Bamboo\Feeds\Highlights;

use Bamboo\Feeds\Base;
use Bamboo\Models;
use Bamboo\Models\Episode;

class Channel extends Base {

    protected $_feed = 'channels/{id}/highlights';
    protected $_response;

    public function __construct($params, $id) {
        $this->_setId($id);
        parent::__construct($params);
    }

	private function _setId($id) {
		$this->_feed = str_replace("{id}", $id, $this->_feed); 
	}

    public function getElements() {
    	$elements = array();
    	foreach ($this->_response->channel_highlights->elements as $item) {
    		$className = $this->_className($item->type);
    		$elements[] = new $className($item);
    	}
    	return $elements;
    }

    public function getChannel() {
        $channel = $this->_response->channel_highlights->channel;

        return new Models\Channel($channel);
    }


}