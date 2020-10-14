<?php

namespace Oforge\Engine\Image\Exceptions;

use Oforge\Engine\Image\Exceptions\Basic\ImageIOException;

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
     */
    public function __construct($message) {
        parent::__construct($message);
    }

}
