<?php

namespace Oforge\Engine\Cache\Managers;

use Oforge\Engine\Cache\Lib\ArrayCache;
use Oforge\Engine\Cache\Lib\ArrayNestedCache;

/**
 * Class CacheManager
 *
 * @package Oforge\Engine\Cache\Managers
 */
class CacheManager {

    /** Prevent Instance */
    private function __construct() {
    }

    /**
     * @return ArrayCache
     */
    public static function initArrayCache() : ArrayCache {
        return new ArrayCache();
    }

    /**
     * @return ArrayNestedCache
     */
    public static function initArrayNestedCache() : ArrayNestedCache {
        return new ArrayNestedCache();
    }

}
