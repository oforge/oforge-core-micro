<?php

namespace Oforge\Engine\Auth\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Core\Abstracts\AbstractModel;

/**
 * Class Permission
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="oforge_auth_permissions")
 *
 * @package Oforge\Engine\Auth\Models
 */
class Permission extends AbstractModel {
    /**
     * @var string $permission
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="permission", type="string")
     */
    private $permission;

    /**
     * @return string
     */
    public function getPermission() : string {
        return $this->permission;
    }

    /**
     * @param string $permission
     *
     * @return Permission
     */
    protected function setPermission(string $permission) : Permission {
        $this->permission = $permission;

        return $this;
    }

}
