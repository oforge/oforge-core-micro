<?php

namespace Oforge\Engine\Core\Helper;

use Oforge\Engine\File\Enums\MimeType;

class FileHelper {

    /** Prevent instance. */
    private function __construct() {
    }

    public static function getExtension(string $filePath) : string {
        return pathinfo($filePath, PATHINFO_EXTENSION);
    }

    /**
     * @param string $filePath
     *
     * @return string|null
     */
    public static function getMimeType(string $filePath) : ?string {
        $mimeType = false;
        if (function_exists('finfo_file')) {
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filePath);
            finfo_close($finfo);

            return $mimeType;
        }
        if (function_exists('mime_content_type')) {
            $mimeType = @mime_content_type($filePath);
        }

        return empty($mimeType) ? null : strtolower($mimeType);
    }

    /**
     * Replacing of file path extension.
     *
     * @param string $filePath
     * @param string $extension
     *
     * @return string
     */
    public static function replaceExtension(string $filePath, string $extension) : string {
        return self::trimExtension($filePath) . '.' . $extension;
    }

    /**
     * Replacing of file path extension.
     *
     * @param string $filePath
     * @param string $mimeType See Oforge\Engine\File\Enums\MimeType.
     *
     * @return string
     */
    public static function replaceExtensionByMimeType(string $filePath, string $mimeType) : string {
        return self::replaceExtension($filePath, MimeType::getExtension($mimeType));
    }

    /**
     * Remove extension from file path.
     *
     * @param $filePath
     *
     * @return string
     */
    public static function trimExtension($filePath) : string {
        $data = pathinfo($filePath);

        return $data['dirname'] . Statics::GLOBAL_SEPARATOR . $data['filename'];
    }

}
