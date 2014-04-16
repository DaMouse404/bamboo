<?php

namespace Bamboo\Exception;

use Bamboo\Exception;

class NotFound extends Exception
{
    protected $_defaultCode = 404;
}