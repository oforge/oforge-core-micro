<?php

namespace Oforge\Engine\Image\Exceptions;

use Oforge\Engine\Image\Exceptions\Basic\ImageModifyException;

/**
 * Class ImageCompressException
 *
 * @package Oforge\Engine\Image\Exceptions
 */
class ImageCompressException extends ImageModifyException {

    /**
     * ImageCompressException constructor.
     *
     * @param string $message
     */
    public function __construct($message) {
        parent::__construct($message);
    }

}
