<?php

namespace Oforge\Engine\Image\Services;

use Exception;
use Oforge\Engine\Core\Exceptions\InvalidClassException;
use Oforge\Engine\Core\Helper\FileHelper;
use Oforge\Engine\Core\Manager\Events\Event;
use Oforge\Engine\Core\Services\ConfigService;
use Oforge\Engine\File\Enums\FileTypeGroup;
use Oforge\Engine\File\Enums\MimeType;
use Oforge\Engine\File\Exceptions\FileNotFoundException;
use Oforge\Engine\File\Models\File;
use Oforge\Engine\File\Traits\Service\FileAccessServiceTrait;
use Oforge\Engine\Image\Enums\ImageConstants;
use Oforge\Engine\Image\Exceptions\Basic\ImageIOException;
use Oforge\Engine\Image\Exceptions\Basic\ImageModifyException;
use Oforge\Engine\Image\Exceptions\ImageCompressException;
use Oforge\Engine\Image\Exceptions\ImageConvertException;
use Oforge\Engine\Image\Exceptions\ImageLoadException;
use Oforge\Engine\Image\Exceptions\ImageResizeException;
use Oforge\Engine\Image\Exceptions\ImageSaveException;
use Oforge\Engine\Image\Exceptions\ImageUnsupportedMimeTypeException;
use Oforge\Engine\Image\Lib\ImageHandler;
use Oforge\Engine\Image\Lib\ImageHandlerFallback;
use Oforge\Engine\Image\Lib\ImageHandlerGD;
use Oforge\Engine\Image\Lib\ImageHandlerImagick;

/**
 * Class ImageService
 *
 * @package Oforge\Engine\Image\Services
 */
class ImageService {
    use FileAccessServiceTrait;

    /** @var string $imageHandlerClass */
    private $imageHandlerClass;

    /** ImageService constructor. */
    public function __construct() {
        $this->initImageHandlerClass();

        Oforge()->Events()->attach(File::class . '::created', Event::SYNC, function (Event $event) {
            if ($event->getDataValue('typeGroup') === FileTypeGroup::IMAGE) {
                /** @var File $image */
                $image = $event->getDataValue('entity');
                $this->createThumbnails($image);
            }
        });
    }

    /**
     * @param int|string|null $pathOrID
     * @param array|int $options
     *
     * @return string|null
     */
    public function getUrl($pathOrID, $options = []) : ?string {
        if ($pathOrID === null) {
            return null;
        }
        if (is_int($pathOrID)) {
            $image = $this->getImageFileByID($pathOrID);
        } else {
            $image = $this->FileAccessService()->getOneBy(['filePath' => $pathOrID, 'typeGroup' => FileTypeGroup::IMAGE]);

            if ($image === null) {
                $image = $this->getImageFileByID($pathOrID);
            }
        }

        return $image === null ? $pathOrID : $this->getUrlByImageFile($image, $options);
    }

    /**
     * @param File $image
     * @param array|int $options
     *
     * @return string
     */
    public function getUrlByImageFile(File $image, $options = []) {
        $width  = 0;
        $height = 0;
        if (is_int($options)) {
            $width = $options;
        } elseif (isset($options['width'])) {
            $width = $options['width'];
        }
        if (isset($options['height'])) {
            $height = $options['height'];
        }
        $relativeSrcFilePath = $image->getFilePath();
        if ($width > 0) {
            $absoluteSrcFilePath = ROOT_PATH . $relativeSrcFilePath;
            if (file_exists($absoluteSrcFilePath)) {
                $relativeDstFilePath = FileHelper::trimExtension($relativeSrcFilePath)#
                                       . '-w' . $width #
                                       . ($height > 0 ? ('-h' . $height) : '')#
                                       . '.' . FileHelper::getExtension($relativeSrcFilePath);

                $absoluteDstFilePath = ROOT_PATH . $relativeDstFilePath;
                $recheckFileExist    = false;
                if (!file_exists($absoluteDstFilePath)) {
                    try {
                        /** @var ConfigService $configService */
                        $configService = Oforge()->Services()->get('config');
                        $quality       = $configService->get('image_access_quality');
                    } catch (Exception $exception) {
                        Oforge()->Logger()->logException($exception, ImageConstants::LOGGER);
                        $quality = ImageConstants::DEFAULT_QUALITY;
                    }
                    try {
                        $imageHandler = $this->initImageHandler($absoluteSrcFilePath);
                        $imageHandler->setSize($options)#
                                     ->setQuality($quality)#
                                     ->save($absoluteDstFilePath);
                        $recheckFileExist = true;
                    } catch (FileNotFoundException | ImageIOException | ImageModifyException $exception) {
                        Oforge()->Logger()->logException($exception, ImageConstants::LOGGER);
                    } catch (ImageUnsupportedMimeTypeException $exception) {
                        // ignore
                    }
                }
                if ($recheckFileExist && file_exists($absoluteDstFilePath)) {
                    return $relativeDstFilePath;
                }
            }
        }

        return $relativeSrcFilePath;
    }

