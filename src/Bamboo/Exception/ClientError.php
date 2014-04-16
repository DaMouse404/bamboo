<?php

namespace Bamboo\Exception;

use Bamboo\Exception;

class ClientError extends Exception
{
    protected $_defaultCode = 400;
}