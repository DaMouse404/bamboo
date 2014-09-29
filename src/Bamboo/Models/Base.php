<?php

namespace Bamboo\Models;

class Base
{
  const TIME_FORMAT = "Y-m-d\TH:i:s.000\Z";

  protected $_id;
  protected $_title;

  public function __construct($object) {
    if ($object) {
      foreach (get_object_vars($object) as $key => $value) {
        $key = "_$key";
        $this->$key = $value;
      }
    }
  }

  public function getId() {
    return $this->_id;
  }

  public function getTitle() {
    return $this->_title;
  }

}