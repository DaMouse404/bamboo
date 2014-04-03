<?php

namespace Bamboo\Tests\Feeds;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Feeds\Categories;

class CategoriesTest extends BambooTestCase
{
  private $_category;
  private $_categories;

  public function setup() {
    parent::setupRequest("categories");
    $feedObject = new Categories();
    $this->_categories = $feedObject->getCategories();
  }

  public function testFeedType() {
    $this->assertTrue(is_array($this->_categories));
  }

  public function testFeedSize() {
    $this->assertEquals(
        sizeof($this->_categories), 
        19
    );
  }

  public function testFeedItemType() {
    $this->assertEquals(
        get_class($this->_categories[0]), 
        "Bamboo\Models\Category"
    );
  }

}