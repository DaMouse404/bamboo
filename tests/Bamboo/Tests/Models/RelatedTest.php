<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooBaseTestCase;
use Bamboo\Models\Related;

class RelatedTest extends BambooBaseTestCase
{
    /**
     * This tests checks the generation of the related links model
     *
     * @access public
     * @return void
     */
    public function testValues() {
        $related = $this->_createRelated();
        $this->assertEquals("Live Chat", $related->getTitle());
        $this->assertEquals(
            "with Jo Joyner",
            $related->getDescription()
        );
        $this->assertEquals(
            "http://www.bbc.co.uk/blogs/eastenders/",
            $related->getUrl()
        );
        $this->assertEquals(
            "priority_content",
            $related->getKind()
        );
    }

    public function testEmptyData() {
        $related = $this->_createRelated(array('title' => '', 'synopses' => array()));
        $this->assertEquals('', $related->getTitle());
        $this->assertEquals('', $related->getDescription());
    }

    private function _createRelated($params = array()) {
        $related = array(
            "id" => "p00hhpxh",
            "kind" => "priority_content",
            "synopses" => (object) array(
                "small" => "with Jo Joyner"
            ),
            "title" => "Live Chat",
            "type" => "link",
            "url" => "http://www.bbc.co.uk/blogs/eastenders/"
        );
        return new Related((object) array_merge($related, $params));
    }
}