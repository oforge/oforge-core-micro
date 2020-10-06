<?php

namespace Oforge\Engine\Core\Exceptions\Basic;

use Exception;

/**
 * Class AlreadyExistException
 *
 * @package Oforge\Engine\Core\Exceptions\Basic
 */
class AlreadyExistException extends Exception {

    /**
     * AlreadyExistException constructor.
     *
     * @param string $message
     */
    public function __construct(string $message) {
        parent::__construct($message);
    }

}
