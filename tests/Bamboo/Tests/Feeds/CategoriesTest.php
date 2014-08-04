<?php

namespace Bamboo\Tests\Feeds;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Feeds\Categories;

class CategoriesTest extends BambooTestCase
{
  private $_category;
  private $_categories;

  public function setup() {
    $feedObject = new Categories();
    $this->_categories = $feedObject->getCategories();
  }

  public function testFeedType() {
    $this->assertTrue(is_array($this->_categories));
  }

  public function testFeedSize() {
    $this->assertEquals(
        sizeof($this->_categories),
        32
    );
  }

  public function testFeedItemType() {
    $this->assertInstanceOf(
        "Bamboo\Models\Category",
        array_pop($this->_categories)
    );
  }

  public function testChildrenIsSet () {
    $comedy = false;
    foreach($this->_categories as $cat) {
      if ($cat->getId() == 'comedy') {
        $comedy = $cat;
        break;
      }
    }

    $this->assertEquals(
      3,
      count($comedy->children)
    );

    $this->assertEquals(
      'comedy-sitcoms',
      $comedy->children[0]->getId()
    );
  }

  public function testParentIsSet () {
    $crime = false;
    foreach($this->_categories as $cat) {
      if ($cat->getId() == 'drama-crime') {
        $crime = $cat;
        break;
      }
    }
    $this->assertEquals(
      'drama-and-soaps',
      $crime->parent->getId()
    );
  }

}
