<?php

namespace Oforge\Engine\Cache\Helper;

use Oforge\Engine\Cache\Lib\ArrayCache;
use Oforge\Engine\Cache\Lib\ArrayNestedCache;

class Cache {

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
