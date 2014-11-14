<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooBaseTestCase;
use Bamboo\Models\Promotion;

class PromotionTest extends BambooBaseTestCase
{
    /**
     * This tests checks the generation of the Promotion model
     *
     * @access public
     * @return void
     */
    public function testValues() {
        $promotion = $this->_createPromotion();
        $this->assertEquals("Donate", $promotion->getPromotionLabel());
        $this->assertEquals('Child in Need', $promotion->getTitle());
        $this->assertEquals(
            "Donate to help to change children's lives",
            $promotion->getSubtitle()
        );
        $this->assertEquals(
            "Donate online, via PayPal or find out about other ways to give us your money.",
            $promotion->getDescription()
        );
        $this->assertEquals(
            'http://www.bbc.co.uk/programmes/b008dk4b/features/cin-donate',
            $promotion->getUrl()
        );
        $this->assertEquals(
            'http://ichef.bbci.co.uk/images/ic/{recipe}/legacy/images/p01hjz4s.jpg',
            $promotion->getStandardImageRecipe('standard')
        );
    }

    public function testEmptyData() {
        $promotion = $this->_createPromotion(
            array(
                'title'    => '',
                'subtitle' => '',
                'synopses' => array(),
                'labels'   => array()
            )
        );
        $this->assertEquals('', $promotion->getTitle());
        $this->assertEquals('', $promotion->getSubtitle());
        $this->assertEquals('', $promotion->getDescription());
        $this->assertEquals('', $promotion->getPromotionLabel());
    }


    private function _createPromotion($params = array()) {
        $promotion = array(
            "id" => "b036tchs",
            "type" => "promotion",
            "title" => "Child in Need",
            "subtitle" => "Donate to help to change children's lives",
            "synopses" => (object) array(
                "small" => "Donate online, via PayPal or find out about other ways to give us your money."
            ),
            "url" => "http://www.bbc.co.uk/programmes/b008dk4b/features/cin-donate",
            "images" => (object) array(
                "standard" => "http://ichef.bbci.co.uk/images/ic/{recipe}/legacy/images/p01hjz4s.jpg"
            ),
            "labels" => (object) array(
                "promotion" => "Donate"
            )
        );
        return new Promotion((object) array_merge($promotion, $params));
    }
}
