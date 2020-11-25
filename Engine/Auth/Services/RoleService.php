<?php

namespace Oforge\Engine\Auth\Services;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Auth\Exceptions\Role\RoleAlreadyExistException;
use Oforge\Engine\Auth\Models\Role;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Core\Manager\Events\Event;

/**
 * Class RoleService
 *
 * @package Oforge\Engine\Auth\Services
 */
class RoleService extends AbstractDatabaseAccess {

    /** RoleService constructor. */
    public function __construct() {
        parent::__construct(Role::class);
    }

    /**
     * @param string $name
     * @param string|null $shortDescription
     * @param bool $active
     *
     * @return array
     * @throws ORMException
     * @throws RoleAlreadyExistException
     */
    public function create(string $name, ?string $shortDescription, bool $active = false) {
        $role = $this->getByName($name);
        if ($role === null) {
            throw new RoleAlreadyExistException($name);
        }
        $role = Role::create([
            'name'             => $name,
            'active'           => $active,
            'shortDescription' => $shortDescription,
        ]);

        $this->entityManager()->create($role);
        $roleData = $role->toArray();
        Oforge()->Events()->trigger(Event::create(Role::class . '::created', $roleData));

        return $roleData;
    }

    /**
     * @param int $roleId
     * @param bool $active
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    public function changeActivationById(int $roleId, bool $active) : void {
        $this->changeActivation($this->getById($roleId), $active, 'id', $roleId);
    }

    /**
     * @param int $name
     * @param bool $active
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    public function changeActivationByName(int $name, bool $active) : void {
        $this->changeActivation($this->getByName($name), $active, 'name', $name);
    }

    /**
     * @param int $roleId
     *
     * @return Role|null
     */
    public function getById(int $roleId) : ?Role {
        return $this->getOneBy(['id' => $roleId]);
    }

    /**
     * @param string $name
     *
     * @return Role|null
     */
    public function getByName(string $name) : ?Role {
        return $this->getOneBy(['name' => $name]);
    }

    /**
     * @param array $criteria
     *
     * @return Role|null
     */
    public function getOneBy(array $criteria) : ?Role {
        /** @var Role|null $entity */
        $entity = $this->repository()->findOneBy($criteria);

        return $entity;
    }

    /**
     * @param int $roleId
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    public function removeById(int $roleId) : void {
        $this->remove($this->getById($roleId), 'id', $roleId);
    }

    /**
     * @param string $name
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    public function removeByName(string $name) : void {
        $this->remove($this->getByName($name), 'name', $name);
    }

    /**
     * @param Role|null $role
     * @param bool $active
     * @param string $key
     * @param int|string $value
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    protected function changeActivation(?Role $role, bool $active, string $key, $value) {
        if ($role === null) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(Role::class, [$key => $value]);
        }
        $role->setActive($active);
        $this->entityManager()->update($role);
    }

    /**
     * @param Role|null $role
     * @param string $key
     * @param $value
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    protected function remove(?Role $role, string $key, $value) : void {
        if ($role === null) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(Role::class, [$key => $value]);
        }
        $roleData = $role->toArray();
        $this->entityManager()->remove($role);
        Oforge()->Events()->trigger(Event::create(Role::class . '::removed', $roleData));
    }

}
