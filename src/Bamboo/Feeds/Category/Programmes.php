<?php

namespace Bamboo\Feeds\Category;

use Bamboo\Feeds\Base;
use Bamboo\Models;
use Bamboo\Models\Episode;

class Programmes extends Base
{

    protected $_feed = 'categories/{id}/programmes';
    protected $_response;

    public function __construct($params, $id) {
        $this->_setId($id);
        parent::__construct($params);
    }

    private function _setId($id) {
        $this->_feed = str_replace("{id}", $id, $this->_feed); 
    }

    public function getElements() {
        return $this->_buildModels($this->_response->category_programmes->elements);
    }

    public function getCategory() {
        $category = $this->_response->category_programmes->category;

        return new Models\Category($category);
    }

    public function getTotalCount() {
        return $this->_response->category_programmes->count;
    }

}