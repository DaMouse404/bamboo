<?php

namespace Bamboo\Feeds\Exception;

use Bamboo\Feeds\Exception;

class ClientError extends Exception
{
    protected $_defaultCode = 400;
}