<?php

namespace Oforge\Engine\Core\Exceptions;

use Exception;

/**
 * Class DependyNotResolvedException
 *
 * @package Oforge\Engine\Core\Exceptions
 */
class DependencyNotResolvedException extends Exception {
    public function __construct(string $pluginName) {
        parent::__construct('Dependency for ' . $pluginName . ' could not be resolved.');
    }
}
