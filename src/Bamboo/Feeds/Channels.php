<?php

//namespace Guzzle\Http;
namespace Bamboo\Feeds;

//include('../../../webapp/php/lib/vendor/bbc/bamboo2/src/Feeds/Base.php');

//use Bamboo\Models\Episode;
//so Bamboo_Models_Episode as new Episode()

class Channels extends Base {
    public $_feed = 'channels';

    public function __construct() {
        parent::__construct();
    }

}