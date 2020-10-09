<?php

namespace Oforge\Engine\Core\Exceptions;

use Oforge\Engine\Core\Exceptions\Basic\NotFoundException;

/**
 * Class ServiceNotFoundException
 *
 * @package Oforge\Engine\Core\Exceptions
 */
class ServiceNotFoundException extends NotFoundException {

    /**
     * ServiceNotFoundException constructor.
     *
     * @param string $serviceName
     */
    public function __construct(string $serviceName) {
        parent::__construct("Service with name '$serviceName' not found!");
    }

}
