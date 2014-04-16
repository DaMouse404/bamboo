<?php

namespace Bamboo\Feeds\Groups;

use Bamboo\Feeds\Base;
use Bamboo\Models\Episode;
use Bamboo\Models\Group;

class Episodes extends Base
{

    protected $_feed = 'groups/{id}/episodes';
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

        foreach ($this->_response->group_episodes->elements as $item) {
            $className = $this->_className($item->type);
            $elements[] = new $className($item);
        }
        
        return $elements;
    }

    public function getTotalCount() {
        return $this->_response->group_episodes->count;
    }

    public function getGroup() {
        $groupObject = $this->_response->group_episodes->group;
        
        return new Group($groupObject);
    }


}