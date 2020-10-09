<?php

namespace Oforge\Engine\Core\Exceptions;

use Oforge\Engine\Core\Exceptions\Basic\NotFoundException;

/**
 * Class ParentNotFoundException
 *
 * @package Oforge\Engine\Core\Exceptions
 */
class ParentNotFoundException extends NotFoundException {

    /**
     * ConfigElementNotFoundException constructor.
     *
     * @param string $parentName
     */
    public function __construct(string $parentName) {
        parent::__construct("Parent element with name '$parentName' not found!");
    }

}
