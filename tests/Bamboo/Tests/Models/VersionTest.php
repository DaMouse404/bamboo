<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Models\Version;

class VersionTest extends BambooTestCase
{
    public function testGetTotalDaysAvailabile() {
        $version = $this->_createVersion(array('availability' => (object) array(
                'start' => '2014-08-30T12:00:00Z',
                'end' => '2014-09-06T12:00:00Z'
            )
        ));

        $this->assertEquals(7, $version->getTotalDaysAvailable());
    }

    public function testNoAvailability() {
        $version = $this->_createVersion(array());
        $this->assertEmpty($version->getTotalDaysAvailable());
    }

    public function testGetRemainingDaysAvailable() {
        $version = $this->_createVersion(array('availability' => (object) array(
            'end' => date('c', strtotime('+5 days'))
        )));

        $this->assertEquals(5, $version->getRemainingDaysAvailable());
    }

    public function testGetAvailabilityDay() {
        $version = $this->_createVersion(array('availability' => (object) array(
            'start' => date('c', strtotime('-3 days -1 hour')),
            'end' => date('c', strtotime('+5 days'))
        )));

        // This is past the 3rd day and thus in the 4th period
        $this->assertEquals(4, $version->getAvailabilityDay());
    }

    public function testGetAvailabilityDayOne() {
        $version = $this->_createVersion(array('availability' => (object) array(
            'start' => date('c', strtotime('today')),
            'end' => date('c', strtotime('+5 days'))
        )));

        // This in the 1st period
        $this->assertEquals(1, $version->getAvailabilityDay());
    }

    public function testNoDaysRemainingAvailabile() {
        $version = $this->_createVersion(array());
        $this->assertEmpty($version->getRemainingDaysAvailable());
    }

    public function testGetRemainingAvailability() {
        $version = $this->_createVersion(array('availability' => (object) array(
            'remaining' => (object) array(
                'text' => 'Available until the end of time',
                'value' => 'P4DT21H24M17S'
            )
        )));

        $this->assertEquals('Available until the end of time', $version->getRemainingAvailability());
    }

    public function testNoRemainingAvailability() {
        $version = $this->_createVersion(array());

        $this->assertEmpty($version->getRemainingAvailability());
    }

    public function testGetAvailability() {
        $version = $this->_createVersion(array('availability' => (object) array(
                'start' => '2014-08-30T12:00:00Z',
                'end' => '2014-09-06T12:00:00Z'
            )
        ));

        $this->assertEquals('2014-08-30T12:00:00Z', $version->getAvailability('start'));
        $this->assertEquals('2014-09-06T12:00:00Z', $version->getAvailability());
        $this->assertEmpty($version->getAvailability('cake'));
    }

    public function testNoDuration() {
        $version = $this->_createVersion(array());
        $this->assertEmpty($version->getDuration());
    }

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

    public function testIncorrectKindRetrievingOnwardJourney() {
        $version = $this->_createVersion(
            array(
                'events' => array(
                    (object) array(
                        'kind' => 'cake'
                    )
                )
            )
        );
        $this->assertEmpty($version->getOnwardJourneyTime());
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

    public function testNoOnwardJourney() {
        $version = $this->_createVersion(array());
        $this->assertEmpty($version->getOnwardJourneyTime());
    }

    public function testGetGuidance() {
        $version = $this->_createVersion(array('guidance'=>'Cake'));
        $this->assertEquals('Cake', $version->getGuidanceObj());
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

    public function testGuidanceId() {
        $version = $this->_createVersion(
            (object) array('guidance' => (object) array(
                'id' => 'cake'
            ))
        );
        $this->assertEquals('cake', $version->getGuidanceID());
    }

    public function testNoGuidanceid() {
        $version = $this->_createVersion(array());
        $this->assertEmpty($version->getGuidanceID());
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

    public function testDurationInSecs() {

        $ver = $this->_createVersion(array());
        $this->assertEquals(0, $ver->getDurationInSecs());

        $this->assertEquals(4800, $this->_getSecsForDuration('80 mins', 'PT1H20M'));
        $this->assertEquals(600, $this->_getSecsForDuration('10 mins', 'PT10M'));
        $this->assertEquals(630, $this->_getSecsForDuration('11 mins', 'PT10M30S'));
    }

    public function testGetRRC() {
        $ver = $this->_createVersion(array('rrc'=>'Cake'));
        $this->assertEquals('Cake', $ver->getRRC());
    }

    public function testGetRRCAttrs() {
        $version = $this->_createVersion(
            (object) array('rrc' => (object) array(
                'description' => (object) array(
                    'small' => 'small text',
                    'large' => 'large text'
                ),
                'url' => 'cake'
            ))
        );

        $this->assertEquals('small text', $version->getRRCShort());
        $this->assertEquals('large text', $version->getRRCLong());
        $this->assertEquals('cake', $version->getRRCUrl());
    }

    public function testNoRRCAttrs() {
        $version = $this->_createVersion(array());

        $this->assertEmpty($version->getRRCShort());
        $this->assertEmpty($version->getRRCLong());
        $this->assertEmpty($version->getRRCUrl());
    }

    private function _getSecsForDuration($text, $value) {
        $ver = $this->_getDuration($text, $value);
        return $ver->getDurationInSecs();
   }

    public function testDurationInMins() {

        $ver = $this->_createVersion(array());
        $this->assertEquals(0, $ver->getDurationInMins());

        $this->assertEquals(80, $this->_getMinsForDuration('80 mins', 'PT1H20M'));
        $this->assertEquals(10, $this->_getMinsForDuration('10 mins', 'PT10M'));
        $this->assertEquals(11, $this->_getMinsForDuration('11 mins', 'PT10M30S'));
    }

    private function _getMinsForDuration($text, $value) {
        $ver = $this->_getDuration($text, $value);
        return $ver->getDurationInMins();
    }

    private function _getDuration($text, $value) {
        $ver = $this->_createVersion(array(
           'duration' => (object) array(
                'text' => $text,
                'value' => $value
            )
	));
        return $ver;
   }

    private function _createVersion($params) {
        return new Version((object) $params);
    }
}
