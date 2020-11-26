<?php

namespace Oforge\Engine\Auth\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Core\Abstracts\AbstractModel;

/**
 * Class RolePermission
 *
 * @ORM\Entity
 * @ORM\Table(name="oforge_auth_role_permissions")
 * @package Oforge\Engine\Auth\Models
 */
class RolePermission extends AbstractModel {
    /**
     * @var int $roleId
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="role_id", type="bigint", options={"unsigned": true})
     */
    private $roleId;
    /**
     * @var string $permission
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="permission", type="string")
     */
    private $permission;
    /**
     * @var bool $enabled
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @return int
     */
    public function getRoleId() : int {
        return $this->roleId;
    }

    /**
     * @param int $roleId
     *
     * @return RolePermission
     */
    protected function setRoleId(int $roleId) : RolePermission {
        if (!isset($this->roleId)) {
            $this->roleId = $roleId;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPermission() : string {
        return $this->permission;
    }

    /**
     * @param string $permission
     *
     * @return RolePermission
     */
    protected function setPermission(string $permission) : RolePermission {
        if (!isset($this->permission)) {
            $this->permission = $permission;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function getEnabled() : bool {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return RolePermission
     */
    public function setEnabled(bool $enabled) : RolePermission {
        $this->enabled = $enabled;

        return $this;
    }

}
