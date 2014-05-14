<?php

namespace Bamboo;

class Exception extends \Exception
{
    protected $_defaultCode = 500;

    public function __construct($message = '', $code = 0, $previous = null) {
        if ($code == 0) {
            $code = $this->_defaultCode;
        }
        $this->_response = $response;

        parent::__construct($message, (int) $code, $previous);
    }

}