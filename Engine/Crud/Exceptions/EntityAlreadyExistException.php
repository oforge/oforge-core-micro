<?php

namespace Oforge\Engine\Crud\Exceptions;

use Oforge\Engine\Core\Exceptions\Basic\AlreadyExistException;

/**
 * Class EntityAlreadyExistException
 *
 * @package Oforge\Engine\Crud\Exceptions
 */
class EntityAlreadyExistException extends AlreadyExistException {

    /**
     * EntityAlreadyExistException constructor.
     *
     * @param string $class
     * @param int|string $id
     */
    public function __construct(string $class, $id) {
        parent::__construct("Entity of type '$class' with ID '$id' already exist!");
    }

}
