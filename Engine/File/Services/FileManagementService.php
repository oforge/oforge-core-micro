<?php

namespace Oforge\Engine\File\Services;

use Doctrine\ORM\ORMException;
use Exception;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Core\Helper\ArrayHelper;
use Oforge\Engine\Core\Helper\FileHelper;
use Oforge\Engine\Core\Helper\FileSystemHelper;
use Oforge\Engine\Core\Helper\Statics;
use Oforge\Engine\Core\Helper\StringHelper;
use Oforge\Engine\Core\Manager\Events\Event;
use Oforge\Engine\File\Enums\FileTypeGroup;
use Oforge\Engine\File\Exceptions\FileEntryNotFoundException;
use Oforge\Engine\File\Exceptions\FileImportException;
use Oforge\Engine\File\Exceptions\FileInUsageException;
use Oforge\Engine\File\Exceptions\FileNotFoundException;
use Oforge\Engine\File\Exceptions\MimeTypeNotAllowedException;
use Oforge\Engine\File\Models\File;
use Oforge\Engine\File\Traits\Service\FileAccessServiceTrait;
use Slim\Http\UploadedFile;

/**
 * Class FileManagementService
 *
 * @package Oforge\Engine\File\Service
 */
class FileManagementService extends AbstractDatabaseAccess {
    use FileAccessServiceTrait;

    /**
     * Rename options:
     *      [
     *          'filename' => '...', // File name (with or without extension), optional
     *          'prefix' => '...', // File name prefix, optional
     *          'suffix' => '...', // File name suffix (before extension),optional
     *      ]
     */
    public const OPTIONS_RENAME = [];
    /**
     * Meta options:
     *      [
     *          'uploaderID' => string|null, // ID of uploader user, optional
     *      ]
     */
    public const OPTIONS_META = [];

    /** FileManagementService constructor. */
    public function __construct() {
        parent::__construct(File::class);
    }

    /**
     * @param array $FILE
     * @param array $options [<br>
     *      'meta'   => [], // Optional, see FileManagementService::OPTIONS_META<br>
     *      'rename' => [], // Optional, see FileManagementService::OPTIONS_RENAME<br>
     *      ]
     *
     * @return File
     * @throws FileImportException
     * @throws FileNotFoundException
     * @throws MimeTypeNotAllowedException
     * @throws ORMException
     * @see FileManagementService::OPTIONS_META
     * @see FileManagementService::OPTIONS_RENAME
     */
    public function importFilesEntry(array $FILE, array $options = []) {
        if (!isset($FILE['error'])
            || !isset($FILE['tmp_name'])
            || !isset($FILE['name'])
            || !isset($FILE['type'])
            || !isset($FILE['size'])
            || !is_uploaded_file($FILE['tmp_name'])) {
            throw new FileImportException('File data is no valid file array or is not a uploaded file!');
        }
        if (($errorCode = $FILE['error']) !== UPLOAD_ERR_OK) {
            throw new FileImportException("File upload with error code: $errorCode");
        }
        try {
            $filename  = $this->prepareFilename($FILE['name'], $options);
            $tmpFolder = FileSystemHelper::getTempFolder(true);
            $tmpFile   = $tmpFolder . Statics::GLOBAL_SEPARATOR . $filename;
            if (move_uploaded_file($FILE['tmp_name'], $tmpFile)) {
                return $this->importFile([
                    'src'      => $tmpFile,
                    'name'     => $filename,
                    'mimeType' => $FILE['type'],
                    'size'     => $FILE['size'],
                ], $options);
            } else {
                throw new FileImportException("Could not move uploaded file to '$tmpFile'.");
            }
        } catch (FileNotFoundException | FileImportException | MimeTypeNotAllowedException | ORMException $exception) {
            throw $exception;
        } finally {
            FileSystemHelper::remove($tmpFolder);
        }
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param array $options [<br>
     *      'meta'   => [], // Optional, see FileManagementService::OPTIONS_META<br>
     *      'rename' => [], // Optional, see FileManagementService::OPTIONS_RENAME<br>
     *      ]
     *
     * @return File
     * @throws FileImportException
     * @throws FileNotFoundException
     * @throws MimeTypeNotAllowedException
     * @throws ORMException
     * @see FileManagementService::OPTIONS_META
     * @see FileManagementService::OPTIONS_RENAME
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function importUploadedFile(UploadedFile $uploadedFile, array $options = []) {
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            $errorCode = $uploadedFile->getError();
            throw new FileImportException("File upload with error code: $errorCode");
        }
        try {
            $filename  = $this->prepareFilename($uploadedFile->getClientFilename(), $options);
            $tmpFolder = FileSystemHelper::getTempFolder(true);
            $tmpFile   = $tmpFolder . Statics::GLOBAL_SEPARATOR . $filename;
            try {
                $uploadedFile->moveTo($tmpFile);
            } catch (Exception $exception) {
                throw new FileImportException("Could not move uploaded file to '$tmpFile'.", $exception);
            }
            if (!file_exists($tmpFile)) {
                throw new FileNotFoundException($tmpFile);
            }

            return $this->importFile([
                'src'      => $tmpFile,
                'name'     => $filename,
                'mimeType' => $uploadedFile->getClientMediaType(),
                'size'     => $uploadedFile->getSize(),
            ], $options);
        } catch (FileNotFoundException | FileImportException | MimeTypeNotAllowedException | ORMException $exception) {
            throw $exception;
        } finally {
            FileSystemHelper::remove($tmpFolder);
        }
    }

