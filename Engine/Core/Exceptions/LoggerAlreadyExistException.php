<?php

namespace Oforge\Engine\Core\Exceptions;

use Oforge\Engine\Core\Exceptions\Basic\AlreadyExistException;

/**
 * Class LoggerAlreadyExistException
 *
 * @package Oforge\Engine\Core\Exceptions
 */
class LoggerAlreadyExistException extends AlreadyExistException {

    /**
     * LoggerAlreadyExistException constructor.
     *
     * @param string $loggerName
     */
    public function __construct(string $loggerName) {
        parent::__construct("Logger with name '$loggerName' already exist!");
    }

}
