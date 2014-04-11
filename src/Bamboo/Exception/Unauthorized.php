<?php

namespace Bamboo\Exception;

use Bamboo\Exception;

class Unauthorized extends Exception
{
    protected $_defaultCode = 403;
}
