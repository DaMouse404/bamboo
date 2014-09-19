<?php

namespace Bamboo\Feeds\Highlights;

use Bamboo\Feeds\Highlights\Base;
use Bamboo\Models;

class Category extends Base
{

    protected $_feed = 'categories/{id}/highlights';
    protected $_response;

    public function getElements() {
        return $this->_buildModels($this->_response->category_highlights->elements);
    }

    public function getCategory() {
        $category = $this->_response->category_highlights->category;

        return new Models\Category($category);
    }

}
