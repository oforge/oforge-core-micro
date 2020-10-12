<?php

namespace Oforge\Engine\File\Exceptions;

use Exception;

/**
 * Class FileInUsageException
 *
 * @package Oforge\Engine\File\Exceptions
 */
class FileInUsageException extends Exception {

    /**
     * FileInUsageException constructor.
     *
     * @param int $fileID
     */
    public function __construct(int $fileID) {
        parent::__construct("File with ID '$fileID' is in use.");
    }

}
