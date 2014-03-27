<?php

namespace Bamboo\Models;

class Base {

    public $id;
    public $title;
    
    public function getId() {
      return $this->id;
    }

    public function getTitle() {
      return $this->title;
    }

	public function __construct($object) {
		foreach (get_object_vars($object) as $key => $value) {
			$this->$key = $value;
      	}
	}
}