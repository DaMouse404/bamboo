<?php

namespace Bamboo\Feeds;

use Bamboo\Models\Category;

class Categories extends StaticBase
{

    protected $_feed = 'categories';
    protected $_response;

    /*
     * Return array of Channel models
     */
    public function getCategories() {
        $categories = array();
        foreach ($this->_response->categories as $category) {
            $categories[] = new Category($category);
        }

        return $categories;
    }

}
