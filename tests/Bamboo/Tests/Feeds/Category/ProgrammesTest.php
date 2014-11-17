<?php

namespace Bamboo\Tests\Feeds\Programmes;

use Bamboo\Tests\BambooClientTestCase;
use Bamboo\Feeds\Category\Programmes;

class ProgrammesTest extends BambooClientTestCase
{

  private $_feedObject;

  public function setUp() {
    parent::setUp();

    \Bamboo\Configuration::addFakeRequest('programmes', 'category_programmes');
    $this->_feedObject = new Programmes(array(), 'arts');
  }

  public function testGetElements() {
    $this->assertInternalType('array', $this->_feedObject->getElements());
  }

  public function testFeedItemType() {
    $elements = $this->_feedObject->getElements();
    $this->assertInstanceOf(
        "Bamboo\Models\Programme",
        $elements[0]
    );
  }

  public function testGetCategory() {
    $this->assertInstanceOf(
        "Bamboo\Models\Category",
        $this->_feedObject->getCategory()
    );
  }

  public function testGetTotalCount() {
    $this->assertEquals(
        $this->_feedObject->getTotalCount(),
        20
    );
  }

}
