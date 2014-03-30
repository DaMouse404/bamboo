<?php

namespace Bamboo\Models;

class Base {

  protected $_id;
  protected $_title;
    
  public function __construct($object) {
    foreach (get_object_vars($object) as $key => $value) {
      $key = "_$key";
      $this->$key = $value;
    }
  }
  
  public function getId() {
    return $this->_id;
  }

  public function getTitle() {
    return $this->_title;
  }

}