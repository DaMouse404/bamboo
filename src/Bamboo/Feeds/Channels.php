<?php

namespace Bamboo\Feeds;

//use Bamboo\Models\Episode;
//so Bamboo_Models_Episode as new Episode()

class Channels {
    public $_feed = 'channels';

    public function __construct() {
 	    $response = Base::getInstance()->request($this->_feed);
 	   	var_dump($response);
 	    die('req');
    }

}