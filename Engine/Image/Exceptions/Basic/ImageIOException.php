<?php

namespace Oforge\Engine\Image\Exceptions\Basic;

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
     */
    public function __construct($message) {
        parent::__construct($message);
    }

}
