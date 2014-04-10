<?php

namespace Bamboo\Feeds\Exception;

use Bamboo\Feeds\Exception;

class MethodNotAllowed extends Exception
{
    protected $_defaultCode = 405;
}