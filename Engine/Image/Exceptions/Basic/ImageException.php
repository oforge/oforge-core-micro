<?php

namespace Oforge\Engine\Image\Exceptions\Basic;

use Exception;

/**
 * Class ImageException
 *
 * @package Oforge\Engine\Image\Exceptions\Basic
 */
class ImageException extends Exception {

    /**
     * ImageException constructor.
     *
     * @param string $message
     */
    public function __construct($message) {
        parent::__construct($message);
    }

}
