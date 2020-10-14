<?php

namespace Oforge\Engine\Image\Exceptions;

use Oforge\Engine\Image\Exceptions\Basic\ImageIOException;

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
     */
    public function __construct($message) {
        parent::__construct($message);
    }

}
