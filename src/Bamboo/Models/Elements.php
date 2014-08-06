<?php

namespace Bamboo\Models;

class Elements extends Base
{

    protected $_type = '';
    protected $_synopses;
    protected $_images;
    // @codingStandardsIgnoreStart
    protected $_master_brand;
    // @codingStandardsIgnoreEnd
    protected $_status = "";

    /**
     * Get the short synopsis (if available)
     *
     * @return string
     */
    public function getShortSynopsis() {
        if (isset($this->_synopses->small)) {
            return $this->_synopses->small;
        }
        return "";
    }

    /**
     * Get the medium synopsis (if available)
     *
     * @return string
     */
    public function getMediumSynopsis() {
        if (isset($this->_synopses->medium)) {
            return $this->_synopses->medium;
        }
        return "";
    }

    /**
     * Get the large synopsis (if available)
     *
     * @return string
     */
    public function getLargeSynopsis() {
        if (isset($this->_synopses->large)) {
            return $this->_synopses->large;
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
        if (isset($this->_images->standard)) {
            return str_replace(
                '{recipe}', $this->_getRecipe($width, $height),
                $this->_images->standard
            );
        }
        return "";
    }

    /**
     * Get the raw standard image for an element. This is a url which includes the recipe.
     *
     * @return string
     */
    public function getStandardImageRecipe() {
        if (isset($this->_images->standard)) {
            return $this->_images->standard;
        }
        return "";
    }

    /**
     * Get the portrait image url for an element
     *
     * @param string|int $width  Desired width of image (default 336).
     *                           if not an integer indicates a recipe name
     * @param int $height Desired height of image (default 581)
     *
     * @return string
     */
    public function getPortraitImage($width = 336, $height = 581) {
        if (isset($this->_images->portrait)) {
            return str_replace(
                '{recipe}',
                $this->_getRecipe($width, $height),
                $this->_images->portrait
            );
        }
        return "";
    }

    /**
     * Get the raw portrait image for an element. This is a url which includes the recipe.
     *
     * @return string
     */
    public function getPortraitImageRecipe() {
        if (isset($this->_images->portrait)) {
            return $this->_images->portrait;
        }
        return "";
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
        if (isset($this->_images->$type)) {
            return str_replace(
                '{recipe}',
                $this->_getRecipe($width, $height),
                $this->_images->$type
            );
        }
        return "";
    }

    /**
     * Get the raw $type image for an element. This is a url which includes the recipe
     *
     * @param string $type Type of image to get (standard|vertical|portrait)
     * @return string
     */
    public function getImageRecipe($type) {
        if (isset($this->_images->$type)) {
            return $this->_images->portrait;
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
        if (isset($this->_master_brand->titles)) {
            $titles = $this->_master_brand->titles;
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
        if (isset($this->_master_brand->id)) {
            return $this->_master_brand->id;
        }
        // @codingStandardsIgnoreEnd
        return "";
    }

    /**
     * Get the 'medium' master brand label
     *
     * @return string
     */
    public function getMediumMasterBrand() {
        // @codingStandardsIgnoreStart
        if (isset($this->_master_brand->titles)) {
            $titles = $this->_master_brand->titles;
            if (isset($titles->medium)) {
                return $titles->medium;
            }
        }
        // @codingStandardsIgnoreEnd
        return "";
    }

    /**
     * Get the master brand attribution
     *
     * @return string
     */
    public function getMasterBrandAttribution() {
        // @codingStandardsIgnoreStart
        if (isset($this->_master_brand->attribution)) {
            return $this->_master_brand->attribution;
        }
        // @codingStandardsIgnoreEnd
        return "";
    }

    /**
     * Get the master brand ident's id
     *
     * @return string
     */
    public function getMasterBrandIdentId() {
        // @codingStandardsIgnoreStart
        if (isset($this->_master_brand->ident_id)) {
            return $this->_master_brand->ident_id;
        }
        // @codingStandardsIgnoreEnd
        return "";
    }

    /**
     * Get the element type
     *
     * @return string
     */
    public function getType() {
        return $this->_type;
    }

    /**
     * Get the programme status
     *
     * @return String
     */
    public function getStatus() {
        return $this->_status;
    }

    /**
     * Is the element status set to 'available'
     *
     * @return bool
     */
    public function isComingSoon() {
        return $this->_status === 'unavailable';
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

}
