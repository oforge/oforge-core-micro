<?php

namespace Oforge\Engine\Auth\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Core\Abstracts\AbstractModel;

/**
 * Class UserRole
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="oforge_auth_user_roles")
 *
 * @package Oforge\Engine\Auth\Models
 */
class UserRole extends AbstractModel {
    /**
     * @var int $userId
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="user_id", type="bigint", options={"unsigned": true})
     */
    private $userId;
    /**
     * @var int $roleId
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="role_id", type="bigint", options={"unsigned": true})
     */
    private $roleId;

    /**
     * @return int
     */
    public function getRoleId() : int {
        return $this->roleId;
    }

    /**
     * @param int $roleId
     *
     * @return UserRole
     */
    protected function setRoleId(int $roleId) : UserRole {
        $this->roleId = $roleId;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId() : int {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return UserRole
     */
    protected function setUserId(int $userId) : UserRole {
        $this->userId = $userId;

        return $this;
    }

}
