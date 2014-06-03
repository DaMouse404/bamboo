<?php

namespace Bamboo\Feeds;

use Bamboo\Client;

class Base
{

    public function __construct($params = array()) {
        $this->_response = Client::getInstance()->request(
            $this->_feed, $params
        );
    }

    /*
     * Translate Class name into Model name.
     *  $words[0] as most only have first index.
     *  Exept Programme and Group which have 'Large' at index 2
     *
     * @return string $return
     */
    protected function _className($string) {
        $words = explode('_', mb_strtoupper($string, "UTF-8"));
        $return = "Bamboo\Models\\";
        $return .=  $this->_ucFirstChar($words[0]);
        return $return;
    }

    /*
     * Upper case first character, lower case all following.
     *
     * @return string
     */
    private function _ucFirstChar($string) {
        $encoding = "UTF-8";

        return mb_strtoupper(
            mb_substr($string, 0, 1, $encoding),
            $encoding
        ) .
        mb_strtolower(
            mb_substr(
                $string, 1, mb_strlen($string),
                $encoding
            ),
            $encoding
        );
    }

}
