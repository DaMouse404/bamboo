<?php

namespace Bamboo\Models;

class Category extends Base {

  public $kind = "";
  public $child_episode_count = 0;

  public function getKind() {
      return $this->kind;
  }

  /**
   * Get the number of episodes for a category
   *
   * @return int
   */
  public function getChildEpisodeCount() {
      return $this->child_episode_count;
  }


  /**
   * Returns whether this category is a children's category
   * @return bool
   */
  public function isChildrens() {
      return $this->id == 'cbbc' || $this->id == 'cbeebies';
  }
  
}