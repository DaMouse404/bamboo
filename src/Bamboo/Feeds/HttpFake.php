<?php

namespace Bamboo\Feeds;

use Bamboo\Feeds\Client;

class HttpFake {
	public function get() {
		//setup request object
		return $this;
	}

	public function send() {
		//grab json from fixture
	}

	public function json() {
		//return body of fixture, return array of data
	}
}
