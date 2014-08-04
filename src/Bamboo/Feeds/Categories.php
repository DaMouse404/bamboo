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
        $index = array();
        $relationships = $this->_response->relationships;
        foreach ($this->_response->categories as $category) {
            $category = new Category($category);
            $categories[$category->getId()] = $category;
        }

        foreach ($relationships as $parent => $children) {
            foreach ($children as $child) {
                $categories[$parent]->children[] = $categories[$child];
                $categories[$child]->parent = $categories[$parent];
            }
        }
        return $categories;
    }

}
