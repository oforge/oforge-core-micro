<?php

namespace Oforge\Engine\Core\Exceptions;

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
     * @param string $text
     */
    public function __construct(string $text) {
        parent::__construct($text);
    }

}
