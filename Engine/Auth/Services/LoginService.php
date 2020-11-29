<?php

namespace Oforge\Engine\Auth\Services;

use Oforge\Engine\Auth\Models\User;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;

/**
 * Class LoginService
 *
 * @package Oforge\Engine\Auth\Services
 */
class LoginService extends AbstractDatabaseAccess {

    /** BaseLoginService constructor. */
    public function __construct() {
        parent::__construct(User::class);
    }

    /**
     * Validate login credentials against entities in the database and if valid, store user data in session and view and respond it.
     *
     * @param string $login
     * @param string $password
     *
     * @return array|null
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function login(string $login, string $password) : ?array {
        /**
         * @var PasswordService $passwordService
         * @noinspection PhpUnhandledExceptionInspection
         */
        $passwordService = Oforge()->Services()->get('auth.password');
        /**
         * @var PermissionService $permissionService
         * @noinspection PhpUnhandledExceptionInspection
         */
        $permissionService = Oforge()->Services()->get('auth.permission');
        /** @var User|null $user */
        $user = $this->repository()->findOneBy([
            'login'  => $login,
            'active' => true,
        ]);
        if ($user !== null && $passwordService->validate($password, $user->getPassword())) {
            $this->logout();
            $userData = $user->toArray(2, ['password']);
            unset($userData['password']);
            $userData += $permissionService->getUserRolesAndPermissions($user->getId());

            Oforge()->View()->assign(['user' => $userData]);
            if (isset($_SESSION)) {
                $_SESSION['user'] = $userData;
            }

            return $userData;
        }

        return null;
    }

    /**
     * Remove user data of session an view.
     */
    public function logout() {
        Oforge()->View()->delete('user');
        if (isset($_SESSION)) {
            unset($_SESSION['user']);
        }
    }

}
