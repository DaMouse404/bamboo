<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooBaseTestCase;
use Bamboo\Models\Category;

class CategoryTest extends BambooBaseTestCase
{
    public function testCBBCIsChildrens() {
        $category = $this->_createCategory(array('id' => 'cbbc'));
        $this->assertTrue($category->isChildrens());
    }

    public function testCbeebiesIsChildrens() {
        $category = $this->_createCategory(array('id' => 'cbeebies'));
        $this->assertTrue($category->isChildrens());
    }

    public function testComedyIsNotChildrens() {
        $category = $this->_createCategory(array('id' => 'comedy'));
        $this->assertFalse($category->isChildrens());
    }

    public function testChildEpisodeCount() {
        $category = $this->_createCategory(array('child_episode_count' => 28));
        $this->assertEquals($category->getChildEpisodeCount(), 28);

        // Test it returns 0 by default
        $category = $this->_createCategory(array());
        $this->assertEquals($category->getChildEpisodeCount(), 0);
    }

    public function testCategoryKind() {
        $category = $this->_createCategory(array('kind' => 'sub-genre'));
        $this->assertEquals('sub-genre', $category->getKind());

     }

    private function _createCategory($params) {
        return new Category((object) $params);
    }
}
