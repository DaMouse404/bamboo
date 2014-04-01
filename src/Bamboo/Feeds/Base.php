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
              $return .=  "\\" . mb_strtoupper(trim($word), "UTF-8");
            } else {
              $return .=  mb_strtoupper(trim($word), "UTF-8");
            }
            $count++;
        }
        return $return;
    }

}