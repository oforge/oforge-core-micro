<?php

namespace Oforge\Engine\Image\Services;

use Oforge\Engine\Core\Helper\Statics;
use Oforge\Engine\Core\Manager\Events\Event;
use Oforge\Engine\File\Enums\FileTypeGroup;
use Oforge\Engine\File\Exceptions\FileEntryNotFoundException;
use Oforge\Engine\File\Models\File;
use Oforge\Engine\File\Traits\Service\FileAccessServiceTrait;

/**
 * Class ImageCleanupService
 *
 * @package Oforge\Engine\Image\Services
 */
class ImageCleanupService {
    use FileAccessServiceTrait;

    /** @var string Glob pattern for thumbnail recognition. */
    private const GLOB_RESIZED_FILE_PATTERN = '*-*.*[jpg|jpeg|gif|png|svg|webp]';

    /** ImageCleanupService constructor. */
    public function __construct() {
        Oforge()->Events()->attach(File::class . '::removed', Event::SYNC, function (Event $event) {
            if ($event->getDataValue('typeGroup') === FileTypeGroup::IMAGE) {
                /** @var int $imageID */
                $imageID = $event->getDataValue('id');
                $this->removeResizedImages($imageID);
            }
        });
    }

    /**
     * Iterate upload folder and remove all resized images (thumbnails) excluding the original file.
     */
    public function removeAllResizedImages() {
        $paths  = glob(implode(Statics::GLOBAL_SEPARATOR, [
            ROOT_PATH . Statics::DIR_UPLOAD,#
            '*',#
            '*',#
            self::GLOB_RESIZED_FILE_PATTERN,#
        ]));
        $images = $this->FileAccessService()->list(['typeGroup' => FileTypeGroup::IMAGE]);
        $this->removeResizedImagesSub($paths, $images);
    }

    /**
     * Remove all resized images (thumbnails) of image (by id) excluding the original file.
     *
     * @param int $imageID
     *
     * @throws FileEntryNotFoundException
     */
    public function removeResizedImages(int $imageID) {
        $image = $this->FileAccessService()->getOneBy(['id' => $imageID, 'typeGroup' => FileTypeGroup::IMAGE]);
        if ($image === null) {
            throw new FileEntryNotFoundException('id', $imageID);
        }
        $paths = glob(ROOT_PATH . dirname($image->getFilePath()) . Statics::GLOBAL_SEPARATOR . self::GLOB_RESIZED_FILE_PATTERN);

        $this->removeResizedImagesSub($paths, [$image]);
    }

    /**
     * @param array $paths
     * @param File[] $images
     */
    protected function removeResizedImagesSub(array $paths, $images) {
        if (!is_array($paths)) {
            return;
        }
        $paths = array_flip($paths);
        foreach ($images as $image) {
            echo '#', $image->getFilePath(), "\n";
            unset($paths[ROOT_PATH . $image->getFilePath()]);
        }
        foreach ($paths as $path => $tmp) {
            unlink($path);
        }
    }

}
