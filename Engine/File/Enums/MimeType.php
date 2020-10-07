<?php

namespace Oforge\Engine\File\Enums;

/**
 * Class MimeTypes
 *
 * @package Oforge\Engine\File\Enums
 */
class MimeType {
    public const AUDIO_MP3  = 'audio/mpeg';
    public const IMAGE_GIF  = 'image/gif';
    public const IMAGE_JPG  = 'image/jpeg';
    public const IMAGE_PNG  = 'image/png';
    public const IMAGE_SVG  = 'image/svg+xml';
    public const IMAGE_WEBP = 'image/webp';
    public const VIDEO_MP4  = 'video/mp4';

    /** Prevent Instance */
    private function __construct() {
    }

    /**
     * @param string $mimeType
     *
     * @return string|null Returns null for not defined mime types.
     */
    public static function getExtension(string $mimeType) : ?string {
        switch ($mimeType) {
            case self::AUDIO_MP3:
                return 'mp3';
            case self::IMAGE_GIF:
                return 'gif';
            case self::IMAGE_JPG:
                return 'jpg';
            case self::IMAGE_PNG:
                return 'png';
            case self::IMAGE_SVG:
                return 'svg';
            case self::IMAGE_WEBP:
                return 'webp';
            case self::VIDEO_MP4:
                return 'mp4';
            default:
                return null;
        }
    }

}
