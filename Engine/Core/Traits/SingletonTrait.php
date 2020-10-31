<?php

namespace Oforge\Engine\Core\Traits;

/**
 * Trait SingletonTrait.
 *
 * @package Oforge\Engine\Core\Traits
 */
trait SingletonTrait {
    /** @var static $instance */
    private static $instances = [];

    /**
     * Get or create and get singleton instance.
     *
     * @return static
     */
    public static function getInstance() {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class();
        }

        return self::$instances[$class];
    }

}