    /**
     * @param string $url
     * @param string|null $dstFilename
     * @param array $options [<br>
     *      'meta'   => [], // Optional, see FileManagementService::OPTIONS_META<br>
     *      'rename' => [], // Optional, see FileManagementService::OPTIONS_RENAME<br>
     *      ]
     *
     * @return File
     * @throws FileImportException
     * @throws FileNotFoundException
     * @throws MimeTypeNotAllowedException
     * @throws ORMException
     * @see FileManagementService::OPTIONS_META
     * @see FileManagementService::OPTIONS_RENAME
     */
    public function importFromUrl(string $url, ?string $dstFilename = null, array $options = []) : File {
        if ($dstFilename === null) {
            $dstFilename = basename($url);
        }
        $dstFilename = $this->prepareFilename($dstFilename, $options);
        $tmpFolder   = FileSystemHelper::getTempFolder(true);
        $tmpFile     = $tmpFolder . Statics::GLOBAL_SEPARATOR . $dstFilename;
        set_time_limit(0); // unlimited max execution time
        file_put_contents($tmpFile, fopen($url, 'r'));
        set_time_limit(OFORGE_SCRIPT_TIMEOUT);
        try {
            return $this->importLocalFile($tmpFile, false);
        } catch (FileNotFoundException | FileImportException | MimeTypeNotAllowedException | ORMException $exception) {
            throw $exception;
        } finally {
            FileSystemHelper::remove($tmpFolder);
        }
    }

