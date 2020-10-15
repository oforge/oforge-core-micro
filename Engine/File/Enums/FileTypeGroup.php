<?php

namespace Oforge\Engine\File\Enums;

/**
 * Class FileTypeGroup
 *
 * @package Oforge\Engine\File\Enums
 */
class FileTypeGroup {
    public const DEFAULT = 'file';
    public const AUDIO   = 'audio';
    public const IMAGE   = 'image';
    public const VIDEO   = 'video';

    /** Prevent Instance */
    private function __construct() {
    }

    /**
     * @param string $typeGroup
     *
     * @return bool
     */
    public static function isValid(string $typeGroup) : bool {
        switch ($typeGroup) {
            case self::AUDIO:
            case self::IMAGE:
            case self::VIDEO:
                return true;
        }

        return false;
    }

}
