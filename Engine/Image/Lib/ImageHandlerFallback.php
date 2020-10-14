<?php

namespace Oforge\Engine\Image\Lib;

/**
 * Class ImageHandlerFallback
 *
 * @package Oforge\Engine\Image\Lib
 */
class ImageHandlerFallback extends ImageHandler {

    /** @inheritdoc */
    public function compress(int $quality) {
        return $this;
    }

    /** @inheritdoc */
    public function convert(string $dstMimeType) {
        return $this;
    }

    /** @inheritdoc */
    public function resize($options) {
        return $this;
    }

    /** @inheritdoc */
    public function load(string $srcFilePath) {
        return $this;
    }

    /** @inheritdoc */
    public function save(string $dstFilePath) : void {
        // nothing to do
    }

}
