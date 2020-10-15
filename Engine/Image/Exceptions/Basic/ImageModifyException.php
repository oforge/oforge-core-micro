<?php

namespace Oforge\Engine\Image\Exceptions\Basic;

use Throwable;

/**
 * Class ImageModifyException
 *
 * @package Oforge\Engine\Image\Exceptions
 */
class ImageModifyException extends ImageException {

    /**
     * ImageModifyException constructor.
     *
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct($message, Throwable $previous = null) {
        parent::__construct($message, $previous);
    }

}
