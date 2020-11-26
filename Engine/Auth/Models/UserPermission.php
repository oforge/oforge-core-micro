<?php

namespace Oforge\Engine\Auth\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Core\Abstracts\AbstractModel;

/**
 * Class UserPermission
 *
 * @ORM\Entity
 * @ORM\Table(name="oforge_auth_user_permissions")
 * @package Oforge\Engine\Auth\Models
 */
class UserPermission extends AbstractModel {
    /**
     * @var int $userId
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="user_id", type="bigint", options={"unsigned": true})
     */
    private $userId;
    /**
     * @var string $permission
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="permission", type="string")
     */
    private $permission;
    /**
     * @var bool|null $enabled
     * @ORM\Column(name="enabled", type="boolean", nullable=true, options={"default":null})
     */
    private $enabled = null;

    /**
     * @return int
     */
    public function getUserId() : int {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return UserPermission
     */
    protected function setUserId(int $userId) : UserPermission {
        if (!isset($this->userId)) {
            $this->userId = $userId;
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
     * @return UserPermission
     */
    protected function setPermission(string $permission) : UserPermission {
        if (!isset($this->permission)) {
            $this->permission = $permission;
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getEnabled() : ?bool {
        return $this->enabled;
    }

    /**
     * @param bool|null $enabled
     *
     * @return UserPermission
     */
    public function setEnabled(?bool $enabled) : UserPermission {
        $this->enabled = $enabled;

        return $this;
    }

}
