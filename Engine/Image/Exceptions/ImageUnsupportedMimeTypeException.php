<?php

namespace Oforge\Engine\Image\Exceptions;

use Oforge\Engine\Image\Exceptions\Basic\ImageException;
use Throwable;

/**
 * Class ImageUnsupportedMimeTypeException
 *
 * @package Oforge\Engine\Image\Exceptions
 */
class ImageUnsupportedMimeTypeException extends ImageException {

    /**
     * ImageUnsupportedMimeTypeException constructor.
     *
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct($message, Throwable $previous = null) {
        parent::__construct($message, $previous);
    }

}
