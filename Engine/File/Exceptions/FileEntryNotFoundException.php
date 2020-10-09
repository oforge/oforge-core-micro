<?php

namespace Oforge\Engine\File\Exceptions;

use Oforge\Engine\Core\Exceptions\NotFoundException;

/**
 * Class FileEntryNotFoundException
 *
 * @package Oforge\Engine\File\Exceptions
 */
class FileEntryNotFoundException extends NotFoundException {

    /**
     * FileEntryNotFoundException constructor.
     *
     * @param string $key
     * @param string $value
     */
    public function __construct(string $key, string $value) {
        parent::__construct("File entry with '$key' = '$value' not found!");
    }

}
