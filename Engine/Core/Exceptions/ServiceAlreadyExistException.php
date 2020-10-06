<?php

namespace Oforge\Engine\Core\Exceptions;

use Oforge\Engine\Core\Exceptions\Basic\AlreadyExistException;

/**
 * Class ServiceAlreadyDefinedException
 *
 * @package Oforge\Engine\Core\Exceptions
 */
class ServiceAlreadyExistException extends AlreadyExistException {

    /**
     * ServiceNotFoundException constructor.
     *
     * @param string $serviceName
     */
    public function __construct(string $serviceName) {
        parent::__construct("Service with name '$serviceName' already exist!");
    }

}
