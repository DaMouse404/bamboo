<?php

namespace Bamboo\Feeds;

use Bamboo\Models\Category;

class Categories extends Base
{

    protected $_feed = 'categories';
    protected $_response;

    /*
     * Return array of Channel models
     */
    public function getCategories() {
        $categories = array();
        $index = array();
        $staticData = $this->_getStaticData();
        $relationships = $staticData->relationships;
        foreach (array_merge(
            $this->_response->categories,
            $staticData->categories
        ) as $category) {
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

    private function _getStaticData($feed) {
        $filePath = dirname(__FILE__) . '/../Assets/categories.json';
        if ( !file_exists($filePath) ) {
            throw new NotFound('Could not find file in /Assets/ for feed: '. $feed);
        }
        return json_decode(file_get_contents($filePath));
    }

}
