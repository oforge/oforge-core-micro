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

    /**
     * BaseLoginService constructor.
     */
    public function __construct() {
        parent::__construct(User::class);
    }

    /**
     * Validate login credentials against entities in the database and if valid, respond with a JWT.
     *
     * @param string $login
     * @param string $password
     *
     * @return array|null
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function login(string $login, string $password) : ?array {
        /** @noinspection PhpUnhandledExceptionInspection */
        $passwordService = Oforge()->Services()->get('auth.password');
        /** @var User|null $user */
        $user = $this->repository()->findOneBy([
            'login'  => $login,
            'active' => true,
        ]);
        if ($user !== null && $passwordService->validate($password, $user->getPassword())) {
            $userData = $user->toArray();
            unset($userData['password']);

            //TODO set direct to session & oforge->view->assign???

            return $userData;
        }

        return null;
    }

    public function logout() {
        // TODO remove session & oforge->view->assign???
    }

}
