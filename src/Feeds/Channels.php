<?php

namespace Guzzle\Http;
namespace Bamboo\Feeds;

use Bamboo\Models\Episode;
//so Bamboo_Models_Episode as new Episode()

class Channels extends Base {
    $_feed = 'channels';

    public function __construct() {
        die('1');
        parent::__construct();
    }

}