<?php

namespace Bamboo\Exception;

use Bamboo\Exception;

class MethodNotAllowed extends Exception
{
    protected $_defaultCode = 405;
}
