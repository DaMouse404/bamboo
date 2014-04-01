<?php

namespace Bamboo\Models;

class Category extends Base
{

  protected $_kind = "";
  // @codingStandardsIgnoreStart
  protected $_child_episode_count = 0;
  // @codingStandardsIgnoreEnd

  public function getKind() {
      return $this->_kind;
  }

  /**
   * Get the number of episodes for a category
   *
   * @return int
   */
  public function getChildEpisodeCount() {
      // @codingStandardsIgnoreStart
      return $this->_child_episode_count;
      // @codingStandardsIgnoreEnd
  }


  /**
   * Returns whether this category is a children's category
   * @return bool
   */
  public function isChildrens() {
      return $this->_id == 'cbbc' || $this->_id == 'cbeebies';
  }
  
}