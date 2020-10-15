<?php

namespace Oforge\Engine\Image\Lib;

use Oforge\Engine\File\Enums\MimeType;
use Oforge\Engine\File\Exceptions\FileNotFoundException;
use Oforge\Engine\Image\Exceptions\ImageCompressException;
use Oforge\Engine\Image\Exceptions\ImageConvertException;
use Oforge\Engine\Image\Exceptions\ImageLoadException;
use Oforge\Engine\Image\Exceptions\ImageResizeException;
use Oforge\Engine\Image\Exceptions\ImageSaveException;
use Oforge\Engine\Image\Exceptions\ImageUnsupportedMimeTypeException;

/**
 * Interface ImageHandler
 *
 * @package Oforge\Engine\Image\Lib
 */
abstract class ImageHandler {
    /** @var string $exceptionPrefix */
    protected static $exceptionPrefix = 'ImageHandler';
    /** @var array $supportedMimeTypes Supported mime types from src to dst */
    protected static $supportedMimeTypes = [];
    /** @var string $mimeType */
    protected $mimeType = '';
    /** @var array<string,mixed> $changes */
    protected $changes = [];

    /**
     * ImageHandler constructor.
     *
     * @param string $mimeType
     */
    public function __construct(string $mimeType) {
        $this->mimeType = $mimeType;
    }

    /**
     * @param string $filePath
     *
     * @return static
     * @throws FileNotFoundException
     * @throws ImageUnsupportedMimeTypeException
     * @throws ImageLoadException
     */
    public abstract static function load(string $filePath);

    /**
     * @param string $mimeType
     *
     * @throws ImageUnsupportedMimeTypeException
     */
    protected static function checkLoadMimeType(string $mimeType) {
        $supported = isset(static::$supportedMimeTypes[$mimeType]);
        if ($supported && $mimeType === MimeType::IMAGE_WEBP) {
            /** @noinspection SpellCheckingInspection */
            $supported = function_exists('imagewebp') && function_exists('imagecreatefromwebp');
        }
        if (!$supported) {
            throw new ImageUnsupportedMimeTypeException(static::$exceptionPrefix . ": Image of mime type '$mimeType' are not supported.");
        }
    }

    /**
     * @param string $filePath
     *
     * @return void
     * @throws ImageCompressException
     * @throws ImageConvertException
     * @throws ImageResizeException
     * @throws ImageSaveException
     */
    public abstract function save(string $filePath) : void;

    /**
     * @param int $quality
     *
     * @return static
     */
    public function setQuality(int $quality) {
        $this->changes['quality'] = $quality;

        return $this;
    }

    /**
     * @param string $toMimeType
     *
     * @return static
     * @throws ImageUnsupportedMimeTypeException
     */
    public function setMimeType(string $toMimeType) {
        if ($this->mimeType !== $toMimeType) {
            if (!isset(static::$supportedMimeTypes[$this->mimeType][$toMimeType])) {
                $this->cleanup();
                throw new ImageUnsupportedMimeTypeException(sprintf(#
                    "%s: Image conversion from '%s' to '%s' is not supported.",#
                    static::$exceptionPrefix, $this->mimeType, $toMimeType#
                ));
            }
            $this->changes['mimeType'] = $toMimeType;
        }

        return $this;
    }

    /**
     * @param array<string,int>|int $size Width (int) or array with keys 'width' and/or 'height' with int values.
     *
     * @return static
     * @throws ImageResizeException
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function setSize($size) {
        $currentWidth  = $this->getCurrentWidth();
        $currentHeight = $this->getCurrentHeight();
        [$width, $height] = $this->resolveScaleSizes($currentWidth, $currentHeight, $size);
        if ($width !== $currentWidth || $height !== $currentHeight) {
            $this->changes['resize'] = [
                'width'  => $width,
                'height' => $height,
            ];
        }

        return $this;
    }

    /**
     * Cleanup resources on exceptions.
     */
    protected abstract function cleanup() : void;

    /**
     * @return int Current width of image
     */
    protected abstract function getCurrentWidth() : int;

    /**
     * @return int Current height of image
     */
    protected abstract function getCurrentHeight() : int;

    /**
     * @param int $currentWidth
     * @param int $currentHeight
     * @param array<string,int>|int $size Width (int) or array with keys 'width' and/or 'height' with int values.
     *
     * @return int[] [width, height]
     */
    protected function resolveScaleSizes(int $currentWidth, int $currentHeight, $size) {
        if (is_int($size)) {
            $width  = $size;
            $height = (int) (1.0 * $width / $currentWidth * $currentHeight);
        } else {
            $width  = null;
            $height = null;
            if (isset($size['width']) && is_int($size['width'])) {
                $width = $size['width'];
            }
            if (isset($size['height']) && is_int($size['height'])) {
                $height = $size['height'];
            }
            if ($width === null && $height === null) {
                $width  = $currentWidth;
                $height = $currentHeight;
            } elseif ($width === null) {
                $width = (int) (1.0 * $height / $currentHeight * $currentWidth);
            } elseif ($height === null) {
                $height = (int) (1.0 * $width / $currentWidth * $currentHeight);
            }
        }

        return [$width, $height];
    }

    /**
     * @param int $quality
     * @param int $defaultQuality
     *
     * @return int
     */
    protected function resolveQuality(int $quality, int $defaultQuality = 100) : int {
        return (($quality < 0 || $quality > 100) ? $defaultQuality : $quality);
    }

}
