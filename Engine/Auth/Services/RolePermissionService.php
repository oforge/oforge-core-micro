<?php

namespace Oforge\Engine\Auth\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Auth\Models\Permission;
use Oforge\Engine\Auth\Models\Role;
use Oforge\Engine\Auth\Models\RolePermission;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Core\Manager\Events\Event;

/**
 * Class RolePermissionService
 *
 * @package Oforge\Engine\Auth\Services
 */
class RolePermissionService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct([
            Permission::class     => Permission::class,
            RolePermission::class => RolePermission::class,
        ]);

        Oforge()->Events()->attach(Role::class . '::removed', Event::SYNC, function (Event $event) {
            $roleId = $event->getDataValue('id');
            $this->removeEntities(RolePermission::class, [
                'roleId' => $roleId,
            ]);
        });
    }

    /**
     * @param int $roleId
     * @param bool $enabled
     * @param string ...$permissions
     *
     * @throws ORMException
     */
    public function addRolePermissions(int $roleId, bool $enabled, string ...$permissions) {
        $this->setRolePermissions($roleId, array_fill_keys($permissions, $enabled), false);
    }

    /**
     * @param int $roleId
     * @param string ...$permissions
     *
     * @throws ORMException
     */
    public function removeRolePermissions(int $roleId, string ...$permissions) {
        $this->removeEntities(RolePermission::class, [
            'roleId'     => $roleId,
            'permission' => $permissions,
        ]);
    }

    /**
     * @param int $roleId
     * @param string $permission
     * @param bool $enabled
     *
     * @throws ORMException
     */
    public function setRolePermission(int $roleId, string $permission, bool $enabled) {
        $this->setRolePermissions($roleId, [$permission => $enabled]);
    }

    /**
     * @param int $roleId
     * @param array<string,bool> $permissions <permission, enabled>
     * @param bool $update
     *
     * @throws ORMException
     */
    public function setRolePermissions(int $roleId, array $permissions, bool $update = true) {
        foreach ($permissions as $permission => $enabled) {
            if ($enabled === null) {
                continue;
            }
            $permissionNotExist = $this->repository(Permission::class)->count(['permission' => $permission]) === 0;
            if ($permissionNotExist) {
                continue;
            }
            $criteria = [
                'roleId'     => $roleId,
                'permission' => $permission,
            ];
            $entity   = $this->repository(RolePermission::class)->findOneBy($criteria);
            if ($entity === null) {
                $criteria['enabled'] = $enabled;
                $entity              = RolePermission::create($criteria);
                $this->entityManager()->create($entity, false);
                Oforge()->Events()->trigger(Event::create(RolePermission::class . '::created', $entity->toArray()));
            } elseif ($update) {
                $entity->setEnabled($enabled);
                $this->entityManager()->update($entity, false);
            }
        }
        $this->entityManager()->flush();
    }

}
