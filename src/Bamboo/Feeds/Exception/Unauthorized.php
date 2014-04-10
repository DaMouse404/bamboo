<?php

namespace Bamboo\Feeds\Exception;

use Bamboo\Feeds\Exception;

class Unauthorized extends Exception
{
    protected $_defaultCode = 403;
}