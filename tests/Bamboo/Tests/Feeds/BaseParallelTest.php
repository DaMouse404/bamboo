<?php

namespace Bamboo\Tests;

use \Bamboo\Client;

class BaseParllelTest extends BambooTestCase
{
	private $originalClient;
	public function setUp() {
		$this->originalClient = clone Client::$instance;

	}
	public function tearDown() {
		Client::$instance = $this->originalClient;
	}

	public function testFetchSingleFeed () {
		$singleResponse = (object) array(
			'programmes' => array('programm'=>'programmes here')
		);
		$params =array('someParam'=>'someValue');
		$feedName = 'home/highlights';

		$stub = $this->getMock('Bamboo\Client');
		$stub
			->expects($this->once())
			->method('request')
			->with($this->equalTo($feedName), $this->identicalTo($params))
			->will($this->returnValue($singleResponse));

		Client::$instance = $stub;

		$feed = new FakeParallelFeed(array($feedName), $params);

		$this->assertEquals($singleResponse, $feed->_response);
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

		$this->assertEquals($programmes, $feed->_response->programmes);
	}
}

class FakeParallelFeed extends \Bamboo\Feeds\BaseParallel {
	public function __construct ($feeds, $params) {
		$this->_feeds = $feeds;
		parent::__construct($params);
	}
}