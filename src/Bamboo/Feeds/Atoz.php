<?php

namespace Bamboo\Feeds;

use Bamboo\Models\Programme;

class Atoz extends Base
{

    protected $_feed = 'atoz/{id}/programmes';
    protected $_response;

    public function __construct($params, $id) {
        $this->_setId($id);
        parent::__construct($params);
    }

    private function _setId($id) {
        $this->_feed = str_replace("{id}", $id, $this->_feed); 
    }

    /*
     * Return array of Channel models
     */
    public function getElements() {
        return $this->_buildModels($this->_response->atoz_programmes->elements);
    }

}