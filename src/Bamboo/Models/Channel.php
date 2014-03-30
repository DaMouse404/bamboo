<?php

namespace Bamboo\Models;

class Channel extends Elements {

	public function getUnregionalisedID() {
    if (preg_match('/(bbc_[a-z]+)(_.+)/i', $this->_id, $matches)) {
        return $matches[1];
    }
    return $this->_id;
	}

  public function getSlug() {
      return preg_replace('/[0-9_]/', '', $this->getUnregionalisedID());
  }

  /**
   * Returns whether this channel is a children's channel
   * @return bool
   */
  public function isChildrens() {
      return $this->_id == 'cbbc' || $this->_id == 'cbeebies';
  }

}