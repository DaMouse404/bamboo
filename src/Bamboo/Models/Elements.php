<?php

namespace Bamboo\Models;

class Elements extends Base {
  
  public $type;
  public $synopses;
  public $master_brand;
  public $images;

  public function getType() {
      return $this->type;
  }
    
    /**
     * Get the short synopsis (if available)
     *
     * @return string
     */
    public function getShortSynopsis() {
        if (isset($this->synopses->small)) {
            return $this->synopses->small;
        }
        return "";
    }

    /**
     * Get the standard image url for an element
     *
     * @param string|int $width  Desired width of image (default 336).
     *                           if not an integer indictes a recipe name
     * @param int $height Desired height of image (default 189)
     *
     * @return string
     */
    public function getStandardImage($width = 336, $height = 189) {
        if (isset($this->images->standard)) {
            return str_replace('{recipe}', $this->_getRecipe($width, $height), $this->images->standard);
        }
        return "";
    }

    /**
     * Get the master brand name
     *
     * @return string
     */
    public function getMasterBrand() {
        // @codingStandardsIgnoreStart
        if (isset($this->master_brand->titles)) {
            $titles = $this->master_brand->titles;
            if (isset($titles->small)) {
                return $titles->small;
            }
        }
        // @codingStandardsIgnoreEnd
        return "";
    }

    /**
     * Get the master brand id
     *
     * @return string
     */
    public function getMasterBrandId() {
        // @codingStandardsIgnoreStart
        if (isset($this->master_brand->id)) {
            return $this->master_brand->id;
        }
        // @codingStandardsIgnoreEnd
        return "";
    }

    /**
     * Returns the recipe for a specific width x height or a recipe name
     *
     * @param string|int $width  Desired width of image
     *                           if not an integer it's a recipe name
     * @param int $height Desired height of image
     * @access private
     * @return void
     */
    private function _getRecipe($width, $height) {
        return is_numeric($width) ? "{$width}x{$height}":$width;
    }

    /**
     * Get the $type image url for an element
     *
     * @param string $type Type of image to get (standard|vertical|portrait)
     * @param string|int $width  Desired width of image (default 336).
     *                           if not an integer indicates a recipe name
     * @param int $height Desired height of image (default 581)
     *
     * @return string
     */
    public function getImage($type = 'standard', $width = 336, $height = 581) {
        if (isset($this->images->{$type})) {
            return str_replace('{recipe}', $this->_getRecipe($width, $height), $this->images->{$type});
        }
        return "";
    }

}