<?php

namespace Oforge\Engine\Core\Exceptions\Basic;

use Exception;

/**
 * Class NotFoundException
 *
 * @package Oforge\Engine\Core\Exceptions
 */
class NotFoundException extends Exception {

    /**
     * NotFoundException constructor.
     *
     * @param string $message
     */
    public function __construct(string $message) {
        parent::__construct($message);
    }

}
