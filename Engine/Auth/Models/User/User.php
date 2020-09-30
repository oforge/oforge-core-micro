<?php
namespace Oforge\Engine\Auth\Models\User;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Core\Abstracts\AbstractModel;

/**
 * @ORM\Entity
 * @ORM\Table(name="oforge_auth_user")
 * @ORM\HasLifecycleCallbacks
 */
class User extends AbstractModel {

    /**
     * Roles
     */
    public const ROLE_SYSTEM        = 0;
    public const ROLE_ADMINISTRATOR = 1;
    public const ROLE_MODERATOR     = 2;
    public const ROLE_LOGGED_IN     = 999;
    public const ROLE_PUBLIC        = 1000;

    /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string $email
     * @ORM\Column(name="email", type="string", nullable=false, unique=true)
     */
    private $email;
    /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $password;
    /**
     * @var DateTimeImmutable $createdAt
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private $createdAt;
    /**
     * @var DateTimeImmutable $updatedAt
     * @ORM\Column(name="updated_at", type="datetime_immutable", nullable=false)
     */
    private $updatedAt;
    /**
     * @var bool $active
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default":false})
     */
    private $active = false;

    /**
     * @var int
     * @ORM\Column (name="role_id")
     */
    private $role;

    public function __construct() {
        $dateTimeNow     = new DateTimeImmutable('now');
        $this->createdAt = $dateTimeNow;
        $this->updatedAt = $dateTimeNow;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps() : void {
        $dateTimeNow     = new DateTimeImmutable('now');
        $this->updatedAt = $dateTimeNow;
        if ($this->createdAt === null) {
            $this->createdAt = $dateTimeNow;
        }
    }

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return User
     */
    public function setId(int $id) : User {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail() : string {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email) : User {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword() : string {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password) : User {
        $this->password = $password;

        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt() : DateTimeImmutable {
        return $this->createdAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedAt() : DateTimeImmutable {
        return $this->updatedAt;
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
     * @return User
     */
    public function setActive(bool $active) : User {
        $this->active = $active;

        return $this;
    }

    /**
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
    }

    /**
     * @param int $role
     * @return User
     */
    public function setRole(int $role): User
    {
        $this->role = $role;
        return $this;
    }
}