    /**
     * @param string $srcFilePath
     * @param bool $copyFile
     * @param array $options [<br>
     *      'meta'   => [], // Optional, see FileManagementService::OPTIONS_META<br>
     *      'rename' => [], // Optional, see FileManagementService::OPTIONS_RENAME<br>
     *      'deleteAfterImport' => bool(false), // If $copyFile=true: Delete source file after import?<br>
     *      'prefixAfterImport' => ?string, // If $copyFile=true: Rename file with prefix after import, eg _<old_filename>?<br>
     *      ]<br>
     *
     * @return File
     * @throws FileImportException
     * @throws FileNotFoundException
     * @throws MimeTypeNotAllowedException
     * @throws ORMException
     * @see FileManagementService::OPTIONS_META
     * @see FileManagementService::OPTIONS_RENAME
     */
    public function importLocalFile(string $srcFilePath, bool $copyFile = false, array $options = []) : File {
        if (!file_exists($srcFilePath)) {
            throw new FileNotFoundException($srcFilePath);
        }

        $filename  = $this->prepareFilename(basename($srcFilePath), $options);
        $tmpFile   = $srcFilePath;
        $tmpFolder = null;
        if ($copyFile) {
            $tmpFolder = FileSystemHelper::getTempFolder(true);
            $tmpFile   = $tmpFolder . Statics::GLOBAL_SEPARATOR . $filename;
            if (!copy($srcFilePath, $tmpFile)) {
                throw new FileImportException("Could not copy file '$srcFilePath' to '$tmpFile'.");
            }
        }
        try {
            $file = $this->importFile([
                'src'      => $tmpFile,
                'name'     => $filename,
                'mimeType' => FileHelper::getMimeType($srcFilePath),
                'size'     => filesize($srcFilePath),
            ], $options);
            if ($copyFile) {
                if (ArrayHelper::get($options, 'deleteAfterImport', false)) {
                    @unlink($srcFilePath);
                } elseif (($prefixAfterImport = ArrayHelper::get($options, 'prefixAfterImport', null)) !== null && is_string($prefixAfterImport)) {
                    @rename($srcFilePath, dirname($srcFilePath) . Statics::GLOBAL_SEPARATOR . $prefixAfterImport . basename($srcFilePath));
                }
            }

            return $file;
        } catch (FileNotFoundException | FileImportException | MimeTypeNotAllowedException | ORMException $exception) {
            throw $exception;
        } finally {
            if ($tmpFolder !== null) {
                FileSystemHelper::remove($tmpFolder);
            }
        }
    }

