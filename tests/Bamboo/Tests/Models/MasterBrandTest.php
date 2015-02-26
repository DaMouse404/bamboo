<?php

namespace Bamboo\Tests\Models;

use Bamboo\Tests\BambooBaseTestCase;
use Bamboo\Models\MasterBrand;

class MasterBrandTest extends BambooBaseTestCase
{
    public function testMasterBrandWithNoData() {
        $masterBrand = $this->_createMasterBrand(array());

        $this->assertEquals($masterBrand->getId(), '');
        $this->assertEquals($masterBrand->getIdentId(), '');
        $this->assertEquals($masterBrand->getAttribution(), '');
        $this->assertEquals($masterBrand->getSmallTitle(), '');
        $this->assertEquals($masterBrand->getMediumTitle(), '');
        $this->assertEquals($masterBrand->getLargeTitle(), '');
    }

    public function testMasterBrandTitleWithNoData() {
        $params =  array('titles' => (object) array());
        $masterBrand = $this->_createMasterBrand($params);

        $this->assertEquals($masterBrand->getSmallTitle(), '');
        $this->assertEquals($masterBrand->getMediumTitle(), '');
        $this->assertEquals($masterBrand->getLargeTitle(), '');
    }

    public function testMasterBrandWithData() {
        $params = array(
            'id' => 'p00st1ck',
            'ident_id' => 'b4rdw3ll',
            'attribution' => 'bbc_one',
            'titles' => (object) array(
                'small' => 'BBC One',
                'medium' => 'BBC One England',
                'large' => 'BBC One England Planet Earth'
            )
        );
        $masterBrand = $this->_createMasterBrand($params);

        $this->assertEquals($masterBrand->getId(), 'p00st1ck');
        $this->assertEquals($masterBrand->getIdentId(), 'b4rdw3ll');
        $this->assertEquals($masterBrand->getAttribution(), 'bbc_one');
        $this->assertEquals($masterBrand->getSmallTitle(), 'BBC One');
        $this->assertEquals($masterBrand->getMediumTitle(), 'BBC One England');
        $this->assertEquals($masterBrand->getLargeTitle(), 'BBC One England Planet Earth');
    }

    private function _createMasterBrand($params) {
        return new MasterBrand((object) $params);
    }
}
