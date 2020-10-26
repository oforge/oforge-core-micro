<?php

namespace Oforge\Engine\File\Services;

use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Crud\Services\GenericCrudService;
use Oforge\Engine\File\Models\File;

/**
 * Class FileService
 *
 * @package Oforge\Engine\File\Service
 */
class FileAccessService extends AbstractDatabaseAccess {

    /** FileService constructor. */
    public function __construct() {
        parent::__construct(File::class);
    }

    /**
     * @param int $fileID
     *
     * @return bool
     */
    public function exist(int $fileID) : bool {
        return $this->repository()->count(['id' => $fileID]) === 1;
    }

    /**
     * @param int $fileID
     *
     * @return File|null
     */
    public function getOneByID(int $fileID) : ?File {
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
    public function getOneByFilePath(string $filePath) : ?File {
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
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return File[]
     */
    public function list(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
        return $this->repository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return File[]
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function listExtended($criteria = [], array $orderBy = null, ?int $limit = null, ?int $offset = null) {
        /** @var GenericCrudService $genericCrudService */
        /** @noinspection PhpUnhandledExceptionInspection */
        $genericCrudService = Oforge()->Services()->get('crud');
        /** @var File[] $entities */
        $entities = $genericCrudService->listByExtendedCriteria(File::class, $criteria, $orderBy, $limit, $offset);

        return $entities;
    }

}
