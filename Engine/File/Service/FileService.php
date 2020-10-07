<?php

namespace Oforge\Engine\File\Service;

use Exception;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Core\Helper\FileHelper;
use Oforge\Engine\Core\Helper\FileSystemHelper;
use Oforge\Engine\Core\Helper\Statics;
use Oforge\Engine\File\Model\File;
use Slim\Http\UploadedFile;

/**
 * Class FileService
 *
 * @package Oforge\Engine\File\Service
 */
class FileService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(File::class);
    }

    /**
     * @param array $array
     */
    public function addFilesEntry(array $array) {
        // TODO
    }

    /**
     * @param UploadedFile $uploadedFile
     */
    public function addUploadedFile(UploadedFile $uploadedFile) {
        // TODO
    }

    /**
     * @param string $srcFilePath
     * @param bool $deleteAfterImport
     */
    public function addLocalFile(string $srcFilePath, bool $deleteAfterImport = false) {
        if (!file_exists($srcFilePath)) {
            //TODO
            // throw new filenotfoundexception();
            throw new Exception();
        }
        $mimeType = FileHelper::getMimeType($srcFilePath);
        $filesize = filesize($srcFilePath);
        $this->add([
            'src'      => $srcFilePath,
            'name'     => basename($srcFilePath),
            'mimeType' => $mimeType,
            'size'     => $filesize,
        ]);
        if ($deleteAfterImport) {
            @unlink($srcFilePath);
        }
    }

    /**
     * @param int $fileID
     *
     * @dev
     */
    public function delete(int $fileID) {
        // TODO: we should be able to delete medias. Possible solution: create a reference table <media_id, full_class_name_and_function of usage>, count usage amount. if this table is empty then usage amount is 0 and file is deletable.
    }

    /**
     * @param string $url
     * @param string $dstFilename
     */
    public function downloadFromUrl(string $url, string $dstFilename) {
        $tmpFolder = FileSystemHelper::createTempFolder(true);
        $tmpFile   = $tmpFolder . Statics::GLOBAL_SEPARATOR . $dstFilename;
        set_time_limit(0); // unlimited max execution time
        file_put_contents($tmpFile, fopen($url, 'r'));
        try {
            $this->addLocalFile($tmpFile, true);
        } catch (Exception $exception) {
            throw $exception;
        } finally {
            FileSystemHelper::delete($tmpFolder);
        }
    }

    /**
     * @param int $fileID
     *
     * @return File|null
     */
    public function getById(int $fileID) : ?File {
        return $this->getOneBy([
            'id' => $fileID,
        ]);
    }

    /**
     * Find media by full path including the filename
     *
     * @param string $filePath
     *
     * @return File|null
     */
    public function getByFilePath(string $filePath) : ?File {
        return $this->getOneBy([
            'filePath' => $filePath,
        ]);
    }

    /**
     * @param array $criteria
     *
     * @return File|null
     */
    public function getOneBy(array $criteria) {
        /** @var File|null $entity */
        $entity = $this->repository()->findOneBy($criteria);

        return $entity;
    }

    /**
     * @param array $data
     */
    protected function add(array $data) {
        // if (!file_exists($src)) {
        //     // throw new filenotfoundexception();
        // }
        // if (empty($mimeType)) {
        //     throw new Exception();
        // }
        // if (empty($size)) {
        //     throw new Exception();
        // }
        //TODO
    }

}
