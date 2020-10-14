<?php

namespace Oforge\Engine\Image\Lib;

use Oforge\Engine\File\Exceptions\FileNotFoundException;
use Oforge\Engine\Image\Exceptions\ImageCompressException;
use Oforge\Engine\Image\Exceptions\ImageConvertException;
use Oforge\Engine\Image\Exceptions\ImageLoadException;
use Oforge\Engine\Image\Exceptions\ImageResizeException;
use Oforge\Engine\Image\Exceptions\ImageSaveException;

/**
 * Interface ImageHandler
 *
 * @package Oforge\Engine\Image\Lib
 */
abstract class ImageHandler {
    /** @var bool $unsavedChanges */
    protected $unsavedChanges = false;

    /**
     * @param int $quality
     *
     * @return static
     * @throws ImageCompressException
     */
    public abstract function compress(int $quality);

    /**
     * @param string $dstMimeType
     *
     * @return static
     * @throws ImageConvertException
     */
    public abstract function convert(string $dstMimeType);

    /**
     * @param array<string,int>|int $options Width (int) or array with keys 'width' and/or 'height' with int values.
     *
     * @return static
     * @throws ImageResizeException
     */
    public abstract function resize($options);

    /**
     * @param string $srcFilePath
     *
     * @return static
     * @throws FileNotFoundException
     * @throws ImageLoadException
     */
    public abstract function load(string $srcFilePath);

    /**
     * @param string $dstFilePath
     *
     * @return void
     * @throws ImageSaveException
     */
    public abstract function save(string $dstFilePath) : void;

    /**
     * @param int $currentWidth
     * @param int $currentHeight
     * @param array<string,int>|int $options Width (int) or array with keys 'width' and/or 'height' with int values.
     *
     * @return int[] [width, height]
     */
    protected function resolveScaleSizes(int $currentWidth, int $currentHeight, $options) {
        $width  = $currentWidth;
        $height = $currentHeight;
        if (is_int($options)) {
            $width  = $options;
            $height = (int) (1.0 * $width / $currentWidth * $currentHeight);
        } else {
            if (isset($options['width']) && is_int($options['width'])) {
                $width = $options['width'];
            }
            if (isset($options['height']) && is_int($options['height'])) {
                $width = $options['height'];
            }
        }

        return [$width, $height];
    }

}
