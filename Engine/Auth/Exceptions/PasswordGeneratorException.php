<?php

namespace Oforge\Engine\Auth\Exceptions;

use Exception;
use Throwable;

/**
 * Class PasswordGenerateException
 *
 * @package Oforge\Engine\Auth\Exceptions
 */
class PasswordGeneratorException extends Exception {

    /**
     * PasswordGenerateException constructor.
     *
     * @param Throwable $previous
     */
    public function __construct(Throwable $previous) {
        parent::__construct($previous->getMessage(), 0, $previous);
    }

}
