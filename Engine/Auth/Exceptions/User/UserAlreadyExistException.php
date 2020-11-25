<?php

namespace Oforge\Engine\Auth\Exceptions\User;

use Oforge\Engine\Core\Exceptions\Basic\AlreadyExistException;

/**
 * Class UserAlreadyExistException
 *
 * @package Oforge\Engine\Auth\Exceptions\User
 */
class UserAlreadyExistException extends AlreadyExistException {

    /**
     * UserAlreadyExistException constructor.
     *
     * @param string $login
     */
    public function __construct(string $login) {
        parent::__construct("User with login '$login' already exists.");
    }

}
