<?php

namespace Oforge\Engine\Core\Exceptions;

/**
 * Class ConfigElementAlreadyExistException
 *
 * @package Oforge\Engine\Core\Exceptions
 */
class ConfigElementAlreadyExistException extends \Exception {

    /**
     * ServiceNotFoundException constructor.
     *
     * @param string $name
     */
    public function __construct(string $name) {
        parent::__construct("Config element '$name' already exist!");
    }

}
