<?php
namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Models\Version;
use Bamboo\Models\Episode;

class EpisodeDownloadsTest extends BambooTestCase
{
    public function testItReturnsAnArray() {
        $version = $this->_createVersions(array('original' => true));
        $episode = $this->_createEpisode(array('versions' => $version));
        $this->assertTrue(is_array($episode->getDownloadURIs()));
    }

    public function testItReturnsALinkForEachVersion() {
        $versions = $this->_createVersions(array('original' => true, 'audio-described' => true));
        $episode = $this->_createEpisode(array('versions' => $versions));
        $this->assertEquals(2, sizeof($episode->getDownloadURIs()));
    }

    public function testItDoesntIncludeNonDownloadableVersions() {
        $versions = $this->_createVersions(array('original' => true, 'audio-described' => false));
        $episode = $this->_createEpisode(array('versions' => $versions));
        $this->assertEquals(1, sizeof($episode->getDownloadURIs()));
    }

    public function testItReturnsEmptyWhenNoVersions() {
        $episode = $this->_createEpisode(array('title' => 'no downloads'));
        $this->assertEquals(array(), $episode->getDownloadURIs());
    }

    public function testItContainsTheVersionAbbreviations() {
        $versions = $this->_createVersions(array('original' => true, 'audio-described' => false, 'signed' => true));
        $episode = $this->_createEpisode(array('versions' => $versions));
        $downloadLinks = $episode->getDownloadURIs();
        $this->assertEquals(array('SD', 'SL'), array_keys($downloadLinks));
    }

    public function testItContainsHDKeyIfHDVersionIsAvailable() {
        $versions = $this->_createHDVersions(array('original' => true));
        $episode = $this->_createEpisode(array('versions' => $versions));
        $this->assertEquals(2, count($episode->getDownloadURIs()));
        $this->assertTrue(array_key_exists('HD', $episode->getDownloadURIs()));
    }

    public function testItContainsHDKeyIfPremiereHDVersionIsAvailable() {
        $versions = $this->_createHDVersions(array('premiere' => true));
        $episode = $this->_createEpisode(array('versions' => $versions));
        $this->assertEquals(2, count($episode->getDownloadURIs()));
        $this->assertTrue(array_key_exists('HD', $episode->getDownloadURIs()));
    }

    public function testItDoesNotContainHDIfOriginalVersionIsNotInHD() {
        $versions = $this->_createHDVersions(array('original' => false, 'audio-described' => true, 'signed' => true));
        $episode = $this->_createEpisode(array('versions' => $versions));
        $this->assertFalse(array_key_exists('HD', $episode->getDownloadURIs()));
    }

    public function testItReturnsADownloadEncodedURIForEachVersion() {
        $versions = $this->_createHDVersions(array('original' => true, 'audio-described' => true, 'signed' => true));
        $episode = $this->_createEpisode(
            array(
                'id' => 'episode-id',
                'title' => 'My Title',
                'subtitle' => 'My Subtitle',
                'versions' => $versions
            )
        );

        $expectedURIs = array(
            'bbc-ipd:download/episode-id/original-id/sd/standard/TXkgVGl0bGUgLSBNeSBTdWJ0aXRsZQ==',
            'bbc-ipd:download/episode-id/original-id/hd/standard/TXkgVGl0bGUgLSBNeSBTdWJ0aXRsZQ==',
            'bbc-ipd:download/episode-id/audio-described-id/sd/dubbedaudiodescribed/TXkgVGl0bGUgLSBNeSBTdWJ0aXRsZQ==',
            'bbc-ipd:download/episode-id/signed-id/sd/signed/TXkgVGl0bGUgLSBNeSBTdWJ0aXRsZQ=='
        );

        $this->assertEquals($expectedURIs, array_values($episode->getDownloadURIs()));
    }

    public function testItRemovesSlashesFromTheTitle() {
        $versions = $this->_createHDVersions(array('original' => false));
        $episode = $this->_createEpisode(
            array(
                'id' => 'episode-id',
                'title' => 'Batman Returns?',
                'versions' => $versions
            )
        );

        $expectedURIs = array(
            'bbc-ipd:download/episode-id/original-id/sd/standard/QmF0bWFuIFJldHVybnM_'
        );

        $this->assertEquals($expectedURIs, array_values($episode->getDownloadURIs()));
    }

    private function _createEpisode($params) {
        return new Episode((object) $params);
    }

    private function _createHDVersions($kinds) {
        $versions = array();
        foreach ($kinds as $kind => $hd) {
            $versions[] = (object) array(
                'id' => $kind . '-id',
                'kind' => $kind,
                'download' => true,
                'hd' => $hd
            );
        }
        return $versions;
    }

    private function _createVersions($kinds) {
        $versions = array();
        foreach ($kinds as $kind => $downloadable) {
            $versions[] = (object) array(
                'kind' => $kind,
                'download' => $downloadable
            );
        }
        return $versions;
    }
}
