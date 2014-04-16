<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Models\Version;

class VersionTest extends BambooTestCase
{
    public function testSlugForOriginalVersion() {
        $version = $this->_createVersion(array('kind' => 'original'));
        $this->assertEquals('', $version->getSlug());
    }

    public function testSlugForSignedVersion() {
        $version = $this->_createVersion(array('kind' => 'signed'));
        $this->assertEquals('sign', $version->getSlug());
    }

    public function testSlugForAudioDescribedVersion() {
        $version = $this->_createVersion(array('kind' => 'audio-described'));
        $this->assertEquals('ad', $version->getSlug());
    }

    public function testSlugForOtherVersions() {
        $version = $this->_createVersion(array('kind' => 'other'));
        $this->assertEquals('', $version->getSlug());
    }

    public function testRetrievingOnwardJourneyTime() {
        $timeOffset = '30';
        $version = $this->_createVersion(
            array(
                'events' => array(
                    (object) array(
                        'kind' => 'onward_journey', 
                        'time_offset_seconds' => $timeOffset
                    )
                )
            )
        );
        $this->assertEquals($timeOffset, $version->getOnwardJourneyTime());
    }

    public function testGuidanceData() {
        $version = $this->_createVersion(
            (object) array('guidance' => (object) array(
                'text' => (object) array(
                    'small' => 'small text',
                    'medium' => 'medium text',
                    'large' => 'large text'
                )
            ))
        );
        $this->assertEquals('small text', $version->getSmallGuidance());
        $this->assertEquals('medium text', $version->getMediumGuidance());
        $this->assertEquals('large text', $version->getLargeGuidance());
    }

    public function testEmptyGuidanceData() {
        $version = $this->_createVersion(array());
        $this->assertEquals('', $version->getSmallGuidance());
        $this->assertEquals('', $version->getMediumGuidance());
        $this->assertEquals('', $version->getLargeGuidance());
    }

    public function testFirstBroadcast() {
        $version = $this->_createVersion(array('first_broadcast' => '8pm 27 Dec 2013'));
        $this->assertEquals('8pm 27 Dec 2013', $version->getFirstBroadcast());
    }

    public function testEmptyFirstBroadcast() {
        $version = $this->_createVersion(array());
        $this->assertEquals('', $version->getFirstBroadcast());
    }

    public function testAbbreviations() {
        $sd = $this->_createVersion(array('kind' => 'original'));
        $ad = $this->_createVersion(array('kind' => 'audio-described'));
        $adhd = $this->_createVersion(array('kind' => 'audio-described', 'hd' => true));
        $sl = $this->_createVersion(array('kind' => 'signed'));
        $slhd = $this->_createVersion(array('kind' => 'signed', 'hd' => true));
        $hd = $this->_createVersion(array('kind' => 'original', 'hd' => true));

        $this->assertEquals('SD', $sd->getAbbreviation());
        $this->assertEquals('AD', $ad->getAbbreviation());
        $this->assertEquals('AD', $adhd->getAbbreviation());
        $this->assertEquals('SL', $sl->getAbbreviation());
        $this->assertEquals('SL', $slhd->getAbbreviation());
        $this->assertEquals('HD', $hd->getAbbreviation());
    }

    private function _createVersion($params) {
        return new Version((object) $params);
    }
}