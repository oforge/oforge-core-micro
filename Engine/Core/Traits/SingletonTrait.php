<?php

namespace Oforge\Engine\Core\Traits;

/**
 * Singleton trait classes.
 *
 * @package Oforge\Engine\Core\Traits
 */
trait SingletonTrait {
    /** @var static $instance */
    private static $instance;

    /**
     * Get or create and get singleton instance.
     *
     * @return static
     */
    public static function getInstance() {
        $class = get_called_class();
        if (!isset(self::$instance)) {
            self::$instance = new $class();
        }

        return self::$instance;
    }

}
