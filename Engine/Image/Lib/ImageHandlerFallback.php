<?php

namespace Oforge\Engine\Image\Lib;

/**
 * Class ImageHandlerFallback
 *
 * @package Oforge\Engine\Image\Lib
 */
class ImageHandlerFallback extends ImageHandler {

    /** @inheritdoc */
    public static function load(string $filePath) {
        return new static('none');
    }

    /** @inheritdoc */
    public function save(string $filePath) : void {
        // nothing to do
    }

    /** @inheritdoc */
    protected function cleanup() : void {
        // nothing to do
    }

    /** @inheritdoc */
    protected function getCurrentWidth() : int {
        // nothing to do
        return 0;
    }

    /** @inheritdoc */
    protected function getCurrentHeight() : int {
        // nothing to do
        return 0;
    }

}
