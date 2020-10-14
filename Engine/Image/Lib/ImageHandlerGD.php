<?php

namespace Oforge\Engine\Image\Lib;

use Oforge\Engine\File\Exceptions\FileNotFoundException;

/**
 * Class ImageHandlerGD
 *
 * @package Oforge\Engine\Image\Lib
 */
class ImageHandlerGD extends ImageHandler {

    /** @inheritdoc */
    public function compress(int $quality) {
        // TODO: Implement compress() method.
        return $this;
    }

    /** @inheritdoc */
    public function convert(string $dstMimeType) {
        // TODO: Implement convert() method.
        return $this;
    }

    /** @inheritdoc */
    public function resize($options) {
        // TODO: Implement resize() method.
        return $this;
    }

    /** @inheritdoc */
    public function load(string $srcFilePath) {
        if (!file_exists($srcFilePath)) {
            throw  new FileNotFoundException($srcFilePath);
        }

        // TODO: Implement loadImage() method.
        return $this;
    }

    /** @inheritdoc */
    public function save(string $dstFilePath) : void {
        if (!$this->unsavedChanges) {
            return;
        }
        // TODO: Implement saveImage() method.
    }

}
