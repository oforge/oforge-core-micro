<?php

namespace Oforge\Engine\Image\Exceptions;

use Oforge\Engine\Image\Exceptions\Basic\ImageIOException;
use Throwable;

/**
 * Class ImageLoadException
 *
 * @package Oforge\Engine\Image\Exceptions
 */
class ImageLoadException extends ImageIOException {

    /**
     * ImageLoadException constructor.
     *
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct($message, Throwable $previous = null) {
        parent::__construct($message, $previous);
    }

}
