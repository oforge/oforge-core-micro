<?php

namespace Oforge\Engine\Image\Exceptions;

use Oforge\Engine\Image\Exceptions\Basic\ImageIOException;
use Throwable;

/**
 * Class ImageSaveException
 *
 * @package Oforge\Engine\Image\Exceptions
 */
class ImageSaveException extends ImageIOException {

    /**
     * ImageSaveException constructor.
     *
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct($message, Throwable $previous = null) {
        parent::__construct($message, $previous);
    }

}
