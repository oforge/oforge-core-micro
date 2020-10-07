<?php

namespace Oforge\Engine\File\Service;

use Doctrine\ORM\ORMException;
use Exception;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Core\Helper\CsvHelper;
use Oforge\Engine\File\Model\FileMimeType;

/**
 * Class MimeTypeService
 *
 * @package Oforge\Engine\File\Service
 */
class MimeTypeService extends AbstractDatabaseAccess {

    /**
     * MimeTypeService constructor.
     */
    public function __construct() {
        parent::__construct(FileMimeType::class);
    }

    /**
     * Install default mime types specified in File/.meta/mimetypes.csv.
     */
    public function install() {
        try {
            CsvHelper::read(dirname(__DIR__) . '/.meta/mimetypes.csv', function (array $row) {
                $this->addMimeType([
                    'mimeType'  => strtolower($row[0]),
                    'typeGroup' => strtolower($row[1]),
                    'allowed'   => ((bool) $row && $row !== 'false'),
                ]);
            });
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);
        }
    }

    /**
     * @param array $config
     */
    public function addMimeType(array $config) : bool {
        try {
            if ($this->isConfigValid($config)) {
                /** @var FileMimeType|null $entity */
                $entity = $this->getByMimeType($config['mimeType']);
                if ($entity === null) {
                    $entity = FileMimeType::create($config);
                    $this->entityManager()->create($entity);
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

    public function getMimeTypeExtension(string $mimeType) {
        /** @var FileMimeType $entity */
        $entity = $this->getByMimeType($mimeType);
        if ($entity === null) {
            return null;
        }

        return $entity->getFileExtension();
    }

    protected function getByMimeType(string $mimeType) : ?FileMimeType {
        /** @var FileMimeType|null $entity */
        $entity = $this->repository()->findOneBy(['id' => strtolower($mimeType)]);

        return $entity;
    }

    protected function isConfigValid(array $config) : bool {
        //TODO isConfigValid
        return true;
    }

}
