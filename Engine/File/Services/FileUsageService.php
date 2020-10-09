<?php

namespace Oforge\Engine\File\Services;

use Doctrine\ORM\ORMException;
use Exception;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Core\Manager\Events\Event;
use Oforge\Engine\File\Models\File;
use Oforge\Engine\File\Models\FileUsage;

/**
 * Class FileUsageService
 *
 * @package Oforge\Engine\File\Service
 */
class FileUsageService extends AbstractDatabaseAccess {

    /** FileService constructor. */
    public function __construct() {
        parent::__construct(FileUsage::class);

        Oforge()->Events()->attach(File::class . '::created', Event::SYNC, function (Event $event) {
            $this->initUsage($event->getDataValue('fileID'));
        });
        Oforge()->Events()->attach(File::class . '::removed', Event::SYNC, function (Event $event) {
            $this->removeUsage($event->getDataValue('fileID'));
        });
    }

    /**
     * @param string $fileID
     *
     * @return int
     */
    public function getUsageAmount(string $fileID) : int {
        $entity = $this->getByID($fileID);

        return $entity === null ? 0 : $entity->getUsageAmount();
    }

    /**
     * @param string $fileID
     *
     * @return bool
     */
    public function isUsed(string $fileID) : bool {
        return $this->getByID($fileID) !== null;
    }

    /**
     * @param string $fileID
     *
     * @return bool
     */
    public function decrementUsageAmount(string $fileID) : bool {
        $query = $this->repository()->createQueryBuilder('e')->update()#
                      ->set('e.usageAmount', 'e.usageAmount - 1')#
                      ->where('e.fileID = :fileID')#
                      ->andWhere('e.usageAmount > 0')->setParameter('fileID', $fileID)#
                      ->getQuery();
        try {
            $rows = $query->getSingleScalarResult();
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);
            $rows = 0;
        }

        return $rows === 1;
    }

    /**
     * @param string $fileID
     *
     * @return bool
     */
    public function incrementUsageAmount(string $fileID) : bool {
        $query = $this->repository()->createQueryBuilder('e')->update()#
                      ->set('e.usageAmount', 'e.usageAmount + 1')#
                      ->where('e.fileID = :fileID')#
                      ->setParameter('fileID', $fileID)#
                      ->getQuery();
        try {
            $rows = $query->getSingleScalarResult();
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);
            $rows = 0;
        }

        return $rows === 1;
    }

    /**
     * @param string|null $oldFileID
     * @param string|null $newFileID
     *
     * @return bool
     */
    public function updateUsageAmounts(?string $oldFileID, ?string $newFileID) {
        if ($oldFileID === $newFileID) {
            return false;
        }
        $success = true;
        if ($oldFileID !== null) {
            $success &= $this->decrementUsageAmount($oldFileID);
        }
        if ($newFileID !== null) {
            $success &= $this->incrementUsageAmount($newFileID);
        }

        return $success;
    }

    /**
     * @param string $fileID
     *
     * @return FileUsage|null
     */
    protected function getByID(string $fileID) : ?FileUsage {
        /** @var FileUsage|null $entity */
        $entity = $this->repository()->findOneBy(['fileID' => $fileID]);

        return $entity;
    }

    /**
     * @param string $fileID
     */
    protected function initUsage(string $fileID) {
        try {
            $entity = $this->getByID($fileID);
            if ($entity === null) {
                $entity = FileUsage::create(['fileID' => $fileID]);
                $this->entityManager()->create($entity);
            }
        } catch (ORMException $exception) {
            Oforge()->Logger()->logException($exception);
        }
    }

    /**
     * @param string $fileID
     */
    protected function removeUsage(string $fileID) {
        try {
            $entity = $this->getByID($fileID);
            if ($entity !== null) {
                $this->entityManager()->remove($entity);
            }
        } catch (ORMException $exception) {
            Oforge()->Logger()->logException($exception);
        }
    }

}
