<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooTestCase;
use Bamboo\Models\Version;
use Bamboo\Models\Episode;

class EpisodeTest extends BambooTestCase
{
    public function testSlugWithEmptyTitle() {
        $episode = $this->_createEpisode(array('title' => ''));
        $this->assertEquals('', $episode->getSlug());
    }

    public function testSlugWithSingleWordTitle() {
        $episode = $this->_createEpisode(array('title' => 'title'));
        $this->assertEquals('title', $episode->getSlug());
    }

    public function testSlugWithTitleContainingDigits() {
        $episode = $this->_createEpisode(array('title' => '90210'));
        $this->assertEquals('90210', $episode->getSlug());
    }

    public function testSlugWithMultiWordTitle() {
        $episode = $this->_createEpisode(array('title' => 'my title'));
        $this->assertEquals('my-title', $episode->getSlug());
    }

    public function testSlugWithTabbedTitle() {
        $episode = $this->_createEpisode(array('title' => "my\ttitle"));
        $this->assertEquals('my-title', $episode->getSlug());
    }

    public function testSlugWithMultiSpaceTitle() {
        $episode = $this->_createEpisode(array('title' => "my    title  has    many    spaces"));
        $this->assertEquals('my-title-has-many-spaces', $episode->getSlug());
    }

    public function testSlugWithLeadingSpaces() {
        $episode = $this->_createEpisode(array('title' => '  my', 'subtitle' => '   subtitle'));
        $this->assertEquals('my-subtitle', $episode->getSlug());
    }

    public function testSlugWithTrailingSpaces() {
        $episode = $this->_createEpisode(array('title' => 'my  ', 'subtitle' => 'subtitle   '));
        $this->assertEquals('my-subtitle', $episode->getSlug());
    }

    public function testSlugWithQuestionMark()
    {
        $episode = $this->_createEpisode(array('title' => 'Question mark?'));
        $this->assertEquals('question-mark', $episode->getSlug());
        $episode = $this->_createEpisode(array('title' => 'Question', 'subtitle' => 'mark?'));
        $this->assertEquals('question-mark', $episode->getSlug());
    }

    public function testSlugWithApostrophe() {
        $episode = $this->_createEpisode(array('title' => 'What\'s the craic', 'subtitle' => 'jack?'));
        $this->assertEquals('whats-the-craic-jack', $episode->getSlug());
    }

    public function testSlugWithMixedCaseTitle() {
        $episode = $this->_createEpisode(array('title' => 'MyTiTlE'));
        $this->assertEquals('mytitle', $episode->getSlug());
    }

    public function testSlugWithAccentedTitle() {
        $episode = $this->_createEpisode(array('title' => 'MÿTītłę'));
        $this->assertEquals('mytitle', $episode->getSlug());

        $episode = $this->_createEpisode(array('title' => "An L\xc3\xa0"));
        $this->assertEquals('an-la', $episode->getSlug());
    }

    public function testSlugWithSubtitle() {
        $episode = $this->_createEpisode(array('title' => 'title', 'subtitle' => 'subtitle'));
        $this->assertEquals('title-subtitle', $episode->getSlug());
    }

    public function testSlugWithAccentedSubtitle() {
        $episode = $this->_createEpisode(array('title' => 'èvéry', 'subtitle' => 'thïng'));
        $this->assertEquals('every-thing', $episode->getSlug());
    }

    public function testSlugWithLongTitleAndSubtitle() {
        $episode = $this->_createEpisode(
            array(
                'title' => "  The Longer\t  \tThë tîtle\t\t   ",
                'subtitle' => "\t  the more hypheñs\t \t \t"
            )
        );
        $this->assertEquals('the-longer-the-title-the-more-hyphens', $episode->getSlug());
    }

    public function testEmpttyTleoType() {
        $episode = $this->_createEpisode(array());
        $this->assertEquals('', $episode->getTleoType());
    }

    public function testTleoType() {
        $episode = $this->_createEpisode(array('tleo_type' => 'brand'));
        $this->assertEquals('brand', $episode->getTleoType());
    }

    public function testPriorityVersionWithMultipleVersions() {
        $versions = $this->_createVersions(array('original', 'audio-described', 'signed', 'other'));
        $episode = $this->_createEpisode(array('versions' => $versions));
        $versions = $episode->getVersions();
        $this->assertEquals('Bamboo\Models\Version', get_class($versions[0]));
        $priorityVersion = $episode->getPriorityVersion();
        $this->assertEquals('original', $priorityVersion->getKind());
    }

    public function testPriorityVersionWithSingleVersion() {
        $versions = $this->_createVersions(array('signed'));
        $episode = $this->_createEpisode(array('versions' => $versions));
        $priorityVersion = $episode->getPriorityVersion();
        $this->assertEquals('signed', $priorityVersion->getKind());
    }

