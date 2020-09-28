<?php

namespace Oforge\Engine\Core\Exceptions;

/**
 * Class ConfigElementNotFoundException
 *
 * @package Oforge\Engine\Core\Exceptions
 */
class ConfigElementNotFoundException extends \Exception {

    /**
     * ConfigElementNotFoundException constructor.
     *
     * @param string $name
     * @param string|null $scope
     */
    public function __construct(string $name, ?string $scope) {
        parent::__construct("Config key $name not found for scope $scope");
    }

}
