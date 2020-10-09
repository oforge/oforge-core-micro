<?php

namespace Oforge\Engine\File\Services;

use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\File\Exceptions\FileEntryNotFoundException;
use Oforge\Engine\File\Models\File;

/**
 * Class FileService
 *
 * @package Oforge\Engine\File\Service
 */
class FileService extends AbstractDatabaseAccess {

    /** FileService constructor. */
    public function __construct() {
        parent::__construct(File::class);
    }

    /**
     * @param int $fileID
     *
     * @throws FileEntryNotFoundException
     * @dev
     */
    public function delete(int $fileID) {
        $file = $this->getById($fileID);
        if ($file === null) {
            throw new FileEntryNotFoundException('id', $fileID);
        }
        // TODO: we should be able to delete files.  Possible solution: create a reference table <fileID, count usage amount. if table entry does not exist usage amount is 0 and file is deletable. if file is used in other model, then inc amount, if file is unset/replaced in other model, then dec amount.
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
     * @return File[]
     * @dev
     */
    public function list() {
        //TODO implement list
        return [];
    }

    /**
     * @return File[]
     * @dev
     */
    public function listExtended() {
        //TODO implement listExtended
        return [];
    }

}
