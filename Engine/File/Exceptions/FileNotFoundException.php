<?php

namespace Oforge\Engine\File\Exceptions;

use Oforge\Engine\Core\Exceptions\NotFoundException;

/**
 * Class FileNotFoundException
 *
 * @package Oforge\Engine\File\Exceptions
 */
class FileNotFoundException extends NotFoundException {

    /**
     * FileNotFoundException constructor.
     *
     * @param string $filePath
     */
    public function __construct(string $filePath) {
        parent::__construct('File not found: ' . $filePath);
    }

}
