<?php

namespace Bamboo\Feeds;

class Base
{

    public function __construct($params = array()) {
        $this->_response = Client::getInstance()->request(
            $this->_feed, $params
        );
    }

    protected function _className($string) {
        $words = explode('_', mb_strtoupper($string, "UTF-8"));

        $return = "Bamboo\Models\\";
        $count = 0;
        foreach ($words as $word) {
            if ($count > 0) {
              $return .=  "\\" . $this->_ucFirstChar($word);
            } else {
              $return .=  $this->_ucFirstChar($word);
            }
            $count++;
        }
        return $return;
    }

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