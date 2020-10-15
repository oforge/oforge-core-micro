<?php

namespace Oforge\Engine\Image\Exceptions;

use Oforge\Engine\Image\Exceptions\Basic\ImageModifyException;
use Throwable;

/**
 * Class ImageConvertException
 *
 * @package Oforge\Engine\Image\Exceptions
 */
class ImageConvertException extends ImageModifyException {

    /**
     * ImageConvertException constructor.
     *
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct($message, Throwable $previous = null) {
        parent::__construct($message, $previous);
    }

}
