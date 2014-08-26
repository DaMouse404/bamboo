<?php

namespace Bamboo\Tests\Feeds;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Feeds\Programmes;

class ProgrammesTest extends BambooTestCase
{
    private $_programmes = array();

    public function setup() {
        parent::setupRequest("programmes@programmes_proms");
        $feedObject = new Programmes(array(), array('gggggggg'));
        $this->_programmes = $feedObject->getElements();
    }

    public function testProgrammeItemType() {
        $this->assertEquals(
                get_class($this->_programmes[0]),
                "Bamboo\Models\Programme"
        );
    }

    // TODO: Fixturator trimTo
    //public function testProgrammeItemCount() {
    //    $this->assertCount(3, $this->_programmes);
    //}

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