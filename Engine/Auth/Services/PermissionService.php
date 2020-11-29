<?php

namespace Oforge\Engine\Auth\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Auth\Models\Permission;
use Oforge\Engine\Auth\Models\Role;
use Oforge\Engine\Auth\Models\RolePermission;
use Oforge\Engine\Auth\Models\UserPermission;
use Oforge\Engine\Auth\Models\UserRole;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;

/**
 * Class PermissionService
 *
 * @package Oforge\Engine\Auth\Services
 */
class PermissionService extends AbstractDatabaseAccess {

    /** UserService constructor. */
    public function __construct() {
        parent::__construct([
            Permission::class     => Permission::class,
            Role::class           => Role::class,
            UserRole::class       => UserRole::class,
            RolePermission::class => RolePermission::class,
            UserPermission::class => UserPermission::class,
        ]);
    }

    /**
     * @param string ...$permissions
     *
     * @throws ORMException
     */
    public function installPermission(string ...$permissions) {
        foreach ($permissions as $permission) {
            $criteria = ['permission' => $permission];
            /** @var Permission $entity */
            $entity = $this->repository(Permission::class)->findOneBy($criteria);
            if ($entity === null) {
                $entity = Permission::create($criteria);
                $this->entityManager()->create($entity, false);
            }
        }
        $this->entityManager()->flush();
    }

    /**
     * @param string ...$permissions
     *
     * @throws ORMException
     */
    public function uninstallPermission(string ...$permissions) {
        $this->removeEntities(Permission::class, [
            'permission' => $permissions,
        ]);
        $this->removeEntities(RolePermission::class, [
            'permission' => $permissions,
        ]);
        $this->removeEntities(UserPermission::class, [
            'permission' => $permissions,
        ]);
    }

    /**
     * @param int $userId
     *
     * @return array<string,bool>
     */
    public function getUserRolesAndPermissions(int $userId) : array {
        //TODO role order (for duplicates/overriding?)
        /**
         * @var Role[] $roleEntities
         * @var UserRole[] $userRoles
         * @var RolePermission[] $rolePermissions
         * @var UserPermission[] $userPermissions
         */
        $roles       = [];
        $permissions = [];
        $userRoles   = $this->repository(UserRole::class)->findBy(['userId' => $userId], null);
        $userRoleIds = [];
        foreach ($userRoles as $userRole) {
            $userRoleIds[] = $userRole->getRoleId();
        }
        $roleEntities = $this->repository(Role::class)->findBy(['roleId' => $userRoleIds]);
        foreach ($roleEntities as $roleEntity) {
            $roles[$roleEntity->getId()] = $roleEntity->getName();
        }
        $rolePermissions = $this->repository(RolePermission::class)->findBy(['roleId' => $userRoleIds]);
        foreach ($rolePermissions as $rolePermission) {
            $permissions[$rolePermission->getPermission()] = $rolePermission->getEnabled();
        }
        $userPermissions = $this->repository(UserPermission::class)->findBy(['userId' => $userId]);
        foreach ($userPermissions as $userPermission) {
            $permission = $userPermission->getPermission();
            $enabled    = $userPermission->getEnabled() ?? $permissions[$permission] ?? false;

            $permissions[$permission] = $enabled;
        }

        return [
            'roles'       => $roles,
            'permissions' => $permissions,
        ];
    }

}
