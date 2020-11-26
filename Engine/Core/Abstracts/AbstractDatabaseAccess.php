<?php

namespace Oforge\Engine\Core\Abstracts;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Core\Forge\ForgeEntityManager;
use Oforge\Engine\Core\Manager\Events\Event;

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
        if (!isset($this->repositories[$class])) {
            $this->repositories[$class] = $this->entityManager()->getRepository($class);
        }

        return $this->repositories[$class];
    }

    /**
     * @param string $name
     *
     * @return EntityRepository|null
     */
    protected function repository(string $name = self::REPOSITORY_DEFAULT) : ?EntityRepository {
        return isset($this->models[$name]) ? $this->getRepository($this->models[$name]) : null;
    }

    /**
     * @param string $name
     * @param array $criteria
     *
     * @throws ORMException
     */
    protected function removeEntities(string $name, array $criteria) {
        /** @var AbstractClassPropertyAccess[] $entities */
        $entityClass = $this->models[$name];
        $entities    = $this->repository($name)->findBy($criteria);
        if (!empty($entities)) {
            $dataEntities = [];
            foreach ($entities as $entity) {
                $dataEntities[] = $entity->toArray();
                $this->entityManager()->remove($entity, false);
            }
            $this->entityManager()->flush();
            foreach ($dataEntities as $dataEntity) {
                Oforge()->Events()->trigger(Event::create($entityClass . '::removed', $dataEntity));
            }
        }
    }

}
