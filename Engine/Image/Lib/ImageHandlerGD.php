<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace Oforge\Engine\Image\Lib;

use Oforge\Engine\Core\Helper\FileHelper;
use Oforge\Engine\File\Enums\MimeType;
use Oforge\Engine\File\Exceptions\FileNotFoundException;
use Oforge\Engine\Image\Exceptions\ImageLoadException;
use Oforge\Engine\Image\Exceptions\ImageResizeException;
use Oforge\Engine\Image\Exceptions\ImageSaveException;

/**
 * Class ImageHandlerGD
 *
 * @package Oforge\Engine\Image\Lib
 */
class ImageHandlerGD extends ImageHandler {
    /** @var string $exceptionPrefix */
    protected static $exceptionPrefix = 'ImageHandlerGD';
    /** @var array $supportedMimeTypes Supported mime types of src & dst in convert */
    protected static $supportedMimeTypes = [
        MimeType::IMAGE_GIF  => [],
        MimeType::IMAGE_JPG  => [
            MimeType::IMAGE_PNG  => 1,
            MimeType::IMAGE_WEBP => 1,
        ],
        MimeType::IMAGE_PNG  => [
            MimeType::IMAGE_JPG  => 1,
            MimeType::IMAGE_WEBP => 1,
        ],
        MimeType::IMAGE_WEBP => [],
    ];
    /** @var resource $image ; */
    private $image;

    /** @inheritdoc */
    public static function load(string $filePath) {
        if (!file_exists($filePath)) {
            throw  new FileNotFoundException($filePath);
        }
        $mimeType = FileHelper::getMimeType($filePath);
        static::checkLoadMimeType($mimeType);
        $loadExceptionMsg = self::$exceptionPrefix . ': Could not load file: ' . $filePath;
        switch ($mimeType) {
            case MimeType::IMAGE_GIF:
                $image = imagecreatefromgif($filePath);
                break;
            case MimeType::IMAGE_JPG:
                $image = imagecreatefromjpeg($filePath);
                break;
            case MimeType::IMAGE_PNG:
                $tmpImage      = imagecreatefrompng($filePath);
                $currentWidth  = self::getWidth($tmpImage);
                $currentHeight = self::getHeight($tmpImage);
                try {
                    self::checkWidthHeight($currentWidth, $currentHeight, ImageLoadException::class, $loadExceptionMsg);
                } /** @noinspection PhpRedundantCatchClauseInspection */ catch (ImageLoadException $exception) {
                    imagedestroy($tmpImage);
                    throw $exception;
                }
                $image = imagecreatetruecolor($currentWidth, $currentHeight);
                if ($image !== false) {
                    imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
                    imagealphablending($image, true);
                    imagecopy($image, $tmpImage, 0, 0, 0, 0, $currentWidth, $currentHeight);
                }
                imagedestroy($tmpImage);
                break;
            case MimeType::IMAGE_WEBP:
                $image = imagecreatefromwebp($filePath);
                break;
            default:
                $image = false;
        }
        if ($image === false) {
            throw new ImageLoadException($loadExceptionMsg);
        }
        $handler        = new static($mimeType);
        $handler->image = $image;

        return $handler;
    }

    /**
     * @param $width
     * @param $height
     * @param string $exceptionClass
     * @param string $exceptionMessage
     */
    protected static function checkWidthHeight($width, $height, string $exceptionClass, string $exceptionMessage) {
        if ($width === false || $height === false) {
            throw new $exceptionClass($exceptionMessage);
        }
    }

    /**
     * @param resource $image
     *
     * @return int|false
     */
    protected static function getWidth($image) {
        return imagesx($image);
    }

    /**
     * @param resource $image
     *
     * @return int|false
     */
    protected static function getHeight($image) {
        return imagesy($image);
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

            $resize      = $this->changes['resize'];
            $width       = $resize['width'];
            $height      = $resize['height'];
            $scaledImage = imagecreatetruecolor($resize['width'], $resize['height']);
            if ($scaledImage === false) {
                $this->cleanup();
                throw new ImageResizeException($exceptionMessage);
            } else {
                imagecopyresampled($scaledImage, $this->image, 0, 0, 0, 0, $width, $height, $this->getCurrentWidth(), $this->getCurrentHeight());
                imagedestroy($this->image);
                $this->image = $scaledImage;
            }
        }

        $mimeType = isset($this->changes['mimeType']) ? $this->changes['mimeType'] : $this->mimeType;
        $quality  = 100;
        if (isset($this->changes['quality'])) {
            $quality = $this->resolveQuality($this->changes['quality']);
        }

        $filePath = FileHelper::replaceExtensionByMimeType($filePath, $mimeType);
        switch ($mimeType) {
            case MimeType::IMAGE_GIF:
                imagesavealpha($this->image, true);
                $success = imagegif($this->image, $filePath);
                break;
            case MimeType::IMAGE_JPG:
                imageinterlace($this->image, true);
                $success = imagejpeg($this->image, $filePath, $quality);
                break;
            case MimeType::IMAGE_PNG:
                imagesavealpha($this->image, true);
                $quality = (int) ($quality === 100 ? 9 : ($quality / 10));
                $success = imagepng($this->image, $filePath, $quality);
                break;
            case MimeType::IMAGE_WEBP:
                imagesavealpha($this->image, true);
                $success = imagewebp($this->image, $filePath, $quality);
                break;
            default:
                // unsupported output format
                $success = false;
        }

        $this->cleanup();
        if (!$success) {
            throw new ImageSaveException(self::$exceptionPrefix . ': Could not save image to: ' . $filePath);
        }
    }

    /** @inheritdoc */
    public function setSize($size) {
        $currentWidth  = $this->getCurrentWidth();
        $currentHeight = $this->getCurrentHeight();
        try {
            self::checkWidthHeight($currentWidth, $currentHeight, ImageResizeException::class, 'ImageHandlerGD: Could not resize image');
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (ImageResizeException $exception) {
            $this->cleanup();
            throw $exception;
        }

        return parent::setSize($size);
    }

    /** @inheritdoc */
    protected function cleanup() : void {
        imagedestroy($this->image);
    }

    /** @inheritdoc */
    protected function getCurrentWidth() : int {
        return static::getWidth($this->image);
    }

    /** @inheritdoc */
    protected function getCurrentHeight() : int {
        return static::getHeight($this->image);
    }

}
