<?php

namespace Oforge\Engine\Core\Controller\Traits;

/**
 * Trait TraitInitializer
 *
 * @package Oforge\Engine\Core\Controller\Traits
 */
trait TraitInitializer {

    /**
     * @param string $prefix
     * @param string $suffix
     * @param mixed ...$params
     */
    protected function callTraitMethod(string $prefix, string $suffix = '', ...$params) {
        $classTraits = class_uses(static::class);
        foreach ($classTraits as $classTrait) {
            $traitSimpleClassName = ltrim(substr($classTrait, strrpos($classTrait, '\\')), '\\');
            $methodName           = $prefix . $traitSimpleClassName . $suffix;
            if (method_exists($this, $methodName)) {
                call_user_func([$this, $methodName], $params);
            }
        }
    }

}
