<?php
namespace Oforge\Engine\Auth\Models\User;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Core\Abstracts\AbstractModel;

/**
 * @ORM\Entity
 * @ORM\Table(name="oforge_auth_role")
 * @package Oforge\Engine\Auth\Models\User
 */
class Role extends AbstractModel
{
    /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $description
     * @ORM\Column(name="description", type="string", nullable=false, unique=true)
     */
    private $description;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Role
     */
    public function setDescription(string $description): Role
    {
        $this->description = $description;
        return $this;
    }
}
