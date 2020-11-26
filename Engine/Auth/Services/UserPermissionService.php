<?php

namespace Oforge\Engine\Auth\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Auth\Models\Permission;
use Oforge\Engine\Auth\Models\User;
use Oforge\Engine\Auth\Models\UserPermission;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Core\Manager\Events\Event;

/**
 * Class UserPermissionService
 *
 * @package Oforge\Engine\Auth\Services
 */
class UserPermissionService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct([
            Permission::class     => Permission::class,
            UserPermission::class => UserPermission::class,
        ]);

        Oforge()->Events()->attach(User::class . '::removed', Event::SYNC, function (Event $event) {
            $userId = $event->getDataValue('id');
            $this->removeEntities(UserPermission::class, [
                'userId' => $userId,
            ]);
        });
    }

    /**
     * @param int $userId
     * @param bool|null $enabled
     * @param string ...$permissions
     *
     * @throws ORMException
     */
    public function addUserPermissions(int $userId, ?bool $enabled, string ...$permissions) {
        $this->setUserPermissions($userId, array_fill_keys($permissions, $enabled), false);
    }

    /**
     * @param int $userId
     * @param string ...$permissions
     *
     * @throws ORMException
     */
    public function removeUserPermissions(int $userId, string ...$permissions) {
        $this->removeEntities(UserPermission::class, [
            'userId'     => $userId,
            'permission' => $permissions,
        ]);
    }

    /**
     * @param int $userId
     * @param string $permission
     * @param bool|null $enabled
     *
     * @throws ORMException
     */
    public function setUserPermission(int $userId, string $permission, ?bool $enabled) {
        $this->setUserPermissions($userId, [$permission => $enabled]);
    }

    /**
     * @param int $userId
     * @param array<string,bool> $permissions <permission, enabled>
     * @param bool $update
     *
     * @throws ORMException
     */
    public function setUserPermissions(int $userId, array $permissions, bool $update = true) {
        foreach ($permissions as $permission => $enabled) {
            $permissionNotExist = $this->repository(Permission::class)->count(['permission' => $permission]) === 0;
            if ($permissionNotExist) {
                continue;
            }
            $criteria = [
                'userId'     => $userId,
                'permission' => $permission,
            ];
            $entity   = $this->repository(UserPermission::class)->findOneBy($criteria);
            if ($entity === null) {
                $criteria['enabled'] = $enabled;
                $entity              = UserPermission::create($criteria);
                $this->entityManager()->create($entity, false);
                Oforge()->Events()->trigger(Event::create(UserPermission::class . '::created', $entity->toArray()));
            } elseif ($update) {
                $entity->setEnabled($enabled);
                $this->entityManager()->update($entity, false);
            }
        }
        $this->entityManager()->flush();
    }

}
