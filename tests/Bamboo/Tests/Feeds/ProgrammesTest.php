<?php

namespace Bamboo\Tests\Feeds;

use Bamboo\Tests\BambooClientTestCase;
use Bamboo\Feeds\Programmes;

class ProgrammesTest extends BambooClientTestCase
{
    private $_programmes = array();

    public function setUp() {
        parent::setupRequest("programmes@programmes_proms");
        $this->_programmes = new Programmes(array(), array('gggggggg'));
    }

    public function testProgrammeItemType() {
        $elements = $this->_programmes->getElements();
        $this->assertInstanceOf(
                "Bamboo\Models\Programme",
                $elements[0]
        );
    }

    public function testGetResponse() {
        $this->assertInternalType('object', $this->_programmes->getResponse());
    }

    public function testMultiFeed() {
        $feedObject = new Programmes(array(), array('a', 'b'));

        $this->assertAttributeEquals(
            array(
                'programmes/a',
                'programmes/b'
            ),
            '_feeds',
            $feedObject
        );
    }

    public function testResponsesAreGrouped() {
        $feedObject = new Programmes(array(), array('a'));
        $this->assertCount(2, $feedObject->getElements());

        $feedObject = new Programmes(array(), array('a', 'b'));
        $this->assertCount(4, $feedObject->getElements());

        $feedObject = new Programmes(array(), array('a', 'b', 'c'));
        $this->assertCount(6, $feedObject->getElements());
    }

    public function testMultiFeedMultiPid() {
        $feedObject = new Programmes(
            array(),
            array(
                array('a', 'b'),
                array('c', 'd')
            )
        );

        $this->assertAttributeEquals(
            array(
                'programmes/a,b',
                'programmes/c,d'
            ),
            '_feeds',
            $feedObject
        );
    }

    public function testSingleFeed() {
        $feedObject = new Programmes(array(), array('a'));

        $this->assertAttributeEquals(
            array('programmes/a'),
            '_feeds',
            $feedObject
        );
    }
}
