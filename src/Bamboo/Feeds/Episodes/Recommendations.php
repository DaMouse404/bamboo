<?php

namespace Bamboo\Feeds\Episodes;

use Bamboo\Feeds\Base;
use Bamboo\Models\Episode;
use Bamboo\Models\Group;

class Recommendations extends Base
{

    protected $_feed = 'episodes/{pid}/recommendations';
    protected $_response;

    public function __construct($params, $id) {
        $this->_setPid($id);
        parent::__construct($params);
    }

    private function _setPid($pid) {
        $this->_feed = str_replace("{pid}", $pid, $this->_feed); 
    }

    public function getElements() {
        $elements = array();

        foreach ($this->_response->episode_recommendations->elements as $item) {
            $className = $this->_className($item->type);
            $elements[] = new $className($item);
        }
        
        return $elements;
    }


}