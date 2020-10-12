<?php

namespace Oforge\Engine\Crud\Exceptions;

use Oforge\Engine\Core\Exceptions\Basic\NotFoundException;

/**
 * Class EntityNotFoundException
 *
 * @package Oforge\Engine\Crud\Exceptions
 */
class EntityNotFoundException extends NotFoundException {

    /**
     * EntityNotFoundException constructor.
     *
     * @param string $class
     * @param int|string $id
     */
    public function __construct(string $class, $id) {
        parent::__construct("Entity of type '$class' with ID '$id' not found!");
    }

}
