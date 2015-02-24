<?php

namespace Bamboo\Models;

use Bamboo\Configuration;

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
        return $this->_getAdjustedUrl($this->getImage('standard', $width, $height));
    }

    /**
     * Get the raw standard image for an element. This is a url which includes the recipe.
     *
     * @return string
     */
    public function getStandardImageRecipe() {
        if (isset($this->_images->standard) && !empty($this->_images->standard)) {
            return $this->_getAdjustedUrl($this->_images->standard);
        }

        return Configuration::getPlaceholderImageUrl();
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
        if (isset($this->_images->$type) && !empty($this->_images->$type)) {
            return $this->_getAdjustedUrl(
                str_replace(
                    '{recipe}',
                    $this->_getRecipe($width, $height),
                    $this->_images->$type
                )
            );
        }

        return Configuration::getPlaceholderImageUrl();
    }

    /**
     * Get the master brand name
     *
     * @return string
     */
    public function getMasterBrand() {
        // @codingStandardsIgnoreStart
        if (isset($this->_master_brand)) {
            return new MasterBrand($this->_master_brand);
        }
        // @codingStandardsIgnoreEnd
        return new MasterBrand((object) array());
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

    /**
     * Replaces the image host with the image host specified within the Configuration class
     *
     * @param  string  $src  The original URL of the image
     * @access private
     * @return string        The adjusted URL of the image, having replaced the original host
     *                       the custom imageHost specified within the Configuration class
     */
    private function _getAdjustedUrl($src) {
        $customImageHost = Configuration::getCustomImageHost();

        if (!!$customImageHost) {
            $src = preg_replace('/[a-zA-Z]+:\/\/[a-zA-Z.\-]+/', $customImageHost, $src);
        }

        return $src;
    }

}
