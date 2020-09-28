<?php

namespace Oforge\Engine\Auth\Enums;

use Exception;

/**
 * Class InvalidPasswordFormatException
 *
 * @package Oforge\Engine\Auth\Enums
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
