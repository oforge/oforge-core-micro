<?php

namespace Oforge\Engine\Auth\Exceptions;

use Exception;

/**
 * Class InvalidPasswordFormatException
 *
 * @package Oforge\Engine\Auth\Exceptions
 */
class InvalidPasswordFormatException extends Exception {

    /**
     * InvalidPasswordFormatException constructor.
     *
     * @param string $message
     */
    public function __construct($message = '') {
        parent::__construct($message);
    }

}
