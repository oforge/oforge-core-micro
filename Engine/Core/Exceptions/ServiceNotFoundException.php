<?php

namespace Oforge\Engine\Core\Exceptions;

use Exception;

/**
 * Class ServiceNotFoundException
 *
 * @package Oforge\Engine\Core\Exceptions
 */
class ServiceNotFoundException extends Exception {

    /**
     * ServiceNotFoundException constructor.
     *
     * @param string $serviceName
     */
    public function __construct(string $serviceName) {
        parent::__construct("Service with name '$serviceName' not found!");
    }

}
