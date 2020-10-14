<?php

namespace Oforge\Engine\Image\Exceptions;

use Oforge\Engine\Image\Exceptions\Basic\ImageModifyException;

/**
 * Class ImageResizeException
 *
 * @package Oforge\Engine\Image\Exceptions
 */
class ImageResizeException extends ImageModifyException {

    /**
     * ImageResizeException constructor.
     *
     * @param string $message
     */
    public function __construct($message) {
        parent::__construct($message);
    }

}