    /**
     * @param int $fileID
     *
     * @throws FileEntryNotFoundException
     * @throws FileInUsageException
     * @throws ORMException
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function remove(int $fileID) {
        $file = $this->FileAccessService()->getOneByID($fileID);
        if ($file === null) {
            throw new FileEntryNotFoundException('id', $fileID);
        }
        /** @var FileUsageService $fileUsageService */
        /** @noinspection PhpUnhandledExceptionInspection */
        $fileUsageService = Oforge()->Services()->get('file.usage');
        if ($fileUsageService->isFileInUsage($fileID)) {
            throw new FileInUsageException($fileID);
        }
        $fileData = $file->toArray();
        $this->entityManager()->remove($file);
        Oforge()->Events()->trigger(Event::create(File::class . '::removed', $fileData));
        //Remove files & empty folders
        $absoluteFilePath = ROOT_PATH . $file->getFilePath();
        if (FileSystemHelper::remove($absoluteFilePath)) {
            $parentFolder = dirname($absoluteFilePath);
            if (FileSystemHelper::remove($parentFolder, false, false)) {
                FileSystemHelper::remove(dirname($absoluteFilePath), false, false);
            }
            // with removing of extra files?
            // if (FileSystemHelper::remove($parentFolder)) {
            //     FileSystemHelper::remove(dirname($absoluteFilePath), false, false);
            // }
        }
    }

    /**
     * @param array $data
     * @param array $options [<br>
     *      'meta'   => [], // Optional, see FileManagementService::OPTIONS_META<br>
     *      ]<br>
     *
     * @return File
     * @throws FileImportException
     * @throws FileNotFoundException
     * @throws MimeTypeNotAllowedException
     * @throws ORMException
     * @see FileManagementService::OPTIONS_META
     */
    protected function importFile(array $data, array $options) : File {
        $src      = $data['src'];
        $filename = $data['name'];
        $mimeType = strtolower($data['mimeType']);
        $size     = $data['size'];
        if (!file_exists($src)) {
            throw new FileNotFoundException($src);
        }
        if (empty($size)) {
            throw new FileImportException('Uploaded file has size of 0');
        }
        if (empty($mimeType)) {
            throw new FileImportException('Mime type of uploaded file is empty.');
        }
        $allowedFileMimeTypeService = $this->AllowedFileMimeTypeService();
        if (!$allowedFileMimeTypeService->isMimeTypeAllowed($mimeType)) {
            throw new MimeTypeNotAllowedException($mimeType);
        }
        $typeGroup = $allowedFileMimeTypeService->getMimeTypeGroup($mimeType);
        if ($typeGroup === null) {
            $mimeTypePrefix = StringHelper::substringBefore($mimeType, '/');

            $typeGroup = FileTypeGroup::isValid($mimeTypePrefix) ? $mimeTypePrefix : FileTypeGroup::DEFAULT;
        }
        $meta       = ArrayHelper::get($options, 'meta', []);
        $uploaderID = ArrayHelper::pop($meta, 'uploaderID', null);

        do {
            $relativeFilePath = implode(Statics::GLOBAL_SEPARATOR, [
                Statics::DIR_UPLOAD,#
                substr(md5(rand()), 0, 2),#
                substr(md5(rand()), 0, 2),#
                $filename,#
            ]);
            $absoluteFilePath = ROOT_PATH . $relativeFilePath;
            $folderPath       = dirname($absoluteFilePath);
        } while (file_exists($absoluteFilePath));
        FileSystemHelper::mkdir($folderPath);

        try {
            if ($typeGroup === 'image') {
                if (extension_loaded('gd')) {
                    /** @noinspection PhpUnusedLocalVariableInspection */
                    $size2 = getimagesize($src);
                }
            }
            if (rename($src, $absoluteFilePath)) {
                $file = File::create([
                    'typeGroup'  => $typeGroup,
                    'mimeType'   => $mimeType,
                    'size'       => $size,
                    'filePath'   => $relativeFilePath,
                    'uploaderID' => $uploaderID,
                    'meta'       => $meta,
                ]);
                $this->entityManager()->create($file);

                $eventData = array_merge($file->toArray(), [
                    'entity'           => $file,
                    'absoluteFilePath' => $absoluteFilePath,
                ]);

                Oforge()->Events()->trigger(Event::create(File::class . '::created', $eventData));

                return $file;
            } else {
                throw new FileImportException("Could not move file from '$src' to '$absoluteFilePath'.");
            }
        } catch (ORMException | FileImportException $exception) {
            throw $exception;
        } finally {
            FileSystemHelper::remove($folderPath);
        }
    }

    /**
     * Prepare target filename.
     *
     * @param string $filename
     * @param array $options [<br>
     *      'rename' => [], // Optional, see FileManagementService::OPTIONS_RENAME<br>
     *      ]<br>
     *
     * @return string
     * @see FileManagementService::OPTIONS_RENAME
     */
    protected function prepareFilename(string $filename, array $options) : string {
        $extension     = FileHelper::getExtension($filename);
        $renameOptions = ArrayHelper::dotGet($options, 'rename', []);
        if (($filename2 = ArrayHelper::dotGet($renameOptions, 'filename')) !== null) {
            $filename = $filename2;
        }
        $pathInfo  = pathinfo($filename);
        $extension = ArrayHelper::get($pathInfo, 'extension', $extension);
        $filename  = $pathInfo['filename'];
        if (($prefix = ArrayHelper::get($renameOptions, 'prefix')) !== null) {
            $filename = $prefix . $filename;
        }
        if (($suffix = ArrayHelper::get($renameOptions, 'suffix')) !== null) {
            $filename = $filename . $suffix;
        }

        return ($this->normalizeFilename($filename) . '.' . strtolower($extension));
    }

    /**
     * Normalize filename (without extension).
     * Lower chars, replace Umlaut (ä,ö,ü,ß) and replace all non-alpha-numeric chars with underline.
     *
     * @param string $filename
     *
     * @return string
     */
    protected function normalizeFilename(string $filename) : string {
        $filename = strtr(strtolower($filename), [
            'ä' => 'ae',
            'ö' => 'oe',
            'ü' => 'ue',
            'ß' => 'ss',
        ]);
        $filename = preg_replace("/_+/", '_', preg_replace("/[^a-z0-9]/", '_', $filename));

        return $filename;
    }

    /**
     * @return AllowedFileMimeTypeService
     * @noinspection PhpDocMissingThrowsInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    protected function AllowedFileMimeTypeService() : AllowedFileMimeTypeService {
        return Oforge()->Services()->get('file.mimeType');
    }

}
