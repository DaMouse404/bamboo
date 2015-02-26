<?php

namespace Bamboo\Models;

class MasterBrand extends Base
{
    protected $_id;
    protected $_titles;
    protected $_attribution;
    // @codingStandardsIgnoreStart
    protected $_ident_id;
    // @codingStandardsIgnoreEnd

    /**
     * Get the 'small' master brand label
     *
     * @return string
     */
    public function getSmallTitle() {
        return $this->_getTitle('small');
    }

    /**
     * Get the 'medium' master brand label
     *
     * @return string
     */
    public function getMediumTitle() {
        return $this->_getTitle('medium');
    }

    /**
     * Get the 'large' master brand label
     *
     * @return string
     */
    public function getLargeTitle() {
        return $this->_getTitle('large');
    }

    /**
     * Get the master brand id
     *
     * @return string
     */
    public function getId() {
        if (isset($this->_id)) {
            return $this->_id;
        }
        return "";
    }

    /**
     * Get the master brand attribution
     *
     * @return string
     */
    public function getAttribution() {
        if (isset($this->_attribution)) {
            return $this->_attribution;
        }
        return "";
    }

    /**
     * Get the master brand ident's id
     *
     * @return string
     */
    public function getIdentId() {
        // @codingStandardsIgnoreStart
        if (isset($this->_ident_id)) {
            return $this->_ident_id;
        }
        // @codingStandardsIgnoreEnd
        return "";
    }

    private function _getTitle($type) {
        if (isset($this->_titles)) {
            $titles = $this->_titles;
            if (isset($titles->$type)) {
                return $titles->$type;
            }
        }
        return "";
    }
}