    public function testPriorityVersionWithPreferenceThatExists() {
        $versions = $this->_createVersions(array('original', 'signed'));
        $episode = $this->_createEpisode(array('versions' => $versions));
        $priorityVersion = $episode->getPriorityVersion('signed');
        $this->assertEquals('signed', $priorityVersion->getKind());
    }

    public function testPriorityVersionWithPreferenceThatDoesntExist() {
        $versions = $this->_createVersions(array('signed', 'other'));
        $episode = $this->_createEpisode(array('versions' => $versions));
        $priorityVersion = $episode->getPriorityVersion('audio-described');
        // It should return the first version instead
        $this->assertEquals('signed', $priorityVersion->getKind());
    }

    public function testPriorityVersionWithBlankPreference() {
        $versions = $this->_createVersions(array('signed', 'audio-described'));
        $episode = $this->_createEpisode(array('versions' => $versions));
        $priorityVersion = $episode->getPriorityVersion('');
        // It should return the first version instead
        $this->assertEquals('signed', $priorityVersion->getKind());
    }

    public function testPriorityVersionWithNoVersions() {
        $episode = $this->_createEpisode(array('title' => 'My Title'));
        $priorityVersion = $episode->getPriorityVersion();
        $this->assertEquals('', $priorityVersion);
    }

