<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Models\Episode;
use Bamboo\Models\Elements;

class ElementsTest extends BambooTestCase
{
    /*
     * Using an Element Mock
     */
    public function testGetType() {
        $params = array('type' => 'episode_large');
        $element = $this->_createElement($params);

        $this->assertEquals($element->getType(), 'episode_large');
    }


    public function testGetSynopsis() {
        $element = $this->_createElement(array());
        $this->assertEquals('', $element->getShortSynopsis());
        $this->assertEquals('', $element->getMediumSynopsis());
        $this->assertEquals('', $element->getLargeSynopsis());

        $params = array(
            'synopses' => (object) array(
                'small' => 'Luther investigates',
                'medium' => 'Luther investigates two horrific cases',
                'large' =>
                    'Luther investigates two horrific cases, unaware his every step is under scrutiny.'
            )
        );
        $element = $this->_createElement($params);

        $this->assertEquals(
            $element->getShortSynopsis(),
            'Luther investigates'
        );
        $this->assertEquals(
            $element->getMediumSynopsis(),
            'Luther investigates two horrific cases'
        );
        $this->assertEquals(
            $element->getLargeSynopsis(),
            'Luther investigates two horrific cases, unaware his every step is under scrutiny.'
        );
    }

    public function testGetMasterBrand() {
        $element = $this->_createElement(array());
        $this->assertEquals($element->getMasterBrand(), '');
        $this->assertEquals($element->getMediumMasterBrand(), '');

        $params =  array('master_brand' => (object) array('titles' => (object) array()));
        $element = $this->_createElement($params);
        $this->assertEquals($element->getMasterBrand(), '');
        $this->assertEquals($element->getMediumMasterBrand(), '');

        $params =  array('master_brand' => (object) array('titles' => (object) array('small' => 'BBC Two', 'medium' => 'BBC Two England')));
        $element = $this->_createElement($params);

        $this->assertEquals($element->getMasterBrand(), 'BBC Two');
        $this->assertEquals($element->getMediumMasterBrand(), 'BBC Two England');
    }

    public function testGetImage() {
        $element = $this->_createElement(array());
        $this->assertEquals($element->getImage(), '');
        $this->assertEquals($element->getStandardImage(), '');
        $this->assertEquals($element->getStandardImageRecipe(), '');

        $params = array('images' =>
            (object) array('standard' => 'http://ichef.live.bbci.co.uk/images/ic/{recipe}/legacy/episode/p01b2b5c.jpg')
        );
        $element = $this->_createElement($params);

        $this->assertEquals(
            $element->getImage(),
            'http://ichef.live.bbci.co.uk/images/ic/336x581/legacy/episode/p01b2b5c.jpg'
        );
        $this->assertEquals(
            $element->getStandardImage(),
            'http://ichef.live.bbci.co.uk/images/ic/336x189/legacy/episode/p01b2b5c.jpg'
        );
    }

    /*
     * Using an Episode Mock and inheritance
     */
    public function testGetEpisodeType() {
        $params =  array('type'=>'episode_large');
        $mockedEpisode = $this->_mockEpisode($params);

        $this->assertEquals($mockedEpisode->getType(), 'episode_large');
    }

    public function testGetEpisodeMasterBrandId() {

        $mockedEpisode = $this->_mockEpisode(array());
        $this->assertEquals($mockedEpisode->getMasterBrandId(), '');

        $params =  array('master_brand' => (object) array('id' => 'bbc_two'));
        $mockedEpisode = $this->_mockEpisode($params);

        $this->assertEquals($mockedEpisode->getMasterBrandId(), 'bbc_two');
    }


    public function testGetEpisodeMasterBrandAttribution() {

        $mockedEpisode = $this->_mockEpisode(array());
        $this->assertEquals($mockedEpisode->getMasterBrandAttribution(), '');

        $params =  array('master_brand' => (object) array('attribution'=>'bbc_two'));
        $mockedEpisode = $this->_mockEpisode($params);

        $this->assertEquals($mockedEpisode->getMasterBrandAttribution(), 'bbc_two');
    }

    public function testGetEpisodeMasterBrandIdentId() {
        $params =  array('master_brand' => (object) array('ident_id'=>'1234'));
        $mockedEpisode = $this->_mockEpisode($params);

        $this->assertEquals($mockedEpisode->getMasterBrandIdentId(), '1234');
    }

    public function testGetEpisodeMasterBrandIdentIdMissing() {
        $params =  array('master_brand' => (object) array());
        $mockedEpisode = $this->_mockEpisode($params);

        $this->assertEmpty($mockedEpisode->getMasterBrandIdentId());
    }

    public function testGetEpisodeImageRecipe() {
        $mockedEpisode = $this->_mockEpisode(array());
        $this->assertEmpty($mockedEpisode->getImageRecipe('portrait'));
        $this->assertEmpty($mockedEpisode->getPortraitImage());
        $this->assertEmpty($mockedEpisode->getPortraitImageRecipe());

        $params = array(
            'images' => (object) array(
                'portrait' => 'http://ichef.bbci.co.uk/images/ic/832x468/p025l9p1.jpg'
            )
        );
        $mockedEpisode = $this->_mockEpisode($params);

        $this->assertEquals('http://ichef.bbci.co.uk/images/ic/832x468/p025l9p1.jpg', $mockedEpisode->getPortraitImage());
        $this->assertEquals('http://ichef.bbci.co.uk/images/ic/832x468/p025l9p1.jpg', $mockedEpisode->getPortraitImageRecipe());
        $this->assertEquals('http://ichef.bbci.co.uk/images/ic/832x468/p025l9p1.jpg', $mockedEpisode->getImageRecipe('portrait'));
    }

    public function testFetchStatus() {
        $episode = $this->_createElement(array('status' => 'unavailable'));
        $this->assertEquals('unavailable', $episode->getStatus());
    }

    public function testIsComingSoon() {
        $episode = $this->_createElement(array('status' => 'unavailable'));
        $this->assertEquals(true, $episode->isComingSoon());

        $episode = $this->_createElement(array('status' => 'available'));
        $this->assertEquals(false, $episode->isComingSoon());
    }

    private function _mockEpisode($params) {
        return new Episode((object) $params);
    }

    private function _createElement($params) {
        return new Elements((object) $params);
    }
}
