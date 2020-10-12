<?php

namespace Oforge\Engine\Crud\Service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Core\Abstracts\AbstractModel;
use Oforge\Engine\Core\Manager\Events\Event;
use Oforge\Engine\Crud\Enum\CrudFilterComparator;
use Oforge\Engine\Crud\Exceptions\EntityAlreadyExistException;
use Oforge\Engine\Crud\Exceptions\EntityNotFoundException;

/**
 * Class GenericCrudService
 *
 * @package Oforge\Engine\Crud\Service
 */
class GenericCrudService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct([]);
    }

    /**
     * @param string $class
     * @param array $criteria
     *
     * @return int
     */
    public function count(string $class, array $criteria = []) : int {
        return $this->getRepository($class)->count($criteria);
    }

    /**
     * @param string $class
     * @param array|callable $criteria
     *
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByExtendedCriteria(string $class, $criteria = []) {
        $queryBuilder = $this->getRepository($class)->createQueryBuilder('e')->select('count(e.id)');
        $this->evaluateExtendedCriteria($queryBuilder, $criteria);

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * Create entity if not exist yet.
     * If options contains a key <b>id</b> and an entity with the id exists, an ConfigElementAlreadyExistsException is thrown.
     *
     * @param string $class
     * @param array $options
     *
     * @return AbstractModel
     * @throws EntityAlreadyExistException
     * @throws ORMException
     */
    public function create(string $class, array $options) : AbstractModel {
        $repository = $this->getRepository($class);

        if (isset($options['id'])) {
            $id    = $options['id'];
            $count = $repository->count([
                'id' => $id,
            ]);
            if ($count > 0) {
                throw new EntityAlreadyExistException($class, $id);
            }
        }
        /** @var AbstractModel $instance */
        $instance = new $class();
        $instance = $instance->fromArray($options);

        $this->entityManager()->create($instance);
        $repository->clear();
        Oforge()->Events()->trigger(Event::create($class . '::created', $instance->toArray(0)));

        return $instance;
    }

    /**
     * Delete entity by id.
     * If entity not exist, an NotFoundException is thrown.
     *
     * @param string $class
     * @param int|string $id
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    public function delete(string $class, $id) {
        $entity = $this->getById($class, $id);
        if ($entity === null) {
            throw new EntityNotFoundException($class, $id);
        }
        $entityData = $entity->toArray(0);
        $this->entityManager()->remove($entity);
        $this->getRepository($class)->clear();
        Oforge()->Events()->trigger(Event::create($class . '::deleted', $entityData));
    }

    /**
     * Get single entity or null if not exist.
     *
     * @param string $class
     * @param int|string $id
     *
     * @return AbstractModel|null
     */
    public function getById(string $class, $id) : ?AbstractModel {
        return $this->getOneBy($class, [
            'id' => $id,
        ]);
    }

    /**
     * Get single entity or null if not exist.
     *
     * @param string $class
     * @param array $criteria
     *
     * @return AbstractModel|null
     */
    public function getOneBy(string $class, array $criteria) : ?AbstractModel {
        /** @var AbstractModel|null $entity */
        $entity = $this->getRepository($class)->findOneBy($criteria);

        return $entity;
    }

    /**
     * @param string $class
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $offset
     * @param int|null $limit
     *
     * @return AbstractModel[]
     */
    public function list(string $class, $criteria = [], array $orderBy = null, ?int $offset = null, ?int $limit = null) {
        return $this->getRepository($class)->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Get list of entities (data by toArray). If $params not empty, find entities by $params.
     *
     * @param string $class
     * @param array|callable $criteria
     * @param array|null $orderBy
     * @param int|null $offset
     * @param int|null $limit
     *
     * @return AbstractModel[]
     */
    public function listByExtendedCriteria(string $class, $criteria = [], array $orderBy = null, ?int $offset = null, ?int $limit = null) : array {
        $queryBuilder = $this->getRepository($class)->createQueryBuilder('e');
        $this->evaluateExtendedCriteria($queryBuilder, $criteria);
        if (!empty($orderBy)) {
            $first = true;
            foreach ($orderBy as $propertyName => $order) {
                if ($first) {
                    $first = false;
                    $queryBuilder->orderBy('e.' . $propertyName, $order);
                } else {
                    $queryBuilder->addOrderBy('e.' . $propertyName, $order);
                }
            }
        }
        if (isset($offset)) {
            $queryBuilder->setFirstResult($offset);
        }
        if (isset($limit)) {
            $queryBuilder->setMaxResults($limit);
        }
        /** @var AbstractModel[] $entities */
        $entities = $queryBuilder->getQuery()->getResult();

        return $entities;
    }

    /**
     * @param string $class
     * @param int|string $id
     * @param array $data
     * @param bool $flush
     *
     * @return AbstractModel
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    public function update(string $class, $id, array $data, bool $flush = true) : AbstractModel {
        $entity = $this->getById($class, $id);
        if ($entity === null) {
            throw new EntityNotFoundException($class, $id);
        }
        if (isset($data['id'])) {
            unset($data['id']);
        }
        $entity->fromArray($data);
        $this->entityManager()->update($entity, $flush);
        Oforge()->Events()->trigger(Event::create($class . '::updated', $entity->toArray(0)));

        return $entity;
    }

    /**
     * @param string $class
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function flush(string $class) {
        $this->entityManager()->flush();
        $this->getRepository($class)->clear();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array|callable $criteria
     */
    protected function evaluateExtendedCriteria(QueryBuilder &$queryBuilder, $criteria) {
        if (is_callable($criteria)) {
            $callable = $criteria;
            call_user_func($callable, $queryBuilder);
        } elseif (!empty($criteria)) {
            $parameters = [];
            $wheres     = [];
            foreach ($criteria as $propertyName => $propertyCriteria) {
                $prefixedProperty   = 'e.' . $propertyName;
                $valuePlaceholder   = ':' . $propertyName;
                $comparatorFunction = $propertyCriteria['comparator'];
                switch ($comparatorFunction) {
                    case CrudFilterComparator::LIKE:
                    case CrudFilterComparator::NOT_LIKE:
                        $parameters[$propertyName] = '%' . $propertyCriteria['value'] . '%';

                        $wheres[] = $queryBuilder->expr()->$comparatorFunction($prefixedProperty, $valuePlaceholder);
                        break;
                    case CrudFilterComparator::GREATER:
                    case CrudFilterComparator::GREATER_EQUALS:
                    case CrudFilterComparator::EQUALS:
                    case CrudFilterComparator::NOT_EQUALS:
                    case CrudFilterComparator::LESS:
                    case CrudFilterComparator::LESS_EQUALS:
                    case CrudFilterComparator::IN:
                    case CrudFilterComparator::NOT_IN:
                        $parameters[$propertyName] = $propertyCriteria['value'];

                        $wheres[] = $queryBuilder->expr()->$comparatorFunction($prefixedProperty, $valuePlaceholder);
                        break;
                    case CrudFilterComparator::IS_NULL:
                        $comparatorFunction = $propertyCriteria['value'] ? 'isNull' : 'isNotNull';

                        $wheres[] = $queryBuilder->expr()->$comparatorFunction($prefixedProperty);
                        break;
                    case CrudFilterComparator::BETWEEN:
                        $values = $propertyCriteria['value'];
                        if ((is_int($values[0]) || is_string($values[0])) && (is_int($values[1]) || is_string($values[1]))) {
                            $wheres[] = $queryBuilder->expr()->between($prefixedProperty, $values[0], $values[1]);
                        }
                        break;
                    default:
                        $parameters[$propertyName] = $propertyCriteria['value'];

                        $wheres[] = $queryBuilder->expr()->eq($prefixedProperty, $valuePlaceholder);
                        break;
                }
            }
            $queryBuilder = $queryBuilder->where($queryBuilder->expr()->andX()->addMultiple($wheres));
            if (!empty($parameters)) {
                $queryBuilder->setParameters($parameters);
            }
        }
    }

}
