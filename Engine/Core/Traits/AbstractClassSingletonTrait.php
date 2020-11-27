<?php

namespace Oforge\Engine\Core\Traits;

/**
 * Singleton trait for abstract classes.
 *
 * @package Oforge\Engine\Core\Traits
 */
trait AbstractClassSingletonTrait {
    /** @var array<string,object> $instance */
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
