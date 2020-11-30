<?php

namespace Oforge\Engine\Auth\Services;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Auth\AuthConstants;
use Oforge\Engine\Auth\Exceptions\Role\RoleAlreadyExistException;
use Oforge\Engine\Auth\Exceptions\Role\RoleImmutableException;
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
     *
     */
    public function installDefaultRoles() {
        foreach ([AuthConstants::ROLE_ANONYMOUS, AuthConstants::ROLE_USER] as $roleName) {
            try {
                $this->create($roleName, true);
            } catch (RoleAlreadyExistException $exception) {
                // nothing to do
            } catch (ORMException $exception) {
                Oforge()->Logger()->logException($exception);
            }
        }
    }

    /**
     * @param string $name
     * @param bool $active
     *
     * @return array
     * @throws ORMException
     * @throws RoleAlreadyExistException
     */
    public function create(string $name, bool $active = false) {
        $role = $this->getByName($name);
        if ($role === null) {
            throw new RoleAlreadyExistException($name);
        }
        $role = Role::create([
            'name'   => $name,
            'active' => $active,
        ]);

        $this->entityManager()->create($role);
        $roleData = $role->toArray();
        Oforge()->Events()->trigger(Event::create(Role::class . '::created', $roleData));

        return $roleData;
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
     * @throws RoleImmutableException
     * @throws ORMException
     */
    public function removeById(int $roleId) : void {
        $this->remove($this->getById($roleId), 'id', $roleId);
    }

    /**
     * @param string $name
     *
     * @throws EntityNotFoundException
     * @throws RoleImmutableException
     * @throws ORMException
     */
    public function removeByName(string $name) : void {
        $this->remove($this->getByName($name), 'name', $name);
    }

    /**
     * @param int $roleId
     * @param bool $active
     *
     * @throws EntityNotFoundException
     * @throws RoleImmutableException
     * @throws ORMException
     */
    public function setActivationById(int $roleId, bool $active) : void {
        $this->update($this->getById($roleId), ['active' => $active], 'id', $roleId);
    }

    /**
     * @param int $name
     * @param bool $active
     *
     * @throws EntityNotFoundException
     * @throws RoleImmutableException
     * @throws ORMException
     */
    public function setActivationByName(int $name, bool $active) : void {
        $this->update($this->getByName($name), ['active' => $active], 'name', $name);
    }

    /**
     * @param int $roleId
     * @param array $data
     *
     * @throws EntityNotFoundException
     * @throws RoleImmutableException
     * @throws ORMException
     */
    public function updateById(int $roleId, array $data) : void {
        $this->update($this->getById($roleId), $data, 'id', $roleId);
    }

    /**
     * @param string $name
     * @param array $data
     *
     * @throws EntityNotFoundException
     * @throws RoleImmutableException
     * @throws ORMException
     */
    public function updateByName(string $name, array $data) : void {
        $this->update($this->getByName($name), $data, 'name', $name);
    }

    /**
     * @param Role|null $role
     * @param string $key
     * @param int|string $value
     *
     * @throws EntityNotFoundException
     * @throws RoleImmutableException
     * @throws ORMException
     */
    protected function remove(?Role $role, string $key, $value) : void {
        if ($role === null) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(Role::class, [$key => $value]);
        }
        $roleName = $role->getName();
        if (in_array($roleName, [AuthConstants::ROLE_ANONYMOUS, AuthConstants::ROLE_USER])) {
            throw new RoleImmutableException("Role '$roleName' is not removable!");
        }
        $roleData = $role->toArray();
        $this->entityManager()->remove($role);
        Oforge()->Events()->trigger(Event::create(Role::class . '::removed', $roleData));
    }

    /**
     * @param Role|null $role
     * @param array $data
     * @param string $key
     * @param int|string $value
     *
     * @throws EntityNotFoundException
     * @throws RoleImmutableException
     * @throws ORMException
     */
    protected function update(?Role $role, array $data, string $key, $value) : void {
        if ($role === null) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(Role::class, [$key => $value]);
        }
        $roleName = $role->getName();
        if (isset($data['active']) && in_array($roleName, [AuthConstants::ROLE_ANONYMOUS, AuthConstants::ROLE_USER])) {
            throw new RoleImmutableException("Activation of role '$roleName' is not changeable!");
        }
        $role->fromArray($data);
        $this->entityManager()->update($role);
    }

}
