<?php

namespace Oforge\Engine\Core\Abstracts;

use Doctrine\ORM\EntityRepository;
use Oforge\Engine\Core\Forge\ForgeEntityManager;

/**
 * Class AbstractModel
 * (Database) Models from Modules or Plugins inherits from AbstractModel.
 *
 * @package Oforge\Engine\Core\Abstracts
 */
abstract class AbstractDatabaseAccess {
    protected const REPOSITORY_DEFAULT = 'default';
    /** @var ForgeEntityManager $forgeEntityManger */
    private $forgeEntityManger;
    /** @var array $repositories */
    private $repositories;
    /** @var array $models */
    private $models;

    /**
     * AbstractDatabaseAccess constructor.
     *
     * @param string|array $models
     */
    public function __construct($models) {
        $this->models = is_string($models) ? [self::REPOSITORY_DEFAULT => $models] : $models;
    }

    /** @return ForgeEntityManager */
    protected function entityManager() : ForgeEntityManager {
        if (!isset($this->forgeEntityManger)) {
            $this->forgeEntityManger = Oforge()->DB()->getForgeEntityManager();
        }

        return $this->forgeEntityManger;
    }

    /**
     * Returns repository for given class.
     *
     * @param string $class
     *
     * @return EntityRepository
     */
    protected function getRepository(string $class) : EntityRepository {
        return $this->entityManager()->getRepository($class);
    }

    /**
     * @param string $name
     *
     * @return EntityRepository
     */
    protected function repository(string $name = self::REPOSITORY_DEFAULT) : EntityRepository {
        if (!isset($this->repositories[$name])) {
            $this->repositories[$name] = $this->entityManager()->getRepository($this->models[$name]);
        }

        return $this->repositories[$name];
    }

}
