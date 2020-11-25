<?php

namespace Oforge\Engine\Auth\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Core\Abstracts\AbstractModel;
use Oforge\Engine\Core\Traits\Model\BigintIdTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="oforge_auth_roles")
 * @package Oforge\Engine\Auth\Models
 */
class Role extends AbstractModel {
    use BigintIdTrait;

    /**
     * @var string $name
     * @ORM\Column(name="role_name", type="string", unique=true)
     */
    private $name;
    /**
     * @var bool $active
     * @ORM\Column(name="active", type="boolean", options={"default":false})
     */
    private $active = false;
    /**
     * @var string $shortDescription
     * @ORM\Column(name="short_description", type="string")
     */
    private $shortDescription;

    /**
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Role
     */
    public function setName(string $name) : Role {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive() : bool {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return Role
     */
    public function setActive(bool $active) : Role {
        $this->active = $active;

        return $this;
    }

    /**
     * @return string
     */
    public function getShortDescription() : string {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     *
     * @return Role
     */
    public function setShortDescription(string $shortDescription) : Role {
        $this->shortDescription = $shortDescription;

        return $this;
    }

}
