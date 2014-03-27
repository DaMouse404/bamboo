<?php

namespace Bamboo\Feeds\Highlights;

use Bamboo\Feeds\Base;
use Bamboo\Models;
use Bamboo\Models\Episode;

class Category extends Base {

    protected $_feed = 'categories/{id}/highlights';
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
    	foreach ($this->_response->category_highlights->elements as $item) {
    		$className = $this->_className($item->type);
    		$elements[] = new $className($item);
    	}
    	return $elements;
    }

    public function getCategory() {
        $category = $this->_response->category_highlights->category;

        return new Models\Category($category);
    }


}