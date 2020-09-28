<?php

namespace Oforge\Engine\Core\Exceptions;

/**
 * Class ConfigOptionKeyNotExistException
 *
 * @package Oforge\Engine\Core\Exceptions
 */
class ConfigOptionKeyNotExistException extends \Exception {

    /**
     * ServiceNotFoundException constructor.
     *
     * @param string $name
     */
    public function __construct(string $name) {
        parent::__construct("Config key '$name' not found in options");
    }

}
