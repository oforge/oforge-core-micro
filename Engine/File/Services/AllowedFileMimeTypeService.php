<?php

namespace Oforge\Engine\File\Services;

use Doctrine\ORM\ORMException;
use Exception;
use Oforge\Engine\Cache\Helper\Cache;
use Oforge\Engine\Cache\Lib\ArrayCache;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Core\Helper\CsvHelper;
use Oforge\Engine\File\Models\FileMimeType;

/**
 * Class AllowedFileMimeTypeService
 *
 * @package Oforge\Engine\File\Service
 */
class AllowedFileMimeTypeService extends AbstractDatabaseAccess {
    /** @var ArrayCache $cache */
    private $cache;

    /** AllowedFileMimeTypeService constructor. */
    public function __construct() {
        parent::__construct(FileMimeType::class);
        $this->cache = Cache::initArrayCache();
    }

    /**
     * Install default mime types specified in File/.meta/mimetypes.csv.
     */
    public function install() {
        try {
            CsvHelper::read(dirname(__DIR__) . '/.meta/mimetypes.csv', function (array $row) {
                $this->addMimeType([
                    'mimeType'  => strtolower($row[0]),
                    'fileExtension' => strtolower($row[1]),
                    'typeGroup' => strtolower($row[2]),
                    'allowed'   => ((bool) $row[3] && $row[3] !== 'false'),
                ]);
            });
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);
        }
    }

    /**
     * @param array $config
     *
     * @return bool
     */
    public function addMimeType(array $config) : bool {
        try {
            if ($this->isConfigValid($config)) {
                /** @var FileMimeType|null $entity */
                $entity = $this->getByMimeType($config['mimeType']);
                if ($entity === null) {
                    $entity = FileMimeType::create($config);
                    $this->entityManager()->create($entity);
                    $this->cache->set($entity->getMimeType(), $entity);
                }

                return true;
            }
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);
        }

        return false;
    }

    /**
     * @param string $mimeType
     * @param bool $allowed
     *
     * @return bool
     */
    public function setAllowed(string $mimeType, bool $allowed) : bool {
        /** @var FileMimeType $entity */
        $entity = $this->getByMimeType($mimeType);
        if ($entity !== null) {
            $entity->setAllowed($allowed);
            try {
                $this->entityManager()->update($entity);

                return true;
            } catch (ORMException $exception) {
                Oforge()->Logger()->logException($exception);
            }
        }

        return false;
    }

    /**
     * @param string $mimeType
     *
     * @return bool
     */
    public function isMimeTypeAllowed(string $mimeType) : bool {
        /** @var FileMimeType $entity */
        $entity = $this->getByMimeType($mimeType);
        if ($entity === null) {
            // TODO setting default value
            return false;
        }

        return $entity->isAllowed();
    }

    public function getMimeTypeExtension(string $mimeType) : ?string {
        /** @var FileMimeType $entity */
        $entity = $this->getByMimeType($mimeType);
        if ($entity === null) {
            return null;
        }

        return $entity->getFileExtension();
    }

    public function getMimeTypeGroup(string $mimeType) : ?string {
        /** @var FileMimeType $entity */
        $entity = $this->getByMimeType($mimeType);
        if ($entity === null) {
            return null;
        }

        return $entity->getTypeGroup();
    }

    protected function getByMimeType(string $mimeType) : ?FileMimeType {
        $mimeType = strtolower($mimeType);

        return $this->cache->getOrCreate($mimeType, function () use ($mimeType) {
            /** @var FileMimeType|null $entity */
            $entity = $this->repository()->findOneBy(['mimeType' => $mimeType]);

            return $entity;
        });
    }

    protected function isConfigValid(array $config) : bool {
        //TODO isConfigValid
        return true;
    }

}
