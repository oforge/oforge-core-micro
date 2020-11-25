<?php

namespace Oforge\Engine\Auth\Exceptions\Role;

use Oforge\Engine\Core\Exceptions\Basic\NotFoundException;

/**
 * Class RoleNotFoundException
 *
 * @package Oforge\Engine\Auth\Exceptions\Role
 */
class RoleNotFoundException extends NotFoundException {

    /**
     * RoleNotFoundException constructor.
     *
     * @param string $key
     * @param int|string $value
     */
    public function __construct(string $key, $value) {
        parent::__construct("Role ($key: $value) not found!");
    }

}
