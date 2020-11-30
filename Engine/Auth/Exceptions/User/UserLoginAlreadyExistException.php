<?php

namespace Oforge\Engine\Auth\Exceptions\User;

use Oforge\Engine\Core\Exceptions\Basic\AlreadyExistException;

/**
 * Class UserLoginAlreadyExistException
 *
 * @package Oforge\Engine\Auth\Exceptions\User
 */
class UserLoginAlreadyExistException extends AlreadyExistException {

    /**
     * UserLoginAlreadyExistException constructor.
     *
     * @param string $login
     */
    public function __construct(string $login) {
        parent::__construct("User with login '$login' already exists.");
    }

}
