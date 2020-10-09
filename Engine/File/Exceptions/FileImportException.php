<?php

namespace Oforge\Engine\File\Exceptions;

use Exception;
use Throwable;

/**
 * Class FileImportException
 *
 * @package Oforge\Engine\File\Exceptions
 */
class FileImportException extends Exception {

    /**
     * FileImportException constructor.
     *
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct(string $message, Throwable $previous = null) {
        parent::__construct($message, 0, $previous);
    }

}
