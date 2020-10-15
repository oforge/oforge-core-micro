<?php

namespace Oforge\Engine\Image\Exceptions\Basic;

use Throwable;

/**
 * Class ImageIOException
 *
 * @package Oforge\Engine\Image\Exceptions
 */
class ImageIOException extends ImageException {

    /**
     * ImageIOException constructor.
     *
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct($message, Throwable $previous = null) {
        parent::__construct($message, $previous);
    }

}
