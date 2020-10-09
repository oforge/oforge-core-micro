<?php

namespace Oforge\Engine\File\Exceptions;

use Exception;

/**
 * Class MimeTypeNotAllowedException
 *
 * @package Oforge\Engine\File\Exceptions
 */
class MimeTypeNotAllowedException extends Exception {

    /**
     * MimeTypeNotAllowedException constructor.
     *
     * @param string $mimeType
     */
    public function __construct(string $mimeType) {
        parent::__construct("Mime type '$mimeType' of file not allowed!");
    }

}
