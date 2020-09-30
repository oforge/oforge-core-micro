<?php


namespace Oforge\Engine\Auth\Services;

use Exception;
use Oforge\Engine\Auth\Enums\InvalidPasswordFormatException;
use Oforge\Engine\Auth\Models\User\User;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Core\Helper\ArrayHelper;

/**
 * Class UserService
 *
 * @package Oforge\Engine\Modules\UserManagement\Services
 */
class UserService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct([
            'User' => User::class,
        ]);
    }

    /**
     * Create backend user.
     *
     * @param string $email
     * @param string $name
     * @param string|null $password
     * @param int $role
     *
     * @return string
     * @throws Exception
     */
    public function createUser(string $email, string $name, ?string $password = null, int $role = User::ROLE_ADMINISTRATOR) {
        if ($this->repository('User')->count(['email' => $email]) > 0) {
            return "User  with email '$email' already exists.";
        }

        $passwordService = new PasswordService();
        if ($password === null) {
            try {
                $password = $passwordService->generatePassword();
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception);
                throw $exception;
            }
        } else {
            try {
                $passwordService->validateFormat($password);
            } catch (InvalidPasswordFormatException $exception) {
                return 'Password format is not valid: ' . $exception->getMessage();
            }
        }
        $passwordHash = $passwordService->hash($password);

        $User = User::create([
            'email'    => $email,
            'name'     => $name,
            'role'     => $role,
            'password' => $passwordHash,
            'active'   => true,
        ]);
        $this->entityManager()->create($User);

        $role = ArrayHelper::get([
            User::ROLE_SYSTEM        => 'system',
            User::ROLE_ADMINISTRATOR => 'admin',
            User::ROLE_MODERATOR     => 'moderator',
        ], $role, $role);

        return "User ($role) created with email '$email' and password: " . $password;
    }

}
