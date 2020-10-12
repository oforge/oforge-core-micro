<?php

namespace Oforge\Engine\File\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\File\Models\FileUsage;
use Oforge\Engine\File\Traits\Service\FileAccessServiceTrait;

/**
 * Class FileUsageService
 *
 * @package Oforge\Engine\File\Service
 */
class FileUsageService extends AbstractDatabaseAccess {
    use FileAccessServiceTrait;

    /** FileUsageService constructor. */
    public function __construct() {
        parent::__construct(FileUsage::class);
    }

    /**
     * @param int $fileID
     * @param string $entityID
     * @param string $entityClass
     * @param string $entityProperty
     * @param string|null $arrayPropertyPath
     *
     * @return bool
     */
    public function addOrChangeUsage(int $fileID, string $entityID, string $entityClass, string $entityProperty, ?string $arrayPropertyPath = null) {
        $existFile = $this->FileAccessService()->exist($fileID);
        if ($existFile) {
            $criteria  = [
                'entityID'          => $entityID,
                'entityClass'       => $entityClass,
                'entityProperty'    => $entityProperty,
                'arrayPropertyPath' => $arrayPropertyPath,
            ];
            $fileUsage = $this->getOneBy($criteria);
            try {
                if ($fileUsage === null) {
                    $fileUsage = FileUsage::create($criteria)->setFileID($fileID);
                    $this->entityManager()->create($fileUsage);

                    return true;
                } else {
                    $fileUsage->setFileID($fileID);
                    $this->entityManager()->update($fileUsage);

                    return true;
                }
            } catch (ORMException $exception) {
                Oforge()->Logger()->logException($exception);
            }
        }

        return false;
    }

    /**
     * @param int $fileID
     *
     * @return bool
     */
    public function isFileInUsage(int $fileID) : bool {
        return $this->repository()->count(['fileID' => $fileID]) > 0;
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return FileUsage[]
     */
    public function list(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
        return $this->repository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param int $fileID
     * @param string $entityID
     * @param string $entityClass
     * @param string $entityProperty
     * @param string|null $arrayPropertyPath
     *
     * @return bool
     */
    public function removeUsage(int $fileID, string $entityID, string $entityClass, string $entityProperty, ?string $arrayPropertyPath = null) {
        $criteria  = [
            'fileID'            => $fileID,
            'entityID'          => $entityID,
            'entityClass'       => $entityClass,
            'entityProperty'    => $entityProperty,
            'arrayPropertyPath' => $arrayPropertyPath,
        ];
        $fileUsage = $this->getOneBy($criteria);
        if ($fileUsage !== null) {
            try {
                $this->entityManager()->remove($fileUsage);

                return true;
            } catch (ORMException $exception) {
                Oforge()->Logger()->logException($exception);
            }
        }

        return false;
    }

    /**
     * @param int|null $oldFileID
     * @param int|null $newFileID
     * @param string $entityID
     * @param string $entityClass
     * @param string $entityProperty
     * @param string|null $arrayPropertyPath
     */
    public function updateUsage(
        ?int $oldFileID,
        ?int $newFileID,
        string $entityID,
        string $entityClass,
        string $entityProperty,
        ?string $arrayPropertyPath = null
    ) {
        if ($oldFileID === $newFileID) {
            return;
        }
        if (isset($oldFileID) && $newFileID === null) {
            $this->removeUsage($oldFileID, $entityID, $entityClass, $entityProperty, $arrayPropertyPath);
        }
        if (isset($newFileID)) {
            $this->addOrChangeUsage($newFileID, $entityID, $entityClass, $entityProperty, $arrayPropertyPath);
        }
    }

    /**
     * @param array $criteria
     *
     * @return FileUsage|null
     */
    protected function getOneBy(array $criteria) : ?FileUsage {
        /** @var FileUsage|null $entity */
        $entity = $this->repository()->findOneBy($criteria);

        return $entity;
    }

}
