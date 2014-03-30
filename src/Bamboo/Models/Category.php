<?php

namespace Bamboo\Models;

class Category extends Base {

  protected $_kind = "";
  protected $_child_episode_count = 0;

  public function getKind() {
      return $this->_kind;
  }

  /**
   * Get the number of episodes for a category
   *
   * @return int
   */
  public function getChildEpisodeCount() {
      return $this->_child_episode_count;
  }


  /**
   * Returns whether this category is a children's category
   * @return bool
   */
  public function isChildrens() {
      return $this->_id == 'cbbc' || $this->_id == 'cbeebies';
  }
  
}