    /**
     * @param File $image
     * @param int|int[]|null $widths If width = null, config value is used.
     */
    public function createThumbnails(File $image, $widths = null) {
        if ($widths === null) {
            try {
                /** @var ConfigService $configService */
                $configService = Oforge()->Services()->get('config');
                $widths        = $configService->get('image_upload_thumbnail_widths');
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception);
                $widths = ImageConstants::DEFAULT_UPLOAD_THUMBNAIL_WIDTHS;
            }
            $widths = array_filter(array_map('trim', explode(',', trim($widths))), 'is_numeric');
        }
        if (is_int($widths)) {
            $widths = [$widths];
        }
        foreach ($widths as $width) {
            $this->getUrlByImageFile($image, (int) $width);
        }
    }

    /**
     * @param string $srcFilePath
     * @param string|null $dstFilePath
     * @param int $quality
     *
     * @throws FileNotFoundException
     * @throws ImageUnsupportedMimeTypeException
     * @throws ImageLoadException
     * @throws ImageCompressException
     * @throws ImageSaveException
     */
    public function compress(string $srcFilePath, ?string $dstFilePath, int $quality = ImageConstants::DEFAULT_QUALITY) {
        $dstFilePath = $this->resolveDstFilePath($srcFilePath, $dstFilePath);
        try {
            $this->initImageHandler($srcFilePath)->setQuality($quality)->save($dstFilePath);
        } catch (ImageConvertException | ImageResizeException $exception) {
            // should technically not happen
            Oforge()->Logger()->logException($exception, ImageConstants::LOGGER);
            throw new ImageSaveException('Unexpected error: ' . $exception->getMessage(), $exception);
        }
    }

    /**
     * @param string $srcFilePath
     * @param string|null $dstFilePath
     * @param string $dstMimeType Value of image mime types in Oforge\Engine\File\Enums\MimeType.
     *
     * @throws FileNotFoundException
     * @throws ImageUnsupportedMimeTypeException
     * @throws ImageLoadException
     * @throws ImageConvertException
     * @throws ImageSaveException
     * @see MimeType
     */
    public function convert(string $srcFilePath, ?string $dstFilePath, string $dstMimeType) {
        $dstFilePath = $this->resolveDstFilePath($srcFilePath, $dstFilePath);
        try {
            $this->initImageHandler($srcFilePath)->setMimeType($dstMimeType)->save($dstFilePath);
        } catch (ImageCompressException | ImageResizeException $exception) {
            // should technically not happen
            Oforge()->Logger()->logException($exception, ImageConstants::LOGGER);
            throw new ImageSaveException('Unexpected error: ' . $exception->getMessage(), $exception);
        }
    }

    /**
     * @param string $srcFilePath
     * @param string|null $dstFilePath
     * @param array $options Key 'resize': Value Width (int) or array with keys 'width' and/or 'height' with int values.<br>
     * Key 'convert': String value with image mime types, see: Oforge\Engine\File\Enums\MimeType.
     * Key 'compress': Int value with $quality (between 0 and 100).
     *
     * @throws FileNotFoundException
     * @throws ImageUnsupportedMimeTypeException
     * @throws ImageLoadException
     * @throws ImageCompressException
     * @throws ImageConvertException
     * @throws ImageResizeException
     * @throws ImageSaveException
     * @see MimeType
     */
    public function modify(string $srcFilePath, ?string $dstFilePath, array $options) {
        $dstFilePath  = $this->resolveDstFilePath($srcFilePath, $dstFilePath);
        $imageHandler = $this->initImageHandler($srcFilePath);
        if (isset($options['compress'])) {
            $quality = $options['compress'];
            $imageHandler->setQuality($quality);
        }
        if (isset($options['convert'])) {
            $dstMimeType = $options['convert'];
            $imageHandler->setMimeType($dstMimeType);
        }
        if (isset($options['resize'])) {
            $resizeOptions = $options['resize'];
            $imageHandler->setSize($resizeOptions);
        }
        $imageHandler->save($dstFilePath);
    }

    /**
     * @param string $srcFilePath
     * @param string|null $dstFilePath
     * @param array<string,int>|int $options Width (int) or array with keys 'width' and/or 'height' with int values.
     *
     * @throws FileNotFoundException
     * @throws ImageUnsupportedMimeTypeException
     * @throws ImageLoadException
     * @throws ImageResizeException
     * @throws ImageSaveException
     */
    public function resize(string $srcFilePath, ?string $dstFilePath, $options) {
        $dstFilePath = $this->resolveDstFilePath($srcFilePath, $dstFilePath);
        try {
            $this->initImageHandler($srcFilePath)->setSize($options)->save($dstFilePath);
        } catch (ImageCompressException | ImageConvertException $exception) {
            // should technically not happen
            Oforge()->Logger()->logException($exception, ImageConstants::LOGGER);
            throw new ImageSaveException('Unexpected error: ' . $exception->getMessage(), $exception);
        }
    }

    /**
     * @param string $imageHandlerClass
     *
     * @throws InvalidClassException
     */
    public function setImageHandlerClass(string $imageHandlerClass) : void {
        if (!class_exists($imageHandlerClass) || !is_subclass_of($imageHandlerClass, ImageHandler::class)) {
            throw new InvalidClassException($imageHandlerClass, ImageHandler::class);
        }
        $this->imageHandlerClass = $imageHandlerClass;
    }

    /**
     * @param int $imageID
     *
     * @return File|null
     */
    protected function getImageFileByID(int $imageID) : ?File {
        return $this->FileAccessService()->getOneBy(['id' => $imageID, 'typeGroup' => FileTypeGroup::IMAGE]);
    }

    /**
     * @param string $filePath
     *
     * @return ImageHandler
     * @throws FileNotFoundException
     * @throws ImageUnsupportedMimeTypeException
     * @throws ImageLoadException
     */
    protected function initImageHandler(string $filePath) : ImageHandler {
        /** @var ImageHandler $class */
        $class = $this->imageHandlerClass;

        return $class::load($filePath);
    }

    /**
     * Init ImageHandler. Will use first available framework of Imagick > GD > Fallback.
     */
    protected function initImageHandlerClass() {
        if (extension_loaded('imagick')) {
            $this->imageHandlerClass = ImageHandlerImagick::class;
        } elseif (extension_loaded('gd')) {
            $this->imageHandlerClass = ImageHandlerGD::class;
        } else {
            $this->imageHandlerClass = ImageHandlerFallback::class;
        }
    }

    /**
     * If $dstFilePath ist null, 'null' or empty then use $srcFilePath.
     *
     * @param string $srcFilePath
     * @param string|null $dstFilePath
     *
     * @return string
     */
    protected final function resolveDstFilePath(string $srcFilePath, ?string $dstFilePath) {
        return ((empty($dstFilePath) || $dstFilePath === 'null') ? $srcFilePath : $dstFilePath);
    }

}
