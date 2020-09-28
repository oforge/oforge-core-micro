<?php

namespace Oforge\Engine\Core\Exceptions;

use Exception;

/**
 * Class ServiceAlreadyDefinedException
 *
 * @package Oforge\Engine\Core\Exceptions
 */
class ServiceAlreadyExistException extends Exception {

    /**
     * ServiceNotFoundException constructor.
     *
     * @param string $serviceName
     */
    public function __construct(string $serviceName) {
        parent::__construct("A service with name '$serviceName' is already exist!");
    }

}