    public function testPriorityVersionSlugCallsPriorityVersion() {
        $stub = $this->getMockBuilder('Bamboo\Models\Episode')
            ->setMethods(array('getPriorityVersion'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->expects($this->once())
            ->method('getPriorityVersion');

        $stub->getPriorityVersionSlug();
    }

    public function testPriorityVersionSlugPassesPreferenceToPriorityVersion() {
        $stub = $this->getMockBuilder('Bamboo\Models\Episode')
            ->setMethods(array('getPriorityVersion'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->expects($this->once())
            ->method('getPriorityVersion')
            ->with('signed');

        $stub->getPriorityVersionSlug('signed');
    }

    public function testPriorityVersionSlugReturnsVersionSlug() {
        $version = $this->getMockBuilder('Bamboo\Models\Version')
            ->setMethods(array('getSlug'))
            ->disableOriginalConstructor()
            ->getMock();

        $episode = $this->getMockBuilder('Bamboo\Models\Episode')
            ->setMethods(array('getPriorityVersion'))
            ->disableOriginalConstructor()
            ->getMock();

        $episode->expects($this->once())
            ->method('getPriorityVersion')
            ->will($this->returnValue($version));

        $version->expects($this->once())
            ->method('getSlug')
            ->will($this->returnValue('slug'));

        $this->assertEquals('slug', $episode->getPriorityVersionSlug());
    }

    public function testPriorityVersionSlugReturnsBlankWithNoVersion() {
        $stub = $this->getMockBuilder('Bamboo\Models\Episode')
            ->setMethods(array('getPriorityVersion'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->expects($this->once())
            ->method('getPriorityVersion')
            ->will($this->returnValue(''));

        $this->assertEquals('', $stub->getPriorityVersionSlug());
    }

    public function testGetDurationReturnsPriorityVersionDuration() {
        $version = new Version((object) array('duration' => (object) array('text' => '40 mins')));

        $stub = $this->getMockBuilder('Bamboo\Models\Episode')
            ->setMethods(array('getPriorityVersion'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->expects($this->once())
            ->method('getPriorityVersion')
            ->will($this->returnValue($version));

        $this->assertEquals('40 mins', $stub->getDuration());
    }

    public function testGetDurationReturnsBlankWhenNoVersionPresent() {
        $stub = $this->getMockBuilder('Bamboo\Models\Episode')
            ->setMethods(array('getPriorityVersion'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->expects($this->once())
            ->method('getPriorityVersion')
            ->will($this->returnValue(''));

        $this->assertEquals('', $stub->getDuration());
    }

    public function testGetDurationInMinsReturnsPriorityVersionDuration() {
        $version = new Version((object) array('duration' => (object) array('text' => '140 mins', 'value' => 'PT2H20M')));

        $stub = $this->getMockBuilder('Bamboo\Models\Episode')
            ->setMethods(array('getPriorityVersion'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->expects($this->once())
            ->method('getPriorityVersion')
            ->will($this->returnValue($version));

        $this->assertEquals(140, $stub->getDurationInMins());
    }

    public function testGetDurationInMinsReturnsZeroWhenNoVersionPresent() {
        $stub = $this->getMockBuilder('Bamboo\Models\Episode')
            ->setMethods(array('getPriorityVersion'))
            ->disableOriginalConstructor()
            ->getMock();

        $stub->expects($this->once())
            ->method('getPriorityVersion')
            ->will($this->returnValue(''));

        $this->assertEquals(0, $stub->getDurationInMins());
    }

    public function testGetMasterBrandAttribution() {
        $episode = $this->_createEpisode(
            (object) array(
                'master_brand' => (object) array(
                    'attribution' => 'bbc_two_wales'
                 )
            )
        );
        $this->assertEquals('bbc_two_wales', $episode->getMasterBrandAttribution());
    }

    public function testGetRelatedLinks() {
        $related = $this->_createVersions(array('priority_content', 'external'));
        $episode = $this->_createEpisode(array('related_links' => $related));
        $links = $episode->getRelatedLinks();
        $this->assertNotEmpty($links);
        $this->assertInstanceOf(
            'Bamboo\Models\Related',
            $links[0]
        );
    }

    public function testGetFirstRelatedLink() {
        $related = $this->_createVersions(array('priority_content', 'external'));
        $episode = $this->_createEpisode(array('related_links' => $related));
        $link = $episode->getFirstRelatedLink();
        $this->assertInstanceOf(
            'Bamboo\Models\Related',
            $link
        );
    }

    public function testHasDownloads() {
        $downloadable = $this->_createEpisode(array('versions' => $this->_createVersions(array('original'))));
        $notDownloadable = $this->_createEpisode(array('versions' => $this->_createVersions(array('original'), false)));
        $this->assertTrue($downloadable->hasDownloads());
        $this->assertFalse($notDownloadable->hasDownloads());
    }

    public function testShowFlags() {
        $episode = $this->_createEpisode(array());
        $this->assertFalse($episode->showFlags());
        $versions = $this->_createVersions(array('original', 'audio-described'), false, false);
        $episode = $this->_createEpisode(array('versions' => $versions));
        $this->assertTrue($episode->showFlags());
        $versions = $this->_createVersions(array('original'), false, false);
        $episode = $this->_createEpisode(array('versions' => $versions));
        $this->assertFalse($episode->showFlags());
    }

    public function testhasHd() {
        $episode = $this->_createEpisode(array());
        $this->assertFalse($episode->hasHD());
        $versions = $this->_createVersions(array('original', 'audio-described'), false, false);
        $episode = $this->_createEpisode(array('versions' => $versions));
        $this->assertFalse($episode->hasHD());
        $versions = $this->_createVersions(array('original', 'audio-described'), false, true);
        $episode = $this->_createEpisode(array('versions' => $versions));
        $this->assertTrue($episode->hasHD());
    }

    public function testDownloadsFirstSDVersion() {
        $this->_assertDownloadVersionsMatch(
            array(
                (object) array(
                    'id' => 'original',
                    'kind' => 'iplayer',
                    'hd' => true,
                    'download' => true
                ),
                (object) array(
                    'id' => 'signedSD',
                    'kind' => 'signed',
                    'hd' => false,
                    'download' => true
                ),
                (object) array(
                    'id' => 'prewatershed',
                    'kind' => 'orignal',
                    'hd' => false,
                    'download' => true
                )
            ),
            array (
                'SD' => 'bbc-ipd:download//original/sd/standard/',
                'SL' => 'bbc-ipd:download//signedSD/sd/signed/',
                'HD' => 'bbc-ipd:download//original/hd/standard/'
            )
        );
    }

    public function testDownloadsMultipleAccessibleVersions() {
        $this->_assertDownloadVersionsMatch(
            array(
                (object) array(
                    'id' => 'audioSD',
                    'kind' => 'audio-described',
                    'hd' => false,
                    'download' => true
                ),
                (object) array(
                    'id' => 'signedSD',
                    'kind' => 'signed',
                    'hd' => false,
                    'download' => true
                ),
                (object) array(
                    'id' => 'originalSD',
                    'kind' => 'orignal',
                    'hd' => false,
                    'download' => true
                )
            ),
            array (
                'SD' => 'bbc-ipd:download//originalSD/sd/standard/',
                'SL' => 'bbc-ipd:download//signedSD/sd/signed/',
                'AD' => 'bbc-ipd:download//audioSD/sd/dubbedaudiodescribed/'
            )
        );
    }

    public function testDownloadsFirstSDOverHDVersions() {
        $this->_assertDownloadVersionsMatch(
            array(
                (object) array(
                    'id' => 'premierSD',
                    'kind' => 'iplayer',
                    'hd' => false,
                    'download' => true
                ),
                (object) array(
                    'id' => 'originalHD',
                    'kind' => 'orignal',
                    'hd' => true,
                    'download' => true
                )
            ),
            array (
                'SD' => 'bbc-ipd:download//premierSD/sd/standard/',
                'HD' => 'bbc-ipd:download//originalHD/hd/standard/'
            )
        );
    }

    public function testDownloadsHDIgnoredOnAccessibleVersions() {
        $this->_assertDownloadVersionsMatch(
            array(
                (object) array(
                    'id' => 'signedHD',
                    'kind' => 'signed',
                    'hd' => true,
                    'download' => true
                ),
                (object) array(
                    'id' => 'audioHD',
                    'kind' => 'audio-described',
                    'hd' => true,
                    'download' => true
                ),
                (object) array(
                    'id' => 'originalSD',
                    'kind' => 'original',
                    'hd' => false,
                    'download' => true
                )
            ),
            array (
                'SD' => 'bbc-ipd:download//originalSD/sd/standard/',
                'SL' => 'bbc-ipd:download//signedHD/sd/signed/',
                'AD' => 'bbc-ipd:download//audioHD/sd/dubbedaudiodescribed/'
            )
        );
    }

    public function testDownloadsHDUsedForSDIfPriority() {
        $this->_assertDownloadVersionsMatch(
            array(
                (object) array(
                    'id' => 'originalHD',
                    'kind' => 'original',
                    'hd' => true,
                    'download' => true
                ),
                (object) array(
                    'id' => 'originalSD',
                    'kind' => 'original',
                    'hd' => false,
                    'download' => true
                )
            ),
            array (
                'SD' => 'bbc-ipd:download//originalHD/sd/standard/',
                'HD' => 'bbc-ipd:download//originalHD/hd/standard/',
            )
        );
    }

    public function testDownloadsSDIgnoredOnAccessibleVersions() {
        $this->_assertDownloadVersionsMatch(
            array(
                (object) array(
                    'id' => 'audioSD',
                    'kind' => 'audio-described',
                    'hd' => false,
                    'download' => true
                ),
                (object) array(
                    'id' => 'signedSD',
                    'kind' => 'signed',
                    'hd' => false,
                    'download' => true
                )
            ),
            array (
                'AD' => 'bbc-ipd:download//audioSD/sd/dubbedaudiodescribed/',
                'SL' => 'bbc-ipd:download//signedSD/sd/signed/',
            )
        );
    }

    public function testIsStacked() {
        $episode = $this->_createEpisode(array());
        $this->assertFalse($episode->isStacked());
        $episode = $this->_createEpisode(array('stacked' => true));
        $this->assertTrue($episode->isStacked());
    }

    public function testIsFilm() {
        $episode = $this->_createEpisode(array());
        $this->assertFalse($episode->isFilm());
        $episode = $this->_createEpisode(array('film' => true));
        $this->assertTrue($episode->isFilm());
    }

    public function testGetHref() {
        $episode = $this->_createEpisode(array('href' => 'www.bbc.co.uk'));
        $this->assertEquals('www.bbc.co.uk', $episode->getHref());
    }

    public function testGetTleoId() {
        $episode = $this->_createEpisode(array('tleo_id' => 'tleo_pid'));
        $this->assertEquals('tleo_pid', $episode->getTleoId());
    }

    public function testGetSubtitle() {
        $episode = $this->_createEpisode(array('subtitle' => 'Dr Who'));
        $this->assertEquals('Dr Who', $episode->getSubtitle());
    }

    public function testGetReleaseDate() {
        $episode = $this->_createEpisode(array('release_date' => '2014'));
        $this->assertEquals('2014', $episode->getReleaseDate());
    }

    public function testGetLabels() {
        $episode = $this->_createEpisode(array());
        $this->assertEquals('', $episode->getTimelinessLabel());
        $this->assertEquals('', $episode->getEditorialLabel());
        $label = (object) array(
            'time' => 123,
            'editorial' => 'Bake Off'
        );
        $episode = $this->_createEpisode(array('labels' => $label));
        $this->assertEquals(123, $episode->getTimelinessLabel());
        $this->assertEquals('Bake Off', $episode->getEditorialLabel());
    }

    private function _assertDownloadVersionsMatch ($versions, $expectation) {
        $episode = $this->_createEpisode(array('versions' => $versions));

        $downloads = $episode->getDownloadURIs();

        $this->assertEquals($expectation, $downloads);
    }

    private function _createEpisode($params) {
        return new Episode((object) $params);
    }

    private function _createVersions($kinds, $downloadable = true, $hd = false) {
        $versions = array();
        foreach ($kinds as $kind) {
            $versions[] = (object) array('kind' => $kind, 'download' => $downloadable, 'hd' => $hd);
        }
        return $versions;
    }
}
