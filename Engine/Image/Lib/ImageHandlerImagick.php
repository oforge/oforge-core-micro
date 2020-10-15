<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace Oforge\Engine\Image\Lib;

use Imagick;
use ImagickException;
use ImagickPixel;
use Oforge\Engine\Core\Helper\FileHelper;
use Oforge\Engine\File\Enums\MimeType;
use Oforge\Engine\File\Exceptions\FileNotFoundException;
use Oforge\Engine\Image\Exceptions\ImageCompressException;
use Oforge\Engine\Image\Exceptions\ImageConvertException;
use Oforge\Engine\Image\Exceptions\ImageLoadException;
use Oforge\Engine\Image\Exceptions\ImageResizeException;
use Oforge\Engine\Image\Exceptions\ImageSaveException;

/**
 * Class ImageHandlerImagick
 *
 * @package Oforge\Engine\Image\Lib
 */
class ImageHandlerImagick extends ImageHandler {
    /** @var array Output image formats */
    private const MIME_TYPE_2_FORMATS = [
        MimeType::IMAGE_GIF  => 'gif',
        MimeType::IMAGE_JPG  => 'jpeg',
        MimeType::IMAGE_PNG  => 'png',
        MimeType::IMAGE_WEBP => 'webp',
    ];
    /** @var string $exceptionPrefix */
    protected static $exceptionPrefix = 'ImageHandlerImagick';
    /** @var array $supportedMimeTypes Supported mime types of src & dst in convert */
    protected static $supportedMimeTypes = [
        MimeType::IMAGE_GIF  => [],
        MimeType::IMAGE_JPG  => [
            MimeType::IMAGE_JPG  => 1,
            MimeType::IMAGE_PNG  => 1,
            MimeType::IMAGE_WEBP => 1,
        ],
        MimeType::IMAGE_PNG  => [
            MimeType::IMAGE_JPG  => 1,
            MimeType::IMAGE_PNG  => 1,
            MimeType::IMAGE_WEBP => 1,
        ],
        MimeType::IMAGE_WEBP => [],
    ];
    /** @var Imagick $imagick */
    private $imagick;

    /** @inheritdoc */
    public static function load(string $filePath) {
        if (!file_exists($filePath)) {
            throw  new FileNotFoundException($filePath);
        }
        try {
            $mimeType = FileHelper::getMimeType($filePath);
            static::checkLoadMimeType($mimeType);

            $imagick = new Imagick($filePath);

            $handler          = new static($mimeType);
            $handler->imagick = $imagick;
        } catch (ImagickException $exception) {
            throw new ImageLoadException(self::$exceptionPrefix . ': Could not load file: ' . $filePath, $exception);
        }

        return $handler;
    }

    /** @inheritdoc */
    public function save(string $filePath) : void {
        if (empty($this->changes)) {
            return;
        }

        if (isset($this->changes['resize'])) {
            $exceptionMessage = sprintf(#
                "%s: Could not resize image.",#
                self::$exceptionPrefix#
            );

            $resize = $this->changes['resize'];
            try {
                if (!$this->imagick->scaleImage($resize['width'], $resize['height'])) {
                    $this->cleanup();
                    throw new ImageResizeException($exceptionMessage);
                }
            } catch (ImagickException $exception) {
                $this->cleanup();
                throw new ImageResizeException($exceptionMessage, $exception);
            }
        }

        $mimeType = isset($this->changes['mimeType']) ? $this->changes['mimeType'] : $this->mimeType;
        if (isset(static::MIME_TYPE_2_FORMATS[$mimeType])) {
            if (!$this->imagick->setImageFormat(static::MIME_TYPE_2_FORMATS[$mimeType])) {
                $this->cleanup();
                throw new ImageConvertException(sprintf(#
                    "%s: Could not convert image from mime type '%s' to '%s'.",#
                    self::$exceptionPrefix, $this->mimeType, $this->changes['mimeType']#
                ));
            }
        }

        if (isset($this->changes['quality'])) {
            $success = true;
            $quality = $this->resolveQuality($this->changes['quality']);
            switch ($mimeType) {
                case MimeType::IMAGE_JPG:
                    $success = $this->imagick->setImageCompressionQuality($quality);
                    break;
                case MimeType::IMAGE_PNG:
                    $success = $this->imagick->setImageDepth(8);
                    break;
            }
            if (!$success) {
                $this->cleanup();
                throw new ImageCompressException(self::$exceptionPrefix . ': Could not compress image.');
            }
            unset($success);
        }

        switch ($mimeType) {
            case MimeType::IMAGE_JPG:
                $this->imagick->setSamplingFactors(['2x2', '1x1', '1x1']);
                //$profiles = $this->imagick->getImageProfiles('icc', true);
                // $this->imagick->stripImage();
                // if (!empty($profiles) && isset($profiles['icc'])) {
                //     $this->imagick->profileImage('icc', $profiles['icc']);
                // }
                $this->imagick->setInterlaceScheme(Imagick::INTERLACE_JPEG);
                $this->imagick->setColorspace(Imagick::COLORSPACE_SRGB);
                break;
            case MimeType::IMAGE_PNG:
                $this->imagick->setInterlaceScheme(Imagick::INTERLACE_PNG);
                break;
            case MimeType::IMAGE_WEBP:
                $this->imagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);
                $this->imagick->setBackgroundColor(new ImagickPixel('transparent'));
                $this->imagick->setOption('webp:lossless', 'true');
                break;
        }

        $filePath = FileHelper::replaceExtensionByMimeType($filePath, $mimeType);
        $success  = $this->imagick->writeImage($filePath);
        $this->cleanup();
        if (!$success) {
            throw new ImageSaveException(self::$exceptionPrefix . ': Could not save image to: ' . $filePath);
        }
    }

    /** @inheritdoc */
    protected function cleanup() : void {
        $this->imagick->clear();
    }

    /** @inheritdoc */
    protected function getCurrentWidth() : int {
        return $this->imagick->getImageWidth();
    }

    /** @inheritdoc */
    protected function getCurrentHeight() : int {
        return $this->imagick->getImageHeight();
    }

}
