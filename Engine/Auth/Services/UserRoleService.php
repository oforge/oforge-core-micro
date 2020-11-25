<?php

namespace Oforge\Engine\Auth\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Auth\Exceptions\Role\RoleNotFoundException;
use Oforge\Engine\Auth\Exceptions\User\UserNotFoundException;
use Oforge\Engine\Auth\Models\Role;
use Oforge\Engine\Auth\Models\User;
use Oforge\Engine\Auth\Models\UserRole;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;

/**
 * Class UserRoleService
 *
 * @package Oforge\Engine\Auth\Services
 */
class UserRoleService extends AbstractDatabaseAccess {

    /** UserRoleService constructor. */
    public function __construct() {
        parent::__construct([
            'default' => UserRole::class,
            'role'    => Role::class,
            'user'    => User::class,
        ]);
    }

    /**
     * @param int $userId
     * @param int[] $roleIds
     *
     * @throws RoleNotFoundException
     * @throws UserNotFoundException
     * @throws ORMException
     */
    public function attachRoles(int $userId, array $roleIds) : void {
        $this->attach($roleIds, [$userId]);
    }

    /**
     * @param int $roleId
     * @param int[] $userIds
     *
     * @throws RoleNotFoundException
     * @throws UserNotFoundException
     * @throws ORMException
     */
    public function attachUsers(int $roleId, array $userIds) : void {
        $this->attach([$roleId], $userIds);
    }

    /**
     * @param int $userId
     * @param int[] $roleIds
     *
     * @throws ORMException
     */
    public function detachRoles(int $userId, array $roleIds) : void {
        $this->detach($roleIds, [$userId]);
    }

    /**
     * @param int $roleId
     * @param int[] $userIds
     *
     * @throws ORMException
     */
    public function detachUsers(int $roleId, array $userIds) : void {
        $this->detach([$roleId], $userIds);
    }

    /**
     * @param int[] $roleIds
     * @param int[] $userIds
     *
     * @throws RoleNotFoundException
     * @throws UserNotFoundException
     * @throws ORMException
     */
    protected function attach(array $roleIds, array $userIds) {
        /**
         * @var User[] $users
         * @var Role[] $roles
         */
        $roles      = $this->repository('role')->findBy(['id' => $roleIds]);
        $users      = $this->repository('user')->findBy(['id' => $userIds]);
        $mapRoleIds = array_flip($roleIds);
        foreach ($roles as $role) {
            unset($mapRoleIds[$role->getId()]);
        }
        if (!empty($mapRoleIds)) {
            $value = implode(',', array_keys($mapRoleIds));
            throw new RoleNotFoundException('id(s)', $value);
        }
        $mapUserIds = array_flip($userIds);
        foreach ($users as $user) {
            unset($mapUserIds[$user->getId()]);
        }
        if (!empty($mapUserIds)) {
            $value = implode(',', array_keys($mapUserIds));
            throw new UserNotFoundException('id(s)', $value);
        }
        foreach ($roles as $role) {
            foreach ($users as $user) {
                $data   = [
                    'roleId' => $role->getId(),
                    'userId' => $user->getId(),
                ];
                $entity = $this->repository()->findOneBy($data);
                if ($entity === null) {
                    $entity = UserRole::create($data);
                    $this->entityManager()->create($entity, false);
                }
            }
        }
        $this->entityManager()->flush();
    }

    /**
     * @param int[] $roleIds
     * @param int[] $userIds
     *
     * @throws ORMException
     */
    protected function detach(array $roleIds, array $userIds) {
        /** @var UserRole[] $entities */
        $entities = $this->repository()->findBy([
            'roleId' => $roleIds,
            'userId' => $userIds,
        ]);
        foreach ($entities as $entity) {
            $this->entityManager()->remove($entity, false);
        }
        $this->entityManager()->flush();
    }

}
