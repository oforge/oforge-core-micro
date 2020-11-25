<?php

namespace Oforge\Engine\Auth\Exceptions\Role;

use Oforge\Engine\Core\Exceptions\Basic\AlreadyExistException;

/**
 * Class RoleAlreadyExistException
 *
 * @package Oforge\Engine\Auth\Exceptions\Role
 */
class RoleAlreadyExistException extends AlreadyExistException {

    /**
     * RoleAlreadyExistException constructor.
     *
     * @param string $name
     */
    public function __construct(string $name) {
        parent::__construct("Role with name '$name' already exists.");
    }

}
