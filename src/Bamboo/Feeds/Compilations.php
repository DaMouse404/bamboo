<?php

namespace Bamboo\Feeds;

use Bamboo\Models;

class Compilations extends Base
{

    protected $_feed = 'compilations/{compilation_id}/groups';
    protected $_response;

    public function __construct($compilationId) {
        $this->_feed = str_replace("{compilation_id}", $compilationId, $this->_feed);
        parent::__construct();
    }

    public function getCompilation() {
        return new Models\Compilation($this->_response->compilation_groups->compilation);
    }

    /**
     * Get the number of groups within this group object
     */
    public function getGroupCount() {
        return count($this->getGroups());
    }

    public function getTotalGroupCount() {
        // Until this feed paginates there are only the current groups
        return $this->getGroupCount();
    }

    /**
     * Return array of Group models
     */
    public function getGroups() {
        return array_map(
            function($group) {
                return new Models\Group($group);
            }, $this->_response->compilation_groups->elements
        );
    }

}
