<?php

namespace Oforge\Engine\Auth\Services;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\ORMException;
use Exception;
use Oforge\Engine\Auth\Exceptions\InvalidPasswordFormatException;
use Oforge\Engine\Auth\Exceptions\PasswordGeneratorException;
use Oforge\Engine\Auth\Exceptions\User\UserAlreadyExistException;
use Oforge\Engine\Auth\Models\User;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Core\Manager\Events\Event;

/**
 * Class UserService
 *
 * @package Oforge\Engine\Auth\Services
 */
class UserService extends AbstractDatabaseAccess {

    /** UserService constructor. */
    public function __construct() {
        parent::__construct(User::class);
    }

    /**
     * @param int $userId
     * @param bool $active
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    public function setActivationById(int $userId, bool $active) : void {
        $this->setActivation($this->getById($userId), $active, 'id', $userId);
    }

    /**
     * @param string $login User login.
     * @param bool $active
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    public function setActivationByLogin(string $login, bool $active) : void {
        $this->setActivation($this->getByLogin($login), $active, 'login', $login);
    }

    /**
     * Create user.
     *
     * @param string $login
     * @param string $email
     * @param string|null $password
     *
     * @return array
     * @throws UserAlreadyExistException
     * @throws PasswordGeneratorException
     * @throws InvalidPasswordFormatException
     * @throws ORMException
     */
    public function create(string $login, string $email, ?string $password = null) : array {
        if ($this->existLogin($login)) {
            throw new UserAlreadyExistException($login);
        }
        /** @var PasswordService $passwordService */
        $passwordService = Oforge()->Services()->get('auth.password');
        if ($password === null) {
            try {
                $password = $passwordService->generatePassword();
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception);
                throw $exception;
            }
        }
        $passwordService->validateFormat($password);
        $passwordHash = $passwordService->hash($password);

        $user = User::create([
            'login'    => $login,
            'password' => $passwordHash,
            'email'    => $email,
            'active'   => true,
        ]);
        $this->entityManager()->create($user);
        $userData = $user->toArray();
        Oforge()->Events()->trigger(Event::create(User::class . '::created', $userData));

        return $userData;
    }

    /**
     * Check if login already exist and optional if from other user.
     *
     * @param string $login
     * @param int|null $userId
     *
     * @return bool
     */
    public function existLogin(string $login, ?int $userId = null) : bool {
        $user = $this->getByLogin($login);
        if ($user === null) {
            return false;
        }

        return $userId === null ? true : ($userId !== $user->getId());
    }

    /**
     * @param int $userId
     *
     * @return User|null
     */
    public function getById(int $userId) : ?User {
        return $this->getOneBy(['id' => $userId]);
    }

    /**
     * @param string $login
     *
     * @return User|null
     */
    public function getByLogin(string $login) : ?User {
        return $this->getOneBy(['login' => $login]);
    }

    /**
     * @param array $criteria
     *
     * @return User|null
     */
    public function getOneBy(array $criteria) : ?User {
        /** @var User|null $entity */
        $entity = $this->repository()->findOneBy($criteria);

        return $entity;
    }

    /**
     * @param int $userId
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    public function removeById(int $userId) : void {
        $this->remove($this->getById($userId), 'id', $userId);
    }

    /**
     * @param string $login User login.
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    public function removeByLogin(string $login) : void {
        $this->remove($this->getByLogin($login), 'login', $login);
    }

    /**
     * @param User|null $user
     * @param bool $active
     * @param string $key
     * @param int|string $value
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    protected function setActivation(?User $user, bool $active, string $key, $value) : void {
        if ($user === null) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(User::class, [$key => $value]);
        }
        $user->setActive($active);
        $this->entityManager()->update($user);
    }

    /**
     * @param User|null $user
     * @param string $key
     * @param int|string $value
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    protected function remove(?User $user, string $key, $value) : void {
        if ($user === null) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(User::class, [$key => $value]);
        }
        $userData = $user->toArray();
        $this->entityManager()->remove($user);
        Oforge()->Events()->trigger(Event::create(User::class . '::removed', $userData));
    }

}
