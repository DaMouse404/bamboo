<?php

namespace Bamboo\Tests\Feeds\Highlights;

use Bamboo\Tests\BambooClientTestCase;
use Bamboo\Feeds\Highlights\Category;

class CategoryTest extends BambooClientTestCase
{

  private $_elements;
  private $_category;

  public function setUp() {
    parent::setupRequest("highlights@category_highlights");
    $feedObject = new Category(array(), 'arts');
    $this->_elements = $feedObject->getElements();
    $this->_category = $feedObject->getCategory();
  }

  public function testGetElements() {
    $this->assertInternalType('array', $this->_elements);
  }

  public function testFeedItemType() {
    $this->assertInstanceOf(
        "Bamboo\Models\Episode",
        $this->_elements[0]
    );
  }

  public function testGetCategory() {
    $this->assertInstanceOf(
        "Bamboo\Models\Category",
        $this->_category
    );
  }

}
