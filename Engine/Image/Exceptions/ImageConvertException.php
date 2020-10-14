<?php

namespace Oforge\Engine\Image\Exceptions;

use Oforge\Engine\Image\Exceptions\Basic\ImageModifyException;

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
     */
    public function __construct($message) {
        parent::__construct($message);
    }

}
