<?php

namespace Oforge\Engine\Auth\Exceptions\User;

use Oforge\Engine\Core\Exceptions\Basic\NotFoundException;

/**
 * Class UserNotFoundException
 *
 * @package Oforge\Engine\Auth\Exceptions\User
 */
class UserNotFoundException extends NotFoundException {

    /**
     * UserNotFoundException constructor.
     *
     * @param string $key
     * @param int|string $value
     */
    public function __construct(string $key, $value) {
        parent::__construct("User ($key: $value) not found!");
    }

}
