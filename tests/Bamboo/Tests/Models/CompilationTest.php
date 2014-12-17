<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooBaseTestCase;
use Bamboo\Models;

class CompilationTest extends BambooBaseTestCase
{
    public function testGetTitle() {
        $compilation = $this->_createCompilation();
        $this->assertEquals('Compilation of cakes', $compilation->getTitle());
    }

    public function testSynopsis() {
        $compilation = $this->_createCompilation();
        $this->assertEquals('short description of cakes', $compilation->getShortSynopsis());
        $this->assertEquals('medium description of cakes', $compilation->getMediumSynopsis());
        $this->assertEquals('long description of cakes', $compilation->getLongSynopsis());
    }

    public function testImage() {
        $compilation = $this->_createCompilation();
        $this->assertEquals('cake_image', $compilation->getHeroImage());
    }

    public function testNoImage () {

        $compilation = new Models\Compilation((object) array());
        $this->assertFalse($compilation->getHeroImage());

        $compilation = new Models\Compilation(
            (object) array(
                'images' => (object) array()
            )
        );
        $this->assertFalse($compilation->getHeroImage());

        $compilation = new Models\Compilation(
            (object) array(
                'images' => (object) array (
                    'hero' => ''
                )
            )
        );
        $this->assertFalse($compilation->getHeroImage());
    }

    private function _createCompilation() {
        $compilation = (object) array (
            "title" => "Compilation of cakes",
            "synopses" => (object) array (
                "small" => "short description of cakes",
                "medium" => "medium description of cakes",
                "long" => "long description of cakes"
            ),
            "images" => (object) array("hero" => "cake_image"),
        );

        return new Models\Compilation($compilation);
    }
}
