<?php
namespace Bamboo\Feeds\Highlights;

use Bamboo\Feeds;

class Base extends Feeds\Base
{

    public function __construct($params, $id) {
        $this->_setId($id);
        parent::__construct($params);
    }

    private function _setId($id) {
        $this->_feed = str_replace("{id}", $id, $this->_feed);
    }

}
