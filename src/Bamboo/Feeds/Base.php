<?php

namespace Bamboo\Feeds;


class Base {

    public function __construct($params = array()) {
 	    $this->_response = Client::getInstance()->request(
 	    	$this->_feed, $params
 	    );
    }

	protected function _className($string) {
		$words = explode('_', strtolower($string));

		$return = "Bamboo\Models\\";
		$count = 0;
		foreach ($words as $word) {
			if ($count > 0) {
			  $return .=  "\\" . ucfirst(trim($word));
			} else {
			  $return .=  ucfirst(trim($word));
			}
			$count++;
		}
		return $return;
    }

}