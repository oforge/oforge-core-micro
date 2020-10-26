<?php

namespace Oforge\Engine\File\Services;

use Doctrine\ORM\ORMException;
use Exception;
use InvalidArgumentException;
use Oforge\Engine\Cache\Managers\CacheManager;
use Oforge\Engine\Cache\Lib\ArrayCache;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Core\Helper\ArrayHelper;
use Oforge\Engine\Core\Helper\CsvHelper;
use Oforge\Engine\Core\Services\ConfigService;
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
        $this->cache = CacheManager::initArrayCache();
    }

    /**
     * Install default mime types specified in File/.meta/mimetypes.csv.
     */
    public function install() {
        try {
            CsvHelper::read(dirname(__DIR__) . '/.meta/mimetypes.csv', function (array $row) {
                $this->addMimeType([
                    'mimeType'      => strtolower($row[0]),
                    'fileExtension' => strtolower($row[1]),
                    'typeGroup'     => strtolower($row[2]),
                    'allowed'       => ((bool) $row[3] && $row[3] !== 'false'),
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
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function isMimeTypeAllowed(string $mimeType) : bool {
        /** @var FileMimeType $entity */
        $entity = $this->getByMimeType($mimeType);
        if ($entity === null) {
            /** @var ConfigService $configService */
            $configService = Oforge()->Services()->get('config');

            /** @noinspection PhpUnhandledExceptionInspection */
            return $configService->get('file_import_mime_type_restriction');
        }

        return $entity->isAllowed();
    }

    /**
     * @param string $mimeType
     *
     * @return string|null
     */
    public function getMimeTypeExtension(string $mimeType) : ?string {
        /** @var FileMimeType $entity */
        $entity = $this->getByMimeType($mimeType);
        if ($entity === null) {
            return null;
        }

        return $entity->getFileExtension();
    }

    /**
     * @param string $mimeType
     *
     * @return string|null
     */
    public function getMimeTypeGroup(string $mimeType) : ?string {
        /** @var FileMimeType $entity */
        $entity = $this->getByMimeType($mimeType);
        if ($entity === null) {
            return null;
        }

        return $entity->getTypeGroup();
    }

    /**
     * @param string $mimeType
     *
     * @return FileMimeType|null
     */
    protected function getByMimeType(string $mimeType) : ?FileMimeType {
        $mimeType = strtolower($mimeType);

        return $this->cache->getOrCreate($mimeType, function () use ($mimeType) {
            /** @var FileMimeType|null $entity */
            $entity = $this->repository()->findOneBy(['mimeType' => $mimeType]);

            return $entity;
        });
    }

    /**
     * @param array $config
     *
     * @return bool
     * @throws ConfigOptionKeyNotExistException
     * @throws InvalidArgumentException
     */
    protected function isConfigValid(array $config) : bool {
        $requiredKeys = [
            'mimeType',
            'typeGroup',
            'allowed',
        ];
        foreach ($requiredKeys as $requiredKey) {
            if (!isset($config[$requiredKey])) {
                throw new ConfigOptionKeyNotExistException($requiredKey);
            }
        }
        $types = [
            'mimeType'      => 'string',
            'fileExtension' => '?string',
            'typeGroup'     => 'string',
            'allowed'       => 'bool',
        ];
        foreach ($types as $key => $type) {
            $value = ArrayHelper::get($config, $key);
            switch ($type) {
                case '?string':
                    if ($value !== null && !is_string($value)) {
                        throw new InvalidArgumentException("Config value '$key' must be of string or null.");
                    }
                    break;
                case 'bool':
                    if (!is_bool($value)) {
                        throw new InvalidArgumentException("Config value '$key' must be of bool.");
                    }
                    break;
                case 'string':
                    if (!is_string($value)) {
                        throw new InvalidArgumentException("Config value '$key' must be of bool.");
                    }
                    break;
            }
        }

        return true;
    }

}
