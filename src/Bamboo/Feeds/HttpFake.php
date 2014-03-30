<?php

namespace Bamboo\Feeds;

use Bamboo\Feeds\Client;

class HttpFake {

	private $_path;
	private $_response;

	public function get($feed, $params = array(), $queryParams = array()) {
		//setup request object
		$this->_buildPath($feed);

		return $this;
	}

	public function send() {
		//grab json from fixture
		$this->_response = file_get_contents($this->_path);

		return $this;
	}

	public function json() {
		//return body of fixture, return array of data
		return json_decode($this->_response, true);
	}

	private function _buildPath($feed) {
		$feed = str_replace("ibl/v1/" ,"" ,  $feed);
		$feed = str_replace(".json" ,"" ,  $feed);
		$path = dirname(__FILE__) . '/../../../tests/fixtures/';
		$this->_path = $path . $feed . '.200';
	}
}
