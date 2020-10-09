<?php

namespace Oforge\Engine\Cache\Lib;

use Oforge\Engine\Core\Helper\ArrayHelper;

/**
 * Class ArrayCache2
 *
 * @package Oforge\Engine\Cache\Lib
 */
class ArrayNestedCache {
    /** @var array $cache */
    private $cache = [];

    /**
     * @param int|string $key
     *
     * @return bool
     */
    public function exist($key) : bool {
        return ArrayHelper::dotExist($this->cache, $key);
    }

    /**
     * @param int|string $key
     * @param callable $callable
     *
     * @return mixed
     */
    public function getOrCreate($key, callable $callable) {
        if (!self::exist($key)) {
            $this->set($key, $callable());
        }

        return ArrayHelper::dotGet($this->cache, $key);
    }

    /**
     * @param int|string $key
     *
     * @return ArrayNestedCache
     */
    public function unset($key) : ArrayNestedCache {
        if (self::exist($key)) {
            ArrayHelper::dotUnset($this->cache, $key);
        }

        return $this;
    }

    /**
     * @param int|string $key
     * @param mixed $value
     *
     * @return ArrayNestedCache
     */
    public function set($key, $value) : ArrayNestedCache {
        $this->cache = ArrayHelper::dotSet($this->cache, $key, $value);

        return $this;
    }

}
