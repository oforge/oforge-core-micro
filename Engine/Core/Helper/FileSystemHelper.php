<?php

namespace Oforge\Engine\Core\Helper;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use SplFileObject;

/**
 * FileSystemHelper
 *
 * @package Oforge\Engine\Core\Helper
 */
class FileSystemHelper {
    public const OMIT = ['.', '..', '.git', '.idea', 'var', 'vendor', 'node_modules'];
    /**
     * Caching of findFiles results.
     *
     * @var array $findCache
     */
    private static $findCache = [];

    /**
     * Prevent instance.
     */
    private function __construct() {
    }

    /**
     * Generate temp folder path and optional create it.
     *
     * @param bool $mkdir mkdir folder?
     * @param bool $returnAbsolutePath Return absolute path or relative path (to oforge root).
     *
     * @return string
     */
    public static function getTempFolder(bool $mkdir, bool $returnAbsolutePath = true) : string {
        do {
            $tmpFolderRelative = Statics::DIR_TMP . Statics::GLOBAL_SEPARATOR . md5(rand());
            $tmpFolderAbsolute = ROOT_PATH . $tmpFolderRelative;
        } while (file_exists($tmpFolderAbsolute));
        if ($mkdir) {
            FileSystemHelper::mkdir($tmpFolderAbsolute);
        }

        return $returnAbsolutePath ? $tmpFolderAbsolute : $tmpFolderRelative;
    }

    /**
     * Remove single file or directory.
     *
     * @param string $path Path to file or directory
     * @param bool $recursive Remove directories recursive?
     * @param bool $removeFilesInDirs Removing of files if path is directory?
     * @param bool $removeEmptyDirs Remove empty path directory?
     *
     * @return bool
     */
    public static function remove(string $path, bool $recursive = false, bool $removeFilesInDirs = true, bool $removeEmptyDirs = true) : bool {
        if (empty($path)) {
            return false;
        }
        $path = realpath($path);
        if (is_dir($path)) {
            $filenames = array_diff(scandir($path), ['.', '..']);
            $success   = true;
            foreach ($filenames as $index => $filename) {
                $filepath = $path . Statics::GLOBAL_SEPARATOR . $filename;
                $removed  = false;
                if (is_dir($filepath)) {
                    if ($recursive) {
                        $removed = self::remove($filepath, $recursive, $removeFilesInDirs, $removeEmptyDirs);
                    }
                } else {
                    if ($removeFilesInDirs) {
                        $removed = self::remove($filepath, $recursive, $removeFilesInDirs, $removeEmptyDirs);
                    }
                }
                if ($removed) {
                    unset($filenames[$index]);
                }
                $success &= $removed;
            }
            if (empty($filenames) && $removeEmptyDirs) {
                $tmp = @rmdir($path);
                if (!$tmp) {
                    Oforge()->Logger()->get()->warning('Could not delete directory: ' . $path);
                }
                $success &= $tmp;
            }

            return $success;
        }
        if (@unlink($path)) {
            return true;
        }
        Oforge()->Logger()->get()->warning('Could not delete file: ' . $path);

        return false;
    }

    /**
     * Search recursive for for files with name inside a path.
     *
     * @param string $path string Directory or file path.
     * @param string $searchFileName Search file name.
     *
     * @return string[] Array with full path to files.
     */
    public static function findFiles(string $path, string $searchFileName) {
        $result = [];
        self::iterateFiles($path, function (SplFileInfo $file) use (&$result, $searchFileName) {
            if (strtolower($file->getFileName()) === $searchFileName) {
                $result[] = $file->getPath() . Statics::GLOBAL_SEPARATOR . $file->getFileName();
            }
        });

        return $result;
    }

    /**
     * @param string $path
     * @param callable $resultCallable Result callable(SplFileInfo $file). Iteration is canceled on false return.
     * @param callable|null $filterCallable Optional filter callable(SplFileInfo $file).
     */
    public static function iterateFiles(string $path, callable $resultCallable, ?callable $filterCallable = null) {
        $omits = array_fill_keys(self::OMIT, true);
        if (($realpath = realpath($path)) !== false) {
            $path = $realpath;
        }
        if ($filterCallable === null) {
            $filterCallable = function ($file) {
                return true;
            };
        }
        $recursiveDirectoryIterator = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
        $recursiveFilterIterator    = new \RecursiveCallbackFilterIterator($recursiveDirectoryIterator,
            function (SplFileInfo $file, $key, $iterator) use ($omits, $filterCallable) {
                return !isset($omits[$file->getFileName()]) && $filterCallable($file);
            });
        $recursiveIteratorIterator  = new RecursiveIteratorIterator($recursiveFilterIterator);
        foreach ($recursiveIteratorIterator as $file) {
            $return = $resultCallable($file);
            if (isset($return) && $return === false) {
                break;
            }
        }
    }

    /**
     * @param string $path
     * @param callable $lineCallable Line callable(string $line). Iteration is canceled on false return.
     */
    public static function iterateFileLines(string $path, callable $lineCallable) {
        if (!is_file($path) || !is_readable($path)) {
            return;
        }
        $splFileObject = new SplFileObject($path);
        $splFileObject->setFlags(SplFileObject::DROP_NEW_LINE);
        while (!$splFileObject->eof()) {
            $line = $splFileObject->fgets();
            if (empty(trim($line))) {
                continue;
            }
            $return = $lineCallable($line);
            if (isset($return) && $return === false) {
                break;
            }
        }
        $splFileObject = null;
    }

    /**
     * Get all Bootstrap.php files inside a defined path
     *
     * @param string $path
     *
     * @return string[]
     */
    public static function getBootstrapFiles(string $path) {
        return self::getCachedOrFind($path, 'bootstrap.php');
    }

    /**
     * Get all sub directories recursive based on the defined path, except the folders to oforge omit.
     *
     * @param string $path
     *
     * @return string[]
     */
    public static function getSubDirectories(string $path) {
        $result = [];
        if (!is_dir($path)) {
            return $result;
        }
        $fileNames = scandir($path);

        foreach ($fileNames as $fileName) {
            $filePath = $path . Statics::GLOBAL_SEPARATOR . $fileName;

            if (is_dir($filePath) && !in_array($fileName, self::OMIT)) {
                $result[] = $filePath;
                $result   = array_merge($result, self::getSubDirectories($filePath));
            }
        }

        return $result;
    }

    /**
     * Helper method for call mkdir.
     *
     * @param string $path
     * @param bool $recursive
     * @param int $mode
     * @param resource|null $context
     */
    public static function mkdir(string $path, bool $recursive = true, int $mode = 0755, $context = null) {
        if (isset($context)) {
            @mkdir($path, $mode, $recursive, $context);
        } else {
            @mkdir($path, $mode, $recursive);
        }
    }

    /**
     * Get cached data or find files with filename in path and cache it.
     *
     * @param string $context
     * @param string $path
     * @param string $filename
     *
     * @return mixed|string[]
     */
    private static function getCachedOrFind(string $path, string $filename) {
        $context = basename($path);
        if (isset(self::$findCache[$context])) {
            return self::$findCache[$context];
        }
        $cacheFile = ROOT_PATH . Statics::DIR_CACHE . Statics::GLOBAL_SEPARATOR . $context . '.cache.php';

        if (file_exists($cacheFile) && Oforge()->Settings()->isProductionMode()) {
            $result = ArrayPhpFileStorage::load($cacheFile);
        } else {
            $result = self::findFiles($path, $filename);
            ArrayPhpFileStorage::write($cacheFile, $result);
        }
        self::$findCache[$filename] = $result;

        return $result;
    }

}
