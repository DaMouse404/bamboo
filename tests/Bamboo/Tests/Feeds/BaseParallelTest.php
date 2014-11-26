<?php

namespace Bamboo\Tests;

use \Bamboo\Client;

class BaseParallelTest extends BambooClientTestCase
{
	private $originalClient;
	public function setUp() {
		$this->originalClient = clone Client::getInstance();

	}
	public function tearDown() {
		Client::$instance = $this->originalClient;
	}

	public function testFetchSingleFeed () {
		$singleResponse = array(
			(object) array(
				'programmes' => array('programm' => 'programmes here')
			)
		);
		$params =array('someParam'=>'someValue');
		$feedName = array('home/highlights');

		$stub = $this->getMock(
			'Bamboo\Client',
			array(
				'requestAll'
			)
		);
		$stub
			->expects($this->once())
			->method('requestAll')
			->with($this->identicalTo(array(array($feedName[0], $params))))
			->will($this->returnValue($singleResponse));

		Client::$instance = $stub;

		$feed = new FakeParallelFeed($feedName, $params);

		$this->assertEquals($singleResponse, $feed->_responses);
	}

	public function testFetchMultipleFeeds () {
		$programmes = array(
			array('program'=>'programme 1 here'),
			array('program'=>'programme 2 here'),
			array('program'=>'programme 3 here')
		);
		$params = array('someParam'=>'someValue');
		$feedName = 'home/highlights';

		$responses = array(
			(object) array('programmes' => array($programmes[0])),
			(object) array('programmes' => array($programmes[1])),
			(object) array('programmes' => array($programmes[2]))
		);

		$stub = $this->getMock('Bamboo\Client');
		$stub
			->method('requestAll')
			->with($this->identicalTo(array(
				array($feedName, $params),
				array($feedName, $params),
				array($feedName, $params)
			)))
			->will($this->returnValue($responses));

		Client::$instance = $stub;

		$feed = new FakeParallelFeed(array(
			$feedName,
			$feedName,
			$feedName
		), $params);

		$this->assertCount(3, $feed->_responses);
		$this->assertEquals($programmes[1], $feed->_responses[1]->programmes[0]);
	}
}

class FakeParallelFeed extends \Bamboo\Feeds\BaseParallel {
	public $_responses;
	public function __construct ($feeds, $params) {
		$this->_feeds = $feeds;
		parent::__construct($params);
	}
}
