<?php

namespace Oforge\Engine\Image\Enums;

/**
 * Class Constants
 *
 * @package Oforge\Engine\Image\Enums
 */
class ImageConstants {
    /** @var string Logger name for ImageService */
    public const LOGGER = 'Engine.ImageService';
    /** @var int Default image quality for compress. */
    public const DEFAULT_QUALITY = 40;
    /** @var string Default widths for creating a thumbnail when uploading an image. */
    public const DEFAULT_UPLOAD_THUMBNAIL_WIDTHS = '400,800';

    /** Prevent Instance */
    private function __construct() {
    }

}
