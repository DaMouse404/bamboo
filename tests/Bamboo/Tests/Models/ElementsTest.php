<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooBaseTestCase;
use Bamboo\Models\Episode;
use Bamboo\Models\MasterBrand;
use Bamboo\Models\Elements;
use Bamboo\Configuration;

class ElementsTest extends BambooBaseTestCase
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
        $params = array('master_brand' => (object) array('id' => 'p00st1ck'));
        $element = $this->_createElement($params);
        $this->assertInstanceOf('Bamboo\Models\MasterBrand', $element->getMasterBrand());

        $element = $this->_createElement(array());
        $this->assertInstanceOf('Bamboo\Models\MasterBrand', $element->getMasterBrand());
    }

    public function testGetImage() {
        $placeholderImageUrl = Configuration::getPlaceholderImageUrl();
        $element = $this->_createElement(array());
        $this->assertEquals($element->getImage(), $placeholderImageUrl);
        $this->assertEquals($element->getStandardImage(), $placeholderImageUrl);
        $this->assertEquals($element->getStandardImageRecipe(), $placeholderImageUrl);

        $params = array('images' => (object) array());
        $element = $this->_createElement($params);
        $this->assertEquals($element->getImage(), $placeholderImageUrl);
        $this->assertEquals($element->getStandardImage(), $placeholderImageUrl);
        $this->assertEquals($element->getStandardImageRecipe(), $placeholderImageUrl);

        $params = array('images' => (object) array('standard' => ''));
        $element = $this->_createElement($params);
        $this->assertEquals($element->getImage(), $placeholderImageUrl);
        $this->assertEquals($element->getStandardImage(), $placeholderImageUrl);
        $this->assertEquals($element->getStandardImageRecipe(), $placeholderImageUrl);

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

    public function testGetImageWithCustomImageHost() {
        Configuration::setCustomImageHost('https://imagehost.co.uk');

        $params = array('images' =>
            (object) array('standard' => 'http://ichef.live.bbci.co.uk/images/ic/{recipe}/legacy/episode/p01b2b5c.jpg')
        );
        $element = $this->_createElement($params);

        $this->assertEquals(
            $element->getImage(),
            'https://imagehost.co.uk/images/ic/336x581/legacy/episode/p01b2b5c.jpg'
        );

        Configuration::setCustomImageHost('http://my-image-host.com');
        $params = array('images' =>
            (object) array('standard' => 'https://image-chef.bbc.co.uk/images/ic/{recipe}/legacy/episode/p01b2b5c.jpg')
        );
        $element = $this->_createElement($params);

        $this->assertEquals(
            $element->getStandardImage(),
            'http://my-image-host.com/images/ic/336x189/legacy/episode/p01b2b5c.jpg'
        );

        Configuration::setCustomImageHost(false);
    }

    /*
     * Using an Episode Mock and inheritance
     */
    public function testGetEpisodeType() {
        $params =  array('type'=>'episode_large');
        $mockedEpisode = $this->_mockEpisode($params);

        $this->assertEquals($mockedEpisode->getType(), 'episode_large');
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
