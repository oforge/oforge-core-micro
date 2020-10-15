<?php

namespace Oforge\Engine\Image\Exceptions\Basic;

use Exception;
use Throwable;

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
     * @param Throwable|null $previous
     */
    public function __construct($message, Throwable $previous = null) {
        parent::__construct($message, 0, $previous);
    }

}
