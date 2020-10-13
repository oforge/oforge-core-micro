<?php

namespace Oforge\Engine\Cache\Lib;

/**
 * Class ArrayCache
 *
 * @package Oforge\Engine\Cache\Lib
 */
class ArrayCache {
    /** @var array $cache */
    private $cache = [];

    /**
     * @param int|string $key
     *
     * @return bool
     */
    public function exist($key) : bool {
        return isset($this->cache[$key]);
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

        return $this->cache[$key];
    }

    /**
     * @param int|string $key
     *
     * @return ArrayCache
     */
    public function unset($key) : ArrayCache {
        if (self::exist($key)) {
            unset($this->cache[$key]);
        }

        return $this;
    }

    /**
     * @param int|string $key
     * @param mixed $value
     *
     * @return ArrayCache
     */
    public function set($key, $value) : ArrayCache {
        $this->cache[$key] = $value;

        return $this;
    }

}
