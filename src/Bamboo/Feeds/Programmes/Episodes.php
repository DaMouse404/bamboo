<?php

namespace Bamboo\Feeds\Programmes;

use Bamboo\Feeds\Base;
use Bamboo\Models\Episode;
use Bamboo\Models\Group;

class Episodes extends Base
{

    protected $_feed = 'programmes/{id}/episodes';
    protected $_response;

    public function __construct($params, $id) {
        $this->_setId($id);
        parent::__construct($params);
    }

    private function _setId($id) {
        $this->_feed = str_replace("{id}", $id, $this->_feed); 
    }

    public function getEpisodes() {
        $elements = array();

        foreach ($this->_response->programme_episodes->elements as $item) {
            $className = $this->_className($item->type);
            $elements[] = new $className($item);
        }
        
        return $elements;
    }

    public function getResponse() {
        return $this->_response->programme_episodes->elements;
    }

    public function getCount() {
        return $this->_response->programme_episodes->count;
    }
}