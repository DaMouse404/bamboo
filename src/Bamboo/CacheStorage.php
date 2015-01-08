<?php

namespace Bamboo;

use Guzzle\Plugin\Cache\DefaultCacheStorage;
use Guzzle\Http\EntityBodyInterface;

class CacheStorage extends DefaultCacheStorage
{

    /**
     * Create a cache key for a response's body, ignoring the body itself
     *
     * @param string              $url  URL of the entry
     * @param EntityBodyInterface $body Response body
     *
     * @return string
     */
    protected function getBodyKey($url, EntityBodyInterface $body)
    {
        return $this->keyPrefix . md5($url);
    }

}
