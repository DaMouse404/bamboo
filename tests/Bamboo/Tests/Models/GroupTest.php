<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooBaseTestCase;
use Bamboo\Models\Group;

class GroupTest extends BambooBaseTestCase
{
    /**
     * This tests checks the generation of the group model
     *
     * @access public
     * @return void
     */

    public function testEditorialType() {
        $group = $this->_createGroup();
        $this->assertEquals('editorial', $group->getIstatsType());
    }

    public function testEditorialLabel() {
        $group = $this->_createGroup();
        $this->assertEquals('Archive', $group->getEditorialLabel());
    }

    public function testBlankEditorialLabel() {
        $group = $this->_createGroup(array('labels' => ''));
        $this->assertEmpty($group->getEditorialLabel());
    }

    public function testStackedType() {
        $group = $this->_createGroup(array('stacked' => true));
        $this->assertEquals('series-catchup', $group->getIstatsType());
    }

    public function testPopularType() {
        $group = $this->_createGroup(array('id' => 'popular'));
        $this->assertEquals('most-popular', $group->getIstatsType());
    }


    public function testGetNoRelatedLinks() {
        $group = $this->_createGroup();
        $links = $group->getRelatedLinks();

        $this->assertEmpty($group->getRelatedLinks());
        $this->assertEmpty($group->getRelatedLinksByKind('standard'));
    }

    public function testGetRelatedLinks() {
        $related = $this->_createRelatedLinks(array('priority_content', 'standard', 'standard'));
        $group = $this->_createGroup(array('related_links' => $related));
        $links = $group->getRelatedLinks();

        $this->assertCount(3, $links);
        $this->assertInstanceOf(
            'Bamboo\Models\Related',
            $links[0]
        );

        $standardLinks = $group->getRelatedLinksByKind('standard');
        $this->assertCount(2, $standardLinks);
        $this->assertInstanceOf(
            'Bamboo\Models\Related',
            $standardLinks[0]
        );
        $this->assertEquals('standard', $standardLinks[1]->getKind());

        $this->assertEmpty($group->getRelatedLinksByKind('invalid'));
    }

    public function testGetSubtitle() {
        $group = $this->_createGroup(array('subtitle' => 'Dr Who'));
        $this->assertEquals('Dr Who', $group->getSubtitle());
    }

    public function testGetLabels() {
        $labels = array('label1', 'label2');
        $group = $this->_createGroup(array('labels' => $labels));
        $labels = $group->getLabels();
        $this->assertInternalType('array', $labels);
        $this->assertEquals('label1', $labels[0]);
    }

    public function testGetEpisodes() {
        $group = $this->_createGroup(
            array(
                'count' => 3,
                'initial_children' => $this->_createEpisodes(array('broadcast', 'episode'))
            )
        );
        $episodes = $group->getEpisodes();
        $this->assertEquals('broadcast', $episodes[0]->getType());
        $this->assertEquals(2, $group->getEpisodeCount());
        $this->assertEquals(3, $group->getTotalEpisodeCount());
    }

    public function testGetHeroImage () {
        $group = $this->_createGroup();

        $this->assertEquals('hero_image_dimensions=100x200', $group->getHeroImage(100,200));
    }

    public function testGetHeroImageDefaults () {
        $group = $this->_createGroup();

        $this->assertRegexp('/^hero_image_dimensions=\d+x\d+$/', $group->getHeroImage());
    }

    public function testGetHeroImageNoImage () {
        $group = $this->_createGroup(
            array ('images' => (object) array())
        );

        $this->assertEquals(false, $group->getHeroImage(100,200));
    }

    private function _createGroup($params = array()) {
        $group = array(
            "id" => "fake_id",
            "labels" => (object) array("editorial" => "Archive"),
            'images' => (object) array(
                'hero' => 'hero_image_dimensions={recipe}'
            )
        );
        return new Group((object) array_merge($group, $params));
    }

    private function _createEpisodes($episodes) {
        $links = array();
        foreach ($episodes as $episode) {
            $links[] = (object) array('type' => $episode);
        }
        return $links;
    }

    private function _createRelatedLinks($kinds) {
        $links = array();
        foreach ($kinds as $kind) {
            $links[] = (object) array('kind' => $kind);
        }
        return $links;
    }

}
