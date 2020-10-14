<?php

namespace Oforge\Engine\Image\Exceptions\Basic;

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
     */
    public function __construct($message) {
        parent::__construct($message);
    }

}
