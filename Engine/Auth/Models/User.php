<?php

namespace Oforge\Engine\Auth\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Core\Abstracts\AbstractModel;
use Oforge\Engine\Core\Traits\Model\BigintIdTrait;
use Oforge\Engine\Core\Traits\Model\TimestampsTrait;

/**
 * Class User
 *
 * @ORM\Entity
 * @ORM\Table(name="oforge_auth_users")
 * @ORM\HasLifecycleCallbacks
 * @package Oforge\Engine\Auth\Models
 */
class User extends AbstractModel {
    use BigintIdTrait;
    use TimestampsTrait;

    /**
     * @var string $login
     * @ORM\Column(name="user_login", type="string", unique=true)
     */
    private $login;
    /**
     * @var bool $active
     * @ORM\Column(name="active", type="boolean", options={"default":false})
     */
    private $active = false;
    /**
     * @var string $password
     * @ORM\Column(name="user_password", type="string")
     */
    private $password;
    /**
     * @var string $email
     * @ORM\Column(name="email", type="string")
     */
    private $email;
    /**
     * @var bool $superAdmin
     * @ORM\Column(name="is_super_admin", type="boolean", options={"default":false})
     */
    private $superAdmin = false;

    /**
     * User constructor.
     */
    public function __construct() {
        $this->updatedTimestamps();
    }

    /**
     * @return string
     */
    public function getLogin() : string {
        return $this->login;
    }

    /**
     * @param string $login
     *
     * @return User
     */
    public function setLogin(string $login) : User {
        $this->login = $login;

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
     * @return User
     */
    public function setActive(bool $active) : User {
        $this->active = $active;

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
     * @return bool
     */
    public function isSuperAdmin() : bool {
        return $this->superAdmin;
    }

    /**
     * @param bool $superAdmin
     *
     * @return User
     */
    public function setSuperAdmin(bool $superAdmin) : User {
        $this->superAdmin = $superAdmin;

        return $this;
    }

}
